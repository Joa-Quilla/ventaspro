<?php

namespace App\Observers;

use App\Models\Customer;
use App\Models\Payment;

class PaymentObserver
{
    /**
     * Handle the Payment "created" event.
     * Cuando se registra un pago, disminuir el balance del cliente
     */
    public function created(Payment $payment): void
    {
        $customer = Customer::find($payment->customer_id);

        if ($customer) {
            // Restar el monto del pago del balance (disminuir deuda)
            $customer->decrement('current_balance', $payment->amount);
        }
    }

    /**
     * Handle the Payment "deleted" event.
     * Si se elimina un pago, restaurar el balance
     */
    public function deleted(Payment $payment): void
    {
        $customer = Customer::find($payment->customer_id);

        if ($customer) {
            // Sumar el monto del pago al balance (aumentar deuda)
            $customer->increment('current_balance', $payment->amount);
        }
    }
}
