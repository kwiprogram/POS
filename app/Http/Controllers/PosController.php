<?php

namespace App\Http\Controllers;

use App\Models\Coffee;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Table;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PosController extends Controller
{
    public function index()
    {
        $categories = Category::where('status', 'active')->get();
        $coffees = Coffee::where('status', 'available')->with('category')->get();
        $customers = Customer::orderBy('name')->get();
        $tables = Table::orderBy('table_number')->get();
        
        $taxPercentage = (float) Setting::get('tax_percentage', 5);
        $currencySymbol = Setting::get('currency_symbol', '$');

        return view('pos.index', compact('categories', 'coffees', 'customers', 'tables', 'taxPercentage', 'currencySymbol'));
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.id' => ['required', 'exists:coffees,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'table_id' => ['nullable', 'exists:tables,id'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'payment_method' => ['required', 'in:cash,credit_card,aba_khqr,acleda,wing'],
            'amount_paid' => ['required', 'numeric', 'min:0'],
        ]);

        $itemsData = $request->items;
        $discount = (float) ($request->discount ?? 0);
        $taxPercent = (float) Setting::get('tax_percentage', 5);

        DB::beginTransaction();
        try {
            // 1. Calculate items subtotal and verify stock
            $calculatedSubtotal = 0;
            $orderItems = [];

            foreach ($itemsData as $item) {
                $coffee = Coffee::findOrFail($item['id']);
                
                if ($coffee->stock < $item['quantity']) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => "Insufficient stock for {$coffee->name}. Only {$coffee->stock} left."
                    ], 422);
                }

                $itemSubtotal = $coffee->price * $item['quantity'];
                $calculatedSubtotal += $itemSubtotal;

                $orderItems[] = [
                    'coffee' => $coffee,
                    'quantity' => $item['quantity'],
                    'price' => $coffee->price,
                    'subtotal' => $itemSubtotal,
                ];

                // Decrement stock
                $coffee->decrement('stock', $item['quantity']);
                if ($coffee->stock <= 0) {
                    $coffee->update(['status' => 'out_of_stock']);
                }
            }

            // 2. Calculations
            $taxAmount = round(($calculatedSubtotal - $discount) * ($taxPercent / 100), 2);
            if ($taxAmount < 0) $taxAmount = 0;
            
            $grandTotal = round(($calculatedSubtotal - $discount + $taxAmount), 2);
            $amountPaid = (float) $request->amount_paid;

            if ($amountPaid < $grandTotal) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Amount paid ($' . number_format($amountPaid, 2) . ') cannot be less than Grand Total ($' . number_format($grandTotal, 2) . ').'
                ], 422);
            }

            $change = round($amountPaid - $grandTotal, 2);

            // 3. Create Order Number
            $todayCode = date('Ymd');
            $countToday = Order::whereDate('created_at', date('Y-m-d'))->count() + 1;
            $orderNumber = 'ORD-' . $todayCode . '-' . str_pad($countToday, 4, '0', STR_PAD_LEFT);

            // 4. Save Order
            $order = Order::create([
                'order_number' => $orderNumber,
                'customer_id' => $request->customer_id,
                'table_id' => $request->table_id,
                'user_id' => Auth::id(),
                'subtotal' => $calculatedSubtotal,
                'discount' => $discount,
                'tax' => $taxAmount,
                'total' => $grandTotal,
                'payment_status' => 'paid',
                'order_status' => 'completed',
            ]);

            // Save Order Items
            foreach ($orderItems as $oi) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'coffee_id' => $oi['coffee']->id,
                    'quantity' => $oi['quantity'],
                    'price' => $oi['price'],
                    'discount' => 0.00,
                    'subtotal' => $oi['subtotal'],
                ]);
            }

            // Save Payment record
            Payment::create([
                'order_id' => $order->id,
                'payment_method' => $request->payment_method,
                'subtotal' => $calculatedSubtotal,
                'discount' => $discount,
                'tax' => $taxAmount,
                'total' => $grandTotal,
                'amount_paid' => $amountPaid,
                'change' => $change,
                'paid_at' => now(),
            ]);

            // Update table status if table selected
            if ($request->table_id) {
                Table::where('id', $request->table_id)->update(['status' => 'occupied']);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order created and payment processed successfully!',
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'redirect_url' => route('receipt.show', $order->id),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error processing order: ' . $e->getMessage()
            ], 500);
        }
    }
}
