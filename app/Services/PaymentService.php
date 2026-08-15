<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * PaymentService
 *
 * النطاق الحالي: الدفع عند الاستلام (COD) فقط، بنفس ما هو مطبق
 * في Checkout الحالي. الدفع الإلكتروني الحقيقي (Gateway/Webhook)
 * لم يُبنَ بعد ومحجوز لمرحلة لاحقة (Payments Phase في الخريطة).
 *
 * هذا الـ Service مسؤول عن:
 * - إنشاء سجل Payment مع كل Order جديد.
 * - تحويل حالته (paid / failed).
 * - تسجيل كل تغيير في payment_logs كـ Audit Trail.
 */
class PaymentService
{
    /**
     * إنشاء سجل دفع "pending" لطلب جديد.
     *
     * يُستدعى مباشرة بعد إنشاء الـ Order (مثلاً من داخل
     * OrderService::createFromCart أو من الـ Controller الذي يستدعيه).
     */
    public function createForOrder(Order $order): Payment
    {
        return DB::transaction(function () use ($order) {

            $existing = Payment::query()
                ->where('order_id', $order->id)
                ->first();

            if ($existing) {
                return $existing;
            }

            $payment = Payment::create([
                'order_id' => $order->id,
                'method' => $order->payment_method,
                'status' => PaymentStatus::PENDING->value,
                'amount' => $order->total,
                'refunded_amount' => 0,
                'currency' => 'EGP',
            ]);

            $this->log($payment, action: 'created', status: PaymentStatus::PENDING->value);

            return $payment;
        });
    }

    /**
     * تسجيل الدفعة كمدفوعة.
     *
     * لـ COD: تُستدعى عادة عند تسليم الطلب (OrderService::deliver).
     * لبوابة إلكترونية لاحقًا: تُستدعى من الـ Webhook بعد التأكد من الدفع.
     */
    public function markPaid(Payment $payment, ?string $transactionId = null): Payment
    {
        return DB::transaction(function () use ($payment, $transactionId) {

            $payment = Payment::query()
                ->whereKey($payment->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($payment->status === PaymentStatus::PAID) {
                throw ValidationException::withMessages([
                    'payment' => 'هذه الدفعة مسجّلة كمدفوعة بالفعل.',
                ]);
            }

            if ($payment->status === PaymentStatus::REFUNDED) {
                throw ValidationException::withMessages([
                    'payment' => 'لا يمكن تعليم دفعة مستردة بالكامل كمدفوعة.',
                ]);
            }

            $payment->update([
                'status' => PaymentStatus::PAID->value,
                'transaction_id' => $transactionId ?? $payment->transaction_id,
                'paid_at' => now(),
            ]);

            $payment->order()->update([
                'payment_status' => PaymentStatus::PAID->value,
            ]);

            $this->log(
                $payment,
                action: 'paid',
                status: PaymentStatus::PAID->value,
                amount: $payment->amount,
                transactionId: $transactionId,
            );

            return $payment->fresh();
        });
    }

    /**
     * تسجيل فشل الدفعة (مثلاً: العميل رفض الاستلام في حالة COD،
     * أو رفضت البوابة الدفعة لاحقًا).
     */
    public function markFailed(Payment $payment, ?string $reason = null): Payment
    {
        return DB::transaction(function () use ($payment, $reason) {

            $payment = Payment::query()
                ->whereKey($payment->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($payment->status === PaymentStatus::PAID) {
                throw ValidationException::withMessages([
                    'payment' => 'لا يمكن تعليم دفعة مدفوعة بالفعل كفاشلة.',
                ]);
            }

            $payment->update([
                'status' => PaymentStatus::FAILED->value,
            ]);

            $payment->order()->update([
                'payment_status' => PaymentStatus::FAILED->value,
            ]);

            $this->log(
                $payment,
                action: 'failed',
                status: PaymentStatus::FAILED->value,
                message: $reason,
            );

            return $payment->fresh();
        });
    }

    /**
     * تسجيل مبلغ مسترد على الدفعة (يُستدعى من RefundService بعد
     * اعتماد الاسترداد، وليس مسؤولاً عن منطق الإرجاع نفسه).
     */
    public function recordRefundedAmount(Payment $payment, float $amount): Payment
    {
        return DB::transaction(function () use ($payment, $amount) {

            $payment = Payment::query()
                ->whereKey($payment->id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($amount <= 0) {
                throw ValidationException::withMessages([
                    'refund' => 'قيمة الاسترداد يجب أن تكون أكبر من صفر.',
                ]);
            }

            if ($amount > $payment->refundableAmount()) {
                throw ValidationException::withMessages([
                    'refund' => sprintf(
                        'قيمة الاسترداد (%s) أكبر من المبلغ القابل للاسترداد (%s).',
                        $amount,
                        $payment->refundableAmount()
                    ),
                ]);
            }

            $newRefundedTotal = (float) $payment->refunded_amount + $amount;

            $payment->update([
                'refunded_amount' => $newRefundedTotal,
                'status' => $newRefundedTotal >= (float) $payment->amount
                    ? PaymentStatus::REFUNDED->value
                    : PaymentStatus::PARTIALLY_REFUNDED->value,
            ]);

            $this->log(
                $payment,
                action: 'refunded',
                status: $payment->status->value,
                amount: $amount,
            );

            return $payment->fresh();
        });
    }

    /**
     * تسجيل حركة في payment_logs.
     */
    protected function log(
        Payment $payment,
        string $action,
        ?string $status = null,
        ?float $amount = null,
        ?string $transactionId = null,
        ?string $message = null,
        ?array $payload = null,
    ): void {
        PaymentLog::create([
            'payment_id' => $payment->id,
            'action' => $action,
            'status' => $status,
            'amount' => $amount,
            'transaction_id' => $transactionId,
            'message' => $message,
            'payload' => $payload,
            'created_by' => auth()->id(),
        ]);
    }
}
