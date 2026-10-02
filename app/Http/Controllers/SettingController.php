<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'shop_name' => Setting::get('shop_name', 'Coffee POS'),
            'shop_address' => Setting::get('shop_address', '123 Street Name'),
            'shop_phone' => Setting::get('shop_phone', '+855 12 345 678'),
            'tax_percentage' => Setting::get('tax_percentage', '5'),
            'currency_symbol' => Setting::get('currency_symbol', '$'),
            'receipt_footer' => Setting::get('receipt_footer', 'Thank you for your visit!'),
        ];

        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'shop_name' => ['required', 'string', 'max:255'],
            'shop_address' => ['required', 'string', 'max:255'],
            'shop_phone' => ['required', 'string', 'max:50'],
            'tax_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'currency_symbol' => ['required', 'string', 'max:10'],
            'receipt_footer' => ['required', 'string', 'max:500'],
        ]);

        foreach ($validated as $key => $val) {
            Setting::set($key, $val);
        }

        return redirect()->route('settings.index')->with('success', 'System settings updated successfully.');
    }
}
