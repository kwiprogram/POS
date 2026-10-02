@extends('layouts.app')

@section('page_title', 'Coffee Menu Management')

@section('content')
<div class="card-custom p-4">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h5 class="fw-bold m-0 brand-font">Coffee Menu Items</h5>
            <small class="text-muted">Manage drinks, pastries, prices & stock levels</small>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <!-- Filter & Search Form -->
            <form action="{{ route('coffees.index') }}" method="GET" class="d-flex flex-wrap gap-2">
                <select name="category_id" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>

                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                    <option value="">All Statuses</option>
                    <option value="available" {{ request('status') == 'available' ? 'selected' : '' }}>Available</option>
                    <option value="out_of_stock" {{ request('status') == 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>

                <div class="input-group input-group-sm">
                    <input type="text" name="search" class="form-control" placeholder="Search coffee..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-light border"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
            </form>

            <button class="btn btn-coffee btn-sm text-nowrap" data-bs-toggle="modal" data-bs-target="#createCoffeeModal">
                <i class="fa-solid fa-plus me-1"></i> Add Coffee
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Image</th>
                    <th>Coffee Name</th>
                    <th>Category</th>
                    <th>Price</th>
                    <th>Stock</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($coffees as $coffee)
                    <tr>
                        <td>
                            <div class="rounded-3 overflow-hidden bg-light d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                @if($coffee->image)
                                    <img src="{{ asset($coffee->image) }}" alt="{{ $coffee->name }}" class="w-100 h-100 object-fit-cover">
                                @else
                                    <i class="fa-solid fa-mug-saucer text-primary fs-5"></i>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark fs-6">{{ $coffee->name }}</div>
                            <small class="text-muted d-block text-truncate" style="max-width: 250px;">{{ $coffee->description ?? 'No description' }}</small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $coffee->category ? $coffee->category->name : 'N/A' }}</span>
                        </td>
                        <td class="fw-bold text-primary">${{ number_format($coffee->price, 2) }}</td>
                        <td>
                            <span class="badge {{ $coffee->stock > 10 ? 'bg-success-subtle text-success' : ($coffee->stock > 0 ? 'bg-warning-subtle text-warning' : 'bg-danger-subtle text-danger') }}">
                                {{ $coffee->stock }} units
                            </span>
                        </td>
                        <td>
                            @php
                                $statusBadges = [
                                    'available' => 'bg-success',
                                    'out_of_stock' => 'bg-warning text-dark',
                                    'inactive' => 'bg-secondary'
                                ];
                            @endphp
                            <span class="badge {{ $statusBadges[$coffee->status] ?? 'bg-secondary' }}">
                                {{ ucfirst(str_replace('_', ' ', $coffee->status)) }}
                            </span>
                        </td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-light border rounded-3 me-1" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editCoffeeModal{{ $coffee->id }}"
                                    title="Edit Coffee">
                                <i class="fa-solid fa-pen-to-square text-primary"></i>
                            </button>

                            <form action="{{ route('coffees.destroy', $coffee->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete {{ addslashes($coffee->name) }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light border rounded-3" title="Delete Coffee">
                                    <i class="fa-solid fa-trash-can text-danger"></i>
                                </button>
                            </form>

                            <!-- Edit Modal -->
                            <div class="modal fade text-start" id="editCoffeeModal{{ $coffee->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow-lg rounded-4">
                                        <div class="modal-header border-0 pb-0">
                                            <h5 class="modal-title fw-bold brand-font">Edit Coffee</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('coffees.update', $coffee->id) }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body p-4">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Category</label>
                                                    <select name="category_id" class="form-select" required>
                                                        @foreach($categories as $cat)
                                                            <option value="{{ $cat->id }}" {{ $coffee->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Coffee Name</label>
                                                    <input type="text" name="name" class="form-control" value="{{ $coffee->name }}" maxlength="150" required>
                                                </div>
                                                <div class="row g-2 mb-3">
                                                    <div class="col-6">
                                                        <label class="form-label fw-semibold">Price ($)</label>
                                                        <input type="number" name="price" class="form-control" value="{{ $coffee->price }}" step="0.01" min="0.01" required>
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label fw-semibold">Stock Quantity</label>
                                                        <input type="number" name="stock" class="form-control" value="{{ $coffee->stock }}" min="0" required>
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Status</label>
                                                    <select name="status" class="form-select">
                                                        <option value="available" {{ $coffee->status === 'available' ? 'selected' : '' }}>Available</option>
                                                        <option value="out_of_stock" {{ $coffee->status === 'out_of_stock' ? 'selected' : '' }}>Out of Stock</option>
                                                        <option value="inactive" {{ $coffee->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Description</label>
                                                    <textarea name="description" class="form-control" rows="2">{{ $coffee->description }}</textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Replace Image (Optional)</label>
                                                    <input type="file" name="image" class="form-control" accept="image/*">
                                                </div>
                                            </div>
                                            <div class="modal-footer border-0 pt-0">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-coffee">Update Coffee</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No coffee items found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $coffees->links() }}
    </div>
</div>

<!-- Create Coffee Modal -->
<div class="modal fade" id="createCoffeeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold brand-font">Add New Coffee Item</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('coffees.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Category</label>
                        <select name="category_id" class="form-select" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Coffee Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Caramel Macchiato" maxlength="150" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Price ($)</label>
                            <input type="number" name="price" class="form-control" placeholder="3.50" step="0.01" min="0.01" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Initial Stock</label>
                            <input type="number" name="stock" class="form-control" placeholder="50" min="0" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select">
                            <option value="available">Available</option>
                            <option value="out_of_stock">Out of Stock</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Ingredients, flavor notes or details"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Coffee Image</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-coffee">Save Coffee Item</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
