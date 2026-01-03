@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 900px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px;">تعديل المشروع: {{ $project->name }}</h1>
    
    <form method="POST" action="{{ route('projects.update', $project) }}" style="background: white; padding: 30px; border-radius: 10px;">
        @csrf
        @method('PUT')
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; font-weight: 600;">كود المشروع</label>
            <input type="text" value="{{ $project->project_code }}" readonly style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; background: #f5f5f7; font-family: 'Cairo', sans-serif;">
        </div>
        
        <p style="text-align: center; padding: 40px; color: #999;">
            تحديث نموذج التعديل قيد التطوير - يرجى استخدام واجهة الإنشاء للآن
        </p>
        
        <div style="display: flex; gap: 15px;">
            <a href="{{ route('projects.index') }}" style="padding: 14px 40px; border: 1px solid #ddd; border-radius: 8px; text-decoration: none; color: #666; font-family: 'Cairo', sans-serif; font-weight: 600; display: inline-block;">رجوع</a>
        </div>
    </form>
</div>
@endsection
