@extends('layouts.app')

@section('content')
<div style="padding: 20px; max-width: 1400px; margin: 0 auto;">
    <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
        <h1 style="font-size: 28px; font-weight: 600; margin: 0;">طلبات عروض الأسعار</h1>
        <a href="{{ route('price-requests.create') }}" style="background: var(--accent); color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 500;">
            إنشاء طلب جديد
        </a>
    </div>

    @if(session('success'))
    <div style="background: #d4edda; color: #155724; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px;">
        {{ session('success') }}
    </div>
    @endif

    <div style="background: white; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f8f9fa;">
                <tr>
                    <th style="padding: 16px; text-align: right; font-weight: 600; border-bottom: 2px solid #dee2e6;">رقم الطلب</th>
                    <th style="padding: 16px; text-align: right; font-weight: 600; border-bottom: 2px solid #dee2e6;">المشروع</th>
                    <th style="padding: 16px; text-align: right; font-weight: 600; border-bottom: 2px solid #dee2e6;">تاريخ الطلب</th>
                    <th style="padding: 16px; text-align: right; font-weight: 600; border-bottom: 2px solid #dee2e6;">مطلوب بتاريخ</th>
                    <th style="padding: 16px; text-align: right; font-weight: 600; border-bottom: 2px solid #dee2e6;">الحالة</th>
                    <th style="padding: 16px; text-align: right; font-weight: 600; border-bottom: 2px solid #dee2e6;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $request)
                <tr style="border-bottom: 1px solid #dee2e6;">
                    <td style="padding: 16px; font-weight: 600;">{{ $request->request_number }}</td>
                    <td style="padding: 16px;">{{ $request->project ? $request->project->name : '-' }}</td>
                    <td style="padding: 16px;">{{ $request->request_date->format('Y-m-d') }}</td>
                    <td style="padding: 16px;">{{ $request->required_by->format('Y-m-d') }}</td>
                    <td style="padding: 16px;">
                        @php
                            $statusColors = [
                                'draft' => 'background: #e2e3e5; color: #383d41;',
                                'sent' => 'background: #d1ecf1; color: #0c5460;',
                                'received' => 'background: #d4edda; color: #155724;',
                                'analyzed' => 'background: #fff3cd; color: #856404;',
                                'closed' => 'background: #f8d7da; color: #721c24;'
                            ];
                            $statusLabels = [
                                'draft' => 'مسودة',
                                'sent' => 'مرسل',
                                'received' => 'مستلم',
                                'analyzed' => 'محلل',
                                'closed' => 'مغلق'
                            ];
                        @endphp
                        <span style="padding: 4px 12px; border-radius: 12px; font-size: 13px; {{ $statusColors[$request->status] }}">
                            {{ $statusLabels[$request->status] }}
                        </span>
                    </td>
                    <td style="padding: 16px;">
                        <a href="{{ route('price-requests.show', $request) }}" style="color: var(--accent); text-decoration: none; margin-left: 12px;">عرض</a>
                        @if($request->status == 'draft')
                        <form action="{{ route('price-requests.send', $request) }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" style="background: none; border: none; color: #28a745; cursor: pointer; padding: 0;">إرسال</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" style="padding: 40px; text-align: center; color: #6c757d;">
                        لا توجد طلبات عروض أسعار
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 20px;">
        {{ $requests->links() }}
    </div>
</div>
@endsection
