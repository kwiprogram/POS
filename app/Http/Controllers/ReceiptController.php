<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReceiptController extends Controller
{
    public function show(Order $order)
    {
        $order->load(['customer', 'table', 'cashier', 'items.coffee', 'payment']);
        $settings = [
            'shop_name' => Setting::get('shop_name', 'Coffee POS'),
            'shop_address' => Setting::get('shop_address', '123 Street Name'),
            'shop_phone' => Setting::get('shop_phone', '+855 12 345 678'),
            'currency' => Setting::get('currency_symbol', '$'),
            'receipt_footer' => Setting::get('receipt_footer', 'Thank you for your visit!'),
        ];

        return view('receipt.show', compact('order', 'settings'));
    }

    public function downloadPdf(Order $order)
    {
        $order->load(['customer', 'table', 'cashier', 'items.coffee', 'payment']);
        $settings = [
            'shop_name' => Setting::get('shop_name', 'Coffee POS'),
            'shop_address' => Setting::get('shop_address', '123 Street Name'),
            'shop_phone' => Setting::get('shop_phone', '+855 12 345 678'),
            'currency' => Setting::get('currency_symbol', '$'),
            'receipt_footer' => Setting::get('receipt_footer', 'Thank you for your visit!'),
        ];

        $pdf = Pdf::loadView('receipt.pdf', compact('order', 'settings'))
            ->setPaper([0, 0, 226.77, 600], 'portrait'); // 80mm thermal receipt paper format

        return $pdf->download("Receipt-{$order->order_number}.pdf");
    }
}
