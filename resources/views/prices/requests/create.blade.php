@extends('layouts.app')

@section('content')
<div style="padding: 20px; max-width: 1200px; margin: 0 auto;">
    <div style="margin-bottom: 20px;">
        <a href="{{ route('price-requests.index') }}" style="color: var(--accent); text-decoration: none; font-size: 14px;">← العودة للقائمة</a>
        <h1 style="font-size: 28px; font-weight: 600; margin: 10px 0;">إنشاء طلب عرض أسعار</h1>
    </div>

    <div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); padding: 24px;">
        <form action="{{ route('price-requests.store') }}" method="POST">
            @csrf
            
            <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">رقم الطلب *</label>
                    <input type="text" name="request_number" required value="{{ old('request_number', 'RFQ-' . date('Ymd') . '-' . rand(100, 999)) }}"
                           style="width: 100%; padding: 12px; border: 1px solid #dee2e6; border-radius: 8px; font-size: 14px;">
                    @error('request_number')
                        <span style="color: #dc3545; font-size: 13px;">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">المشروع</label>
                    <select name="project_id"
                            style="width: 100%; padding: 12px; border: 1px solid #dee2e6; border-radius: 8px; font-size: 14px;">
                        <option value="">بدون مشروع</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}" {{ old('project_id') == $project->id ? 'selected' : '' }}>
                                {{ $project->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">تاريخ الطلب *</label>
                    <input type="date" name="request_date" required value="{{ old('request_date', date('Y-m-d')) }}"
                           style="width: 100%; padding: 12px; border: 1px solid #dee2e6; border-radius: 8px; font-size: 14px;">
                    @error('request_date')
                        <span style="color: #dc3545; font-size: 13px;">{{ $message }}</span>
                    @enderror
                </div>

                <div>
                    <label style="display: block; font-weight: 600; margin-bottom: 8px;">مطلوب بتاريخ *</label>
                    <input type="date" name="required_by" required value="{{ old('required_by') }}"
                           style="width: 100%; padding: 12px; border: 1px solid #dee2e6; border-radius: 8px; font-size: 14px;">
                    @error('required_by')
                        <span style="color: #dc3545; font-size: 13px;">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-weight: 600; margin-bottom: 8px;">ملاحظات</label>
                <textarea name="notes" rows="3"
                          style="width: 100%; padding: 12px; border: 1px solid #dee2e6; border-radius: 8px; font-size: 14px;">{{ old('notes') }}</textarea>
            </div>

            <div style="border-top: 2px solid #dee2e6; padding-top: 20px; margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <h3 style="font-size: 18px; font-weight: 600; margin: 0;">البنود المطلوبة</h3>
                    <button type="button" onclick="addItem()"
                            style="background: #28a745; color: white; padding: 8px 16px; border: none; border-radius: 6px; font-size: 14px; cursor: pointer;">
                        + إضافة بند
                    </button>
                </div>

                <div id="items-container">
                    <div class="item-row" style="display: grid; grid-template-columns: 2fr 2fr 1fr 1fr auto; gap: 12px; margin-bottom: 12px;">
                        <div>
                            <input type="text" name="items[0][item_description]" placeholder="وصف البند *" required
                                   style="width: 100%; padding: 10px; border: 1px solid #dee2e6; border-radius: 6px; font-size: 14px;">
                        </div>
                        <div>
                            <input type="text" name="items[0][specifications]" placeholder="المواصفات"
                                   style="width: 100%; padding: 10px; border: 1px solid #dee2e6; border-radius: 6px; font-size: 14px;">
                        </div>
                        <div>
                            <input type="text" name="items[0][unit]" placeholder="الوحدة *" required
                                   style="width: 100%; padding: 10px; border: 1px solid #dee2e6; border-radius: 6px; font-size: 14px;">
                        </div>
                        <div>
                            <input type="number" name="items[0][quantity]" placeholder="الكمية *" step="0.01" required
                                   style="width: 100%; padding: 10px; border: 1px solid #dee2e6; border-radius: 6px; font-size: 14px;">
                        </div>
                        <div>
                            <button type="button" onclick="removeItem(this)" disabled
                                    style="background: #dc3545; color: white; padding: 10px 16px; border: none; border-radius: 6px; cursor: pointer;">
                                حذف
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div style="display: flex; gap: 12px;">
                <button type="submit"
                        style="background: var(--accent); color: white; padding: 12px 32px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
                    حفظ
                </button>
                <a href="{{ route('price-requests.index') }}"
                   style="background: #6c757d; color: white; padding: 12px 32px; border-radius: 8px; text-decoration: none; font-weight: 600;">
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</div>

<script>
let itemIndex = 1;

function addItem() {
    const container = document.getElementById('items-container');
    const newItem = document.createElement('div');
    newItem.className = 'item-row';
    newItem.style = 'display: grid; grid-template-columns: 2fr 2fr 1fr 1fr auto; gap: 12px; margin-bottom: 12px;';
    newItem.innerHTML = `
        <div>
            <input type="text" name="items[${itemIndex}][item_description]" placeholder="وصف البند *" required
                   style="width: 100%; padding: 10px; border: 1px solid #dee2e6; border-radius: 6px; font-size: 14px;">
        </div>
        <div>
            <input type="text" name="items[${itemIndex}][specifications]" placeholder="المواصفات"
                   style="width: 100%; padding: 10px; border: 1px solid #dee2e6; border-radius: 6px; font-size: 14px;">
        </div>
        <div>
            <input type="text" name="items[${itemIndex}][unit]" placeholder="الوحدة *" required
                   style="width: 100%; padding: 10px; border: 1px solid #dee2e6; border-radius: 6px; font-size: 14px;">
        </div>
        <div>
            <input type="number" name="items[${itemIndex}][quantity]" placeholder="الكمية *" step="0.01" required
                   style="width: 100%; padding: 10px; border: 1px solid #dee2e6; border-radius: 6px; font-size: 14px;">
        </div>
        <div>
            <button type="button" onclick="removeItem(this)"
                    style="background: #dc3545; color: white; padding: 10px 16px; border: none; border-radius: 6px; cursor: pointer;">
                حذف
            </button>
        </div>
    `;
    container.appendChild(newItem);
    itemIndex++;
}

function removeItem(button) {
    button.closest('.item-row').remove();
}
</script>
@endsection
