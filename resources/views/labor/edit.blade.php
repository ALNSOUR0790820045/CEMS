@extends('layouts.app')

@section('content')
<div style="padding: 40px;">
    <div style="max-width: 900px; margin: 0 auto;">
        <h1 style="font-size: 2rem; color: #1d1d1f; margin-bottom: 30px;">تعديل بيانات العامل</h1>

        <form method="POST" action="{{ route('labor.update', $laborer) }}" style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            @csrf
            @method('PUT')

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #1d1d1f;">الاسم *</label>
                    <input type="text" name="name" value="{{ $laborer->name }}" required style="width: 100%; padding: 10px; border: 1px solid #d2d2d7; border-radius: 6px; font-family: 'Cairo', sans-serif;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #1d1d1f;">الفئة *</label>
                    <select name="category_id" required style="width: 100%; padding: 10px; border: 1px solid #d2d2d7; border-radius: 6px; font-family: 'Cairo', sans-serif;">
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ $laborer->category_id == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #1d1d1f;">نوع التوظيف *</label>
                    <select name="employment_type" required style="width: 100%; padding: 10px; border: 1px solid #d2d2d7; border-radius: 6px; font-family: 'Cairo', sans-serif;">
                        <option value="permanent" {{ $laborer->employment_type == 'permanent' ? 'selected' : '' }}>دائم</option>
                        <option value="temporary" {{ $laborer->employment_type == 'temporary' ? 'selected' : '' }}>مؤقت</option>
                        <option value="subcontractor" {{ $laborer->employment_type == 'subcontractor' ? 'selected' : '' }}>مقاول باطن</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #1d1d1f;">تاريخ الالتحاق *</label>
                    <input type="date" name="joining_date" value="{{ $laborer->joining_date?->format('Y-m-d') }}" required style="width: 100%; padding: 10px; border: 1px solid #d2d2d7; border-radius: 6px;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #1d1d1f;">الأجر اليومي *</label>
                    <input type="number" name="daily_wage" value="{{ $laborer->daily_wage }}" required step="0.01" style="width: 100%; padding: 10px; border: 1px solid #d2d2d7; border-radius: 6px;">
                </div>
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #1d1d1f;">الحالة *</label>
                    <select name="status" required style="width: 100%; padding: 10px; border: 1px solid #d2d2d7; border-radius: 6px; font-family: 'Cairo', sans-serif;">
                        <option value="available" {{ $laborer->status == 'available' ? 'selected' : '' }}>متاح</option>
                        <option value="assigned" {{ $laborer->status == 'assigned' ? 'selected' : '' }}>مخصص</option>
                        <option value="on_leave" {{ $laborer->status == 'on_leave' ? 'selected' : '' }}>إجازة</option>
                        <option value="sick" {{ $laborer->status == 'sick' ? 'selected' : '' }}>مريض</option>
                        <option value="terminated" {{ $laborer->status == 'terminated' ? 'selected' : '' }}>منتهي</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600; color: #1d1d1f;">المشروع الحالي</label>
                    <select name="current_project_id" style="width: 100%; padding: 10px; border: 1px solid #d2d2d7; border-radius: 6px; font-family: 'Cairo', sans-serif;">
                        <option value="">لا يوجد</option>
                        @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ $laborer->current_project_id == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div style="display: flex; gap: 10px; margin-top: 30px;">
                <button type="submit" style="background: linear-gradient(135deg, #0071e3 0%, #00c4cc 100%); color: white; padding: 12px 24px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-family: 'Cairo', sans-serif;">
                    تحديث
                </button>
                <a href="{{ route('labor.index') }}" style="background: #e2e3e5; color: #383d41; padding: 12px 24px; border-radius: 8px; text-decoration: none; display: inline-block;">
                    إلغاء
                </a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
@endsection
