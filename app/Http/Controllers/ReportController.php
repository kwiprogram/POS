<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Customer;
use App\Models\Setting;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $period = $request->get('period', 'daily'); // daily, weekly, monthly, yearly
        $dateStr = $request->get('date', Carbon::today()->format('Y-m-d'));

        $startDate = Carbon::parse($dateStr)->startOfDay();
        $endDate = Carbon::parse($dateStr)->endOfDay();

        if ($period === 'weekly') {
            $startDate = Carbon::parse($dateStr)->startOfWeek();
            $endDate = Carbon::parse($dateStr)->endOfWeek();
        } elseif ($period === 'monthly') {
            $startDate = Carbon::parse($dateStr)->startOfMonth();
            $endDate = Carbon::parse($dateStr)->endOfMonth();
        } elseif ($period === 'yearly') {
            $startDate = Carbon::parse($dateStr)->startOfYear();
            $endDate = Carbon::parse($dateStr)->endOfYear();
        }

        $query = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('payment_status', 'paid');

        $totalRevenue = (float) $query->sum('total');
        $totalOrders = $query->count();
        $uniqueCustomers = (int) $query->distinct('customer_id')->count('customer_id');

        // Best seller item
        $topItems = OrderItem::select('coffee_id', DB::raw('SUM(quantity) as total_qty'), DB::raw('SUM(subtotal) as total_amount'))
            ->whereHas('order', function($q) use ($startDate, $endDate) {
                $q->whereBetween('created_at', [$startDate, $endDate])
                  ->where('payment_status', 'paid');
            })
            ->groupBy('coffee_id')
            ->orderByDesc('total_qty')
            ->with('coffee')
            ->take(10)
            ->get();

        $bestSeller = $topItems->first()?->coffee?->name ?? 'N/A';

        $orders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->with(['customer', 'cashier', 'payment'])
            ->latest()
            ->get();

        return view('reports.index', compact(
            'period', 'dateStr', 'startDate', 'endDate',
            'totalRevenue', 'totalOrders', 'uniqueCustomers', 'bestSeller',
            'topItems', 'orders'
        ));
    }

    public function exportPdf(Request $request)
    {
        $period = $request->get('period', 'daily');
        $dateStr = $request->get('date', Carbon::today()->format('Y-m-d'));

        $startDate = Carbon::parse($dateStr)->startOfDay();
        $endDate = Carbon::parse($dateStr)->endOfDay();

        if ($period === 'weekly') {
            $startDate = Carbon::parse($dateStr)->startOfWeek();
            $endDate = Carbon::parse($dateStr)->endOfWeek();
        } elseif ($period === 'monthly') {
            $startDate = Carbon::parse($dateStr)->startOfMonth();
            $endDate = Carbon::parse($dateStr)->endOfMonth();
        } elseif ($period === 'yearly') {
            $startDate = Carbon::parse($dateStr)->startOfYear();
            $endDate = Carbon::parse($dateStr)->endOfYear();
        }

        $orders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('payment_status', 'paid')
            ->with(['customer', 'cashier', 'payment'])
            ->get();

        $totalRevenue = $orders->sum('total');
        $totalOrders = $orders->count();

        $shopName = Setting::get('shop_name', 'Coffee POS');

        $pdf = Pdf::loadView('reports.pdf', compact('period', 'startDate', 'endDate', 'orders', 'totalRevenue', 'totalOrders', 'shopName'));
        return $pdf->download("SalesReport_{$period}_{$dateStr}.pdf");
    }

    public function exportExcel(Request $request)
    {
        $period = $request->get('period', 'daily');
        $dateStr = $request->get('date', Carbon::today()->format('Y-m-d'));

        $startDate = Carbon::parse($dateStr)->startOfDay();
        $endDate = Carbon::parse($dateStr)->endOfDay();

        if ($period === 'weekly') {
            $startDate = Carbon::parse($dateStr)->startOfWeek();
            $endDate = Carbon::parse($dateStr)->endOfWeek();
        } elseif ($period === 'monthly') {
            $startDate = Carbon::parse($dateStr)->startOfMonth();
            $endDate = Carbon::parse($dateStr)->endOfMonth();
        } elseif ($period === 'yearly') {
            $startDate = Carbon::parse($dateStr)->startOfYear();
            $endDate = Carbon::parse($dateStr)->endOfYear();
        }

        $orders = Order::whereBetween('created_at', [$startDate, $endDate])
            ->where('payment_status', 'paid')
            ->with(['customer', 'cashier', 'payment'])
            ->get();

        $filename = "SalesReport_{$period}_{$dateStr}.csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Order Number', 'Date', 'Customer', 'Cashier', 'Payment Method', 'Subtotal', 'Tax', 'Discount', 'Total']);

            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_number,
                    $order->created_at->format('Y-m-d H:i:s'),
                    $order->customer ? $order->customer->name : 'Walk-in',
                    $order->cashier ? $order->cashier->name : 'N/A',
                    $order->payment ? strtoupper($order->payment->payment_method) : 'N/A',
                    $order->subtotal,
                    $order->tax,
                    $order->discount,
                    $order->total,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
