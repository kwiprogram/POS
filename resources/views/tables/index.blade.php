@extends('layouts.app')

@section('page_title', 'Table Management')

@section('content')
<div class="card-custom p-4">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h5 class="fw-bold m-0 brand-font">Shop Tables</h5>
            <small class="text-muted">Manage table numbers, seating capacity & real-time status</small>
        </div>

        <button class="btn btn-coffee btn-sm text-nowrap" data-bs-toggle="modal" data-bs-target="#createTableModal">
            <i class="fa-solid fa-plus me-1"></i> Add Table
        </button>
    </div>

    <!-- Visual Table Grid -->
    <div class="row g-3">
        @forelse($tables as $table)
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="card-custom p-3 text-center border position-relative h-100 d-flex flex-column justify-content-between">
                    <div>
                        @php
                            $statusConfig = [
                                'available' => ['bg' => '#D1FAE5', 'color' => '#059669', 'label' => 'Available', 'icon' => 'fa-circle-check'],
                                'occupied'  => ['bg' => '#FEF3C7', 'color' => '#D97706', 'label' => 'Occupied', 'icon' => 'fa-mug-hot'],
                                'reserved'  => ['bg' => '#FEE2E2', 'color' => '#DC2626', 'label' => 'Reserved', 'icon' => 'fa-user-lock'],
                            ];
                            $st = $statusConfig[$table->status] ?? $statusConfig['available'];
                        @endphp
                        
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="badge rounded-pill" style="background: {{ $st['bg'] }}; color: {{ $st['color'] }};">
                                <i class="fa-solid {{ $st['icon'] }} me-1"></i> {{ $st['label'] }}
                            </span>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light border-0" data-bs-toggle="dropdown">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                                    <li>
                                        <button class="dropdown-item fs-7" data-bs-toggle="modal" data-bs-target="#editTableModal{{ $table->id }}">
                                            <i class="fa-solid fa-pen text-primary me-2"></i> Edit
                                        </button>
                                    </li>
                                    <li>
                                        <form action="{{ route('tables.destroy', $table->id) }}" method="POST" onsubmit="return confirm('Delete {{ addslashes($table->table_number) }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item fs-7 text-danger">
                                                <i class="fa-solid fa-trash me-2"></i> Delete
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="my-3">
                            <i class="fa-solid fa-chair fs-1 text-secondary mb-2"></i>
                            <h4 class="fw-bold m-0 text-dark brand-font">{{ $table->table_number }}</h4>
                            <small class="text-muted"><i class="fa-solid fa-users me-1"></i> {{ $table->capacity }} Seats</small>
                        </div>
                    </div>

                    <!-- Edit Modal -->
                    <div class="modal fade text-start" id="editTableModal{{ $table->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow-lg rounded-4">
                                <div class="modal-header border-0 pb-0">
                                    <h5 class="modal-title fw-bold brand-font">Edit Table</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <form action="{{ route('tables.update', $table->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <div class="modal-body p-4">
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Table Number / Identifier</label>
                                            <input type="text" name="table_number" class="form-control" value="{{ $table->table_number }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Capacity (Seats)</label>
                                            <input type="number" name="capacity" class="form-control" value="{{ $table->capacity }}" min="0" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label fw-semibold">Status</label>
                                            <select name="status" class="form-select">
                                                <option value="available" {{ $table->status === 'available' ? 'selected' : '' }}>Available</option>
                                                <option value="occupied" {{ $table->status === 'occupied' ? 'selected' : '' }}>Occupied</option>
                                                <option value="reserved" {{ $table->status === 'reserved' ? 'selected' : '' }}>Reserved</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0 pt-0">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-coffee">Update Table</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center text-muted py-5">
                <p>No tables configured yet.</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Create Table Modal -->
<div class="modal fade" id="createTableModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold brand-font">Add New Table</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('tables.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Table Number / Identifier</label>
                        <input type="text" name="table_number" class="form-control" placeholder="e.g. Table 07" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Capacity (Seats)</label>
                        <input type="number" name="capacity" class="form-control" placeholder="4" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select">
                            <option value="available">Available</option>
                            <option value="occupied">Occupied</option>
                            <option value="reserved">Reserved</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-coffee">Save Table</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
