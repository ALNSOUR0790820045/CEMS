@extends('layouts.app')

@section('content')
<div style="padding: 40px;">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h1 style="font-size: 2rem; color: #1d1d1f; margin-bottom: 5px;">إدارة العمالة</h1>
            <p style="color: #86868b;">عرض وإدارة جميع العمال في النظام</p>
        </div>
        <a href="{{ route('labor.create') }}" style="background: linear-gradient(135deg, #0071e3 0%, #00c4cc 100%); color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
            <i data-lucide="user-plus" style="width: 18px; height: 18px;"></i>
            إضافة عامل جديد
        </a>
    </div>

    <!-- Success Message -->
    @if(session('success'))
    <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
        {{ session('success') }}
    </div>
    @endif

    <!-- Statistics Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <div style="font-size: 0.85rem; color: #86868b; margin-bottom: 5px;">إجمالي العمال</div>
            <div style="font-size: 2rem; font-weight: 700; color: #0071e3;">{{ $laborers->count() }}</div>
        </div>
        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <div style="font-size: 0.85rem; color: #86868b; margin-bottom: 5px;">متاح</div>
            <div style="font-size: 2rem; font-weight: 700; color: #34c759;">{{ $laborers->where('status', 'available')->count() }}</div>
        </div>
        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <div style="font-size: 0.85rem; color: #86868b; margin-bottom: 5px;">مخصص</div>
            <div style="font-size: 2rem; font-weight: 700; color: #ff9500;">{{ $laborers->where('status', 'assigned')->count() }}</div>
        </div>
        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <div style="font-size: 0.85rem; color: #86868b; margin-bottom: 5px;">إجازة / مريض</div>
            <div style="font-size: 2rem; font-weight: 700; color: #ff3b30;">{{ $laborers->whereIn('status', ['on_leave', 'sick'])->count() }}</div>
        </div>
    </div>

    <!-- Laborers Table -->
    <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        @if($laborers->count() > 0)
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f5f5f7;">
                <tr>
                    <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">رقم العامل</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">الاسم</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">الفئة</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">الجنسية</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">المشروع الحالي</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">الحالة</th>
                    <th style="padding: 15px; text-align: center; font-weight: 600; color: #1d1d1f;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @foreach($laborers as $laborer)
                <tr style="border-bottom: 1px solid #f0f0f0;">
                    <td style="padding: 15px; font-family: monospace;">{{ $laborer->labor_number }}</td>
                    <td style="padding: 15px; font-weight: 600;">{{ $laborer->name }}</td>
                    <td style="padding: 15px; color: #86868b;">{{ $laborer->category->name ?? '-' }}</td>
                    <td style="padding: 15px; color: #86868b;">{{ $laborer->nationality ?? '-' }}</td>
                    <td style="padding: 15px; color: #86868b;">{{ $laborer->currentProject->name ?? '-' }}</td>
                    <td style="padding: 15px;">
                        @if($laborer->status === 'available')
                        <span style="background: #d4edda; color: #155724; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; font-weight: 500;">متاح</span>
                        @elseif($laborer->status === 'assigned')
                        <span style="background: #fff3cd; color: #856404; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; font-weight: 500;">مخصص</span>
                        @elseif($laborer->status === 'on_leave')
                        <span style="background: #cfe2ff; color: #084298; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; font-weight: 500;">إجازة</span>
                        @elseif($laborer->status === 'sick')
                        <span style="background: #f8d7da; color: #721c24; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; font-weight: 500;">مريض</span>
                        @else
                        <span style="background: #e2e3e5; color: #383d41; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; font-weight: 500;">{{ $laborer->status }}</span>
                        @endif
                    </td>
                    <td style="padding: 15px; text-align: center;">
                        <div style="display: inline-flex; gap: 8px;">
                            <a href="{{ route('labor.show', $laborer) }}" style="background: #34c759; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 0.85rem;">عرض</a>
                            <a href="{{ route('labor.edit', $laborer) }}" style="background: #0071e3; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 0.85rem;">تعديل</a>
                            <form method="POST" action="{{ route('labor.destroy', $laborer) }}" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذا العامل؟');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: #ff3b30; color: white; padding: 6px 12px; border-radius: 6px; border: none; cursor: pointer; font-size: 0.85rem; font-family: 'Cairo', sans-serif;">حذف</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div style="padding: 60px; text-align: center;">
            <i data-lucide="users" style="width: 64px; height: 64px; color: #d2d2d7; margin-bottom: 20px;"></i>
            <h3 style="color: #86868b; margin-bottom: 10px;">لا يوجد عمال</h3>
            <p style="color: #d2d2d7;">ابدأ بإضافة عامل جديد</p>
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
@endsection
