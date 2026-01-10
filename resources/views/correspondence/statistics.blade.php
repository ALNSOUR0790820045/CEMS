@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1200px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px;">إحصائيات المراسلات</h1>

    <!-- Overview Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 40px;">
        <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <div style="color: #86868b; font-size: 0.9rem; margin-bottom: 10px;">إجمالي المراسلات</div>
            <div style="font-size: 2.5rem; font-weight: 700; color: #1d1d1f;">{{ $stats['total'] }}</div>
        </div>
        
        <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <div style="color: #86868b; font-size: 0.9rem; margin-bottom: 10px;">الوارد</div>
            <div style="font-size: 2.5rem; font-weight: 700; color: #0071e3;">{{ $stats['incoming'] }}</div>
        </div>
        
        <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <div style="color: #86868b; font-size: 0.9rem; margin-bottom: 10px;">الصادر</div>
            <div style="font-size: 2.5rem; font-weight: 700; color: #34c759;">{{ $stats['outgoing'] }}</div>
        </div>
        
        <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <div style="color: #86868b; font-size: 0.9rem; margin-bottom: 10px;">بانتظار الرد</div>
            <div style="font-size: 2.5rem; font-weight: 700; color: #ff9500;">{{ $stats['pending'] }}</div>
        </div>
        
        <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <div style="color: #86868b; font-size: 0.9rem; margin-bottom: 10px;">المتأخرة</div>
            <div style="font-size: 2.5rem; font-weight: 700; color: #ff3b30;">{{ $stats['overdue'] }}</div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
        <!-- By Status -->
        <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <h2 style="margin: 0 0 20px 0; font-size: 1.2rem;">حسب الحالة</h2>
            <div>
                @foreach($stats['by_status'] as $status => $count)
                    <div style="padding: 12px 0; border-bottom: 1px solid #f5f5f7;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="color: #1d1d1f;">{{ $status }}</span>
                            <span style="font-weight: 600; color: #0071e3;">{{ $count }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- By Category -->
        <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <h2 style="margin: 0 0 20px 0; font-size: 1.2rem;">حسب التصنيف</h2>
            <div>
                @foreach($stats['by_category'] as $category => $count)
                    <div style="padding: 12px 0; border-bottom: 1px solid #f5f5f7;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="color: #1d1d1f;">{{ $category }}</span>
                            <span style="font-weight: 600; color: #34c759;">{{ $count }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- By Priority -->
        <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <h2 style="margin: 0 0 20px 0; font-size: 1.2rem;">حسب الأولوية</h2>
            <div>
                @foreach($stats['by_priority'] as $priority => $count)
                    <div style="padding: 12px 0; border-bottom: 1px solid #f5f5f7;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="color: #1d1d1f;">{{ $priority }}</span>
                            <span style="font-weight: 600; color: #ff9500;">{{ $count }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
@endsection
