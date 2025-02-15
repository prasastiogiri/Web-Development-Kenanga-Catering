<?php

if (!function_exists('getStatusColor')) {
    function getStatusColor($status) {
        return match ($status) {
            'success' => 'success',
            'pending' => 'warning',
            'failed' => 'danger',
            'expired' => 'secondary',
            'challenge' => 'info',
            default => 'secondary',
        };
    }
}

if (!function_exists('formatPaymentMethod')) {
    function formatPaymentMethod($method) {
        return match ($method) {
            'credit_card' => 'Kartu Kredit',
            'bca_va' => 'BCA Virtual Account',
            'bni_va' => 'BNI Virtual Account',
            'bri_va' => 'BRI Virtual Account',
            'gopay' => 'GoPay',
            'shopeepay' => 'ShopeePay',
            default => ucfirst(str_replace('_', ' ', $method)),
        };
    }
}
