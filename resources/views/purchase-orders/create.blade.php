@extends('layouts.app')

@section('content')
<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding: 20px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .page-title {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--text);
    }

    .form-card {
        background: white;
        border-radius: 12px;
        padding: 30px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .form-section-title {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #f5f5f7;
        color: var(--text);
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 25px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group label {
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 8px;
        color: #1d1d1f;
    }

    .form-control, .form-select {
        padding: 12px 15px;
        border: 1px solid #d2d2d7;
        border-radius: 8px;
        font-size: 0.95rem;
        transition: all 0.2s;
        font-family: 'Cairo', sans-serif;
        background: white;
    }

    .form-control:focus, .form-select:focus {
        outline: none;
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(0, 113, 227, 0.1);
    }

    .btn-primary {
        background: var(--accent);
        color: white;
        padding: 12px 24px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
        font-size: 0.95rem;
    }

    .btn-primary:hover {
        background: #0077ED;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 113, 227, 0.3);
    }

    .btn-secondary {
        background: #f5f5f7;
        color: var(--text);
        padding: 12px 24px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
        font-size: 0.95rem;
    }

    .btn-secondary:hover {
        background: #e8e8ed;
    }

    .btn-add {
        background: #34c759;
        color: white;
    }

    .btn-add:hover {
        background: #30b350;
    }

    .btn-remove {
        background: #ff3b30;
        color: white;
        padding: 8px 12px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .btn-remove:hover {
        background: #ff2d20;
    }

    .table-responsive {
        overflow-x: auto;
        margin-bottom: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    thead {
        background: #f5f5f7;
    }

    th {
        padding: 12px;
        text-align: right;
        font-weight: 600;
        font-size: 0.85rem;
        color: #1d1d1f;
        border-bottom: 2px solid #d2d2d7;
    }

    td {
        padding: 10px;
        border-bottom: 1px solid #f5f5f7;
    }

    .item-input {
        padding: 8px 10px;
        border: 1px solid #d2d2d7;
        border-radius: 6px;
        font-size: 0.9rem;
        width: 100%;
        font-family: 'Cairo', sans-serif;
    }

    .item-input:focus {
        outline: none;
        border-color: var(--accent);
    }

    .totals-section {
        background: #f5f5f7;
        padding: 20px;
        border-radius: 8px;
        margin-top: 20px;
    }

    .totals-row {
        display: flex;
        justify-content: space-between;
        padding: 10px 0;
        font-size: 0.95rem;
    }

    .totals-row.total {
        border-top: 2px solid #d2d2d7;
        margin-top: 10px;
        padding-top: 15px;
        font-size: 1.2rem;
        font-weight: 700;
        color: var(--accent);
    }

    .form-actions {
        display: flex;
        gap: 15px;
        justify-content: flex-end;
        margin-top: 30px;
    }

    .alert {
        padding: 15px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-weight: 500;
    }

    .alert-error {
        background: #ffebee;
        color: #d32f2f;
        border: 1px solid #ffcdd2;
    }
</style>

<div class="page-header">
    <h1 class="page-title">إنشاء أمر شراء جديد</h1>
</div>

@if($errors->any())
    <div class="alert alert-error">
        <ul style="margin: 0; padding-right: 20px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('purchase-orders.store') }}" id="purchaseOrderForm">
    @csrf

    <div class="form-card">
        <h3 class="form-section-title">معلومات الأمر</h3>
        <div class="form-grid">
            <div class="form-group">
                <label>المورد <span style="color: #ff3b30;">*</span></label>
                <select name="supplier_id" class="form-select" required>
                    <option value="">اختر المورد</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>المستودع <span style="color: #ff3b30;">*</span></label>
                <select name="warehouse_id" class="form-select" required>
                    <option value="">اختر المستودع</option>
                    @foreach($warehouses as $warehouse)
                        <option value="{{ $warehouse->id }}" {{ old('warehouse_id') == $warehouse->id ? 'selected' : '' }}>
                            {{ $warehouse->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>تاريخ الأمر <span style="color: #ff3b30;">*</span></label>
                <input type="date" name="order_date" class="form-control" value="{{ old('order_date', date('Y-m-d')) }}" required>
            </div>

            <div class="form-group">
                <label>التاريخ المتوقع للاستلام</label>
                <input type="date" name="expected_date" class="form-control" value="{{ old('expected_date') }}">
            </div>

            <div class="form-group">
                <label>شروط الدفع <span style="color: #ff3b30;">*</span></label>
                <select name="payment_term_id" class="form-select" required>
                    <option value="">اختر شروط الدفع</option>
                    @foreach($paymentTerms as $term)
                        <option value="{{ $term->id }}" {{ old('payment_term_id') == $term->id ? 'selected' : '' }}>
                            {{ $term->name }} ({{ $term->days }} يوم)
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>خصم على الإجمالي</label>
                <input type="number" step="0.01" name="discount" class="form-control" value="{{ old('discount', 0) }}" id="orderDiscount">
            </div>
        </div>

        <div class="form-group">
            <label>ملاحظات</label>
            <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
        </div>
    </div>

    <div class="form-card">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3 class="form-section-title" style="margin: 0; border: none; padding: 0;">المنتجات</h3>
            <button type="button" class="btn-primary btn-add" onclick="addItem()">
                <i data-lucide="plus"></i>
                إضافة منتج
            </button>
        </div>

        <div class="table-responsive">
            <table id="itemsTable">
                <thead>
                    <tr>
                        <th>المنتج</th>
                        <th style="width: 100px;">الكمية</th>
                        <th style="width: 120px;">سعر الوحدة</th>
                        <th style="width: 100px;">الضريبة %</th>
                        <th style="width: 120px;">الخصم</th>
                        <th style="width: 120px;">الإجمالي</th>
                        <th style="width: 80px;">إجراءات</th>
                    </tr>
                </thead>
                <tbody id="itemsTableBody">
                    <!-- Items will be added here -->
                </tbody>
            </table>
        </div>

        <div class="totals-section">
            <div class="totals-row">
                <span>المجموع الفرعي:</span>
                <strong><span id="subtotalDisplay">0.00</span> ريال</strong>
            </div>
            <div class="totals-row">
                <span>الضريبة:</span>
                <strong><span id="taxDisplay">0.00</span> ريال</strong>
            </div>
            <div class="totals-row">
                <span>الخصم:</span>
                <strong><span id="discountDisplay">0.00</span> ريال</strong>
            </div>
            <div class="totals-row total">
                <span>الإجمالي النهائي:</span>
                <strong><span id="totalDisplay">0.00</span> ريال</strong>
            </div>
        </div>
    </div>

    <div class="form-actions">
        <a href="{{ route('purchase-orders.index') }}" class="btn-secondary">
            <i data-lucide="x"></i>
            إلغاء
        </a>
        <button type="submit" class="btn-primary">
            <i data-lucide="save"></i>
            حفظ الأمر
        </button>
    </div>
</form>

@push('scripts')
<script>
    lucide.createIcons();

    let itemCount = 0;
    const products = @json($products);

    function addItem() {
        const tbody = document.getElementById('itemsTableBody');
        const row = document.createElement('tr');
        row.id = `item-${itemCount}`;
        
        row.innerHTML = `
            <td>
                <select name="items[${itemCount}][product_id]" class="item-input" required onchange="updateProductPrice(${itemCount})">
                    <option value="">اختر المنتج</option>
                    ${products.map(p => `<option value="${p.id}" data-price="${p.cost_price}" data-tax="${p.tax_rate}">${p.name} (${p.sku})</option>`).join('')}
                </select>
            </td>
            <td>
                <input type="number" step="0.001" name="items[${itemCount}][quantity]" class="item-input" value="1" min="0.001" required onchange="calculateItem(${itemCount})">
            </td>
            <td>
                <input type="number" step="0.01" name="items[${itemCount}][unit_price]" class="item-input" value="0" min="0" required onchange="calculateItem(${itemCount})">
            </td>
            <td>
                <input type="number" step="0.01" name="items[${itemCount}][tax_rate]" class="item-input" value="15" min="0" max="100" onchange="calculateItem(${itemCount})">
            </td>
            <td>
                <input type="number" step="0.01" name="items[${itemCount}][discount]" class="item-input" value="0" min="0" onchange="calculateItem(${itemCount})">
            </td>
            <td>
                <strong><span id="item-total-${itemCount}">0.00</span></strong>
            </td>
            <td>
                <button type="button" class="btn-remove" onclick="removeItem(${itemCount})">
                    <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                </button>
            </td>
        `;
        
        tbody.appendChild(row);
        lucide.createIcons();
        itemCount++;
    }

    function removeItem(index) {
        const row = document.getElementById(`item-${index}`);
        if (row) {
            row.remove();
            calculateTotals();
        }
    }

    function updateProductPrice(index) {
        const select = document.querySelector(`select[name="items[${index}][product_id]"]`);
        const selectedOption = select.options[select.selectedIndex];
        
        if (selectedOption.value) {
            const price = selectedOption.getAttribute('data-price');
            const tax = selectedOption.getAttribute('data-tax');
            
            document.querySelector(`input[name="items[${index}][unit_price]"]`).value = price;
            document.querySelector(`input[name="items[${index}][tax_rate]"]`).value = tax;
            
            calculateItem(index);
        }
    }

    function calculateItem(index) {
        const quantity = parseFloat(document.querySelector(`input[name="items[${index}][quantity]"]`).value) || 0;
        const unitPrice = parseFloat(document.querySelector(`input[name="items[${index}][unit_price]"]`).value) || 0;
        const taxRate = parseFloat(document.querySelector(`input[name="items[${index}][tax_rate]"]`).value) || 0;
        const discount = parseFloat(document.querySelector(`input[name="items[${index}][discount]"]`).value) || 0;
        
        const subtotal = (quantity * unitPrice) - discount;
        const tax = subtotal * (taxRate / 100);
        const total = subtotal + tax;
        
        document.getElementById(`item-total-${index}`).textContent = total.toFixed(2);
        
        calculateTotals();
    }

    function calculateTotals() {
        let subtotal = 0;
        let taxAmount = 0;
        
        const rows = document.querySelectorAll('#itemsTableBody tr');
        rows.forEach((row, index) => {
            const quantity = parseFloat(row.querySelector('input[name*="[quantity]"]')?.value) || 0;
            const unitPrice = parseFloat(row.querySelector('input[name*="[unit_price]"]')?.value) || 0;
            const taxRate = parseFloat(row.querySelector('input[name*="[tax_rate]"]')?.value) || 0;
            const discount = parseFloat(row.querySelector('input[name*="[discount]"]')?.value) || 0;
            
            const itemSubtotal = (quantity * unitPrice) - discount;
            const itemTax = itemSubtotal * (taxRate / 100);
            
            subtotal += itemSubtotal;
            taxAmount += itemTax;
        });
        
        const orderDiscount = parseFloat(document.getElementById('orderDiscount').value) || 0;
        const total = subtotal + taxAmount - orderDiscount;
        
        document.getElementById('subtotalDisplay').textContent = subtotal.toFixed(2);
        document.getElementById('taxDisplay').textContent = taxAmount.toFixed(2);
        document.getElementById('discountDisplay').textContent = orderDiscount.toFixed(2);
        document.getElementById('totalDisplay').textContent = total.toFixed(2);
    }

    // Add first item on load
    addItem();

    // Update totals when order discount changes
    document.getElementById('orderDiscount').addEventListener('input', calculateTotals);

    // Validate form before submit
    document.getElementById('purchaseOrderForm').addEventListener('submit', function(e) {
        const rows = document.querySelectorAll('#itemsTableBody tr');
        if (rows.length === 0) {
            e.preventDefault();
            alert('يجب إضافة منتج واحد على الأقل');
            return false;
        }
    });
</script>
@endpush
@endsection
