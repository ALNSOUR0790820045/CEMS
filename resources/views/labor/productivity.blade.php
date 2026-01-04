@extends('layouts.app')

@section('content')
<div style="padding: 40px;">
    <h1 style="font-size: 2rem; color: #1d1d1f; margin-bottom: 30px;">تسجيل الإنتاجية</h1>

    @if(session('success'))
    <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        {{ session('success') }}
    </div>
    @endif

    <!-- Productivity Form -->
    <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); margin-bottom: 30px;">
        <h2 style="color: #1d1d1f; margin-bottom: 20px;">تسجيل إنتاجية جديدة</h2>
        <form method="POST" action="{{ route('labor.productivity.store') }}">
            @csrf
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 15px;">
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">المشروع *</label>
                    <select name="project_id" required style="width: 100%; padding: 10px; border: 1px solid #d2d2d7; border-radius: 6px; font-family: 'Cairo', sans-serif;">
                        <option value="">اختر المشروع</option>
                        @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">التاريخ *</label>
                    <input type="date" name="date" value="{{ date('Y-m-d') }}" required style="width: 100%; padding: 10px; border: 1px solid #d2d2d7; border-radius: 6px;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">عدد العمال *</label>
                    <input type="number" name="labor_count" required min="1" style="width: 100%; padding: 10px; border: 1px solid #d2d2d7; border-radius: 6px;">
                </div>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">وصف النشاط *</label>
                <input type="text" name="activity_description" required style="width: 100%; padding: 10px; border: 1px solid #d2d2d7; border-radius: 6px; font-family: 'Cairo', sans-serif;">
            </div>
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-bottom: 15px;">
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">الكمية المنجزة *</label>
                    <input type="number" name="quantity_achieved" required step="0.01" style="width: 100%; padding: 10px; border: 1px solid #d2d2d7; border-radius: 6px;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">الوحدة *</label>
                    <input type="text" name="unit" required placeholder="مثل: متر، طن" style="width: 100%; padding: 10px; border: 1px solid #d2d2d7; border-radius: 6px; font-family: 'Cairo', sans-serif;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">إجمالي الساعات *</label>
                    <input type="number" name="total_hours" required step="0.5" style="width: 100%; padding: 10px; border: 1px solid #d2d2d7; border-radius: 6px;">
                </div>
            </div>
            <button type="submit" style="background: linear-gradient(135deg, #0071e3 0%, #00c4cc 100%); color: white; padding: 12px 24px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-family: 'Cairo', sans-serif;">
                حفظ
            </button>
        </form>
    </div>

    <!-- Productivity Records -->
    <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        <div style="padding: 20px; border-bottom: 1px solid #f0f0f0;">
            <h2 style="color: #1d1d1f;">سجلات الإنتاجية</h2>
        </div>
        @if($productivityRecords->count() > 0)
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f5f5f7;">
                <tr>
                    <th style="padding: 15px; text-align: right; font-weight: 600;">التاريخ</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600;">المشروع</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600;">النشاط</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600;">الكمية</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600;">عدد العمال</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600;">معدل الإنتاجية</th>
                </tr>
            </thead>
            <tbody>
                @foreach($productivityRecords as $record)
                <tr style="border-bottom: 1px solid #f0f0f0;">
                    <td style="padding: 15px;">{{ $record->date->format('Y-m-d') }}</td>
                    <td style="padding: 15px;">{{ $record->project->name }}</td>
                    <td style="padding: 15px;">{{ $record->activity_description }}</td>
                    <td style="padding: 15px;">{{ $record->quantity_achieved }} {{ $record->unit }}</td>
                    <td style="padding: 15px;">{{ $record->labor_count }}</td>
                    <td style="padding: 15px; font-weight: 600; color: #0071e3;">{{ number_format($record->productivity_rate, 2) }} / ساعة</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div style="padding: 20px;">
            {{ $productivityRecords->links() }}
        </div>
        @else
        <div style="padding: 60px; text-align: center; color: #86868b;">
            لا توجد سجلات إنتاجية
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
