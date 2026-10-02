@extends('layouts.app')

@section('page_title', 'Order Details')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card-custom p-4 mb-4">
            <div class="d-flex align-items-center justify-content-between border-bottom pb-3 mb-4">
                <div>
                    <h4 class="fw-bold brand-font m-0 text-dark">{{ $order->order_number }}</h4>
                    <small class="text-muted">Placed on {{ $order->created_at->format('F d, Y \a\t h:i A') }}</small>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('receipt.show', $order->id) }}" class="btn btn-outline-secondary btn-sm rounded-pill">
                        <i class="fa-solid fa-receipt me-1"></i> Receipt
                    </a>
                    <a href="{{ route('orders.index') }}" class="btn btn-light border btn-sm rounded-pill">
                        <i class="fa-solid fa-arrow-left me-1"></i> Back to Orders
                    </a>
                </div>
            </div>

            <!-- Order Metadata Cards -->
            <div class="row g-3 mb-4">
                <div class="col-sm-4">
                    <div class="p-3 bg-light rounded-3">
                        <small class="text-muted fw-semibold d-block">Customer</small>
                        <strong class="text-dark">{{ $order->customer ? $order->customer->name : 'Walk-in Customer' }}</strong>
                        @if($order->customer && $order->customer->phone)
                            <div class="fs-7 text-muted">{{ $order->customer->phone }}</div>
                        @endif
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="p-3 bg-light rounded-3">
                        <small class="text-muted fw-semibold d-block">Table / Seating</small>
                        <strong class="text-dark">{{ $order->table ? $order->table->table_number : 'Takeaway / Counter' }}</strong>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="p-3 bg-light rounded-3">
                        <small class="text-muted fw-semibold d-block">Cashier</small>
                        <strong class="text-dark">{{ $order->cashier ? $order->cashier->name : 'System' }}</strong>
                    </div>
                </div>
            </div>

            <!-- Items Table -->
            <h6 class="fw-bold brand-font mb-3">Order Items</h6>
            <div class="table-responsive mb-4">
                <table class="table align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Item</th>
                            <th>Unit Price</th>
                            <th class="text-center">Qty</th>
                            <th class="text-end">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $item->coffee ? $item->coffee->name : 'Unknown Item' }}</div>
                                </td>
                                <td>${{ number_format($item->price, 2) }}</td>
                                <td class="text-center fw-bold">{{ $item->quantity }}</td>
                                <td class="text-end fw-bold">${{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Financial Summary -->
            <div class="row justify-content-end">
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal</span>
                            <span class="fw-semibold">${{ number_format($order->subtotal, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Discount</span>
                            <span class="fw-semibold text-danger">-${{ number_format($order->discount, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Tax</span>
                            <span class="fw-semibold">${{ number_format($order->tax, 2) }}</span>
                        </div>
                        <hr class="my-2">
                        <div class="d-flex justify-content-between">
                            <span class="fw-bold fs-6 text-dark">Grand Total</span>
                            <span class="fw-bold fs-5 text-primary">${{ number_format($order->total, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            @if($order->payment)
                <div class="mt-4 p-3 border rounded-3 bg-white">
                    <h6 class="fw-bold brand-font mb-2"><i class="fa-solid fa-credit-card text-success me-2"></i> Payment Details</h6>
                    <div class="row g-2 fs-7">
                        <div class="col-6"><strong>Payment Method:</strong> {{ strtoupper($order->payment->payment_method) }}</div>
                        <div class="col-6"><strong>Paid At:</strong> {{ $order->payment->paid_at ? $order->payment->paid_at->format('M d, Y h:i A') : 'N/A' }}</div>
                        <div class="col-6"><strong>Amount Paid:</strong> ${{ number_format($order->payment->amount_paid, 2) }}</div>
                        <div class="col-6"><strong>Change Returned:</strong> ${{ number_format($order->payment->change, 2) }}</div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
