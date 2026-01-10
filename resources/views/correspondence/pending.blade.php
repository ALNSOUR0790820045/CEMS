@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1400px; margin: 0 auto;">
    <h1 style="margin-bottom: 30px;">المراسلات بانتظار الرد</h1>

    <div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); overflow: hidden;">
        @if($correspondences->isEmpty())
            <div style="padding: 60px; text-align: center; color: #86868b;">
                <i data-lucide="clock" style="width: 64px; height: 64px; margin-bottom: 20px; opacity: 0.3;"></i>
                <p style="font-size: 1.1rem; margin: 0;">لا توجد مراسلات بانتظار الرد</p>
            </div>
        @else
            <table style="width: 100%; border-collapse: collapse;">
                <thead style="background: #f5f5f7;">
                    <tr>
                        <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">الرقم المرجعي</th>
                        <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">الموضوع</th>
                        <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">من</th>
                        <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">تاريخ الرد المطلوب</th>
                        <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">المدة المتبقية</th>
                        <th style="padding: 15px; text-align: center; font-weight: 600; color: #1d1d1f;">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($correspondences as $correspondence)
                    <tr style="border-bottom: 1px solid #f5f5f7; {{ $correspondence->isOverdue() ? 'background: #ff3b3011;' : '' }}">
                        <td style="padding: 15px;">
                            <a href="{{ route('correspondence.show', $correspondence) }}" style="color: #0071e3; text-decoration: none; font-weight: 500;">
                                {{ $correspondence->reference_number }}
                            </a>
                        </td>
                        <td style="padding: 15px;">{{ Str::limit($correspondence->subject, 50) }}</td>
                        <td style="padding: 15px;">{{ $correspondence->from_entity }}</td>
                        <td style="padding: 15px; color: {{ $correspondence->isOverdue() ? '#ff3b30' : '#1d1d1f' }}; font-weight: {{ $correspondence->isOverdue() ? '600' : '400' }};">
                            {{ $correspondence->response_required_date->format('Y-m-d') }}
                        </td>
                        <td style="padding: 15px;">
                            @if($correspondence->isOverdue())
                                <span style="color: #ff3b30; font-weight: 600;">
                                    متأخر {{ $correspondence->response_required_date->diffForHumans() }}
                                </span>
                            @else
                                <span style="color: #ff9500;">
                                    {{ $correspondence->response_required_date->diffForHumans() }}
                                </span>
                            @endif
                        </td>
                        <td style="padding: 15px; text-align: center;">
                            <a href="{{ route('correspondence.show', $correspondence) }}" style="color: #0071e3; text-decoration: none; padding: 8px; display: inline-block;">
                                <i data-lucide="eye" style="width: 18px; height: 18px;"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>

            <div style="padding: 20px;">
                {{ $correspondences->links() }}
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
