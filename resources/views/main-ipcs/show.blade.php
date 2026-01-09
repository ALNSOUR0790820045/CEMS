@extends('layouts.app')

@section('content')
<div style="max-width: 1400px; margin: 0 auto;">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h1 style="font-size: 2rem; font-weight: 700; margin-bottom: 5px;">{{ $mainIpc->ipc_number }}</h1>
            <p style="color: #86868b; font-size: 0.9rem;">{{ $mainIpc->project->name }}</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('main-ipcs.index') }}" style="padding: 10px 20px; background: #f5f5f7; color: #1d1d1f; border-radius: 8px; text-decoration: none; font-weight: 600;">
                رجوع
            </a>
            @if($mainIpc->status === 'draft')
                <form method="POST" action="{{ route('main-ipcs.submit', $mainIpc) }}" style="display: inline;">
                    @csrf
                    <button type="submit" style="padding: 10px 20px; background: var(--accent); color: white; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">
                        إرسال للموافقة
                    </button>
                </form>
            @endif
            @if(in_array($mainIpc->status, ['pending_pm', 'pending_technical', 'pending_consultant', 'pending_client', 'pending_finance']))
                <a href="{{ route('main-ipcs.approve', $mainIpc) }}" style="padding: 10px 20px; background: #34c759; color: white; border-radius: 8px; text-decoration: none; font-weight: 600;">
                    مراجعة والموافقة
                </a>
            @endif
            @if($mainIpc->status === 'approved_for_payment')
                <a href="{{ route('main-ipcs.payment', $mainIpc) }}" style="padding: 10px 20px; background: #ff9500; color: white; border-radius: 8px; text-decoration: none; font-weight: 600;">
                    تسجيل الدفع
                </a>
            @endif
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
        <!-- Main Content -->
        <div>
            <!-- Approval Chain (6 Stages) -->
            <div style="background: white; padding: 25px; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                <h3 style="font-size: 1.2rem; font-weight: 600; margin-bottom: 20px;">سلسلة الموافقات</h3>
                
                <div style="position: relative;">
                    @php
                        $stages = [
                            ['id' => 1, 'status' => 'pm', 'label' => 'مدير المشروع', 'user' => $mainIpc->pmPreparer, 'at' => $mainIpc->pm_prepared_at, 'active' => in_array($mainIpc->status, ['draft', 'pending_pm'])],
                            ['id' => 2, 'status' => 'technical', 'label' => 'المدير الفني', 'user' => $mainIpc->technicalReviewer, 'at' => $mainIpc->technical_reviewed_at, 'decision' => $mainIpc->technical_decision, 'active' => $mainIpc->status === 'pending_technical'],
                            ['id' => 3, 'status' => 'consultant', 'label' => 'الاستشاري', 'user' => $mainIpc->consultantReviewer, 'at' => $mainIpc->consultant_reviewed_at, 'decision' => $mainIpc->consultant_decision, 'due' => $mainIpc->consultant_due_date, 'days' => $mainIpc->consultant_days_remaining, 'active' => $mainIpc->status === 'pending_consultant'],
                            ['id' => 4, 'status' => 'client', 'label' => 'العميل', 'user' => $mainIpc->clientApprover, 'at' => $mainIpc->client_approved_at, 'decision' => $mainIpc->client_decision, 'due' => $mainIpc->client_due_date, 'days' => $mainIpc->client_days_remaining, 'active' => $mainIpc->status === 'pending_client'],
                            ['id' => 5, 'status' => 'finance', 'label' => 'المالية', 'user' => $mainIpc->financeReviewer, 'at' => $mainIpc->finance_reviewed_at, 'decision' => $mainIpc->finance_decision, 'active' => $mainIpc->status === 'pending_finance'],
                            ['id' => 6, 'status' => 'payment', 'label' => 'الدفع', 'user' => $mainIpc->paidByUser, 'at' => $mainIpc->payment_date, 'active' => $mainIpc->status === 'approved_for_payment'],
                        ];
                    @endphp

                    @foreach($stages as $stage)
                        <div style="display: flex; gap: 15px; margin-bottom: {{ $loop->last ? '0' : '25px' }}; position: relative;">
                            <!-- Icon -->
                            <div style="position: relative; z-index: 2;">
                                <div style="width: 50px; height: 50px; border-radius: 50%; 
                                    background: {{ $stage['user'] ? '#34c759' : ($stage['active'] ? '#0071e3' : '#e5e5e7') }};
                                    display: flex; align-items: center; justify-content: center; color: white;">
                                    @if($stage['user'])
                                        <i data-lucide="check" style="width: 24px; height: 24px;"></i>
                                    @elseif($stage['active'])
                                        <i data-lucide="clock" style="width: 24px; height: 24px;"></i>
                                    @else
                                        <i data-lucide="circle" style="width: 24px; height: 24px;"></i>
                                    @endif
                                </div>
                                @if(!$loop->last)
                                    <div style="position: absolute; top: 50px; left: 50%; transform: translateX(-50%); width: 2px; height: 25px; background: #e5e5e7;"></div>
                                @endif
                            </div>

                            <!-- Content -->
                            <div style="flex: 1; padding: 10px 0;">
                                <div style="font-weight: 600; margin-bottom: 5px;">{{ $stage['id'] }}. {{ $stage['label'] }}</div>
                                
                                @if($stage['user'])
                                    <div style="color: #34c759; font-size: 0.85rem; margin-bottom: 3px;">
                                        ✓ تمت الموافقة
                                    </div>
                                    <div style="color: #86868b; font-size: 0.8rem;">
                                        بواسطة: {{ $stage['user']->name }}
                                        @if($stage['at'])
                                            | {{ $stage['at']->format('Y/m/d H:i') }}
                                        @endif
                                    </div>
                                @elseif($stage['active'])
                                    <div style="color: #0071e3; font-size: 0.85rem; margin-bottom: 3px;">
                                        ⏳ معلق - قيد المراجعة
                                    </div>
                                    @if(isset($stage['due']))
                                        <div style="color: {{ $stage['days'] < 0 ? '#ff3b30' : '#86868b' }}; font-size: 0.8rem;">
                                            @if($stage['days'] !== null)
                                                @if($stage['days'] > 0)
                                                    متبقي {{ $stage['days'] }} يوم
                                                @elseif($stage['days'] === 0)
                                                    آخر يوم
                                                @else
                                                    متأخر {{ abs($stage['days']) }} يوم
                                                @endif
                                                | تاريخ الاستحقاق: {{ $stage['due']->format('Y/m/d') }}
                                            @endif
                                        </div>
                                    @endif
                                @else
                                    <div style="color: #86868b; font-size: 0.85rem;">
                                        معلق
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- IPC Items -->
            <div style="background: white; padding: 25px; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                <h3 style="font-size: 1.2rem; font-weight: 600; margin-bottom: 20px;">بنود الأعمال</h3>
                
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                        <thead>
                            <tr style="background: #f5f5f7;">
                                <th style="padding: 10px; text-align: right; font-weight: 600; border: 1px solid #e5e5e7;">كود</th>
                                <th style="padding: 10px; text-align: right; font-weight: 600; border: 1px solid #e5e5e7;">الوصف</th>
                                <th style="padding: 10px; text-align: right; font-weight: 600; border: 1px solid #e5e5e7;">الوحدة</th>
                                <th style="padding: 10px; text-align: right; font-weight: 600; border: 1px solid #e5e5e7;">الكمية التعاقدية</th>
                                <th style="padding: 10px; text-align: right; font-weight: 600; border: 1px solid #e5e5e7;">السابقة</th>
                                <th style="padding: 10px; text-align: right; font-weight: 600; border: 1px solid #e5e5e7;">الحالية</th>
                                <th style="padding: 10px; text-align: right; font-weight: 600; border: 1px solid #e5e5e7;">التراكمية</th>
                                <th style="padding: 10px; text-align: right; font-weight: 600; border: 1px solid #e5e5e7;">سعر الوحدة</th>
                                <th style="padding: 10px; text-align: right; font-weight: 600; border: 1px solid #e5e5e7;">القيمة الحالية</th>
                                <th style="padding: 10px; text-align: right; font-weight: 600; border: 1px solid #e5e5e7;">% الإنجاز</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($mainIpc->items as $item)
                                <tr style="border-bottom: 1px solid #e5e5e7;">
                                    <td style="padding: 10px; border: 1px solid #e5e5e7;">{{ $item->item_code }}</td>
                                    <td style="padding: 10px; border: 1px solid #e5e5e7;">{{ $item->description }}</td>
                                    <td style="padding: 10px; border: 1px solid #e5e5e7;">{{ $item->unit }}</td>
                                    <td style="padding: 10px; border: 1px solid #e5e5e7;">{{ number_format($item->contract_quantity, 3) }}</td>
                                    <td style="padding: 10px; border: 1px solid #e5e5e7;">{{ number_format($item->previous_quantity, 3) }}</td>
                                    <td style="padding: 10px; border: 1px solid #e5e5e7; font-weight: 600;">{{ number_format($item->current_quantity, 3) }}</td>
                                    <td style="padding: 10px; border: 1px solid #e5e5e7;">{{ number_format($item->cumulative_quantity, 3) }}</td>
                                    <td style="padding: 10px; border: 1px solid #e5e5e7;">{{ number_format($item->unit_price, 2) }}</td>
                                    <td style="padding: 10px; border: 1px solid #e5e5e7; font-weight: 600;">{{ number_format($item->current_amount, 2) }}</td>
                                    <td style="padding: 10px; border: 1px solid #e5e5e7;">
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <div style="flex: 1; height: 6px; background: #e5e5e7; border-radius: 3px; overflow: hidden;">
                                                <div style="width: {{ $item->completion_percent }}%; height: 100%; background: #34c759;"></div>
                                            </div>
                                            <span>{{ number_format($item->completion_percent, 1) }}%</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Status Card -->
            <div style="background: white; padding: 20px; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                <h4 style="font-size: 0.9rem; color: #86868b; margin-bottom: 15px;">الحالة</h4>
                <span style="padding: 8px 16px; border-radius: 8px; font-size: 0.85rem; font-weight: 600; display: inline-block;
                    background: rgba({{ $mainIpc->status_badge['color'] === 'green' ? '52, 199, 89' : ($mainIpc->status_badge['color'] === 'blue' ? '0, 113, 227' : ($mainIpc->status_badge['color'] === 'yellow' ? '255, 204, 0' : ($mainIpc->status_badge['color'] === 'red' ? '255, 59, 48' : '134, 134, 139'))) }}, 0.1);
                    color: {{ $mainIpc->status_badge['color'] === 'green' ? '#34c759' : ($mainIpc->status_badge['color'] === 'blue' ? '#0071e3' : ($mainIpc->status_badge['color'] === 'yellow' ? '#ffcc00' : ($mainIpc->status_badge['color'] === 'red' ? '#ff3b30' : '#86868b'))) }};">
                    {{ $mainIpc->status_badge['label'] }}
                </span>

                <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #e5e5e7;">
                    <div style="font-size: 0.8rem; color: #86868b; margin-bottom: 5px;">التقدم</div>
                    <div style="height: 8px; background: #e5e5e7; border-radius: 4px; overflow: hidden; margin-bottom: 8px;">
                        <div style="width: {{ $mainIpc->approval_progress }}%; height: 100%; background: linear-gradient(90deg, #0071e3, #00c4cc);"></div>
                    </div>
                    <div style="font-size: 0.85rem; font-weight: 600;">{{ $mainIpc->approval_progress }}% مكتمل</div>
                </div>
            </div>

            <!-- Financial Summary -->
            <div style="background: white; padding: 20px; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                <h4 style="font-size: 0.9rem; color: #86868b; margin-bottom: 15px;">الملخص المالي</h4>
                
                <div style="margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #e5e5e7;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                        <span style="color: #86868b; font-size: 0.85rem;">التراكمي السابق</span>
                        <span style="font-weight: 600;">{{ number_format($mainIpc->previous_cumulative, 2) }} ر.س</span>
                    </div>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                        <span style="color: #86868b; font-size: 0.85rem;">الأعمال الحالية</span>
                        <span style="font-weight: 600; color: var(--accent);">{{ number_format($mainIpc->current_period_work, 2) }} ر.س</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span style="font-weight: 600;">التراكمي الحالي</span>
                        <span style="font-weight: 700;">{{ number_format($mainIpc->current_cumulative, 2) }} ر.س</span>
                    </div>
                </div>

                <div style="margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid #e5e5e7;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                        <span style="color: #86868b; font-size: 0.85rem;">الاستبقاء ({{ $mainIpc->retention_percent }}%)</span>
                        <span style="color: #ff9500;">-{{ number_format($mainIpc->retention_amount, 2) }} ر.س</span>
                    </div>
                    @if($mainIpc->advance_payment_deduction > 0)
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                            <span style="color: #86868b; font-size: 0.85rem;">استرداد دفعة مقدمة</span>
                            <span style="color: #ff9500;">-{{ number_format($mainIpc->advance_payment_deduction, 2) }} ر.س</span>
                        </div>
                    @endif
                    @if($mainIpc->other_deductions > 0)
                        <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                            <span style="color: #86868b; font-size: 0.85rem;">خصومات أخرى</span>
                            <span style="color: #ff9500;">-{{ number_format($mainIpc->other_deductions, 2) }} ر.س</span>
                        </div>
                    @endif
                    <div style="display: flex; justify-content: space-between;">
                        <span style="color: #86868b; font-size: 0.85rem;">الضريبة ({{ $mainIpc->tax_rate }}%)</span>
                        <span style="color: #34c759;">+{{ number_format($mainIpc->tax_amount, 2) }} ر.س</span>
                    </div>
                </div>

                <div style="background: #f5f5f7; padding: 15px; border-radius: 8px;">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-weight: 600; font-size: 1.1rem;">الصافي للدفع</span>
                        <span style="font-weight: 700; font-size: 1.4rem; color: #34c759;">{{ number_format($mainIpc->net_payable, 2) }} ر.س</span>
                    </div>
                </div>
            </div>

            <!-- Period Info -->
            <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                <h4 style="font-size: 0.9rem; color: #86868b; margin-bottom: 15px;">معلومات الفترة</h4>
                
                <div style="margin-bottom: 12px;">
                    <div style="color: #86868b; font-size: 0.8rem; margin-bottom: 3px;">من تاريخ</div>
                    <div style="font-weight: 600;">{{ $mainIpc->period_from->format('Y/m/d') }}</div>
                </div>
                
                <div style="margin-bottom: 12px;">
                    <div style="color: #86868b; font-size: 0.8rem; margin-bottom: 3px;">إلى تاريخ</div>
                    <div style="font-weight: 600;">{{ $mainIpc->period_to->format('Y/m/d') }}</div>
                </div>
                
                <div>
                    <div style="color: #86868b; font-size: 0.8rem; margin-bottom: 3px;">تاريخ التقديم</div>
                    <div style="font-weight: 600;">{{ $mainIpc->submission_date->format('Y/m/d') }}</div>
                </div>
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
