@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1200px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px;">قوالب المراسلات</h1>

    <div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); overflow: hidden;">
        @if($templates->isEmpty())
            <div style="padding: 60px; text-align: center; color: #86868b;">
                <i data-lucide="file-text" style="width: 64px; height: 64px; margin-bottom: 20px; opacity: 0.3;"></i>
                <p style="font-size: 1.1rem; margin: 0;">لا توجد قوالب</p>
            </div>
        @else
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background: #f5f5f7;">
                    <tr>
                        <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">الاسم</th>
                        <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">النوع</th>
                        <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">التصنيف</th>
                        <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">قالب الموضوع</th>
                        <th style="padding: 15px; text-align: center; font-weight: 600; color: #1d1d1f;">الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($templates as $template)
                    <tr style="border-bottom: 1px solid #f5f5f7;">
                        <td style="padding: 15px; font-weight: 500;">{{ $template->name }}</td>
                        <td style="padding: 15px;">
                            <span style="display: inline-block; padding: 4px 12px; background: {{ $template->type === 'incoming' ? '#e3f2fd' : '#e8f5e9' }}; color: {{ $template->type === 'incoming' ? '#0071e3' : '#34c759' }}; border-radius: 12px; font-size: 0.85rem; font-weight: 500;">
                                {{ $template->type === 'incoming' ? 'وارد' : 'صادر' }}
                            </span>
                        </td>
                        <td style="padding: 15px;">{{ $template->category }}</td>
                        <td style="padding: 15px; color: #86868b; font-size: 0.9rem;">{{ Str::limit($template->subject_template, 40) }}</td>
                        <td style="padding: 15px; text-align: center;">
                            @if($template->is_active)
                                <span style="display: inline-block; padding: 4px 12px; background: #34c75922; color: #34c759; border-radius: 12px; font-size: 0.85rem; font-weight: 500;">
                                    نشط
                                </span>
                            @else
                                <span style="display: inline-block; padding: 4px 12px; background: #86868b22; color: #86868b; border-radius: 12px; font-size: 0.85rem; font-weight: 500;">
                                    غير نشط
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
@endsection
