@extends('layouts.app')

@section('content')
<div style="padding: 40px;">
    <div style="max-width: 1400px; margin: 0 auto;">
        <h1 style="font-size: 2rem; font-weight: 700; margin-bottom: 30px;">البنود التعاقدية</h1>

        <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            @if($clauses->count() > 0)
            @foreach($clauses as $clause)
            <div style="border-bottom: 1px solid #f5f5f7; padding: 25px 0; {{ $loop->first ? 'padding-top: 0;' : '' }}">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                    <div>
                        <h3 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 5px;">
                            البند {{ $clause->clause_number }}: {{ $clause->clause_title }}
                        </h3>
                        <p style="color: #86868b; font-size: 0.9rem;">
                            العقد: {{ $clause->contract->title }}
                        </p>
                    </div>
                    <span style="background: #34c759; color: white; padding: 6px 15px; border-radius: 20px; font-size: 0.85rem; font-weight: 600;">
                        فترة الإشعار: {{ $clause->notice_period_days }} يوم
                    </span>
                </div>
                
                <div style="background: #f5f5f7; padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                    <p style="line-height: 1.6; font-size: 0.95rem;">{{ $clause->clause_text }}</p>
                </div>

                @if($clause->notice_requirements)
                <div>
                    <h4 style="font-weight: 600; margin-bottom: 8px; color: #86868b; font-size: 0.85rem;">متطلبات الإشعار:</h4>
                    <p style="line-height: 1.6; font-size: 0.95rem;">{{ $clause->notice_requirements }}</p>
                </div>
                @endif
            </div>
            @endforeach

            <div style="margin-top: 30px;">
                {{ $clauses->links() }}
            </div>
            @else
            <div style="text-align: center; padding: 60px 20px;">
                <i data-lucide="file-text" style="width: 64px; height: 64px; color: #86868b; margin-bottom: 20px;"></i>
                <h3 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 10px;">لا توجد بنود تعاقدية</h3>
                <p style="color: #86868b;">لم يتم إضافة أي بنود تعاقدية بعد</p>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
    lucide.createIcons();
</script>
@endsection
