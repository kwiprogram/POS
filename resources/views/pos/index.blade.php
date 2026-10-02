@extends('layouts.app')

@section('page_title', 'POS Terminal')

@section('styles')
<style>
    /* POS Grid & Sidebar Layout */
    .pos-container {
        display: grid;
        grid-template-columns: 1fr 400px;
        gap: 24px;
        height: calc(100vh - 120px);
    }

    .pos-menu-area {
        display: flex;
        flex-direction: column;
        height: 100%;
        overflow: hidden;
    }

    .pos-cart-area {
        background: #FFFFFF;
        border: 1px solid #E7E5E4;
        border-radius: var(--radius-lg);
        display: flex;
        flex-direction: column;
        height: 100%;
        box-shadow: var(--shadow-sm);
    }

    /* Category Filter Pills */
    .category-pills {
        display: flex;
        gap: 8px;
        overflow-x: auto;
        padding-bottom: 8px;
        scrollbar-width: thin;
    }

    .category-pill {
        background: #FFFFFF;
        border: 1px solid #E7E5E4;
        padding: 8px 18px;
        border-radius: 30px;
        font-weight: 500;
        font-size: 14px;
        white-space: nowrap;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .category-pill.active, .category-pill:hover {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
        box-shadow: 0 4px 12px rgba(111, 78, 55, 0.25);
    }

    /* Coffee Item Cards */
    .coffee-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 16px;
        overflow-y: auto;
        padding-right: 4px;
        flex: 1;
    }

    .coffee-card {
        background: #FFFFFF;
        border: 1px solid #E7E5E4;
        border-radius: var(--radius-md);
        padding: 14px;
        cursor: pointer;
        transition: all 0.2s ease;
        display: flex;
        flex-direction: column;
        position: relative;
    }

    .coffee-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-md);
        border-color: var(--secondary);
    }

    .coffee-img-placeholder {
        width: 100%;
        height: 110px;
        background: #F5F5F4;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--primary);
        font-size: 36px;
        margin-bottom: 10px;
        overflow: hidden;
    }

    .coffee-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 10px;
    }

    .coffee-name {
        font-weight: 600;
        font-size: 14px;
        color: var(--text-main);
        margin-bottom: 4px;
        line-height: 1.3;
    }

    .coffee-price {
        font-weight: 700;
        color: var(--primary);
        font-size: 15px;
    }

    /* Cart List */
    .cart-header {
        padding: 16px 20px;
        border-bottom: 1px solid #E7E5E4;
    }

    .cart-items-list {
        flex: 1;
        overflow-y: auto;
        padding: 16px 20px;
    }

    .cart-item-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 0;
        border-bottom: 1px dashed #E7E5E4;
    }

    .cart-qty-btn {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        border: 1px solid #D6D3D1;
        background: #FFFFFF;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .cart-qty-btn:hover {
        background: var(--primary);
        color: white;
        border-color: var(--primary);
    }

    .cart-footer {
        padding: 20px;
        background: #FAFAF9;
        border-top: 1px solid #E7E5E4;
        border-bottom-left-radius: var(--radius-lg);
        border-bottom-right-radius: var(--radius-lg);
    }

    @media (max-width: 991px) {
        .pos-container {
            grid-template-columns: 1fr;
            height: auto;
        }
        .pos-cart-area {
            height: 600px;
        }
    }
</style>
@endsection

@section('content')
<div class="pos-container">
    <!-- Left Area: Coffee Menu Grid -->
    <div class="pos-menu-area">
        <!-- Search & Filter Header -->
        <div class="mb-3 d-flex gap-3">
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0 rounded-start-3 text-muted">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
                <input type="text" id="searchInput" class="form-control border-start-0 ps-0" placeholder="Search coffee menu..." onkeyup="filterCoffees()">
            </div>
        </div>

        <!-- Category Pills -->
        <div class="category-pills mb-3">
            <div class="category-pill active" onclick="filterCategory('all', this)">
                <i class="fa-solid fa-mug-hot me-1"></i> All Items
            </div>
            @foreach($categories as $cat)
                <div class="category-pill" onclick="filterCategory('{{ $cat->id }}', this)">
                    {{ $cat->name }}
                </div>
            @endforeach
        </div>

        <!-- Coffee Grid -->
        <div class="coffee-grid" id="coffeeGrid">
            @foreach($coffees as $coffee)
                <div class="coffee-card item-card" 
                     data-id="{{ $coffee->id }}" 
                     data-name="{{ $coffee->name }}" 
                     data-price="{{ $coffee->price }}" 
                     data-stock="{{ $coffee->stock }}"
                     data-category="{{ $coffee->category_id }}"
                     onclick="addToCart({{ $coffee->id }}, '{{ addslashes($coffee->name) }}', {{ $coffee->price }}, {{ $coffee->stock }})">
                    
                    <div class="coffee-img-placeholder">
                        @if($coffee->image)
                            <img src="{{ asset($coffee->image) }}" alt="{{ $coffee->name }}" onerror="this.onerror=null; this.parentNode.innerHTML='<i class=\'fa-solid fa-mug-saucer\'></i>';">
                        @else
                            <i class="fa-solid fa-mug-saucer"></i>
                        @endif
                    </div>
                    
                    <div class="coffee-name">{{ $coffee->name }}</div>
                    <div class="d-flex align-items-center justify-content-between mt-auto">
                        <span class="coffee-price">${{ number_format($coffee->price, 2) }}</span>
                        <small class="badge {{ $coffee->stock > 10 ? 'bg-light text-dark' : 'bg-danger-subtle text-danger' }}">
                            Stock: {{ $coffee->stock }}
                        </small>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Right Area: Order Cart Sidebar -->
    <div class="pos-cart-area">
        <div class="cart-header">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <h5 class="fw-bold m-0 brand-font"><i class="fa-solid fa-basket-shopping text-primary me-2"></i> Current Order</h5>
                <button class="btn btn-sm btn-outline-danger border-0 rounded-circle" onclick="clearCart()" title="Clear Cart">
                    <i class="fa-solid fa-trash-can"></i>
                </button>
            </div>

            <!-- Customer & Table Selection -->
            <div class="row g-2 mt-1">
                <div class="col-6">
                    <label class="form-label fs-7 fw-semibold text-muted mb-1">Customer</label>
                    <div class="input-group input-group-sm">
                        <select id="customerSelect" class="form-select border-radius-sm">
                            <option value="">Walk-in Customer</option>
                            @foreach($customers as $cust)
                                <option value="{{ $cust->id }}">{{ $cust->name }} ({{ ucfirst($cust->member_status) }})</option>
                            @endforeach
                        </select>
                        <button class="btn btn-outline-secondary" type="button" data-bs-toggle="modal" data-bs-target="#quickCustomerModal" title="Quick Add Customer">
                            <i class="fa-solid fa-user-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="col-6">
                    <label class="form-label fs-7 fw-semibold text-muted mb-1">Table</label>
                    <select id="tableSelect" class="form-select form-select-sm">
                        <option value="">Takeaway / Counter</option>
                        @foreach($tables as $tbl)
                            <option value="{{ $tbl->id }}">{{ $tbl->table_number }} ({{ ucfirst($tbl->status) }})</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Cart Items List -->
        <div class="cart-items-list" id="cartItemsList">
            <div class="text-center text-muted py-5" id="emptyCartNotice">
                <i class="fa-solid fa-cart-flatbed fs-1 text-light-emphasis mb-2"></i>
                <p class="small mb-0">No coffee items selected yet.<br>Click on any item on the left to add.</p>
            </div>
        </div>

        <!-- Cart Calculations Footer -->
        <div class="cart-footer">
            <div class="d-flex justify-content-between fs-7 mb-1 text-muted">
                <span>Subtotal</span>
                <span class="fw-semibold text-dark" id="subtotalDisplay">$0.00</span>
            </div>

            <div class="d-flex justify-content-between align-items-center fs-7 mb-1 text-muted">
                <span>Discount ($)</span>
                <input type="number" id="discountInput" class="form-control form-control-sm text-end" style="width: 90px;" value="0.00" min="0" step="0.5" onchange="calculateCartTotals()">
            </div>

            <div class="d-flex justify-content-between fs-7 mb-2 text-muted">
                <span>Tax ({{ $taxPercentage }}%)</span>
                <span class="fw-semibold text-dark" id="taxDisplay">$0.00</span>
            </div>

            <hr class="my-2 border-dashed">

            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="fw-bold fs-6 text-dark">Grand Total</span>
                <span class="fw-bold fs-4 text-primary" id="grandTotalDisplay">$0.00</span>
            </div>

            <button class="btn btn-coffee w-100 py-3 text-uppercase tracking-wider fw-bold shadow-sm" onclick="openCheckoutModal()">
                <i class="fa-solid fa-credit-card me-2"></i> Proceed to Pay
            </button>
        </div>
    </div>
</div>

<!-- Checkout / Payment Modal -->
<div class="modal fade" id="checkoutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-dark text-white border-0 py-3">
                <h5 class="modal-title fw-bold brand-font"><i class="fa-solid fa-cash-register me-2 text-warning"></i> Complete Payment</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center p-3 bg-light rounded-3 mb-4">
                    <small class="text-muted text-uppercase fw-semibold d-block">Amount Due</small>
                    <h2 class="fw-bold text-primary m-0" id="modalGrandTotal">$0.00</h2>
                </div>

                <!-- Payment Method Selection -->
                <div class="mb-4">
                    <label class="form-label fw-bold text-dark fs-7">Select Payment Method</label>
                    <div class="row g-2" id="paymentMethodGroup">
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="payment_method" id="payCash" value="cash" checked onchange="togglePaymentView()">
                            <label class="btn btn-outline-secondary w-100 py-2 rounded-3 text-start" for="payCash">
                                <i class="fa-solid fa-money-bill-wave me-1 text-success"></i> Cash
                            </label>
                        </div>
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="payment_method" id="payKHQR" value="aba_khqr" onchange="togglePaymentView()">
                            <label class="btn btn-outline-secondary w-100 py-2 rounded-3 text-start" for="payKHQR">
                                <i class="fa-solid fa-qrcode me-1 text-danger"></i> ABA KHQR
                            </label>
                        </div>
                        <div class="col-4">
                            <input type="radio" class="btn-check" name="payment_method" id="payCard" value="credit_card" onchange="togglePaymentView()">
                            <label class="btn btn-outline-secondary w-100 py-2 rounded-3 text-start" for="payCard">
                                <i class="fa-solid fa-credit-card me-1 text-primary"></i> Card
                            </label>
                        </div>
                    </div>
                </div>

                <!-- KHQR Preview Area (Shown when ABA KHQR is selected) -->
                <div id="khqrPreviewArea" class="text-center p-3 mb-3 border rounded-3 bg-white" style="display: none;">
                    <div class="badge bg-danger mb-2 px-3 py-1">ABA PAY KHQR</div>
                    <div class="d-flex justify-content-center mb-2">
                        <!-- Dynamic QR canvas -->
                        <div id="qrCodeContainer" class="p-2 border rounded-3 bg-light" style="width: 170px; height: 170px;">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=ABA_KHQR_COFFEE_PAY" alt="KHQR Code" class="img-fluid">
                        </div>
                    </div>
                    <small class="text-muted d-block">Scan with any Mobile Banking App (ABA, ACLEDA, Bakong)</small>
                </div>

                <!-- Amount Paid & Change Calculation -->
                <div class="mb-3">
                    <label class="form-label fw-bold text-dark fs-7">Amount Paid ($)</label>
                    <input type="number" id="amountPaidInput" class="form-control form-control-lg fw-bold text-success" step="0.01" oninput="calculateChange()">
                </div>

                <!-- Quick Cash Presets -->
                <div class="d-flex gap-2 mb-4">
                    <button class="btn btn-sm btn-outline-secondary flex-fill rounded-3" onclick="setQuickCash('exact')">Exact</button>
                    <button class="btn btn-sm btn-outline-secondary flex-fill rounded-3" onclick="setQuickCash(5)">$5</button>
                    <button class="btn btn-sm btn-outline-secondary flex-fill rounded-3" onclick="setQuickCash(10)">$10</button>
                    <button class="btn btn-sm btn-outline-secondary flex-fill rounded-3" onclick="setQuickCash(20)">$20</button>
                    <button class="btn btn-sm btn-outline-secondary flex-fill rounded-3" onclick="setQuickCash(50)">$50</button>
                </div>

                <div class="d-flex justify-content-between align-items-center p-3 rounded-3 bg-light mb-4">
                    <span class="fw-bold text-dark fs-6">Change Return</span>
                    <span class="fw-bold fs-4 text-danger" id="changeDisplay">$0.00</span>
                </div>

                <button class="btn btn-coffee w-100 py-3 fw-bold fs-5 rounded-3" onclick="submitCheckout()">
                    <i class="fa-solid fa-circle-check me-2"></i> Confirm Order & Print Receipt
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Quick Add Customer Modal -->
<div class="modal fade" id="quickCustomerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold brand-font">Register New Customer</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="quickCustomerForm" onsubmit="saveQuickCustomer(event)">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Customer Name</label>
                        <input type="text" id="quickCustName" class="form-control" placeholder="e.g. Bopha Vong" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Phone Number</label>
                        <input type="text" id="quickCustPhone" class="form-control" placeholder="e.g. 012345678">
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Member Status</label>
                        <select id="quickCustMember" class="form-select">
                            <option value="regular">Regular</option>
                            <option value="member">Member</option>
                            <option value="vip">VIP</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-coffee w-100 py-2 fw-semibold">Save Customer</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let cart = [];
    const taxRate = {{ $taxPercentage }};

    function addToCart(id, name, price, stock) {
        let existing = cart.find(item => item.id === id);
        if (existing) {
            if (existing.quantity >= stock) {
                alert(`Cannot add more. Stock limit of ${stock} reached for ${name}.`);
                return;
            }
            existing.quantity += 1;
        } else {
            if (stock <= 0) {
                alert(`Item ${name} is out of stock!`);
                return;
            }
            cart.push({ id: id, name: name, price: price, quantity: 1, stock: stock });
        }
        renderCart();
    }

    function updateQty(id, delta) {
        let item = cart.find(i => i.id === id);
        if (!item) return;

        item.quantity += delta;
        if (item.quantity > item.stock) {
            alert(`Stock limit reached (${item.stock}).`);
            item.quantity = item.stock;
        }

        if (item.quantity <= 0) {
            cart = cart.filter(i => i.id !== id);
        }
        renderCart();
    }

    function removeFromCart(id) {
        cart = cart.filter(i => i.id !== id);
        renderCart();
    }

    function clearCart() {
        cart = [];
        renderCart();
    }

    function renderCart() {
        const cartList = document.getElementById('cartItemsList');
        const emptyNotice = document.getElementById('emptyCartNotice');

        if (cart.length === 0) {
            cartList.innerHTML = `
                <div class="text-center text-muted py-5" id="emptyCartNotice">
                    <i class="fa-solid fa-cart-flatbed fs-1 text-light-emphasis mb-2"></i>
                    <p class="small mb-0">No coffee items selected yet.<br>Click on any item on the left to add.</p>
                </div>`;
            calculateCartTotals();
            return;
        }

        let html = '';
        cart.forEach(item => {
            let itemTotal = (item.price * item.quantity).toFixed(2);
            html += `
                <div class="cart-item-row">
                    <div style="flex: 1;">
                        <div class="fw-bold text-dark fs-7">${item.name}</div>
                        <small class="text-muted">$${item.price.toFixed(2)} x ${item.quantity}</small>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button class="cart-qty-btn" onclick="updateQty(${item.id}, -1)">-</button>
                        <span class="fw-bold fs-7" style="min-width: 20px; text-align: center;">${item.quantity}</span>
                        <button class="cart-qty-btn" onclick="updateQty(${item.id}, 1)">+</button>
                        <span class="fw-bold text-dark fs-7 ms-2" style="min-width: 55px; text-align: right;">$${itemTotal}</span>
                        <button class="btn btn-sm text-danger p-0 ms-1" onclick="removeFromCart(${item.id})">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </div>
                </div>
            `;
        });

        cartList.innerHTML = html;
        calculateCartTotals();
    }

    function calculateCartTotals() {
        let subtotal = cart.reduce((sum, i) => sum + (i.price * i.quantity), 0);
        let discount = parseFloat(document.getElementById('discountInput').value) || 0;
        let tax = Math.max(0, (subtotal - discount) * (taxRate / 100));
        let grandTotal = Math.max(0, subtotal - discount + tax);

        document.getElementById('subtotalDisplay').innerText = '$' + subtotal.toFixed(2);
        document.getElementById('taxDisplay').innerText = '$' + tax.toFixed(2);
        document.getElementById('grandTotalDisplay').innerText = '$' + grandTotal.toFixed(2);

        return { subtotal, discount, tax, grandTotal };
    }

    function filterCategory(catId, el) {
        document.querySelectorAll('.category-pill').forEach(p => p.classList.remove('active'));
        el.classList.add('active');

        let cards = document.querySelectorAll('.item-card');
        cards.forEach(card => {
            if (catId === 'all' || card.getAttribute('data-category') === String(catId)) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function filterCoffees() {
        let search = document.getElementById('searchInput').value.toLowerCase();
        let cards = document.querySelectorAll('.item-card');
        cards.forEach(card => {
            let name = card.getAttribute('data-name').toLowerCase();
            if (name.includes(search)) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    }

    function openCheckoutModal() {
        if (cart.length === 0) {
            alert('Your cart is empty! Add items before proceeding to pay.');
            return;
        }

        let totals = calculateCartTotals();
        document.getElementById('modalGrandTotal').innerText = '$' + totals.grandTotal.toFixed(2);
        document.getElementById('amountPaidInput').value = totals.grandTotal.toFixed(2);
        calculateChange();

        let modal = new bootstrap.Modal(document.getElementById('checkoutModal'));
        modal.show();
    }

    function togglePaymentView() {
        let selectedMethod = document.querySelector('input[name="payment_method"]:checked').value;
        let khqrArea = document.getElementById('khqrPreviewArea');
        if (selectedMethod === 'aba_khqr') {
            khqrArea.style.display = 'block';
        } else {
            khqrArea.style.display = 'none';
        }
    }

    function setQuickCash(val) {
        let totals = calculateCartTotals();
        if (val === 'exact') {
            document.getElementById('amountPaidInput').value = totals.grandTotal.toFixed(2);
        } else {
            document.getElementById('amountPaidInput').value = parseFloat(val).toFixed(2);
        }
        calculateChange();
    }

    function calculateChange() {
        let totals = calculateCartTotals();
        let paid = parseFloat(document.getElementById('amountPaidInput').value) || 0;
        let change = paid - totals.grandTotal;

        let changeEl = document.getElementById('changeDisplay');
        if (change < 0) {
            changeEl.innerText = '$0.00';
            changeEl.className = 'fw-bold fs-4 text-danger';
        } else {
            changeEl.innerText = '$' + change.toFixed(2);
            changeEl.className = 'fw-bold fs-4 text-success';
        }
    }

    function submitCheckout() {
        let totals = calculateCartTotals();
        let paid = parseFloat(document.getElementById('amountPaidInput').value) || 0;

        if (paid < totals.grandTotal) {
            alert(`Amount paid ($${paid.toFixed(2)}) is less than Grand Total ($${totals.grandTotal.toFixed(2)}).`);
            return;
        }

        let paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
        let customerId = document.getElementById('customerSelect').value;
        let tableId = document.getElementById('tableSelect').value;

        let payload = {
            items: cart.map(i => ({ id: i.id, quantity: i.quantity })),
            customer_id: customerId || null,
            table_id: tableId || null,
            discount: totals.discount,
            payment_method: paymentMethod,
            amount_paid: paid
        };

        fetch("{{ route('pos.checkout') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(payload)
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                window.location.href = data.redirect_url;
            } else {
                alert(data.message || 'Payment processing failed.');
            }
        })
        .catch(err => {
            alert('Error processing order: ' + err.message);
        });
    }

    function saveQuickCustomer(e) {
        e.preventDefault();
        let name = document.getElementById('quickCustName').value;
        let phone = document.getElementById('quickCustPhone').value;
        let member = document.getElementById('quickCustMember').value;

        fetch("{{ route('customers.store') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ name: name, phone: phone, member_status: member })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                let select = document.getElementById('customerSelect');
                let opt = document.createElement('option');
                opt.value = data.customer.id;
                opt.innerText = `${data.customer.name} (${data.customer.member_status})`;
                opt.selected = true;
                select.appendChild(opt);

                bootstrap.Modal.getInstance(document.getElementById('quickCustomerModal')).hide();
                document.getElementById('quickCustomerForm').reset();
            }
        });
    }
</script>
@endsection
