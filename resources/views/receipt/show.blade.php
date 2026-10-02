<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - {{ $order->order_number }}</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inconsolata:wght@400;600;700&family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #E7E5E4;
            font-family: 'Inter', sans-serif;
            padding: 30px 10px;
        }

        .receipt-card {
            width: 320px;
            margin: 0 auto;
            background: #FFFFFF;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            font-family: 'Inconsolata', monospace;
            font-size: 14px;
            color: #1C1917;
        }

        .receipt-header {
            text-align: center;
            border-bottom: 1px dashed #A8A29E;
            padding-bottom: 16px;
            margin-bottom: 16px;
        }

        .receipt-header h4 {
            font-weight: 700;
            margin: 0 0 4px 0;
            font-size: 18px;
        }

        .dashed-divider {
            border-top: 1px dashed #A8A29E;
            margin: 12px 0;
        }

        .receipt-item-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 6px;
        }

        .action-bar {
            width: 320px;
            margin: 0 auto 20px auto;
            display: flex;
            gap: 10px;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }
            .action-bar {
                display: none !important;
            }
            .receipt-card {
                box-shadow: none;
                width: 100%;
                padding: 0;
            }
        }
    </style>
</head>
<body>

    <div class="action-bar d-print-none">
        <button class="btn btn-dark btn-sm flex-fill rounded-3 fw-bold" onclick="window.print()">
            <i class="fa-solid fa-print me-1"></i> Print Receipt
        </button>
        <a href="{{ route('receipt.pdf', $order->id) }}" class="btn btn-outline-danger btn-sm flex-fill rounded-3 fw-bold">
            <i class="fa-solid fa-file-pdf me-1"></i> Download PDF
        </a>
        <a href="{{ route('pos.index') }}" class="btn btn-light border btn-sm rounded-3">
            <i class="fa-solid fa-arrow-left"></i> POS
        </a>
    </div>

    <div class="receipt-card">
        <div class="receipt-header">
            <h4>☕ {{ $settings['shop_name'] }}</h4>
            <div>{{ $settings['shop_address'] }}</div>
            <div>Tel: {{ $settings['shop_phone'] }}</div>
        </div>

        <div class="mb-2">
            <div><strong>Order #:</strong> {{ $order->order_number }}</div>
            <div><strong>Date:</strong> {{ $order->created_at->format('Y-m-d H:i:s') }}</div>
            <div><strong>Cashier:</strong> {{ $order->cashier ? $order->cashier->name : 'Staff' }}</div>
            <div><strong>Table:</strong> {{ $order->table ? $order->table->table_number : 'Takeaway' }}</div>
            <div><strong>Customer:</strong> {{ $order->customer ? $order->customer->name : 'Walk-in' }}</div>
        </div>

        <div class="dashed-divider"></div>

        <div class="fw-bold mb-2 d-flex justify-content-between">
            <span>Item</span>
            <span>Qty x Price</span>
            <span>Total</span>
        </div>

        @foreach($order->items as $item)
            <div class="receipt-item-row">
                <span style="max-width: 130px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                    {{ $item->coffee ? $item->coffee->name : 'Item' }}
                </span>
                <span>{{ $item->quantity }} x {{ number_format($item->price, 2) }}</span>
                <span class="fw-bold">${{ number_format($item->subtotal, 2) }}</span>
            </div>
        @endforeach

        <div class="dashed-divider"></div>

        <div class="d-flex justify-content-between">
            <span>Subtotal:</span>
            <span>${{ number_format($order->subtotal, 2) }}</span>
        </div>
        <div class="d-flex justify-content-between">
            <span>Discount:</span>
            <span>-${{ number_format($order->discount, 2) }}</span>
        </div>
        <div class="d-flex justify-content-between">
            <span>Tax:</span>
            <span>${{ number_format($order->tax, 2) }}</span>
        </div>
        <div class="d-flex justify-content-between fw-bold fs-6 mt-1">
            <span>GRAND TOTAL:</span>
            <span>${{ number_format($order->total, 2) }}</span>
        </div>

        <div class="dashed-divider"></div>

        @if($order->payment)
            <div class="d-flex justify-content-between">
                <span>Payment Method:</span>
                <span>{{ strtoupper($order->payment->payment_method) }}</span>
            </div>
            <div class="d-flex justify-content-between">
                <span>Amount Paid:</span>
                <span>${{ number_format($order->payment->amount_paid, 2) }}</span>
            </div>
            <div class="d-flex justify-content-between fw-bold">
                <span>Change:</span>
                <span>${{ number_format($order->payment->change, 2) }}</span>
            </div>
        @endif

        <div class="dashed-divider"></div>

        <div class="text-center mt-3 fs-7" style="font-style: italic;">
            {{ $settings['receipt_footer'] }}
        </div>
    </div>

</body>
</html>
