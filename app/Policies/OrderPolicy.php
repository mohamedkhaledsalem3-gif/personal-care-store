<?php

namespace App\Policies;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * السماح للمستخدم بعرض الطلب.
     *
     * العميل يستطيع مشاهدة طلباته فقط.
     */
    public function view(User $user, Order $order): bool
    {
        return (int) $order->user_id === (int) $user->id;
    }

    /**
     * السماح للمستخدم بإلغاء الطلب.
     *
     * الملكية شرط أساسي، بالإضافة إلى أن حالة الطلب
     * يجب أن تكون من الحالات التي يسمح فيها الـOrderService بالإلغاء.
     */
    public function cancel(User $user, Order $order): bool
    {
        if ((int) $order->user_id !== (int) $user->id) {
            return false;
        }

        $status = OrderStatus::from($order->status);

        return in_array(
            $status,
            [
                OrderStatus::PENDING,
                OrderStatus::CONFIRMED,
                OrderStatus::PROCESSING,
            ],
            true
        );
    }
}