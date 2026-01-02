@extends('layouts.app')

@section('content')
<div style="max-width: 1600px; margin: 0 auto;">
    <div style="margin-bottom: 30px;">
        <h1 style="font-size: 2rem; font-weight: 700; margin-bottom: 5px;">تقرير المستخلصات الشامل</h1>
        <p style="color: #86868b; font-size: 0.9rem;">تحليل شامل لجميع المستخلصات والأداء</p>
    </div>

    <!-- Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 25px; border-radius: 12px; color: white; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 10px;">
                <i data-lucide="file-text" style="width: 32px; height: 32px;"></i>
                <span style="font-size: 0.9rem; opacity: 0.9;">إجمالي المستخلصات</span>
            </div>
            <div style="font-size: 2.2rem; font-weight: 700; margin-bottom: 5px;">{{ $statistics['total_count'] }}</div>
            <div style="font-size: 0.9rem; opacity: 0.8;">{{ number_format($statistics['total_value'], 2) }} ر.س</div>
        </div>

        <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); padding: 25px; border-radius: 12px; color: white; box-shadow: 0 4px 12px rgba(240, 147, 251, 0.3);">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 10px;">
                <i data-lucide="clock" style="width: 32px; height: 32px;"></i>
                <span style="font-size: 0.9rem; opacity: 0.9;">معلقة</span>
            </div>
            <div style="font-size: 2.2rem; font-weight: 700; margin-bottom: 5px;">{{ $statistics['pending_count'] }}</div>
            <div style="font-size: 0.9rem; opacity: 0.8;">{{ number_format($statistics['pending_value'], 2) }} ر.س</div>
        </div>

        <div style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); padding: 25px; border-radius: 12px; color: white; box-shadow: 0 4px 12px rgba(79, 172, 254, 0.3);">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 10px;">
                <i data-lucide="check-circle" style="width: 32px; height: 32px;"></i>
                <span style="font-size: 0.9rem; opacity: 0.9;">معتمدة</span>
            </div>
            <div style="font-size: 2.2rem; font-weight: 700; margin-bottom: 5px;">{{ $statistics['approved_count'] }}</div>
            <div style="font-size: 0.9rem; opacity: 0.8;">{{ number_format($statistics['approved_value'], 2) }} ر.س</div>
        </div>

        <div style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); padding: 25px; border-radius: 12px; color: white; box-shadow: 0 4px 12px rgba(67, 233, 123, 0.3);">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 10px;">
                <i data-lucide="coins" style="width: 32px; height: 32px;"></i>
                <span style="font-size: 0.9rem; opacity: 0.9;">مدفوعة</span>
            </div>
            <div style="font-size: 2.2rem; font-weight: 700; margin-bottom: 5px;">{{ $statistics['paid_count'] }}</div>
            <div style="font-size: 0.9rem; opacity: 0.8;">{{ number_format($statistics['paid_value'], 2) }} ر.س</div>
        </div>

        <div style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); padding: 25px; border-radius: 12px; color: white; box-shadow: 0 4px 12px rgba(250, 112, 154, 0.3);">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 10px;">
                <i data-lucide="piggy-bank" style="width: 32px; height: 32px;"></i>
                <span style="font-size: 0.9rem; opacity: 0.9;">إجمالي الاستبقاء</span>
            </div>
            <div style="font-size: 2.2rem; font-weight: 700; margin-bottom: 5px;">{{ number_format($statistics['retention_total'], 0) }}</div>
            <div style="font-size: 0.9rem; opacity: 0.8;">ر.س</div>
        </div>

        <div style="background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); padding: 25px; border-radius: 12px; color: #1d1d1f; box-shadow: 0 4px 12px rgba(168, 237, 234, 0.3);">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 10px;">
                <i data-lucide="trending-up" style="width: 32px; height: 32px;"></i>
                <span style="font-size: 0.9rem; opacity: 0.8;">متوسط أيام الموافقة</span>
            </div>
            <div style="font-size: 2.2rem; font-weight: 700; margin-bottom: 5px;">
                {{ number_format(($avgConsultantDays + $avgClientDays) / 2, 1) }}
            </div>
            <div style="font-size: 0.9rem; opacity: 0.8;">يوم</div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin-bottom: 30px;">
        <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
            <h3 style="font-size: 1.2rem; font-weight: 600; margin-bottom: 20px;">متوسط وقت المراجعة</h3>
            <div style="display: grid; gap: 15px;">
                <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px; background: #f5f5f7; border-radius: 8px;">
                    <div>
                        <div style="font-weight: 600; margin-bottom: 5px;">مراجعة الاستشاري</div>
                        <div style="font-size: 0.8rem; color: #86868b;">7-14 يوم متوقع</div>
                    </div>
                    <div style="font-size: 1.8rem; font-weight: 700; color: var(--accent);">
                        {{ number_format($avgConsultantDays, 1) }} يوم
                    </div>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; padding: 15px; background: #f5f5f7; border-radius: 8px;">
                    <div>
                        <div style="font-weight: 600; margin-bottom: 5px;">اعتماد العميل</div>
                        <div style="font-size: 0.8rem; color: #86868b;">14-21 يوم متوقع</div>
                    </div>
                    <div style="font-size: 1.8rem; font-weight: 700; color: var(--accent);">
                        {{ number_format($avgClientDays, 1) }} يوم
                    </div>
                </div>
            </div>
        </div>

        <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
            <h3 style="font-size: 1.2rem; font-weight: 600; margin-bottom: 20px;">التدفق النقدي</h3>
            <div style="height: 200px; display: flex; align-items: end; gap: 10px; padding: 20px 0;">
                @php
                    $maxValue = max($statistics['total_value'], $statistics['paid_value'], $statistics['retention_total']);
                @endphp
                <div style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 10px;">
                    <div style="width: 100%; height: {{ ($statistics['total_value'] / $maxValue) * 100 }}%; background: linear-gradient(180deg, #667eea, #764ba2); border-radius: 8px 8px 0 0; min-height: 30px; display: flex; align-items: center; justify-content: center; color: white; font-size: 0.8rem; font-weight: 600;"></div>
                    <div style="font-size: 0.75rem; font-weight: 600; text-align: center;">إجمالي</div>
                </div>
                <div style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 10px;">
                    <div style="width: 100%; height: {{ ($statistics['paid_value'] / $maxValue) * 100 }}%; background: linear-gradient(180deg, #43e97b, #38f9d7); border-radius: 8px 8px 0 0; min-height: 30px;"></div>
                    <div style="font-size: 0.75rem; font-weight: 600; text-align: center;">مدفوع</div>
                </div>
                <div style="flex: 1; display: flex; flex-direction: column; align-items: center; gap: 10px;">
                    <div style="width: 100%; height: {{ ($statistics['retention_total'] / $maxValue) * 100 }}%; background: linear-gradient(180deg, #fa709a, #fee140); border-radius: 8px 8px 0 0; min-height: 30px;"></div>
                    <div style="font-size: 0.75rem; font-weight: 600; text-align: center;">استبقاء</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Table -->
    <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
        <div style="padding: 25px; border-bottom: 1px solid #e5e5e7; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 1.2rem; font-weight: 600;">جدول المستخلصات التفصيلي</h3>
            <button onclick="window.print()" style="padding: 10px 20px; background: var(--accent); color: white; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600; display: flex; align-items: center; gap: 8px;">
                <i data-lucide="printer" style="width: 16px; height: 16px;"></i>
                طباعة
            </button>
        </div>

        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                <thead>
                    <tr style="background: #f5f5f7;">
                        <th style="padding: 12px; text-align: right; font-weight: 600; border: 1px solid #e5e5e7;">رقم IPC</th>
                        <th style="padding: 12px; text-align: right; font-weight: 600; border: 1px solid #e5e5e7;">المشروع</th>
                        <th style="padding: 12px; text-align: right; font-weight: 600; border: 1px solid #e5e5e7;">التاريخ</th>
                        <th style="padding: 12px; text-align: right; font-weight: 600; border: 1px solid #e5e5e7;">القيمة</th>
                        <th style="padding: 12px; text-align: right; font-weight: 600; border: 1px solid #e5e5e7;">الاستبقاء</th>
                        <th style="padding: 12px; text-align: right; font-weight: 600; border: 1px solid #e5e5e7;">الصافي</th>
                        <th style="padding: 12px; text-align: right; font-weight: 600; border: 1px solid #e5e5e7;">الحالة</th>
                        <th style="padding: 12px; text-align: right; font-weight: 600; border: 1px solid #e5e5e7;">أيام المراجعة</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ipcs as $ipc)
                        <tr style="border-bottom: 1px solid #e5e5e7;">
                            <td style="padding: 10px; border: 1px solid #e5e5e7;">
                                <a href="{{ route('main-ipcs.show', $ipc) }}" style="color: var(--accent); font-weight: 600; text-decoration: none;">
                                    {{ $ipc->ipc_number }}
                                </a>
                            </td>
                            <td style="padding: 10px; border: 1px solid #e5e5e7;">{{ $ipc->project->name }}</td>
                            <td style="padding: 10px; border: 1px solid #e5e5e7;">{{ $ipc->submission_date->format('Y/m/d') }}</td>
                            <td style="padding: 10px; border: 1px solid #e5e5e7; font-weight: 600;">{{ number_format($ipc->current_cumulative, 2) }}</td>
                            <td style="padding: 10px; border: 1px solid #e5e5e7; color: #ff9500;">{{ number_format($ipc->retention_amount, 2) }}</td>
                            <td style="padding: 10px; border: 1px solid #e5e5e7; font-weight: 700; color: #34c759;">{{ number_format($ipc->net_payable, 2) }}</td>
                            <td style="padding: 10px; border: 1px solid #e5e5e7;">
                                <span style="padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 600;
                                    background: rgba({{ $ipc->status_badge['color'] === 'green' ? '52, 199, 89' : ($ipc->status_badge['color'] === 'blue' ? '0, 113, 227' : ($ipc->status_badge['color'] === 'yellow' ? '255, 204, 0' : '134, 134, 139')) }}, 0.1);
                                    color: {{ $ipc->status_badge['color'] === 'green' ? '#34c759' : ($ipc->status_badge['color'] === 'blue' ? '#0071e3' : ($ipc->status_badge['color'] === 'yellow' ? '#ffcc00' : '#86868b')) }};">
                                    {{ $ipc->status_badge['label'] }}
                                </span>
                            </td>
                            <td style="padding: 10px; border: 1px solid #e5e5e7;">
                                @if($ipc->consultant_review_days)
                                    استشاري: {{ $ipc->consultant_review_days }} يوم<br>
                                @endif
                                @if($ipc->client_review_days)
                                    عميل: {{ $ipc->client_review_days }} يوم
                                @endif
                                @if(!$ipc->consultant_review_days && !$ipc->client_review_days)
                                    <span style="color: #86868b;">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr style="background: #f5f5f7; font-weight: 700;">
                        <td colspan="3" style="padding: 12px; border: 1px solid #e5e5e7; text-align: right;">المجموع</td>
                        <td style="padding: 12px; border: 1px solid #e5e5e7;">{{ number_format($ipcs->sum('current_cumulative'), 2) }}</td>
                        <td style="padding: 12px; border: 1px solid #e5e5e7;">{{ number_format($ipcs->sum('retention_amount'), 2) }}</td>
                        <td style="padding: 12px; border: 1px solid #e5e5e7; color: #34c759;">{{ number_format($ipcs->sum('net_payable'), 2) }}</td>
                        <td colspan="2" style="padding: 12px; border: 1px solid #e5e5e7;"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
@endsection
