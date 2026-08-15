<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Enums\RefundStatus;
use App\Models\Order;
use App\Models\ProductReturn;
use App\Models\Refund;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class RefundService
{
    /**
     * إنشاء Refund جديد من Return مكتمل.
     *
     * الشروط:
     * - Return يجب أن يكون completed.
     * - الطلب يجب أن يكون مدفوعًا.
     * - لا يوجد Refund سابق لنفس Return.
     * - مبلغ Refund لا يتجاوز المبلغ المدفوع.
     */
    public function create(
        ProductReturn $return,
        float $amount,
        ?string $reason = null
    ): Refund {
        return DB::transaction(function () use ($return, $amount, $reason) {

            $return->loadMissing('order');

            if ($return->status !== 'completed') {
                throw ValidationException::withMessages([
                    'return' => 'لا يمكن إنشاء Refund إلا بعد اكتمال عملية الإرجاع.',
                ]);
            }

            $order = $return->order;

            if (!$order) {
                throw ValidationException::withMessages([
                    'order' => 'الطلب المرتبط بالـ Return غير موجود.',
                ]);
            }

            /*
             * مهم:
             * لا نسمح بالـ Refund إذا كان الدفع Pending.
             */
            $paymentStatus = $order->payment_status;

            if ($paymentStatus instanceof PaymentStatus) {
                $paymentStatus = $paymentStatus->value;
            }

            if ($paymentStatus !== PaymentStatus::PAID->value) {
                throw ValidationException::withMessages([
                    'payment_status' => 'لا يمكن رد مبلغ لطلب لم يتم دفعه.',
                ]);
            }

            /*
             * منع Refund مرتين لنفس Return.
             */
            $existingRefund = Refund::where('return_id', $return->id)
                ->lockForUpdate()
                ->first();

            if ($existingRefund) {
                throw ValidationException::withMessages([
                    'refund' => 'يوجد Refund بالفعل لهذا الطلب المرتجع.',
                ]);
            }

            /*
             * التحقق من المبلغ.
             */
            if ($amount <= 0) {
                throw ValidationException::withMessages([
                    'amount' => 'مبلغ Refund يجب أن يكون أكبر من صفر.',
                ]);
            }

            if ($amount > (float) $order->total) {
                throw ValidationException::withMessages([
                    'amount' => 'مبلغ Refund لا يمكن أن يكون أكبر من إجمالي الطلب المدفوع.',
                ]);
            }

            return Refund::create([
                'return_id' => $return->id,
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'amount' => $amount,
                'status' => RefundStatus::PENDING->value,
                'reason' => $reason,
                'transaction_id' => null,
                'processed_at' => null,
                'completed_at' => null,
            ]);
        });
    }

    /**
     * تحويل Refund:
     *
     * pending → processing
     */
    public function process(Refund $refund): Refund
    {
        return DB::transaction(function () use ($refund) {

            $refund->refresh();

            $this->ensureStatus(
                $refund,
                [RefundStatus::PENDING]
            );

            $refund->update([
                'status' => RefundStatus::PROCESSING->value,
            ]);

            return $refund->refresh();
        });
    }

    /**
     * إكمال Refund:
     *
     * processing → completed
     */
    public function complete(
        Refund $refund,
        string $transactionId
    ): Refund {
        return DB::transaction(function () use ($refund, $transactionId) {

            $refund->refresh();

            $this->ensureStatus(
                $refund,
                [RefundStatus::PROCESSING]
            );

            if (trim($transactionId) === '') {
                throw ValidationException::withMessages([
                    'transaction_id' => 'Transaction ID مطلوب لإكمال عملية Refund.',
                ]);
            }

            $refund->update([
                'status' => RefundStatus::COMPLETED->value,
                'transaction_id' => $transactionId,
                'processed_at' => now(),
                'completed_at' => now(),
            ]);

            /*
             * تحديث حالة الدفع في الطلب.
             *
             * لا نعتبر الطلب "paid" بعد Refund كامل.
             */
            if ($refund->order) {
                $refund->order->update([
                    'payment_status' => PaymentStatus::REFUNDED->value,
                ]);
            }

            return $refund->refresh();
        });
    }

    /**
     * إلغاء Refund:
     *
     * pending / processing → cancelled
     *
     * لا يمكن إلغاء Refund مكتمل.
     */
    public function cancel(
        Refund $refund,
        ?string $reason = null
    ): Refund {
        return DB::transaction(function () use ($refund, $reason) {

            $refund->refresh();

            $this->ensureStatus(
                $refund,
                [
                    RefundStatus::PENDING,
                    RefundStatus::PROCESSING,
                ]
            );

            $refund->update([
                'status' => RefundStatus::CANCELLED->value,
                'reason' => $reason ?: $refund->reason,
            ]);

            return $refund->refresh();
        });
    }

    /**
     * التحقق من حالة Refund.
     *
     * مهم:
     * Model casts قد يعيد status كـ RefundStatus enum.
     *
     * لذلك لا نستخدم:
     *
     * RefundStatus::from($refund->status)
     *
     * مباشرة.
     *
     * إذا كانت القيمة Enum نستخدمها كما هي،
     * وإذا كانت string نحولها إلى Enum.
     */
    protected function ensureStatus(
        Refund $refund,
        array $allowedStatuses
    ): void {
        $current = $refund->status;

        if (!$current instanceof RefundStatus) {
            $current = RefundStatus::from($current);
        }

        $allowedValues = array_map(
            function ($status) {
                if ($status instanceof RefundStatus) {
                    return $status->value;
                }

                return RefundStatus::from($status)->value;
            },
            $allowedStatuses
        );

        if (!in_array($current->value, $allowedValues, true)) {
            throw ValidationException::withMessages([
                'status' => sprintf(
                    'لا يمكن تنفيذ العملية على Refund حالته الحالية: %s',
                    $current->value
                ),
            ]);
        }
    }
}