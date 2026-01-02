@extends('layouts.app')

@section('content')
<style>
    .content-wrapper {
        max-width: 1200px;
        margin: 0 auto;
        padding: 40px 20px;
    }

    .page-header {
        margin-bottom: 30px;
    }

    .page-header h1 {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text);
        margin-bottom: 8px;
    }

    .form-card {
        background: white;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        margin-bottom: 20px;
    }

    .form-section {
        margin-bottom: 30px;
    }

    .form-section h3 {
        font-size: 1.2rem;
        font-weight: 600;
        margin-bottom: 20px;
        color: var(--text);
        padding-bottom: 10px;
        border-bottom: 2px solid #f0f0f0;
    }

    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 20px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: var(--text);
        font-size: 0.9rem;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-family: 'Cairo', sans-serif;
        font-size: 0.95rem;
        transition: border-color 0.2s;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        outline: none;
        border-color: var(--accent);
    }

    .form-group textarea {
        resize: vertical;
        min-height: 100px;
    }

    .products-section {
        margin-top: 30px;
    }

    .product-item {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 15px;
        position: relative;
    }

    .product-item-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
    }

    .product-item-header h4 {
        font-size: 1rem;
        font-weight: 600;
        color: var(--text);
    }

    .btn-remove {
        background: #dc3545;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 0.85rem;
        font-weight: 500;
        transition: all 0.2s;
    }

    .btn-remove:hover {
        background: #c82333;
    }

    .btn-add-item {
        background: #28a745;
        color: white;
        padding: 10px 20px;
        border-radius: 8px;
        border: none;
        cursor: pointer;
        font-size: 0.9rem;
        font-weight: 600;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-family: 'Cairo', sans-serif;
    }

    .btn-add-item:hover {
        background: #218838;
    }

    .form-actions {
        display: flex;
        gap: 15px;
        justify-content: flex-end;
        margin-top: 30px;
    }

    .btn-primary {
        background: var(--accent);
        color: white;
        padding: 12px 30px;
        border-radius: 8px;
        border: none;
        font-weight: 600;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.2s;
        font-family: 'Cairo', sans-serif;
    }

    .btn-primary:hover {
        background: #0056b3;
    }

    .btn-secondary {
        background: white;
        color: #666;
        padding: 12px 30px;
        border-radius: 8px;
        border: 1px solid #ddd;
        font-weight: 600;
        font-size: 0.95rem;
        text-decoration: none;
        display: inline-block;
        transition: all 0.2s;
    }

    .btn-secondary:hover {
        background: #f8f9fa;
    }

    .alert {
        padding: 12px 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    .alert-danger {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
</style>

<div class="content-wrapper">
    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>خطأ!</strong>
            <ul style="margin: 10px 0 0 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="page-header">
        <h1>{{ isset($salesQuotation) ? 'تعديل عرض سعر' : 'إضافة عرض سعر جديد' }}</h1>
    </div>

    <form method="POST" action="{{ isset($salesQuotation) ? route('sales-quotations.update', $salesQuotation) : route('sales-quotations.store') }}" id="quotationForm">
        @csrf
        @if(isset($salesQuotation))
            @method('PUT')
        @endif

        <div class="form-card">
            <div class="form-section">
                <h3>المعلومات الأساسية</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>رقم العرض</label>
                        <input type="text" value="{{ $salesQuotation->quotation_number ?? $quotationNumber }}" disabled style="background: #f8f9fa;">
                    </div>

                    <div class="form-group">
                        <label>العميل *</label>
                        <select name="customer_id" required>
                            <option value="">اختر العميل</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ (old('customer_id', isset($salesQuotation) ? $salesQuotation->customer_id : '') == $customer->id) ? 'selected' : '' }}>
                                    {{ $customer->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>تاريخ العرض *</label>
                        <input type="date" name="quotation_date" value="{{ old('quotation_date', isset($salesQuotation) ? $salesQuotation->quotation_date->format('Y-m-d') : date('Y-m-d')) }}" required>
                    </div>

                    <div class="form-group">
                        <label>صالح حتى *</label>
                        <input type="date" name="valid_until" value="{{ old('valid_until', isset($salesQuotation) ? $salesQuotation->valid_until->format('Y-m-d') : date('Y-m-d', strtotime('+30 days'))) }}" required>
                    </div>

                    <div class="form-group">
                        <label>الحالة *</label>
                        <select name="status" required>
                            <option value="draft" {{ old('status', isset($salesQuotation) ? $salesQuotation->status : 'draft') == 'draft' ? 'selected' : '' }}>مسودة</option>
                            <option value="sent" {{ old('status', isset($salesQuotation) ? $salesQuotation->status : '') == 'sent' ? 'selected' : '' }}>مرسل</option>
                            <option value="accepted" {{ old('status', isset($salesQuotation) ? $salesQuotation->status : '') == 'accepted' ? 'selected' : '' }}>مقبول</option>
                            <option value="rejected" {{ old('status', isset($salesQuotation) ? $salesQuotation->status : '') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                            <option value="expired" {{ old('status', isset($salesQuotation) ? $salesQuotation->status : '') == 'expired' ? 'selected' : '' }}>منتهي</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>الشروط والأحكام</label>
                        <textarea name="terms_conditions">{{ old('terms_conditions', isset($salesQuotation) ? $salesQuotation->terms_conditions : '') }}</textarea>
                    </div>

                    <div class="form-group">
                        <label>ملاحظات</label>
                        <textarea name="notes">{{ old('notes', isset($salesQuotation) ? $salesQuotation->notes : '') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-card">
            <div class="form-section products-section">
                <h3>المنتجات</h3>
                
                <div id="productsContainer">
                    @if(isset($salesQuotation) && $salesQuotation->items->count() > 0)
                        @foreach($salesQuotation->items as $index => $item)
                            <div class="product-item">
                                <div class="product-item-header">
                                    <h4>منتج {{ $index + 1 }}</h4>
                                    <button type="button" class="btn-remove" onclick="removeProduct(this)">حذف</button>
                                </div>
                                
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>المنتج *</label>
                                        <select name="items[{{ $index }}][product_id]" class="product-select" required onchange="updateProductPrice(this)">
                                            <option value="">اختر المنتج</option>
                                            @foreach($products as $product)
                                                <option value="{{ $product->id }}" data-price="{{ $product->price }}" {{ $item->product_id == $product->id ? 'selected' : '' }}>
                                                    {{ $product->name }} - {{ number_format($product->price, 2) }} ر.س
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>الكمية *</label>
                                        <input type="number" name="items[{{ $index }}][quantity]" step="0.001" min="0.001" value="{{ $item->quantity }}" required onchange="calculateItemTotal(this)">
                                    </div>

                                    <div class="form-group">
                                        <label>سعر الوحدة *</label>
                                        <input type="number" name="items[{{ $index }}][unit_price]" step="0.01" min="0" value="{{ $item->unit_price }}" required onchange="calculateItemTotal(this)">
                                    </div>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label>نسبة الضريبة (%) *</label>
                                        <input type="number" name="items[{{ $index }}][tax_rate]" step="0.01" min="0" max="100" value="{{ $item->tax_rate }}" required onchange="calculateItemTotal(this)">
                                    </div>

                                    <div class="form-group">
                                        <label>الخصم</label>
                                        <input type="number" name="items[{{ $index }}][discount]" step="0.01" min="0" value="{{ $item->discount }}" onchange="calculateItemTotal(this)">
                                    </div>

                                    <div class="form-group">
                                        <label>الإجمالي</label>
                                        <input type="text" class="item-total" value="{{ number_format($item->total, 2) }}" disabled style="background: #f8f9fa; font-weight: 600;">
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="product-item">
                            <div class="product-item-header">
                                <h4>منتج 1</h4>
                                <button type="button" class="btn-remove" onclick="removeProduct(this)">حذف</button>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label>المنتج *</label>
                                    <select name="items[0][product_id]" class="product-select" required onchange="updateProductPrice(this)">
                                        <option value="">اختر المنتج</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                                                {{ $product->name }} - {{ number_format($product->price, 2) }} ر.س
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label>الكمية *</label>
                                    <input type="number" name="items[0][quantity]" step="0.001" min="0.001" value="1" required onchange="calculateItemTotal(this)">
                                </div>

                                <div class="form-group">
                                    <label>سعر الوحدة *</label>
                                    <input type="number" name="items[0][unit_price]" step="0.01" min="0" value="0" required onchange="calculateItemTotal(this)">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-group">
                                    <label>نسبة الضريبة (%) *</label>
                                    <input type="number" name="items[0][tax_rate]" step="0.01" min="0" max="100" value="15" required onchange="calculateItemTotal(this)">
                                </div>

                                <div class="form-group">
                                    <label>الخصم</label>
                                    <input type="number" name="items[0][discount]" step="0.01" min="0" value="0" onchange="calculateItemTotal(this)">
                                </div>

                                <div class="form-group">
                                    <label>الإجمالي</label>
                                    <input type="text" class="item-total" value="0.00" disabled style="background: #f8f9fa; font-weight: 600;">
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <button type="button" class="btn-add-item" onclick="addProduct()">
                    <i data-lucide="plus" style="width: 18px; height: 18px;"></i>
                    إضافة منتج
                </button>
            </div>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-primary">حفظ</button>
            <a href="{{ route('sales-quotations.index') }}" class="btn-secondary">إلغاء</a>
        </div>
    </form>
</div>

@push('scripts')
<script>
    lucide.createIcons();

    let productIndex = {{ isset($salesQuotation) ? $salesQuotation->items->count() : 1 }};

    function addProduct() {
        const container = document.getElementById('productsContainer');
        const productHtml = `
            <div class="product-item">
                <div class="product-item-header">
                    <h4>منتج ${productIndex + 1}</h4>
                    <button type="button" class="btn-remove" onclick="removeProduct(this)">حذف</button>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>المنتج *</label>
                        <select name="items[${productIndex}][product_id]" class="product-select" required onchange="updateProductPrice(this)">
                            <option value="">اختر المنتج</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" data-price="{{ $product->price }}">
                                    {{ $product->name }} - {{ number_format($product->price, 2) }} ر.س
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label>الكمية *</label>
                        <input type="number" name="items[${productIndex}][quantity]" step="0.001" min="0.001" value="1" required onchange="calculateItemTotal(this)">
                    </div>

                    <div class="form-group">
                        <label>سعر الوحدة *</label>
                        <input type="number" name="items[${productIndex}][unit_price]" step="0.01" min="0" value="0" required onchange="calculateItemTotal(this)">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>نسبة الضريبة (%) *</label>
                        <input type="number" name="items[${productIndex}][tax_rate]" step="0.01" min="0" max="100" value="15" required onchange="calculateItemTotal(this)">
                    </div>

                    <div class="form-group">
                        <label>الخصم</label>
                        <input type="number" name="items[${productIndex}][discount]" step="0.01" min="0" value="0" onchange="calculateItemTotal(this)">
                    </div>

                    <div class="form-group">
                        <label>الإجمالي</label>
                        <input type="text" class="item-total" value="0.00" disabled style="background: #f8f9fa; font-weight: 600;">
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', productHtml);
        productIndex++;
        lucide.createIcons();
    }

    function removeProduct(button) {
        const items = document.querySelectorAll('.product-item');
        if (items.length > 1) {
            button.closest('.product-item').remove();
        } else {
            alert('يجب أن يحتوي العرض على منتج واحد على الأقل');
        }
    }

    function updateProductPrice(select) {
        const option = select.options[select.selectedIndex];
        const price = option.getAttribute('data-price');
        const productItem = select.closest('.product-item');
        const priceInput = productItem.querySelector('input[name*="[unit_price]"]');
        
        if (price && priceInput) {
            priceInput.value = price;
            calculateItemTotal(priceInput);
        }
    }

    function calculateItemTotal(input) {
        const productItem = input.closest('.product-item');
        const quantity = parseFloat(productItem.querySelector('input[name*="[quantity]"]').value) || 0;
        const unitPrice = parseFloat(productItem.querySelector('input[name*="[unit_price]"]').value) || 0;
        const taxRate = parseFloat(productItem.querySelector('input[name*="[tax_rate]"]').value) || 0;
        const discount = parseFloat(productItem.querySelector('input[name*="[discount]"]').value) || 0;

        const subtotal = quantity * unitPrice;
        const afterDiscount = subtotal - discount;
        const tax = afterDiscount * (taxRate / 100);
        const total = afterDiscount + tax;

        productItem.querySelector('.item-total').value = total.toFixed(2);
    }
</script>
@endpush
@endsection
