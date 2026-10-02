@extends('layouts.app')

@section('page_title', 'Category Management')

@section('content')
<div class="card-custom p-4">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3 mb-4">
        <div>
            <h5 class="fw-bold m-0 brand-font">Coffee Categories</h5>
            <small class="text-muted">Organize menu items into distinct categories</small>
        </div>

        <div class="d-flex gap-2">
            <form action="{{ route('categories.index') }}" method="GET" class="d-flex gap-2">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search categories..." value="{{ request('search') }}">
                <button type="submit" class="btn btn-sm btn-light border"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>

            <button class="btn btn-coffee btn-sm text-nowrap" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                <i class="fa-solid fa-plus me-1"></i> Add Category
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>#ID</th>
                    <th>Category Name</th>
                    <th>Description</th>
                    <th>Total Items</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                    <tr>
                        <td class="fw-bold text-muted">#{{ $category->id }}</td>
                        <td class="fw-bold text-dark fs-6">{{ $category->name }}</td>
                        <td class="text-muted fs-7">{{ $category->description ?? '—' }}</td>
                        <td>
                            <span class="badge bg-secondary-subtle text-secondary px-3 py-1 rounded-pill">
                                {{ $category->coffees_count }} Items
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $category->status === 'active' ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                                {{ ucfirst($category->status) }}
                            </span>
                        </td>
                        <td class="fs-7 text-muted">{{ $category->created_at->format('M d, Y') }}</td>
                        <td class="text-end">
                            <button class="btn btn-sm btn-light border rounded-3 me-1" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editCategoryModal{{ $category->id }}"
                                    title="Edit Category">
                                <i class="fa-solid fa-pen-to-square text-primary"></i>
                            </button>

                            <form action="{{ route('categories.destroy', $category->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this category?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-light border rounded-3" title="Delete Category">
                                    <i class="fa-solid fa-trash-can text-danger"></i>
                                </button>
                            </form>

                            <!-- Edit Modal -->
                            <div class="modal fade text-start" id="editCategoryModal{{ $category->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow-lg rounded-4">
                                        <div class="modal-header border-0 pb-0">
                                            <h5 class="modal-title fw-bold brand-font">Edit Category</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <form action="{{ route('categories.update', $category->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <div class="modal-body p-4">
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Category Name</label>
                                                    <input type="text" name="name" class="form-control" value="{{ $category->name }}" maxlength="100" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Description</label>
                                                    <textarea name="description" class="form-control" rows="3">{{ $category->description }}</textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-semibold">Status</label>
                                                    <select name="status" class="form-select">
                                                        <option value="active" {{ $category->status === 'active' ? 'selected' : '' }}>Active</option>
                                                        <option value="inactive" {{ $category->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-0 pt-0">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-coffee">Update Category</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No categories found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $categories->links() }}
    </div>
</div>

<!-- Create Category Modal -->
<div class="modal fade" id="createCategoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold brand-font">Create New Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('categories.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Category Name</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Espresso & Hot Coffee" maxlength="100" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Brief details about items in this category"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Status</label>
                        <select name="status" class="form-select">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-coffee">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
