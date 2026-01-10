@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1400px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 style="margin: 0;">لوحة تحكم المراسلات</h1>
        <a href="{{ route('correspondence.create') }}" style="background: #0071e3; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600;">
            <i data-lucide="plus" style="width: 18px; height: 18px; vertical-align: middle;"></i>
            مراسلة جديدة
        </a>
    </div>

    <!-- Statistics Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 40px;">
        <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 8px;">إجمالي المراسلات</div>
            <div style="font-size: 2rem; font-weight: 700; color: #1d1d1f;">{{ $stats['total'] }}</div>
        </div>
        
        <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 8px;">الوارد</div>
            <div style="font-size: 2rem; font-weight: 700; color: #0071e3;">{{ $stats['incoming'] }}</div>
        </div>
        
        <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 8px;">الصادر</div>
            <div style="font-size: 2rem; font-weight: 700; color: #34c759;">{{ $stats['outgoing'] }}</div>
        </div>
        
        <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 8px;">بانتظار الرد</div>
            <div style="font-size: 2rem; font-weight: 700; color: #ff9500;">{{ $stats['pending'] }}</div>
        </div>
        
        <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 8px;">متأخرة</div>
            <div style="font-size: 2rem; font-weight: 700; color: #ff3b30;">{{ $stats['overdue'] }}</div>
        </div>
        
        <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 8px;">مسودات</div>
            <div style="font-size: 2rem; font-weight: 700; color: #86868b;">{{ $stats['draft'] }}</div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 400px; gap: 20px;">
        <!-- Recent Correspondence -->
        <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <h2 style="margin: 0 0 20px 0; font-size: 1.2rem;">آخر المراسلات</h2>
            <div>
                @forelse($recentCorrespondence as $item)
                <a href="{{ route('correspondence.show', $item) }}" style="display: block; padding: 15px; border-bottom: 1px solid #f5f5f7; text-decoration: none; color: inherit; transition: all 0.2s;">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px;">
                        <div>
                            <span style="font-weight: 600; color: #1d1d1f;">{{ $item->reference_number }}</span>
                            <span style="display: inline-block; margin: 0 8px; padding: 2px 8px; background: {{ $item->type === 'incoming' ? '#e3f2fd' : '#e8f5e9' }}; color: {{ $item->type === 'incoming' ? '#0071e3' : '#34c759' }}; border-radius: 4px; font-size: 0.75rem;">
                                {{ $item->type === 'incoming' ? 'وارد' : 'صادر' }}
                            </span>
                        </div>
                        <span style="color: #86868b; font-size: 0.85rem;">{{ $item->document_date->format('Y-m-d') }}</span>
                    </div>
                    <div style="color: #1d1d1f; margin-bottom: 5px;">{{ $item->subject }}</div>
                    <div style="color: #86868b; font-size: 0.85rem;">{{ $item->from_entity }} → {{ $item->to_entity }}</div>
                </a>
                @empty
                <p style="text-align: center; color: #86868b; padding: 40px;">لا توجد مراسلات</p>
                @endforelse
            </div>
        </div>

        <!-- Overdue Items -->
        <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <h2 style="margin: 0 0 20px 0; font-size: 1.2rem; color: #ff3b30;">المراسلات المتأخرة</h2>
            <div>
                @forelse($overdueItems as $item)
                <a href="{{ route('correspondence.show', $item) }}" style="display: block; padding: 15px; border-bottom: 1px solid #f5f5f7; text-decoration: none; color: inherit;">
                    <div style="font-weight: 600; color: #1d1d1f; margin-bottom: 5px;">{{ $item->reference_number }}</div>
                    <div style="color: #1d1d1f; font-size: 0.9rem; margin-bottom: 5px;">{{ Str::limit($item->subject, 40) }}</div>
                    <div style="color: #ff3b30; font-size: 0.85rem;">
                        متأخر منذ {{ $item->response_required_date->diffForHumans() }}
                    </div>
                </a>
                @empty
                <p style="text-align: center; color: #86868b; padding: 40px;">لا توجد مراسلات متأخرة</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 20px;">
        <a href="{{ route('correspondence.incoming') }}" style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); text-decoration: none; color: inherit; transition: all 0.2s;">
            <i data-lucide="inbox" style="width: 24px; height: 24px; color: #0071e3; margin-bottom: 10px;"></i>
            <div style="font-weight: 600; margin-bottom: 5px;">الوارد</div>
            <div style="color: #86868b; font-size: 0.85rem;">عرض جميع المراسلات الواردة</div>
        </a>
        
        <a href="{{ route('correspondence.outgoing') }}" style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); text-decoration: none; color: inherit; transition: all 0.2s;">
            <i data-lucide="send" style="width: 24px; height: 24px; color: #34c759; margin-bottom: 10px;"></i>
            <div style="font-weight: 600; margin-bottom: 5px;">الصادر</div>
            <div style="color: #86868b; font-size: 0.85rem;">عرض جميع المراسلات الصادرة</div>
        </a>
        
        <a href="{{ route('correspondence.search') }}" style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); text-decoration: none; color: inherit; transition: all 0.2s;">
            <i data-lucide="search" style="width: 24px; height: 24px; color: #ff9500; margin-bottom: 10px;"></i>
            <div style="font-weight: 600; margin-bottom: 5px;">البحث المتقدم</div>
            <div style="color: #86868b; font-size: 0.85rem;">البحث في جميع المراسلات</div>
        </a>
        
        <a href="{{ route('correspondence.templates') }}" style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); text-decoration: none; color: inherit; transition: all 0.2s;">
            <i data-lucide="file-text" style="width: 24px; height: 24px; color: #5856d6; margin-bottom: 10px;"></i>
            <div style="font-weight: 600; margin-bottom: 5px;">القوالب</div>
            <div style="color: #86868b; font-size: 0.85rem;">إدارة قوالب المراسلات</div>
        </a>
    </div>
</div>

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
@endsection
