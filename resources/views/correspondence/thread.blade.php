@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1000px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px;">سلسلة المراسلات</h1>

    <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
        @foreach($thread as $item)
            <div style="padding: 25px; margin-bottom: 20px; border: 2px solid {{ $item->id === $correspondence->id ? '#0071e3' : '#f5f5f7' }}; border-radius: 12px; background: {{ $item->id === $correspondence->id ? '#e3f2fd' : 'white' }};">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                    <div>
                        <a href="{{ route('correspondence.show', $item) }}" style="font-weight: 600; font-size: 1.1rem; color: #0071e3; text-decoration: none;">
                            {{ $item->reference_number }}
                        </a>
                        <div style="display: flex; gap: 10px; align-items: center; margin-top: 5px;">
                            <span style="display: inline-block; padding: 4px 12px; background: {{ $item->type === 'incoming' ? '#e3f2fd' : '#e8f5e9' }}; color: {{ $item->type === 'incoming' ? '#0071e3' : '#34c759' }}; border-radius: 12px; font-size: 0.85rem; font-weight: 500;">
                                {{ $item->type === 'incoming' ? 'وارد' : 'صادر' }}
                            </span>
                            @if($item->id === $correspondence->id)
                                <span style="display: inline-block; padding: 4px 12px; background: #0071e322; color: #0071e3; border-radius: 12px; font-size: 0.85rem; font-weight: 500;">
                                    المراسلة الحالية
                                </span>
                            @endif
                        </div>
                    </div>
                    <div style="text-align: left; color: #86868b; font-size: 0.9rem;">
                        {{ $item->document_date->format('Y-m-d') }}
                    </div>
                </div>

                <h3 style="margin: 0 0 10px 0; font-size: 1rem; color: #1d1d1f;">{{ $item->subject }}</h3>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                    <div style="background: #f5f5f7; padding: 12px; border-radius: 6px;">
                        <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 3px;">من</div>
                        <div style="font-weight: 500;">{{ $item->from_entity }}</div>
                    </div>
                    <div style="background: #f5f5f7; padding: 12px; border-radius: 6px;">
                        <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 3px;">إلى</div>
                        <div style="font-weight: 500;">{{ $item->to_entity }}</div>
                    </div>
                </div>

                @if($item->summary)
                    <div style="color: #1d1d1f; line-height: 1.6; background: #f5f5f7; padding: 15px; border-radius: 6px;">
                        {{ $item->summary }}
                    </div>
                @endif

                <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center;">
                    <div style="color: #86868b; font-size: 0.85rem;">
                        أنشئ بواسطة: <span style="color: #1d1d1f; font-weight: 500;">{{ $item->creator->name }}</span>
                    </div>
                    @if($item->replyTo)
                        <div style="color: #86868b; font-size: 0.85rem;">
                            <i data-lucide="corner-down-left" style="width: 14px; height: 14px; vertical-align: middle;"></i>
                            رداً على: <a href="#corr-{{ $item->replyTo->id }}" style="color: #0071e3; text-decoration: none;">{{ $item->replyTo->reference_number }}</a>
                        </div>
                    @endif
                </div>

                @if($item->id !== $correspondence->id)
                    <div style="margin-top: 15px;">
                        <a href="{{ route('correspondence.show', $item) }}" style="display: inline-block; padding: 8px 16px; background: #0071e3; color: white; border-radius: 6px; text-decoration: none; font-size: 0.9rem; font-weight: 500;">
                            عرض التفاصيل
                        </a>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <div style="margin-top: 20px; text-align: center;">
        <a href="{{ route('correspondence.show', $correspondence) }}" style="display: inline-block; padding: 12px 24px; background: #f5f5f7; color: #1d1d1f; border-radius: 8px; text-decoration: none; font-weight: 600;">
            العودة إلى المراسلة
        </a>
    </div>
</div>

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
@endsection
