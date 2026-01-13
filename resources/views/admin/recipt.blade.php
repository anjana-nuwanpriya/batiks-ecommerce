@php
    // Auto-adjust perPage based on total orders for optimal A4 usage
    $totalOrders = $orders->count();

    if (!isset($perPage)) {
        if ($totalOrders == 8 || ($totalOrders > 8 && $totalOrders % 8 == 0)) {
            $perPage = 8; // Use 8 per page for optimal A4 usage
        } elseif ($totalOrders <= 4) {
            $perPage = $totalOrders; // Use actual count if 4 or less
        } else {
            $perPage = 4; // Default to 4
        }
    }
@endphp
@foreach ($orders->chunk($perPage) as $chunk)
    <!DOCTYPE html>
    <html>

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <meta charset="utf-8">
        <style>
            @page {
                size: A4;
                margin: 0;
            }

            * {
                box-sizing: border-box;
            }

            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 0;
                font-size: 10px;
                line-height: 1.2;
            }

            /* Print-specific styles */
            @media print {
                body {
                    -webkit-print-color-adjust: exact;
                    color-adjust: exact;
                }

                .no-print {
                    display: none !important;
                }

                .page-container {
                    page-break-after: always;
                }

                .page-container:last-child {
                    page-break-after: auto;
                }

                /* Ensure proper scaling for different printers */
                .labels-grid {
                    transform-origin: top left;
                }
            }

            /* Screen-only styles */
            @media screen {
                .print-controls {
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    z-index: 9999;
                    background: white;
                    padding: 15px;
                    border-radius: 8px;
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                    border: 1px solid #ddd;
                }

                .print-btn {
                    background: #007bff;
                    color: white;
                    border: none;
                    padding: 10px 20px;
                    border-radius: 5px;
                    cursor: pointer;
                    font-size: 14px;
                    margin-right: 10px;
                    transition: background-color 0.2s;
                }

                .print-btn:hover {
                    background: #0056b3;
                }

                .print-btn.secondary {
                    background: #6c757d;
                }

                .print-btn.secondary:hover {
                    background: #545b62;
                }

                .page-container {
                    margin: 20px auto;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                }
            }

            .page-container {
                width: 210mm;
                height: 297mm;
                position: relative;
                page-break-after: always;
            }

            /* Cutting marks */
            .cut-mark {
                position: absolute;
                background: #000;
                z-index: 1000;
            }

            /* Horizontal cutting marks */
            .cut-h {
                width: 8mm;
                height: 0.3mm;
            }

            /* Vertical cutting marks */
            .cut-v {
                width: 0.3mm;
                height: 8mm;
            }

            /* Center horizontal line */
            .cut-mark.center-h {
                top: 148.5mm;
                left: 0;
                width: 210mm;
                height: 0.2mm;
            }

            /* Center vertical line */
            .cut-mark.center-v {
                left: 105mm;
                top: 0;
                width: 0.2mm;
                height: 297mm;
            }

            /* Additional vertical lines for 8-per-page layout */
            .cut-mark.quarter-v-1 {
                left: 52.5mm;
                top: 0;
                width: 0.2mm;
                height: 297mm;
            }

            .cut-mark.quarter-v-2 {
                left: 157.5mm;
                top: 0;
                width: 0.2mm;
                height: 297mm;
            }

            /* Corner marks */
            .cut-mark.tl-h {
                top: 0;
                left: 0;
            }

            .cut-mark.tl-v {
                top: 0;
                left: 0;
            }

            .cut-mark.tr-h {
                top: 0;
                right: 0;
            }

            .cut-mark.tr-v {
                top: 0;
                right: 0;
            }

            .cut-mark.bl-h {
                bottom: 0;
                left: 0;
            }

            .cut-mark.bl-v {
                bottom: 0;
                left: 0;
            }

            .cut-mark.br-h {
                bottom: 0;
                right: 0;
            }

            .cut-mark.br-v {
                bottom: 0;
                right: 0;
            }

            /* Center marks */
            .cut-mark.tc-h-l {
                top: 0;
                left: 100mm;
            }

            .cut-mark.tc-h-r {
                top: 0;
                left: 110mm;
            }

            .cut-mark.bc-h-l {
                bottom: 0;
                left: 100mm;
            }

            .cut-mark.bc-h-r {
                bottom: 0;
                left: 110mm;
            }

            /* Quarter marks for 8-per-page layout */
            .cut-mark.tq1-h-l {
                top: 0;
                left: 47.5mm;
            }

            .cut-mark.tq1-h-r {
                top: 0;
                left: 57.5mm;
            }

            .cut-mark.tq2-h-l {
                top: 0;
                left: 152.5mm;
            }

            .cut-mark.tq2-h-r {
                top: 0;
                left: 162.5mm;
            }

            .cut-mark.bq1-h-l {
                bottom: 0;
                left: 47.5mm;
            }

            .cut-mark.bq1-h-r {
                bottom: 0;
                left: 57.5mm;
            }

            .cut-mark.bq2-h-l {
                bottom: 0;
                left: 152.5mm;
            }

            .cut-mark.bq2-h-r {
                bottom: 0;
                left: 162.5mm;
            }

            /* Middle horizontal marks for quarters */
            .cut-mark.mq1-h {
                top: 148.5mm;
                left: 52.5mm;
                width: 8mm;
                height: 0.3mm;
            }

            .cut-mark.mq2-h {
                top: 148.5mm;
                left: 157.5mm;
                width: 8mm;
                height: 0.3mm;
            }

            .cut-mark.ml-h {
                top: 148.5mm;
                left: 0;
            }

            .cut-mark.mr-h {
                top: 148.5mm;
                right: 0;
            }

            .cut-mark.ml-v-t {
                top: 143.5mm;
                left: 0;
            }

            .cut-mark.ml-v-b {
                top: 153.5mm;
                left: 0;
            }

            .cut-mark.mr-v-t {
                top: 143.5mm;
                right: 0;
            }

            .cut-mark.mr-v-b {
                top: 153.5mm;
                right: 0;
            }

            /* Center intersection marks */
            .cut-mark.cc-h-l {
                top: 148.5mm;
                left: 100mm;
            }

            .cut-mark.cc-h-r {
                top: 148.5mm;
                left: 110mm;
            }

            .cut-mark.cc-v-t {
                top: 143.5mm;
                left: 105mm;
            }

            .cut-mark.cc-v-b {
                top: 153.5mm;
                left: 105mm;
            }

            /* Dynamic grid layouts */
            .labels-grid {
                display: grid;
                width: 100%;
                height: 100%;
                gap: 0;
            }

            .labels-grid.count-1 {
                grid-template-columns: 210mm;
                grid-template-rows: 297mm;
            }

            .labels-grid.count-2 {
                grid-template-columns: 105mm 105mm;
                grid-template-rows: 297mm;
            }

            .labels-grid.count-3 {
                grid-template-columns: 105mm 105mm;
                grid-template-rows: 148.5mm 148.5mm;
            }

            .labels-grid.count-4 {
                grid-template-columns: 105mm 105mm;
                grid-template-rows: 148.5mm 148.5mm;
            }

            .labels-grid.count-5,
            .labels-grid.count-6,
            .labels-grid.count-7,
            .labels-grid.count-8 {
                grid-template-columns: 52.5mm 52.5mm 52.5mm 52.5mm;
                grid-template-rows: 148.5mm 148.5mm;
            }

            .label {
                border: 1px solid #000;
                padding: 5mm;
                position: relative;
                background: white;
                overflow: hidden;
            }

            .company-header {
                text-align: right;
                font-size: 8px;
                line-height: 1.3;
                margin-bottom: 8mm;
                font-weight: bold;
            }

            .barcode-section {
                text-align: left;
                margin-bottom: 5mm;
            }

            .barcode-section .barcode {
                margin-bottom: 2mm;
            }

            .order-id {
                font-size: 12px;
                font-weight: bold;
                margin-bottom: 3mm;
            }

            .to-section {
                border-top: 2px solid #000;
                padding-top: 3mm;
                margin-bottom: 5mm;
            }

            .to-header {
                font-weight: bold;
                font-size: 9px;
                margin-bottom: 2mm;
            }

            .address-content {
                font-size: 9px;
                line-height: 1.4;
            }

            .products-section {
                margin-bottom: 5mm;
            }

            .products-header {
                background: #000;
                color: white;
                text-align: center;
                padding: 2mm;
                font-weight: bold;
                font-size: 9px;
                margin-bottom: 0;
            }

            .products-content {
                border: 1px solid #000;
                border-top: none;
                padding: 3mm;
                min-height: 15mm;
                font-size: 8px;
                line-height: 1.4;
            }

            .pieces-weight {
                margin-bottom: 5mm;
                font-size: 9px;
                line-height: 1.5;
            }

            .reference-section {
                border: 1px solid #000;
                padding: 3mm;
                font-size: 8px;
                line-height: 1.4;
            }

            .reference-header {
                font-weight: bold;
                margin-bottom: 2mm;
            }

            .bold {
                font-weight: bold;
            }

            /* Responsive label sizing */
            .count-1 .label {
                font-size: 14px;
            }

            .count-1 .company-header {
                font-size: 12px;
                margin-bottom: 15mm;
            }

            .count-1 .order-id {
                font-size: 18px;
            }

            .count-2 .label {
                font-size: 11px;
            }

            .count-2 .company-header {
                font-size: 9px;
                margin-bottom: 10mm;
            }

            /* 8-per-page layout (4x2 grid) */
            .count-5 .label,
            .count-6 .label,
            .count-7 .label,
            .count-8 .label {
                font-size: 7px;
                padding: 2mm;
            }

            .count-5 .company-header,
            .count-6 .company-header,
            .count-7 .company-header,
            .count-8 .company-header {
                font-size: 6px;
                margin-bottom: 3mm;
                line-height: 1.2;
            }

            .count-5 .order-id,
            .count-6 .order-id,
            .count-7 .order-id,
            .count-8 .order-id {
                font-size: 8px;
                margin-bottom: 2mm;
            }

            .count-5 .to-header,
            .count-6 .to-header,
            .count-7 .to-header,
            .count-8 .to-header {
                font-size: 6px;
                margin-bottom: 1mm;
            }

            .count-5 .address-content,
            .count-6 .address-content,
            .count-7 .address-content,
            .count-8 .address-content {
                font-size: 6px;
                line-height: 1.3;
            }

            .count-5 .products-header,
            .count-6 .products-header,
            .count-7 .products-header,
            .count-8 .products-header {
                font-size: 6px;
                padding: 1mm;
            }

            .count-5 .products-content,
            .count-6 .products-content,
            .count-7 .products-content,
            .count-8 .products-content {
                font-size: 5px;
                padding: 2mm;
                min-height: 8mm;
                line-height: 1.3;
            }

            .count-5 .pieces-weight,
            .count-6 .pieces-weight,
            .count-7 .pieces-weight,
            .count-8 .pieces-weight {
                font-size: 6px;
                margin-bottom: 2mm;
                line-height: 1.3;
            }

            .count-5 .reference-section,
            .count-6 .reference-section,
            .count-7 .reference-section,
            .count-8 .reference-section {
                font-size: 5px;
                padding: 2mm;
                line-height: 1.3;
            }

            .count-5 .reference-header,
            .count-6 .reference-header,
            .count-7 .reference-header,
            .count-8 .reference-header {
                margin-bottom: 1mm;
            }

            .count-5 .barcode-section .barcode,
            .count-6 .barcode-section .barcode,
            .count-7 .barcode-section .barcode,
            .count-8 .barcode-section .barcode {
                margin-bottom: 1mm;
            }

            .count-5 .barcode-section,
            .count-6 .barcode-section,
            .count-7 .barcode-section,
            .count-8 .barcode-section {
                margin-bottom: 2mm;
            }

            .count-5 .to-section,
            .count-6 .to-section,
            .count-7 .to-section,
            .count-8 .to-section {
                padding-top: 2mm;
                margin-bottom: 2mm;
            }

            .count-5 .products-section,
            .count-6 .products-section,
            .count-7 .products-section,
            .count-8 .products-section {
                margin-bottom: 2mm;
            }
        </style>
    </head>

    <body>
        <!-- Print Controls (only visible on screen) -->
        <div class="print-controls no-print">
            <button class="print-btn" onclick="printPage()">🖨️ Print</button>
            <button class="print-btn secondary" onclick="printPreview()">👁️ Preview</button>
            <button class="print-btn secondary" onclick="closeWindow()">✕ Close</button>
        </div>

        <div class="page-container">
            @php
                $actualCount = $chunk->count();
                $needsVerticalCut = $actualCount >= 2;
                $needsHorizontalCut = $actualCount >= 3;
                $needsQuarterCuts = $actualCount >= 5; // For 8-per-page layout
            @endphp

            <!-- Corner marks (always present) -->
            <div class="cut-mark cut-h tl-h"></div>
            <div class="cut-mark cut-v tl-v"></div>
            <div class="cut-mark cut-h tr-h"></div>
            <div class="cut-mark cut-v tr-v"></div>
            <div class="cut-mark cut-h bl-h"></div>
            <div class="cut-mark cut-v bl-v"></div>
            <div class="cut-mark cut-h br-h"></div>
            <div class="cut-mark cut-v br-v"></div>

            @if ($needsVerticalCut)
                <!-- Vertical center line and marks -->
                <div class="cut-mark center-v"></div>
                <div class="cut-mark cut-h tc-h-l"></div>
                <div class="cut-mark cut-h tc-h-r"></div>
                <div class="cut-mark cut-h bc-h-l"></div>
                <div class="cut-mark cut-h bc-h-r"></div>
            @endif

            @if ($needsHorizontalCut)
                <!-- Horizontal center line and marks -->
                <div class="cut-mark center-h"></div>
                <div class="cut-mark cut-h ml-h"></div>
                <div class="cut-mark cut-v ml-v-t"></div>
                <div class="cut-mark cut-v ml-v-b"></div>
                <div class="cut-mark cut-h mr-h"></div>
                <div class="cut-mark cut-v mr-v-t"></div>
                <div class="cut-mark cut-v mr-v-b"></div>
            @endif

            @if ($needsVerticalCut && $needsHorizontalCut)
                <!-- Center intersection marks -->
                <div class="cut-mark cut-h cc-h-l"></div>
                <div class="cut-mark cut-h cc-h-r"></div>
                <div class="cut-mark cut-v cc-v-t"></div>
                <div class="cut-mark cut-v cc-v-b"></div>
            @endif

            @if ($needsQuarterCuts)
                <!-- Quarter vertical lines for 8-per-page layout -->
                <div class="cut-mark quarter-v-1"></div>
                <div class="cut-mark quarter-v-2"></div>

                <!-- Quarter marks at top -->
                <div class="cut-mark cut-h tq1-h-l"></div>
                <div class="cut-mark cut-h tq1-h-r"></div>
                <div class="cut-mark cut-h tq2-h-l"></div>
                <div class="cut-mark cut-h tq2-h-r"></div>

                <!-- Quarter marks at bottom -->
                <div class="cut-mark cut-h bq1-h-l"></div>
                <div class="cut-mark cut-h bq1-h-r"></div>
                <div class="cut-mark cut-h bq2-h-l"></div>
                <div class="cut-mark cut-h bq2-h-r"></div>

                <!-- Quarter marks at middle horizontal -->
                <div class="cut-mark mq1-h"></div>
                <div class="cut-mark mq2-h"></div>
            @endif

            <!-- Labels Grid -->
            <div class="labels-grid count-{{ $actualCount }}">
                @foreach ($chunk as $order)
                    <div class="label">
                        {{-- Company Header --}}
                        <div class="company-header">
                            {{ env('APP_NAME') }}<br>
                            {{ get_setting('address') }}<br>
                            {{ get_setting('phone') }}
                        </div>

                        {{-- Barcode Section --}}
                        <div class="barcode-section">
                            <div class="barcode">
                                {!! DNS1D::getBarcodeHTML(str_pad($order->id, 4, '0', STR_PAD_LEFT), 'C128', 1.2, 25) !!}
                            </div>
                            <div class="order-id">{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</div>
                        </div>

                        {{-- TO Section --}}
                        @php
                            $address = is_string($order->shipping_address)
                                ? json_decode($order->shipping_address)
                                : (object) $order->shipping_address;
                        @endphp
                        <div class="to-section">
                            <div class="to-header">TO - Buyer address</div>
                            <div class="address-content">
                                @if (!empty($order->user))
                                    {{ $order->user->name }}<br>
                                @else
                                    {{ $address->name ?? '' }}<br>
                                @endif
                                {{ $address->address ?? '' }}<br>
                                {{ $address->city ?? '' }}, {{ $address->state ?? '' }}<br>
                                {{ $address->postal_code ?? '' }}<br>
                                {{ $address->phone ?? '' }}
                            </div>
                        </div>

                        {{-- Products Section --}}
                        <div class="products-section">
                            <div class="products-header">PRODUCTS</div>
                            <div class="products-content">
                                @foreach ($order->items as $item)
                                    {{ $item->product->name }}{{ $item->variant != 'Standard' ? ' - ' . $item->variant : '' }}
                                    x {{ $item->quantity }}<br>
                                @endforeach
                            </div>
                        </div>

                        {{-- Pieces & Weight --}}
                        <div class="pieces-weight">
                            <div><span class="bold">PIECES:</span> {{ $order->items->sum('quantity') }}</div>
                            <div><span class="bold">WEIGHT:</span>
                                {{ number_format($order->items->sum('weight') / 1000, 2) }} KG</div>
                        </div>

                        {{-- Reference Section --}}
                        <div class="reference-section">
                            <div class="reference-header">REFERENCE</div>
                            <div>REF: {{ $order->code }}</div>
                            <div>Date: {{ date('Y-m-d') }}</div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </body>

    <script>
        // Print functionality
        function printPage() {
            // Hide print controls before printing
            const controls = document.querySelector('.print-controls');
            if (controls) {
                controls.style.display = 'none';
            }

            // Print the page
            window.print();

            // Show controls again after print dialog
            setTimeout(() => {
                if (controls) {
                    controls.style.display = 'block';
                }
            }, 100);
        }

        function printPreview() {
            // Open print preview
            window.print();
        }

        function closeWindow() {
            // Try multiple methods to close the window
            try {
                // Method 1: Try to close the window (works if opened by script)
                window.close();
            } catch (e) {
                // Method 2: If that fails, try to go back in history
                if (window.history.length > 1) {
                    window.history.back();
                } else {
                    // Method 3: Navigate to admin dashboard or previous page
                    window.location.href = '{{ route("admin.dashboard") ?? "/admin" }}';
                }
            }

            // Method 4: If window.close() doesn't work immediately, show a message
            setTimeout(() => {
                if (!window.closed) {
                    if (confirm('Unable to close window automatically. Close this tab manually or click OK to go back to dashboard.')) {
                        window.location.href = '{{ route("admin.dashboard") ?? "/admin" }}';
                    }
                }
            }, 100);
        }

        // Auto-focus for immediate printing (optional)
        document.addEventListener('DOMContentLoaded', function() {
            // Check if this page was opened specifically for printing
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('auto_print') === '1') {
                setTimeout(() => {
                    printPage();
                }, 500);
            }
        });

        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl+P or Cmd+P for print
            if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                e.preventDefault();
                printPage();
            }

            // Escape to close
            if (e.key === 'Escape') {
                closeWindow();
            }
        });

        // Handle browser print button
        window.addEventListener('beforeprint', function() {
            const controls = document.querySelector('.print-controls');
            if (controls) {
                controls.style.display = 'none';
            }
        });

        window.addEventListener('afterprint', function() {
            const controls = document.querySelector('.print-controls');
            if (controls) {
                controls.style.display = 'block';
            }
        });
    </script>

    </html>
@endforeach
