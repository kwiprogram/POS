@extends('layouts.app')

@section('page_title', 'Orders History & Management')

@section('content')
<div class="card-custom p-4">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h5 class="fw-bold m-0 brand-font">Customer Orders</h5>
            <small class="text-muted">Track order progress, update preparation status or view receipts</small>
        </div>

        <form action="{{ route('orders.index') }}" method="GET" class="d-flex flex-wrap gap-2">
            <input type="date" name="date" class="form-control form-control-sm" value="{{ request('date') }}" onchange="this.form.submit()">

            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">All Statuses</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="preparing" {{ request('status') === 'preparing' ? 'selected' : '' }}>Preparing</option>
                <option value="ready" {{ request('status') === 'ready' ? 'selected' : '' }}>Ready</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>

            <div class="input-group input-group-sm" style="width: 220px;">
                <input type="text" name="search" class="form-control" placeholder="Search order / customer..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-light border"><i class="fa-solid fa-magnifying-glass"></i></button>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Order #</th>
                    <th>Date & Time</th>
                    <th>Customer</th>
                    <th>Table</th>
                    <th>Cashier</th>
                    <th>Items</th>
                    <th>Total</th>
                    <th>Order Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td class="fw-bold text-dark">{{ $order->order_number }}</td>
                        <td class="fs-7 text-muted">{{ $order->created_at->format('M d, Y h:i A') }}</td>
                        <td>{{ $order->customer ? $order->customer->name : 'Walk-in' }}</td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $order->table ? $order->table->table_number : 'Takeaway' }}</span>
                        </td>
                        <td class="fs-7 text-muted">{{ $order->cashier ? $order->cashier->name : 'N/A' }}</td>
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary">
                                {{ $order->items->sum('quantity') }} items
                            </span>
                        </td>
                        <td class="fw-bold text-primary">${{ number_format($order->total, 2) }}</td>
                        <td>
                            @php
                                $statusColors = [
                                    'pending' => 'bg-secondary',
                                    'preparing' => 'bg-info text-dark',
                                    'ready' => 'bg-primary',
                                    'completed' => 'bg-success',
                                    'cancelled' => 'bg-danger'
                                ];
                            @endphp
                            
                            <form action="{{ route('orders.updateStatus', $order->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <select name="order_status" class="form-select form-select-sm border-0 fw-semibold {{ $statusColors[$order->order_status] ?? 'bg-secondary' }} text-white" onchange="this.form.submit()" style="width: 120px;">
                                    <option value="pending" {{ $order->order_status === 'pending' ? 'selected' : '' }} class="bg-white text-dark">Pending</option>
                                    <option value="preparing" {{ $order->order_status === 'preparing' ? 'selected' : '' }} class="bg-white text-dark">Preparing</option>
                                    <option value="ready" {{ $order->order_status === 'ready' ? 'selected' : '' }} class="bg-white text-dark">Ready</option>
                                    <option value="completed" {{ $order->order_status === 'completed' ? 'selected' : '' }} class="bg-white text-dark">Completed</option>
                                    <option value="cancelled" {{ $order->order_status === 'cancelled' ? 'selected' : '' }} class="bg-white text-dark">Cancelled</option>
                                </select>
                            </form>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-light border rounded-3 me-1" title="View Order Details">
                                <i class="fa-solid fa-eye text-primary"></i>
                            </a>

                            <a href="{{ route('receipt.show', $order->id) }}" class="btn btn-sm btn-light border rounded-3 me-1" title="Print Receipt">
                                <i class="fa-solid fa-print text-secondary"></i>
                            </a>

                            @if(Auth::user()->isAdmin())
                                <form action="{{ route('orders.destroy', $order->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete order {{ $order->order_number }} permanently?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border rounded-3" title="Delete Order">
                                        <i class="fa-solid fa-trash-can text-danger"></i>
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">No orders found matching the filter.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $orders->links() }}
    </div>
</div>
@endsection
