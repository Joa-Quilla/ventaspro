<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket de Venta #{{ $sale->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            line-height: 1.4;
            padding: 10mm;
            max-width: 80mm;
            margin: 0 auto;
        }

        .ticket {
            width: 100%;
        }

        .header {
            text-align: center;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 2px dashed #000;
        }

        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .header p {
            font-size: 11px;
            margin: 2px 0;
        }

        .info-section {
            margin: 10px 0;
            padding: 10px 0;
            border-bottom: 1px dashed #000;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
            font-size: 11px;
        }

        .info-label {
            font-weight: bold;
        }

        .products-section {
            margin: 10px 0;
        }

        .products-header {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            padding: 5px 0;
            border-bottom: 1px solid #000;
            font-size: 11px;
        }

        .product-item {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            font-size: 11px;
        }

        .product-name {
            flex: 1;
        }

        .product-qty {
            width: 40px;
            text-align: center;
        }

        .product-price {
            width: 60px;
            text-align: right;
        }

        .product-subtotal {
            width: 70px;
            text-align: right;
            font-weight: bold;
        }

        .totals-section {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 2px solid #000;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
            font-size: 12px;
        }

        .total-row.grand-total {
            font-size: 16px;
            font-weight: bold;
            margin-top: 8px;
            padding-top: 8px;
            border-top: 2px solid #000;
        }

        .footer {
            text-align: center;
            margin-top: 15px;
            padding-top: 10px;
            border-top: 2px dashed #000;
            font-size: 11px;
        }

        .footer p {
            margin: 3px 0;
        }

        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            margin-top: 5px;
        }

        .status-completed {
            background-color: #22c55e;
            color: white;
        }

        .status-cancelled {
            background-color: #ef4444;
            color: white;
        }

        @media print {
            body {
                padding: 0;
            }

            .no-print {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="ticket">
        <!-- HEADER -->
        <div class="header">
            <h1>VENTASPRO</h1>
            <p>Sistema POS Multi-Tenant</p>
            <p>NIT: 12345678-9</p>
            <p>Tel: (502) 1234-5678</p>
            <p>Guatemala, Guatemala</p>
        </div>

        <!-- INFO DE VENTA -->
        <div class="info-section">
            <div class="info-row">
                <span class="info-label">Ticket #:</span>
                <span>{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Fecha:</span>
                <span>{{ $sale->created_at->format('d/m/Y H:i:s') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Cajero:</span>
                <span>{{ $sale->user->name }}</span>
            </div>
            @if ($sale->customer_name)
                <div class="info-row">
                    <span class="info-label">Cliente:</span>
                    <span>{{ $sale->customer_name }}</span>
                </div>
            @endif
            @if ($sale->customer_phone)
                <div class="info-row">
                    <span class="info-label">Teléfono:</span>
                    <span>{{ $sale->customer_phone }}</span>
                </div>
            @endif
            <div class="info-row">
                <span class="info-label">Estado:</span>
                <span class="status-badge status-{{ $sale->status }}">
                    {{ $sale->status === 'completed' ? 'COMPLETADA' : 'CANCELADA' }}
                </span>
            </div>
        </div>

        <!-- PRODUCTOS -->
        <div class="products-section">
            <div class="products-header">
                <span style="flex: 1;">Producto</span>
                <span style="width: 40px; text-align: center;">Cant</span>
                <span style="width: 60px; text-align: right;">Precio</span>
                <span style="width: 70px; text-align: right;">Total</span>
            </div>

            @foreach ($sale->items as $item)
                <div class="product-item">
                    <span class="product-name">{{ $item->product->name }}</span>
                    <span class="product-qty">{{ $item->quantity }}</span>
                    <span class="product-price">Q{{ number_format($item->unit_price, 2) }}</span>
                    <span class="product-subtotal">Q{{ number_format($item->subtotal, 2) }}</span>
                </div>
            @endforeach
        </div>

        <!-- TOTALES -->
        <div class="totals-section">
            <div class="total-row">
                <span>Subtotal:</span>
                <span>Q{{ number_format($sale->subtotal, 2) }}</span>
            </div>
            @if ($sale->tax > 0)
                <div class="total-row">
                    <span>IVA (12%):</span>
                    <span>Q{{ number_format($sale->tax, 2) }}</span>
                </div>
            @endif
            <div class="total-row grand-total">
                <span>TOTAL:</span>
                <span>Q{{ number_format($sale->total, 2) }}</span>
            </div>
            <div class="total-row">
                <span>Método de Pago:</span>
                <span>
                    @php
                        $paymentLabel = match ($sale->payment_method) {
                            'cash' => 'EFECTIVO',
                            'card' => 'TARJETA',
                            'transfer' => 'TRANSFERENCIA',
                            default => strtoupper($sale->payment_method),
                        };
                    @endphp
                    {{ $paymentLabel }}
                </span>
            </div>
        </div>

        <!-- FOOTER -->
        <div class="footer">
            <p>¡Gracias por su compra!</p>
            <p>Este documento no es una factura fiscal</p>
            <p>Para factura, solicítela al momento de la compra</p>
            <p style="margin-top: 10px; font-size: 10px;">
                Sistema VentasPro v1.0
            </p>
        </div>
    </div>

    <script>
        // Auto-imprimir al cargar
        window.onload = function() {
            window.print();
        };

        // Cerrar pestaña después de imprimir o cancelar
        window.onafterprint = function() {
            window.close();
        };
    </script>
</body>

</html>
