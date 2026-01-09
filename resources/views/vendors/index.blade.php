@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1400px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 style="font-size: 2rem; font-weight: 700;">إدارة الموردين</h1>
        <a href="{{ route('vendors.create') }}" style="background: #0071e3; color: white; padding: 12px 30px; border-radius: 8px; text-decoration: none; font-weight: 600;">
            <i data-lucide="plus" style="width: 18px; height: 18px; display: inline-block; vertical-align: middle;"></i>
            إضافة مورد جديد
        </a>
    </div>

    @if(session('success'))
    <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
        {{ session('success') }}
    </div>
    @endif

    <!-- KPI Cards -->
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px;">
        <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <div style="font-size: 0.85rem; color: #86868b; margin-bottom: 8px;">إجمالي الموردين</div>
            <div style="font-size: 2rem; font-weight: 700; color: #0071e3;">{{ $vendors->total() }}</div>
        </div>
        <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <div style="font-size: 0.85rem; color: #86868b; margin-bottom: 8px;">معتمد</div>
            <div style="font-size: 2rem; font-weight: 700; color: #34c759;">{{ $vendors->where('is_approved', true)->count() }}</div>
        </div>
        <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <div style="font-size: 0.85rem; color: #86868b; margin-bottom: 8px;">قيد الانتظار</div>
            <div style="font-size: 2rem; font-weight: 700; color: #ff9500;">{{ $vendors->where('is_approved', false)->count() }}</div>
        </div>
        <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <div style="font-size: 0.85rem; color: #86868b; margin-bottom: 8px;">نشط</div>
            <div style="font-size: 2rem; font-weight: 700; color: #0071e3;">{{ $vendors->where('is_active', true)->count() }}</div>
        </div>
    </div>

    <!-- Filters -->
    <div style="background: white; padding: 20px; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
        <form method="GET" action="{{ route('vendors.index') }}" style="display: grid; grid-template-columns: repeat(5, 1fr) auto; gap: 15px; align-items: end;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-size: 0.85rem; color: #86868b;">نوع المورد</label>
                <select name="vendor_type" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: 'Cairo', sans-serif;">
                    <option value="">الكل</option>
                    <option value="materials_supplier">مورد مواد</option>
                    <option value="equipment_supplier">مورد معدات</option>
                    <option value="services_provider">مزود خدمات</option>
                    <option value="subcontractor">مقاول باطن</option>
                    <option value="consultant">استشاري</option>
                </select>
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; font-size: 0.85rem; color: #86868b;">التصنيف</label>
                <select name="vendor_category" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: 'Cairo', sans-serif;">
                    <option value="">الكل</option>
                    <option value="strategic">استراتيجي</option>
                    <option value="preferred">مفضل</option>
                    <option value="regular">عادي</option>
                    <option value="blacklisted">محظور</option>
                </select>
            </div>
            <div>
                <label style="display: block; margin-bottom: 5px; font-size: 0.85rem; color: #86868b;">الحالة</label>
                <select name="is_approved" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: 'Cairo', sans-serif;">
                    <option value="">الكل</option>
                    <option value="1">معتمد</option>
                    <option value="0">قيد الانتظار</option>
                </select>
            </div>
            <div style="grid-column: span 2;">
                <label style="display: block; margin-bottom: 5px; font-size: 0.85rem; color: #86868b;">بحث</label>
                <input type="text" name="search" placeholder="كود، اسم، رقم ضريبي، هاتف..." style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: 'Cairo', sans-serif;">
            </div>
            <div>
                <button type="submit" style="background: #0071e3; color: white; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-family: 'Cairo', sans-serif;">بحث</button>
            </div>
        </form>
    </div>

    <!-- Vendors Table -->
    <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f5f5f7; border-bottom: 2px solid #e5e5e7;">
                    <th style="padding: 15px; text-align: right; font-weight: 600; font-size: 0.85rem; color: #1d1d1f;">كود المورد</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; font-size: 0.85rem; color: #1d1d1f;">الاسم</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; font-size: 0.85rem; color: #1d1d1f;">النوع</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; font-size: 0.85rem; color: #1d1d1f;">التصنيف</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; font-size: 0.85rem; color: #1d1d1f;">الهاتف</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; font-size: 0.85rem; color: #1d1d1f;">البريد</th>
                    <th style="padding: 15px; text-align: center; font-weight: 600; font-size: 0.85rem; color: #1d1d1f;">الحالة</th>
                    <th style="padding: 15px; text-align: center; font-weight: 600; font-size: 0.85rem; color: #1d1d1f;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vendors as $vendor)
                <tr style="border-bottom: 1px solid #f5f5f7; transition: background 0.2s;" onmouseover="this.style.background='#f9f9f9'" onmouseout="this.style.background='white'">
                    <td style="padding: 15px;">
                        <span style="font-weight: 600; color: #0071e3;">{{ $vendor->vendor_code }}</span>
                    </td>
                    <td style="padding: 15px;">
                        <div style="font-weight: 600;">{{ $vendor->name }}</div>
                        @if($vendor->name_en)
                        <div style="font-size: 0.8rem; color: #86868b;">{{ $vendor->name_en }}</div>
                        @endif
                    </td>
                    <td style="padding: 15px;">
                        @php
                            $typeLabels = [
                                'materials_supplier' => 'مورد مواد',
                                'equipment_supplier' => 'مورد معدات',
                                'services_provider' => 'مزود خدمات',
                                'subcontractor' => 'مقاول باطن',
                                'consultant' => 'استشاري',
                            ];
                        @endphp
                        <span style="padding: 4px 12px; border-radius: 12px; font-size: 0.75rem; background: #e3f2fd; color: #1976d2;">
                            {{ $typeLabels[$vendor->vendor_type] ?? $vendor->vendor_type }}
                        </span>
                    </td>
                    <td style="padding: 15px;">
                        @php
                            $categoryColors = [
                                'strategic' => ['bg' => '#e8f5e9', 'color' => '#2e7d32'],
                                'preferred' => ['bg' => '#e3f2fd', 'color' => '#1976d2'],
                                'regular' => ['bg' => '#f5f5f5', 'color' => '#666'],
                                'blacklisted' => ['bg' => '#ffebee', 'color' => '#c62828'],
                            ];
                            $categoryLabels = [
                                'strategic' => 'استراتيجي',
                                'preferred' => 'مفضل',
                                'regular' => 'عادي',
                                'blacklisted' => 'محظور',
                            ];
                            $colors = $categoryColors[$vendor->vendor_category] ?? ['bg' => '#f5f5f5', 'color' => '#666'];
                        @endphp
                        <span style="padding: 4px 12px; border-radius: 12px; font-size: 0.75rem; background: {{ $colors['bg'] }}; color: {{ $colors['color'] }};">
                            {{ $categoryLabels[$vendor->vendor_category] ?? $vendor->vendor_category }}
                        </span>
                    </td>
                    <td style="padding: 15px;">{{ $vendor->phone ?? '-' }}</td>
                    <td style="padding: 15px;">{{ $vendor->email ?? '-' }}</td>
                    <td style="padding: 15px; text-align: center;">
                        <div style="display: flex; flex-direction: column; gap: 4px; align-items: center;">
                            @if($vendor->is_approved)
                            <span style="padding: 4px 12px; border-radius: 12px; font-size: 0.75rem; background: #d4edda; color: #155724;">معتمد</span>
                            @else
                            <span style="padding: 4px 12px; border-radius: 12px; font-size: 0.75rem; background: #fff3cd; color: #856404;">قيد الانتظار</span>
                            @endif
                            @if($vendor->is_active)
                            <span style="padding: 4px 12px; border-radius: 12px; font-size: 0.75rem; background: #d4edda; color: #155724;">نشط</span>
                            @else
                            <span style="padding: 4px 12px; border-radius: 12px; font-size: 0.75rem; background: #f8d7da; color: #721c24;">غير نشط</span>
                            @endif
                        </div>
                    </td>
                    <td style="padding: 15px; text-align: center;">
                        <div style="display: flex; gap: 8px; justify-content: center;">
                            <a href="{{ route('vendors.show', $vendor) }}" style="padding: 6px 12px; background: #0071e3; color: white; border-radius: 6px; text-decoration: none; font-size: 0.85rem;" title="عرض">
                                <i data-lucide="eye" style="width: 16px; height: 16px;"></i>
                            </a>
                            <a href="{{ route('vendors.edit', $vendor) }}" style="padding: 6px 12px; background: #34c759; color: white; border-radius: 6px; text-decoration: none; font-size: 0.85rem;" title="تعديل">
                                <i data-lucide="edit" style="width: 16px; height: 16px;"></i>
                            </a>
                            <form method="POST" action="{{ route('vendors.destroy', $vendor) }}" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذا المورد؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="padding: 6px 12px; background: #ff3b30; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 0.85rem;" title="حذف">
                                    <i data-lucide="trash-2" style="width: 16px; height: 16px;"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="padding: 40px; text-align: center; color: #86868b;">
                        <i data-lucide="package-x" style="width: 48px; height: 48px; margin-bottom: 10px;"></i>
                        <div>لا توجد موردين</div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($vendors->hasPages())
    <div style="margin-top: 20px;">
        {{ $vendors->links() }}
    </div>
    @endif
</div>

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
@endsection
