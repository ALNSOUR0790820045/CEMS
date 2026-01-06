@extends('layouts.app')

@section('content')
<style>
    .page-header {
        background: white;
        border-radius: 12px;
        padding: 20px 25px;
        margin-bottom: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .header-info h1 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1d1d1f;
        margin: 0 0 5px 0;
    }
    
    .toolbar {
        background: white;
        border-radius: 12px;
        padding: 15px 20px;
        margin-bottom: 15px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        display: flex;
        gap: 10px;
        align-items: center;
    }
    
    .btn {
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        border: none;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f5f5f7;
        color: #1d1d1f;
    }
    
    .btn:hover {
        background: #e8e8ed;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #0071e3, #0077ed);
        color: white;
    }
    
    .btn-success {
        background: #34c759;
        color: white;
    }
    
    .spreadsheet-container {
        background: white;
        border-radius: 12px;
        padding: 0;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        overflow: hidden;
    }
    
    .spreadsheet-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.85rem;
    }
    
    .spreadsheet-table thead {
        background: #f9fafb;
        position: sticky;
        top: 0;
        z-index: 10;
    }
    
    .spreadsheet-table th {
        padding: 10px 8px;
        text-align: right;
        font-size: 0.8rem;
        font-weight: 600;
        color: #6b7280;
        border: 1px solid #e5e7eb;
    }
    
    .spreadsheet-table td {
        padding: 0;
        border: 1px solid #e5e7eb;
    }
    
    .spreadsheet-table input,
    .spreadsheet-table select,
    .spreadsheet-table textarea {
        width: 100%;
        padding: 8px;
        border: none;
        font-size: 0.85rem;
        font-family: 'Cairo', sans-serif;
        background: transparent;
    }
    
    .spreadsheet-table input:focus,
    .spreadsheet-table select:focus,
    .spreadsheet-table textarea:focus {
        outline: 2px solid #0071e3;
        background: #f0f9ff;
    }
    
    .spreadsheet-table textarea {
        resize: vertical;
        min-height: 40px;
    }
    
    .section-row {
        background: #f3f4f6;
        font-weight: 700;
    }
    
    .section-row td {
        padding: 12px 10px;
    }
    
    .btn-icon {
        width: 28px;
        height: 28px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        cursor: pointer;
        background: #f5f5f7;
        border: none;
        transition: all 0.2s;
    }
    
    .btn-icon:hover {
        background: #ff3b30;
        color: white;
    }
    
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 1000;
        align-items: center;
        justify-content: center;
    }
    
    .modal.active {
        display: flex;
    }
    
    .modal-content {
        background: white;
        border-radius: 12px;
        padding: 25px;
        max-width: 500px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
    }
    
    .modal-header {
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 20px;
    }
    
    .form-group {
        margin-bottom: 15px;
    }
    
    .form-label {
        display: block;
        font-size: 0.85rem;
        font-weight: 600;
        margin-bottom: 5px;
    }
    
    .form-control {
        width: 100%;
        padding: 10px;
        border: 1px solid #d2d2d7;
        border-radius: 6px;
        font-size: 0.9rem;
        font-family: 'Cairo', sans-serif;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #0071e3;
        box-shadow: 0 0 0 3px rgba(0, 113, 227, 0.1);
    }
    
    .modal-actions {
        display: flex;
        gap: 10px;
        margin-top: 20px;
    }
</style>

<div class="page-header">
    <div class="header-info">
        <h1>{{ $boq->name }}</h1>
        <span style="font-size: 0.85rem; color: #86868b;">{{ $boq->boq_number }}</span>
    </div>
    <a href="{{ route('boq.show', $boq) }}" class="btn">
        <i data-lucide="eye" style="width: 16px; height: 16px;"></i>
        معاينة
    </a>
</div>

<div class="toolbar">
    <button class="btn btn-primary" onclick="openSectionModal()">
        <i data-lucide="plus" style="width: 14px; height: 14px;"></i>
        إضافة قسم
    </button>
    <button class="btn btn-primary" onclick="addItemRow()">
        <i data-lucide="file-plus" style="width: 14px; height: 14px;"></i>
        إضافة بند
    </button>
    <button class="btn btn-success" onclick="saveAll()">
        <i data-lucide="save" style="width: 14px; height: 14px;"></i>
        حفظ الكل
    </button>
    <button class="btn" onclick="calculateTotals()">
        <i data-lucide="calculator" style="width: 14px; height: 14px;"></i>
        إعادة الحساب
    </button>
</div>

<div class="spreadsheet-container">
    <table class="spreadsheet-table" id="boqTable">
        <thead>
            <tr>
                <th style="width: 80px;">رقم البند</th>
                <th style="width: 50px;">الكود</th>
                <th style="width: 300px;">الوصف</th>
                <th style="width: 80px;">الوحدة</th>
                <th style="width: 100px;">الكمية</th>
                <th style="width: 120px;">سعر الوحدة</th>
                <th style="width: 120px;">المبلغ</th>
                <th style="width: 60px;">حذف</th>
            </tr>
        </thead>
        <tbody id="boqBody">
            @foreach($boq->sections as $section)
            <tr class="section-row" data-section-id="{{ $section->id }}">
                <td colspan="6">
                    <strong>{{ $section->code }} - {{ $section->name }}</strong>
                </td>
                <td colspan="2" style="text-align: left; font-weight: 700; color: #0071e3;">
                    {{ number_format($section->total_amount, 2) }}
                </td>
            </tr>
            @foreach($section->items as $item)
            <tr data-item-id="{{ $item->id }}" data-section-id="{{ $section->id }}">
                <td><input type="text" value="{{ $item->item_number }}" data-field="item_number"></td>
                <td><input type="text" value="{{ $item->code }}" data-field="code"></td>
                <td><textarea data-field="description" rows="2">{{ $item->description }}</textarea></td>
                <td>
                    <select data-field="unit">
                        @foreach($units as $unit)
                        <option value="{{ $unit->code }}" {{ $item->unit === $unit->code ? 'selected' : '' }}>
                            {{ $unit->name }}
                        </option>
                        @endforeach
                    </select>
                </td>
                <td><input type="number" step="0.01" value="{{ $item->quantity }}" data-field="quantity" onchange="calculateRow(this)"></td>
                <td><input type="number" step="0.01" value="{{ $item->unit_rate }}" data-field="unit_rate" onchange="calculateRow(this)"></td>
                <td><input type="number" step="0.01" value="{{ $item->amount }}" data-field="amount" readonly style="background: #f9fafb; font-weight: 600;"></td>
                <td>
                    <button class="btn-icon" onclick="deleteItem(this, {{ $item->id }})">
                        <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                    </button>
                </td>
            </tr>
            @endforeach
            @endforeach
        </tbody>
    </table>
</div>

<!-- Add Section Modal -->
<div id="sectionModal" class="modal">
    <div class="modal-content">
        <h3 class="modal-header">إضافة قسم جديد</h3>
        <div class="form-group">
            <label class="form-label">الكود</label>
            <input type="text" id="sectionCode" class="form-control" placeholder="مثال: A, B, 1, 2">
        </div>
        <div class="form-group">
            <label class="form-label">الاسم</label>
            <input type="text" id="sectionName" class="form-control" placeholder="اسم القسم">
        </div>
        <div class="form-group">
            <label class="form-label">الاسم بالإنجليزية</label>
            <input type="text" id="sectionNameEn" class="form-control" placeholder="Section Name">
        </div>
        <div class="modal-actions">
            <button class="btn btn-primary" onclick="saveSection()">حفظ</button>
            <button class="btn" onclick="closeSectionModal()">إلغاء</button>
        </div>
    </div>
</div>

<script>
    lucide.createIcons();
    
    let currentSectionId = null;
    
    function openSectionModal() {
        document.getElementById('sectionModal').classList.add('active');
    }
    
    function closeSectionModal() {
        document.getElementById('sectionModal').classList.remove('active');
        document.getElementById('sectionCode').value = '';
        document.getElementById('sectionName').value = '';
        document.getElementById('sectionNameEn').value = '';
    }
    
    function saveSection() {
        const code = document.getElementById('sectionCode').value;
        const name = document.getElementById('sectionName').value;
        const nameEn = document.getElementById('sectionNameEn').value;
        
        if (!code || !name) {
            alert('الرجاء إدخال الكود والاسم');
            return;
        }
        
        fetch('{{ route('boq.sections.store', $boq) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ code, name, name_en: nameEn })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        });
    }
    
    function addItemRow() {
        const sections = document.querySelectorAll('.section-row');
        if (sections.length === 0) {
            alert('الرجاء إضافة قسم أولاً');
            return;
        }
        
        const lastSection = sections[sections.length - 1];
        const sectionId = lastSection.dataset.sectionId;
        
        const newRow = document.createElement('tr');
        newRow.dataset.sectionId = sectionId;
        newRow.dataset.isNew = 'true';
        newRow.innerHTML = `
            <td><input type="text" data-field="item_number"></td>
            <td><input type="text" data-field="code"></td>
            <td><textarea data-field="description" rows="2"></textarea></td>
            <td>
                <select data-field="unit">
                    @foreach($units as $unit)
                    <option value="{{ $unit->code }}">{{ $unit->name }}</option>
                    @endforeach
                </select>
            </td>
            <td><input type="number" step="0.01" data-field="quantity" value="0" onchange="calculateRow(this)"></td>
            <td><input type="number" step="0.01" data-field="unit_rate" value="0" onchange="calculateRow(this)"></td>
            <td><input type="number" step="0.01" data-field="amount" value="0" readonly style="background: #f9fafb; font-weight: 600;"></td>
            <td>
                <button class="btn-icon" onclick="this.closest('tr').remove()">
                    <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                </button>
            </td>
        `;
        
        lastSection.after(newRow);
        lucide.createIcons();
    }
    
    function calculateRow(input) {
        const row = input.closest('tr');
        const quantity = parseFloat(row.querySelector('[data-field="quantity"]').value) || 0;
        const unitRate = parseFloat(row.querySelector('[data-field="unit_rate"]').value) || 0;
        const amount = quantity * unitRate;
        row.querySelector('[data-field="amount"]').value = amount.toFixed(2);
    }
    
    function deleteItem(btn, itemId) {
        if (!confirm('هل أنت متأكد من حذف هذا البند؟')) return;
        
        fetch(`{{ route('boq.items.destroy', ['boq' => $boq, 'item' => '__ITEM__']) }}`.replace('__ITEM__', itemId), {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                btn.closest('tr').remove();
            }
        });
    }
    
    function saveAll() {
        const rows = document.querySelectorAll('#boqBody tr:not(.section-row)');
        const promises = [];
        
        rows.forEach(row => {
            const itemId = row.dataset.itemId;
            const isNew = row.dataset.isNew;
            const sectionId = row.dataset.sectionId;
            
            const data = {
                boq_section_id: sectionId,
                item_number: row.querySelector('[data-field="item_number"]').value,
                code: row.querySelector('[data-field="code"]').value,
                description: row.querySelector('[data-field="description"]').value,
                unit: row.querySelector('[data-field="unit"]').value,
                quantity: parseFloat(row.querySelector('[data-field="quantity"]').value) || 0,
                unit_rate: parseFloat(row.querySelector('[data-field="unit_rate"]').value) || 0,
            };
            
            let url, method;
            if (isNew) {
                url = '{{ route('boq.items.store', $boq) }}';
                method = 'POST';
            } else {
                url = `{{ route('boq.items.update', ['boq' => $boq, 'item' => '__ITEM__']) }}`.replace('__ITEM__', itemId);
                method = 'PUT';
            }
            
            promises.push(
                fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(data)
                })
            );
        });
        
        Promise.all(promises)
            .then(() => {
                alert('تم الحفظ بنجاح');
                location.reload();
            })
            .catch(err => {
                alert('حدث خطأ أثناء الحفظ');
                console.error(err);
            });
    }
    
    function calculateTotals() {
        fetch('{{ route('boq.calculate', $boq) }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert(`الإجمالي: ${data.final_amount}`);
                location.reload();
            }
        });
    }
</script>
@endsection
