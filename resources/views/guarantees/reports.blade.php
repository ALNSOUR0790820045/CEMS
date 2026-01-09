@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1200px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px;">تقارير خطابات الضمان</h1>

    <div style="background: white; padding: 30px; border-radius: 10px; text-align: center;">
        <i data-lucide="file-bar-chart" style="width: 64px; height: 64px; color: #0071e3; margin-bottom: 20px;"></i>
        <h2 style="margin: 0 0 15px 0;">قريباً</h2>
        <p style="color: #666; margin: 0;">ستتوفر التقارير المفصلة قريباً</p>
    </div>
</div>

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
@endsection
