@extends('layouts.app')

@section('page_title', 'Customer Management')

@section('content')
<div class="card-custom p-4">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h5 class="fw-bold m-0 brand-font">Customer Directory</h5>
            <small class="text-muted">Manage coffee shop members and loyal guests</small>
        </div>

        <div class="d-flex gap-2">
            <form action="{{ route('customers.index') }}" method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by name or phone..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-sm btn-light border"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>

            <button class="btn btn-coffee btn-sm text-nowrap" data-bs-toggle="modal" data-bs-target="#createCustomerModal">
                <i class="fa-solid fa-user-plus me-1"></i> Register Customer
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#ID</th>
                    <th>Customer Name</th>
                    <th>Phone</th>
                    <th>Member Tier</th>
                    <th>Total Orders</th>
                    <th>Registered At</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                    <tr>
                        <td class="fw-bold text-muted">#{{ $customer->id }}</td>
                        <td class="fw-bold text-dark fs-6">{{ $customer->name }}</td>
                        <td>{{ $customer->phone ?? '—' }}</td>
                        <td>
                            @php
                                $tierBadges = [
                                    'regular' => 'bg-secondary-subtle text-secondary',
                                    'member' => 'bg-primary-subtle text-primary',
                                    'vip' => 'bg-warning-subtle text-warning-emphasis'
                                ];
                            @endphp
                            <span class="badge {{ $tierBadges[$customer->member_status] ?? 'bg-secondary' }} px-3 py-1 rounded-pill">
                                {{ strtoupper($customer->member_status) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">
                                {{ $customer->orders_count }} Orders
                            </span>
                        </td>
                        <td class="fs-7 text-muted">{{ $customer->created_at->format('M d, Y') }}</td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-light border rounded-3 me-1" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editCustomerModal{{ $customer->id }}"
                                    title="Edit Customer">
                                <i class="fa-solid fa-pen-to-square text-primary"></i>
                            </button>

                            <form action="{{ route('customers.destroy', $customer->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete customer {{ addslashes($customer->name) }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light border rounded-3" title="Delete Customer">
                                    <i class="fa-solid fa-trash-can text-danger"></i>
                                </button>
                            </form>

                            <!-- Edit Modal -->
                            <div class="modal fade text-start" id="editCustomerModal{{ $customer->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow-lg rounded-4">
                                        <div class="modal-header border-0 pb-0">
                                            <h5 class="modal-title fw-bold brand-font">Edit Customer</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('customers.update', $customer->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body p-4">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Customer Name</label>
                                                    <input type="text" name="name" class="form-control" value="{{ $customer->name }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Phone Number</label>
                                                    <input type="text" name="phone" class="form-control" value="{{ $customer->phone }}">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Member Tier</label>
                                                    <select name="member_status" class="form-select">
                                                        <option value="regular" {{ $customer->member_status === 'regular' ? 'selected' : '' }}>Regular</option>
                                                        <option value="member" {{ $customer->member_status === 'member' ? 'selected' : '' }}>Member</option>
                                                        <option value="vip" {{ $customer->member_status === 'vip' ? 'selected' : '' }}>VIP</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-0 pt-0">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-coffee">Update Customer</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No customers registered yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $customers->links() }}
    </div>
</div>

<!-- Create Customer Modal -->
<div class="modal fade" id="createCustomerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold brand-font">Register New Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('customers.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Customer Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. John Doe" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Phone Number</label>
                        <input type="text" name="phone" class="form-control" placeholder="e.g. 012345678">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Member Tier</label>
                        <select name="member_status" class="form-select">
                            <option value="regular">Regular</option>
                            <option value="member">Member</option>
                            <option value="vip">VIP</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-coffee">Save Customer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
