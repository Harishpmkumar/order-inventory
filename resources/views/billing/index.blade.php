<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Store Billing — New Order</title>

    {{-- Bootstrap 5.3 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #f3f4f6;
        }

        .app-header {
            background: #172554;
        }

        .app-header .subtitle {
            color: #c7d2fe;
        }

        .card {
            border: 0;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
        }

        .card-title {
            color: #111827;
        }

        .form-label {
            color: #374151;
            font-weight: 600;
            font-size: 14px;
        }

        .form-control,
        .form-select {
            font-size: 14px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 0.2rem rgba(37, 99, 235, 0.12);
        }

        .products-table th {
            background: #f9fafb;
            color: #374151;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
        }

        .products-table td {
            vertical-align: middle;
        }

        .products-table .price-display,
        .products-table .line-total {
            white-space: nowrap;
            font-weight: 600;
        }

        .remove-btn {
            width: 34px;
            height: 34px;
            padding: 0;
            border: 0;
            background: transparent;
            color: #dc2626;
            font-size: 21px;
            line-height: 1;
        }

        .remove-btn:hover {
            color: #991b1b;
        }

        .add-product-btn {
            border: 1px solid #2563eb;
            color: #2563eb;
            background: #fff;
        }

        .add-product-btn:hover {
            background: #eff6ff;
            border-color: #2563eb;
            color: #1d4ed8;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            padding: 8px 0;
        }

        .summary-row strong {
            white-space: nowrap;
        }

        .summary-total {
            border-top: 1px solid #e5e7eb;
            margin-top: 8px;
            padding-top: 14px;
            font-size: 18px;
        }

        .balance-box {
            background: #f0fdf4;
            color: #166534;
            border-radius: 7px;
        }

        .balance-box.insufficient {
            background: #fef2f2;
            color: #991b1b;
        }

        .denominations {
            font-size: 13px;
            line-height: 1.5;
        }

        .low-stock-card {
            background: #fff7ed;
            border: 1px solid #fed7aa;
        }

        .low-stock-card .card-title {
            color: #9a3412;
        }

        .low-stock-list li {
            border-bottom: 1px solid #fed7aa;
        }

        .low-stock-list li:last-child {
            border-bottom: 0;
        }

        .stock-count {
            color: #c2410c;
            white-space: nowrap;
            font-weight: 700;
        }

        .bill-card {
            display: none;
        }

        .bill-customer {
            background: #f9fafb;
            border-radius: 6px;
        }

        .bill-table th {
            background: #f9fafb;
            white-space: nowrap;
        }

        .bill-totals {
            max-width: 350px;
            margin-left: auto;
        }

        .loading {
            opacity: 0.65;
            pointer-events: none;
        }

        /*
         * Mobile product rows
         *
         * Instead of forcing a wide table onto a phone,
         * each product becomes a clean card-like row.
         */
        @media (max-width: 767.98px) {

            .app-header {
                padding-top: 1rem !important;
                padding-bottom: 1rem !important;
            }

            .app-header h1 {
                font-size: 1.2rem;
            }

            .app-header .subtitle {
                font-size: 0.8rem;
            }

            .main-container {
                padding-left: 10px;
                padding-right: 10px;
            }

            .card-body {
                padding: 1rem;
            }

            /* Hide desktop table header */
            .products-table thead {
                display: none;
            }

            .products-table,
            .products-table tbody {
                display: block;
                width: 100%;
            }

            .products-table tr {
                display: block;
                padding: 1rem 0;
                border-bottom: 1px solid #e5e7eb;
            }

            .products-table tr:first-child {
                padding-top: 0;
            }

            .products-table tr:last-child {
                border-bottom: 0;
            }

            .products-table td {
                display: block;
                width: 100%;
                padding: 0;
                border: 0;
                margin-bottom: 0.75rem;
            }

            .products-table td:last-child {
                margin-bottom: 0;
            }

            .products-table td:nth-child(1)::before {
                content: "Product";
                display: block;
                margin-bottom: 0.35rem;
                color: #6b7280;
                font-size: 12px;
                font-weight: 600;
            }

            .products-table td:nth-child(2)::before {
                content: "Quantity";
                display: block;
                margin-bottom: 0.35rem;
                color: #6b7280;
                font-size: 12px;
                font-weight: 600;
            }

            .products-table td:nth-child(3),
            .products-table td:nth-child(4) {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 0.5rem 0;
                border-top: 1px solid #f3f4f6;
            }

            .products-table td:nth-child(3)::before {
                content: "Price";
                color: #6b7280;
                font-size: 13px;
                font-weight: 600;
            }

            .products-table td:nth-child(4)::before {
                content: "Line total";
                color: #6b7280;
                font-size: 13px;
                font-weight: 600;
            }

            .products-table td:nth-child(5) {
                text-align: right;
                margin-top: 0.25rem;
            }

            .products-table .quantity-input {
                width: 100%;
            }

            .products-table .remove-btn {
                visibility: hidden;
            }

            .products-table .remove-btn.show-remove {
                visibility: visible;
            }

            .add-product-btn {
                width: 100%;
            }

            .bill-table-wrapper {
                overflow-x: auto;
            }

            .bill-table {
                min-width: 520px;
            }

            .bill-totals {
                max-width: none;
            }

            .summary-row {
                font-size: 14px;
            }

            .summary-total {
                font-size: 17px;
            }
        }

        @media (max-width: 380px) {

            .main-container {
                padding-left: 8px;
                padding-right: 8px;
            }

            .card-body {
                padding: 0.8rem;
            }

            .app-header h1 {
                font-size: 1.05rem;
            }

            .card-title {
                font-size: 1.05rem;
            }
        }
    </style>
</head>

<body>

    {{-- =========================
         HEADER
    ========================== --}}

    <header class="app-header text-white">
        <div class="container-fluid px-3 px-md-4 py-3">

            <h1 class="h4 mb-1">
                Store Billing — New Order
            </h1>

            <p class="subtitle mb-0">
                Store Order &amp; Inventory Management
            </p>

        </div>
    </header>


    {{-- =========================
         MAIN
    ========================== --}}

    <main class="container-fluid main-container py-3 py-md-4">

        <div class="container-xl">

            <div id="message" class="alert d-none mb-3"></div>

            <div class="row g-4">

                {{-- =========================
                     MAIN COLUMN
                ========================== --}}

                <div class="col-12 col-lg-8 col-xl-8">

                    {{-- CUSTOMER --}}

                    <section class="card mb-3">

                        <div class="card-body">

                            <h2 class="card-title h5 mb-4">
                                Customer
                            </h2>

                            <div class="row g-3">

                                <div class="col-12 col-md-6">

                                    <label for="customer-email" class="form-label">
                                        Customer email
                                    </label>

                                    <input type="email" id="customer-email" class="form-control"
                                        placeholder="customer@example.com" autocomplete="email">

                                    <div id="customer-status" class="small mt-2"></div>

                                </div>

                                <div class="col-12 col-md-6">

                                    <label for="customer-name" class="form-label">
                                        Name
                                    </label>

                                    <input type="text" id="customer-name" class="form-control"
                                        placeholder="Customer name" autocomplete="name">

                                </div>

                            </div>

                        </div>

                    </section>


                    {{-- PRODUCTS --}}

                    <section class="card mb-3">

                        <div class="card-body">

                            <h2 class="card-title h5 mb-4">
                                Products
                            </h2>

                            <div class="table-responsive">

                                <table class="table products-table align-middle mb-0">

                                    <thead>

                                        <tr>

                                            <th style="width: 40%;">
                                                Product
                                            </th>

                                            <th style="width: 15%;">
                                                Qty
                                            </th>

                                            <th style="width: 15%;">
                                                Price
                                            </th>

                                            <th style="width: 20%;">
                                                Line total
                                            </th>

                                            <th style="width: 10%;"></th>

                                        </tr>

                                    </thead>

                                    <tbody id="product-rows"></tbody>

                                </table>

                            </div>

                            <button type="button" class="btn add-product-btn mt-3" id="add-product-btn">
                                + Add product
                            </button>

                        </div>

                    </section>


                    {{-- PAYMENT --}}

                    <section class="card mb-3">

                        <div class="card-body">

                            <h2 class="card-title h5 mb-4">
                                Payment
                            </h2>

                            <div class="ms-lg-auto" style="max-width: 500px;">

                                <div class="summary-row">
                                    <span>Subtotal</span>

                                    <strong id="subtotal">
                                        ₹0.00
                                    </strong>
                                </div>

                                <div class="summary-row">
                                    <span>Tax</span>

                                    <strong id="tax">
                                        ₹0.00
                                    </strong>
                                </div>

                                <div class="summary-row summary-total">
                                    <span>Grand total</span>

                                    <strong id="grand-total">
                                        ₹0.00
                                    </strong>
                                </div>


                                <div class="mt-4">

                                    <label for="amount-given" class="form-label">
                                        Amount given by customer
                                    </label>

                                    <input type="number" id="amount-given" class="form-control" min="0"
                                        step="0.01" placeholder="0.00">

                                </div>


                                <div id="balance" class="balance-box p-3 mt-3">

                                    Balance to return: ₹0.00

                                    <div id="denominations" class="denominations mt-1"></div>

                                </div>


                                <button type="button" class="btn btn-primary w-100 fw-bold mt-3 py-2"
                                    id="generate-btn">
                                    Generate bill
                                </button>

                            </div>

                        </div>

                    </section>


                    {{-- GENERATED BILL --}}

                    <section class="card bill-card mb-3" id="bill-card">

                        <div class="card-body">

                            <div class="d-flex flex-column flex-sm-row justify-content-between gap-2 mb-3">

                                <div>

                                    <h2 class="card-title h5 mb-1">
                                        Generated Bill
                                    </h2>

                                    <div id="bill-id" class="text-secondary small"></div>

                                </div>

                                <strong id="bill-status">
                                    Confirmed
                                </strong>

                            </div>


                            <div id="bill-customer" class="bill-customer p-3 mb-3"></div>


                            <div class="bill-table-wrapper">

                                <table class="table table-bordered bill-table mb-0">

                                    <thead>

                                        <tr>

                                            <th>Product</th>
                                            <th>Qty</th>
                                            <th>Price</th>
                                            <th>Tax</th>
                                            <th>Total</th>

                                        </tr>

                                    </thead>

                                    <tbody id="bill-items"></tbody>

                                </table>

                            </div>


                            <div class="bill-totals mt-3">

                                <div class="summary-row">
                                    <span>Subtotal</span>

                                    <strong id="bill-subtotal">
                                        ₹0.00
                                    </strong>
                                </div>

                                <div class="summary-row">
                                    <span>Tax</span>

                                    <strong id="bill-tax">
                                        ₹0.00
                                    </strong>
                                </div>

                                <div class="summary-row summary-total">
                                    <span>Grand total</span>

                                    <strong id="bill-grand-total">
                                        ₹0.00
                                    </strong>
                                </div>

                            </div>

                        </div>

                    </section>

                </div>


                {{-- =========================
                     LOW STOCK
                ========================== --}}

                <div class="col-12 col-lg-4 col-xl-4">

                    <aside>

                        <section class="card low-stock-card">

                            <div class="card-body">

                                <h2 class="card-title h5 mb-3">
                                    ⚠ Low stock alert
                                </h2>

                                <ul class="low-stock-list list-unstyled mb-0" id="low-stock-list">

                                    @forelse ($lowStockProducts as $product)
                                        <li class="py-2 d-flex justify-content-between align-items-start gap-2">

                                            <span class="text-break">
                                                {{ $product->name }}
                                            </span>

                                            <span class="stock-count">
                                                {{ $product->stock_on_hand }}
                                                units left
                                            </span>

                                        </li>

                                    @empty

                                        <li class="py-2">
                                            <span>
                                                No low-stock products.
                                            </span>
                                        </li>
                                    @endforelse

                                </ul>

                            </div>

                        </section>

                    </aside>

                </div>

            </div>

        </div>

    </main>


    {{-- Bootstrap JS --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>


    <script>
        /* =========================
                   PRODUCTS
                ========================== */

        const products = {!! json_encode(
            $products->map(function ($product) {
                    return [
                        'id' => $product->id,
                        'name' => $product->name,
                        'code' => $product->code,
                        'price' => (float) $product->price,
                        'tax_percentage' => (float) $product->tax_percentage,
                        'stock_on_hand' => $product->stock_on_hand,
                    ];
                })->values(),
        ) !!};


        /* =========================
           ELEMENTS
        ========================== */

        const productRows =
            document.getElementById('product-rows');

        const addProductButton =
            document.getElementById('add-product-btn');

        const customerEmail =
            document.getElementById('customer-email');

        const customerName =
            document.getElementById('customer-name');

        const customerStatus =
            document.getElementById('customer-status');

        const subtotalElement =
            document.getElementById('subtotal');

        const taxElement =
            document.getElementById('tax');

        const grandTotalElement =
            document.getElementById('grand-total');

        const amountGiven =
            document.getElementById('amount-given');

        const balanceElement =
            document.getElementById('balance');

        const generateButton =
            document.getElementById('generate-btn');

        const messageElement =
            document.getElementById('message');

        const billCard =
            document.getElementById('bill-card');

        const billId =
            document.getElementById('bill-id');

        const billCustomer =
            document.getElementById('bill-customer');

        const billItems =
            document.getElementById('bill-items');


        let lookupTimer = null;


        /* =========================
           HELPERS
        ========================== */

        function formatCurrency(value) {

            return '₹' +
                Number(value).toFixed(2);
        }


        function escapeHtml(value) {

            return String(value)
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        }


        function showMessage(
            message,
            type = 'success'
        ) {

            messageElement.textContent =
                message;

            messageElement.className =
                'alert mb-3 ' +
                (
                    type === 'success' ?
                    'alert-success' :
                    'alert-danger'
                );
        }


        function hideMessage() {

            messageElement.textContent = '';

            messageElement.className =
                'alert d-none mb-3';
        }


        function getProduct(productId) {

            return products.find(
                product =>
                Number(product.id) ===
                Number(productId)
            );
        }


        /* =========================
           PRODUCT OPTIONS
        ========================== */

        function getSelectedProductIds() {

            return Array.from(
                    document.querySelectorAll(
                        '.product-select'
                    )
                )
                .map(select => select.value)
                .filter(Boolean)
                .map(Number);
        }


        function refreshProductOptions() {

            const selectedIds =
                getSelectedProductIds();

            document
                .querySelectorAll(
                    '.product-select'
                )
                .forEach(select => {

                    const currentValue =
                        Number(select.value);

                    Array.from(
                        select.options
                    ).forEach(option => {

                        if (!option.value) {
                            return;
                        }

                        const optionId =
                            Number(option.value);

                        option.disabled =
                            selectedIds.includes(
                                optionId
                            ) &&
                            optionId !==
                            currentValue;
                    });
                });
        }


        function createProductOptions(
            selectedId = ''
        ) {

            let html = `
                <option value="">
                    Select product
                </option>
            `;

            products.forEach(product => {

                const selected =
                    Number(selectedId) ===
                    Number(product.id) ?
                    'selected' :
                    '';

                const stockText =
                    product.stock_on_hand > 0 ?
                    `${product.stock_on_hand} in stock` :
                    'Out of stock';

                html += `
                    <option
                        value="${product.id}"
                        ${selected}
                    >
                        ${escapeHtml(product.name)}
                        (${escapeHtml(product.code)}) —
                        ₹${product.price.toFixed(2)} —
                        ${stockText}
                    </option>
                `;
            });

            return html;
        }


        /* =========================
           REMOVE BUTTONS
        ========================== */

        function updateRemoveButtons() {

            const rows =
                productRows.querySelectorAll('tr');

            const shouldShow =
                rows.length > 1;

            rows.forEach(row => {

                const button =
                    row.querySelector(
                        '.remove-btn'
                    );

                if (!button) {
                    return;
                }

                if (shouldShow) {

                    button.classList.add(
                        'show-remove'
                    );

                } else {

                    button.classList.remove(
                        'show-remove'
                    );
                }
            });
        }


        /* =========================
           ADD PRODUCT ROW
        ========================== */

        function addProductRow() {

            if (products.length === 0) {

                showMessage(
                    'No products are available.',
                    'error'
                );

                return;
            }

            const row =
                document.createElement('tr');

            row.innerHTML = `
                <td>
                    <select
                        class="form-select product-select"
                    >
                        ${createProductOptions()}
                    </select>
                </td>

                <td>
                    <input
                        type="number"
                        class="form-control quantity-input"
                        min="1"
                        value="1"
                    >
                </td>

                <td>
                    <span class="price-display">
                        ₹0.00
                    </span>
                </td>

                <td>
                    <span class="line-total">
                        ₹0.00
                    </span>
                </td>

                <td>
                    <button
                        type="button"
                        class="remove-btn"
                        title="Remove product"
                        aria-label="Remove product"
                    >
                        ×
                    </button>
                </td>
            `;

            productRows.appendChild(row);


            const select =
                row.querySelector(
                    '.product-select'
                );

            const quantityInput =
                row.querySelector(
                    '.quantity-input'
                );

            const removeButton =
                row.querySelector(
                    '.remove-btn'
                );


            select.addEventListener(
                'change',
                () => {

                    updateRow(row);
                    refreshProductOptions();
                    calculateTotals();
                }
            );


            quantityInput.addEventListener(
                'input',
                () => {

                    updateRow(row);
                    calculateTotals();
                }
            );


            removeButton.addEventListener(
                'click',
                () => {

                    row.remove();

                    updateRemoveButtons();
                    refreshProductOptions();
                    calculateTotals();
                }
            );


            refreshProductOptions();
            updateRemoveButtons();
        }


        /* =========================
           UPDATE PRODUCT ROW
        ========================== */

        function updateRow(row) {

            const select =
                row.querySelector(
                    '.product-select'
                );

            const quantityInput =
                row.querySelector(
                    '.quantity-input'
                );

            const priceDisplay =
                row.querySelector(
                    '.price-display'
                );

            const lineTotal =
                row.querySelector(
                    '.line-total'
                );

            const product =
                getProduct(select.value);


            if (!product) {

                priceDisplay.textContent =
                    '₹0.00';

                lineTotal.textContent =
                    '₹0.00';

                return;
            }


            const quantity =
                Math.max(
                    0,
                    Number(
                        quantityInput.value
                    ) || 0
                );


            const subtotal =
                product.price *
                quantity;


            const tax =
                subtotal *
                product.tax_percentage /
                100;


            const total =
                subtotal + tax;


            priceDisplay.textContent =
                formatCurrency(
                    product.price
                );


            lineTotal.textContent =
                formatCurrency(
                    total
                );
        }


        /* =========================
           ORDER ITEMS
        ========================== */

        function getOrderItems() {

            const rows =
                Array.from(
                    productRows.querySelectorAll(
                        'tr'
                    )
                );

            return rows
                .map(row => {

                    const productId =
                        row.querySelector(
                            '.product-select'
                        ).value;

                    const quantity =
                        Number(
                            row.querySelector(
                                '.quantity-input'
                            ).value
                        );

                    return {
                        productId,
                        quantity,
                    };
                })
                .filter(
                    item =>
                    item.productId
                );
        }


        /* =========================
           TOTALS
        ========================== */

        function calculateTotals() {

            let subtotal = 0;
            let tax = 0;


            document
                .querySelectorAll(
                    '#product-rows tr'
                )
                .forEach(row => {

                    const select =
                        row.querySelector(
                            '.product-select'
                        );

                    const quantityInput =
                        row.querySelector(
                            '.quantity-input'
                        );

                    const product =
                        getProduct(
                            select.value
                        );


                    if (!product) {
                        return;
                    }


                    const quantity =
                        Math.max(
                            0,
                            Number(
                                quantityInput.value
                            ) || 0
                        );


                    const itemSubtotal =
                        product.price *
                        quantity;


                    const itemTax =
                        itemSubtotal *
                        product.tax_percentage /
                        100;


                    subtotal +=
                        itemSubtotal;

                    tax +=
                        itemTax;


                    updateRow(row);
                });


            subtotalElement.textContent =
                formatCurrency(
                    subtotal
                );


            taxElement.textContent =
                formatCurrency(
                    tax
                );


            grandTotalElement.textContent =
                formatCurrency(
                    subtotal + tax
                );


            calculateBalance();
        }


        function getGrandTotal() {

            const text =
                grandTotalElement
                .textContent
                .replace('₹', '');

            return Number(text) || 0;
        }


        /* =========================
           BALANCE
        ========================== */

        function calculateBalance() {

            const total =
                getGrandTotal();

            const given =
                Number(
                    amountGiven.value
                ) || 0;

            const balance =
                given - total;


            balanceElement.classList.remove(
                'insufficient'
            );


            if (given === 0) {

                balanceElement.innerHTML = `
                    Balance to return: ₹0.00

                    <div class="denominations mt-1"></div>
                `;

                return;
            }


            if (balance < 0) {

                balanceElement.classList.add(
                    'insufficient'
                );

                balanceElement.innerHTML = `
                    Amount given is insufficient.

                    <div class="denominations mt-1">
                        Short by
                        ${formatCurrency(
                            Math.abs(balance)
                        )}
                    </div>
                `;

                return;
            }


            const roundedBalance =
                Math.round(
                    balance * 100
                ) / 100;


            let denominationText = '';


            if (
                Number.isInteger(
                    roundedBalance
                )
            ) {

                denominationText =
                    getDenominationBreakdown(
                        roundedBalance
                    );
            }


            balanceElement.innerHTML = `
                Balance to return:
                ${formatCurrency(
                    roundedBalance
                )}

                <div class="denominations mt-1">
                    ${denominationText}
                </div>
            `;
        }


        function getDenominationBreakdown(
            amount
        ) {

            const denominations = [
                500,
                200,
                100,
                50,
                20,
                10,
                5,
                2,
                1
            ];


            let remaining =
                amount;

            const parts = [];


            denominations.forEach(
                value => {

                    if (
                        remaining >= value
                    ) {

                        const count =
                            Math.floor(
                                remaining /
                                value
                            );


                        remaining -=
                            count * value;


                        parts.push(
                            `${count} × ₹${value}`
                        );
                    }
                }
            );


            return parts.length ?
                parts.join(' + ') :
                'No change';
        }


        /* =========================
           CUSTOMER LOOKUP
        ========================== */

        async function lookupCustomer() {

            const email =
                customerEmail.value.trim();


            if (!email) {

                customerStatus.textContent =
                    '';

                customerStatus.className =
                    'small mt-2';

                return;
            }


            try {

                const response =
                    await fetch(
                        `{{ route('customers.lookup') }}?email=` +
                        encodeURIComponent(
                            email
                        ), {
                            headers: {
                                'Accept': 'application/json'
                            }
                        }
                    );


                if (!response.ok) {
                    return;
                }


                const result =
                    await response.json();


                if (
                    result.found &&
                    result.data
                ) {

                    customerName.value =
                        result.data.name;

                    customerStatus.textContent =
                        'Existing customer found.';

                    customerStatus.className =
                        'small mt-2 text-success';

                } else {

                    customerStatus.textContent =
                        'New customer.';

                    customerStatus.className =
                        'small mt-2 text-secondary';
                }

            } catch (error) {

                console.error(
                    'Customer lookup failed:',
                    error
                );
            }
        }


        /* =========================
           EVENTS
        ========================== */

        customerEmail.addEventListener(
            'input',
            () => {

                clearTimeout(
                    lookupTimer
                );

                lookupTimer =
                    setTimeout(
                        lookupCustomer,
                        500
                    );
            }
        );


        amountGiven.addEventListener(
            'input',
            calculateBalance
        );


        addProductButton.addEventListener(
            'click',
            addProductRow
        );


        generateButton.addEventListener(
            'click',
            generateBill
        );


        /* =========================
           VALIDATION
        ========================== */

        function validateOrder() {

            const email =
                customerEmail.value.trim();

            const name =
                customerName.value.trim();

            const items =
                getOrderItems();


            if (!name) {

                showMessage(
                    'Customer name is required.',
                    'error'
                );

                customerName.focus();

                return false;
            }


            if (!email) {

                showMessage(
                    'Customer email is required.',
                    'error'
                );

                customerEmail.focus();

                return false;
            }


            if (items.length === 0) {

                showMessage(
                    'Add at least one product.',
                    'error'
                );

                return false;
            }


            for (const item of items) {

                if (item.quantity < 1) {

                    showMessage(
                        'Quantity must be at least 1.',
                        'error'
                    );

                    return false;
                }


                const product =
                    getProduct(
                        item.productId
                    );


                if (
                    product &&
                    item.quantity >
                    product.stock_on_hand
                ) {

                    showMessage(
                        `${product.name} has only ` +
                        `${product.stock_on_hand} unit(s) in stock.`,
                        'error'
                    );

                    return false;
                }
            }


            const total =
                getGrandTotal();


            const given =
                Number(
                    amountGiven.value
                ) || 0;


            if (given < total) {

                showMessage(
                    'Amount given is insufficient.',
                    'error'
                );

                return false;
            }


            return true;
        }


        /* =========================
           GENERATE BILL
        ========================== */

        async function generateBill() {

            hideMessage();


            if (!validateOrder()) {
                return;
            }


            const items =
                getOrderItems();


            generateButton.disabled =
                true;

            generateButton.textContent =
                'Generating bill...';


            document.body.classList.add(
                'loading'
            );


            const payload = {

                customer: {

                    name: customerName.value.trim(),

                    email: customerEmail.value.trim(),
                },

                items: items.map(item => ({

                    product_id: Number(
                        item.productId
                    ),

                    quantity: item.quantity,
                })),
            };


            try {

                const response =
                    await fetch(
                        '{{ url('/api/orders') }}', {
                            method: 'POST',

                            headers: {

                                'Content-Type': 'application/json',

                                'Accept': 'application/json',
                            },

                            body: JSON.stringify(
                                payload
                            ),
                        }
                    );


                const result =
                    await response.json();


                if (!response.ok) {

                    let errorMessage =
                        result.message ||
                        'Unable to create order.';


                    if (result.errors) {

                        const firstError =
                            Object.values(
                                result.errors
                            )[0];


                        if (
                            Array.isArray(
                                firstError
                            ) &&
                            firstError.length
                        ) {

                            errorMessage =
                                firstError[0];
                        }
                    }


                    throw new Error(
                        errorMessage
                    );
                }


                displayBill(
                    result.data
                );


                showMessage(
                    'Order created successfully. Confirmation has been queued.',
                    'success'
                );


                refreshLowStock();

            } catch (error) {

                showMessage(
                    error.message ||
                    'Something went wrong while creating the order.',
                    'error'
                );

            } finally {

                generateButton.disabled =
                    false;

                generateButton.textContent =
                    'Generate bill';

                document.body.classList.remove(
                    'loading'
                );
            }
        }


        /* =========================
           DISPLAY BILL
        ========================== */

        function displayBill(order) {

            billCard.style.display =
                'block';


            billId.textContent =
                `Order #${order.id}`;


            billCustomer.innerHTML = `
                <strong>
                    ${escapeHtml(
                        order.customer.name
                    )}
                </strong>

                <br>

                ${escapeHtml(
                    order.customer.email
                )}
            `;


            billItems.innerHTML =
                '';


            order.order_items.forEach(
                item => {

                    const row =
                        document.createElement(
                            'tr'
                        );


                    row.innerHTML = `
                        <td>
                            ${escapeHtml(
                                item.product.name
                            )}
                        </td>

                        <td>
                            ${item.quantity}
                        </td>

                        <td>
                            ${formatCurrency(
                                item.unit_price
                            )}
                        </td>

                        <td>
                            ${formatCurrency(
                                item.tax_amount
                            )}
                        </td>

                        <td>
                            ${formatCurrency(
                                item.total
                            )}
                        </td>
                    `;


                    billItems.appendChild(
                        row
                    );
                }
            );


            document.getElementById(
                    'bill-subtotal'
                ).textContent =
                formatCurrency(
                    order.subtotal
                );


            document.getElementById(
                    'bill-tax'
                ).textContent =
                formatCurrency(
                    order.tax
                );


            document.getElementById(
                    'bill-grand-total'
                ).textContent =
                formatCurrency(
                    order.grand_total
                );


            billCard.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }


        /* =========================
           LOW STOCK
        ========================== */

        async function refreshLowStock() {

            try {

                const response =
                    await fetch(
                        '{{ url('/api/products/low-stock') }}', {
                            headers: {
                                'Accept': 'application/json'
                            }
                        }
                    );


                if (!response.ok) {
                    return;
                }


                const result =
                    await response.json();


                const list =
                    document.getElementById(
                        'low-stock-list'
                    );


                if (!result.data.length) {

                    list.innerHTML = `
                        <li class="py-2">
                            <span>
                                No low-stock products.
                            </span>
                        </li>
                    `;

                    return;
                }


                list.innerHTML =
                    result.data
                    .map(product => `
                            <li class="py-2 d-flex justify-content-between align-items-start gap-2">

                                <span class="text-break">
                                    ${escapeHtml(
                                        product.name
                                    )}
                                </span>

                                <span class="stock-count">
                                    ${product.stock_on_hand}
                                    units left
                                </span>

                            </li>
                        `)
                    .join('');


            } catch (error) {

                console.error(
                    'Low-stock refresh failed:',
                    error
                );
            }
        }


        /* =========================
           INITIALIZE
        ========================== */

        addProductRow();

        calculateTotals();
    </script>

</body>

</html>
