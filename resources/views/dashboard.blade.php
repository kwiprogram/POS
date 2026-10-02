@extends('layouts.app')

@section('page_title', 'Dashboard & Analytics')

@section('content')
<div class="row g-4 mb-4">
    <!-- Stat 1: Today's Revenue -->
    <div class="col-xl-3 col-md-6">
        <div class="card-custom p-4 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted fw-semibold fs-7 d-block mb-1">Today's Revenue</span>
                    <h3 class="fw-bold m-0 text-dark">${{ number_format($todayRevenue, 2) }}</h3>
                </div>
                <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="background: #FEF3C7; color: #D97706; width: 54px; height: 54px;">
                    <i class="fa-solid fa-sack-dollar fs-4"></i>
                </div>
            </div>
            <div class="mt-3 fs-7 text-muted">
                <span class="text-success fw-bold"><i class="fa-solid fa-arrow-up"></i> Live</span> updated today
            </div>
        </div>
    </div>

    <!-- Stat 2: Total Orders -->
    <div class="col-xl-3 col-md-6">
        <div class="card-custom p-4 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted fw-semibold fs-7 d-block mb-1">Today's Orders</span>
                    <h3 class="fw-bold m-0 text-dark">{{ $totalOrdersToday }}</h3>
                </div>
                <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="background: #E0E7FF; color: #4F46E5; width: 54px; height: 54px;">
                    <i class="fa-solid fa-receipt fs-4"></i>
                </div>
            </div>
            <div class="mt-3 fs-7 text-muted">
                <span class="badge bg-warning text-dark me-1">{{ $pendingOrders }} Pending</span>
                <span class="badge bg-success me-1">{{ $completedOrders }} Completed</span>
            </div>
        </div>
    </div>

    <!-- Stat 3: Best Selling Coffee -->
    <div class="col-xl-3 col-md-6">
        <div class="card-custom p-4 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted fw-semibold fs-7 d-block mb-1">Best Selling Coffee</span>
                    <h5 class="fw-bold m-0 text-dark text-truncate" style="max-width: 160px;">{{ $bestSellingCoffee }}</h5>
                </div>
                <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="background: #ECE0D1; color: #6F4E37; width: 54px; height: 54px;">
                    <i class="fa-solid fa-crown fs-4"></i>
                </div>
            </div>
            <div class="mt-3 fs-7 text-muted">
                Top customer favorite choice
            </div>
        </div>
    </div>

    <!-- Stat 4: Total Customers -->
    <div class="col-xl-3 col-md-6">
        <div class="card-custom p-4 h-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <span class="text-muted fw-semibold fs-7 d-block mb-1">Total Customers</span>
                    <h3 class="fw-bold m-0 text-dark">{{ $totalCustomers }}</h3>
                </div>
                <div class="rounded-circle p-3 d-flex align-items-center justify-content-center" style="background: #D1FAE5; color: #059669; width: 54px; height: 54px;">
                    <i class="fa-solid fa-users fs-4"></i>
                </div>
            </div>
            <div class="mt-3 fs-7 text-muted">
                Registered coffee lovers
            </div>
        </div>
    </div>
</div>

<!-- Sales Charts Section -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card-custom p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-bold m-0"><i class="fa-solid fa-chart-area text-primary me-2"></i> Daily Sales Trend (Last 7 Days)</h5>
            </div>
            <div style="height: 280px;">
                <canvas id="dailySalesChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card-custom p-4 h-100">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h5 class="fw-bold m-0"><i class="fa-solid fa-chart-column text-success me-2"></i> Monthly Revenue</h5>
            </div>
            <div style="height: 280px;">
                <canvas id="monthlySalesChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders Table -->
<div class="card-custom p-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h5 class="fw-bold m-0"><i class="fa-solid fa-clock-rotate-left me-2 text-secondary"></i> Recent Orders</h5>
        <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">View All Orders</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Order #</th>
                    <th>Date & Time</th>
                    <th>Customer</th>
                    <th>Table</th>
                    <th>Total</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentOrders as $order)
                    <tr>
                        <td class="fw-bold text-dark">{{ $order->order_number }}</td>
                        <td class="text-muted fs-7">{{ $order->created_at->format('M d, Y h:i A') }}</td>
                        <td>{{ $order->customer ? $order->customer->name : 'Walk-in' }}</td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $order->table ? $order->table->table_number : 'Takeaway' }}</span>
                        </td>
                        <td class="fw-bold text-dark">${{ number_format($order->total, 2) }}</td>
                        <td>
                            <span class="badge {{ $order->payment_status === 'paid' ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning' }}">
                                {{ strtoupper($order->payment_status) }}
                            </span>
                        </td>
                        <td>
                            @php
                                $badgeMap = [
                                    'pending' => 'bg-secondary',
                                    'preparing' => 'bg-info text-dark',
                                    'ready' => 'bg-primary',
                                    'completed' => 'bg-success',
                                    'cancelled' => 'bg-danger'
                                ];
                            @endphp
                            <span class="badge {{ $badgeMap[$order->order_status] ?? 'bg-secondary' }}">
                                {{ ucfirst($order->order_status) }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('receipt.show', $order->id) }}" class="btn btn-sm btn-light border rounded-circle" title="View Receipt">
                                <i class="fa-solid fa-receipt text-secondary"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">No recent orders found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Daily Sales Chart
        const dailyCtx = document.getElementById('dailySalesChart').getContext('2d');
        new Chart(dailyCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($dailyLabels) !!},
                datasets: [{
                    label: 'Revenue ($)',
                    data: {!! json_encode($dailyData) !!},
                    borderColor: '#6F4E37',
                    backgroundColor: 'rgba(111, 78, 55, 0.12)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointRadius: 5,
                    pointBackgroundColor: '#6F4E37'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#F3F4F6' } },
                    x: { grid: { display: false } }
                }
            }
        });

        // Monthly Sales Chart
        const monthlyCtx = document.getElementById('monthlySalesChart').getContext('2d');
        new Chart(monthlyCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($monthlyLabels) !!},
                datasets: [{
                    label: 'Revenue ($)',
                    data: {!! json_encode($monthlyData) !!},
                    backgroundColor: '#D4A373',
                    borderRadius: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: '#F3F4F6' } },
                    x: { grid: { display: false } }
                }
            }
        });
    });
</script>
@endsection
