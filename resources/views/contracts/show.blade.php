@extends('layouts.app')

@section('content')
<div style="max-width: 1400px; margin: 0 auto;">
    <!-- Header -->
    <div style="background: white; border-radius: 12px; padding: 30px; margin-bottom: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                    <a href="{{ route('contracts.index') }}" style="color: #0071e3; text-decoration: none; display: flex; align-items: center;">
                        <i data-lucide="arrow-right" style="width: 20px; height: 20px;"></i>
                    </a>
                    <h1 style="font-size: 28px; font-weight: 700; color: #1d1d1f; margin: 0;">{{ $contract->contract_code }}</h1>
                    @php
                        $statusColors = [
                            'draft' => '#86868b',
                            'under_negotiation' => '#f5a623',
                            'signed' => '#7ed321',
                            'active' => '#0071e3',
                            'on_hold' => '#f5a623',
                            'completed' => '#34c759',
                            'terminated' => '#ff3b30',
                            'closed' => '#1d1d1f'
                        ];
                        $statusLabels = [
                            'draft' => 'مسودة',
                            'under_negotiation' => 'قيد التفاوض',
                            'signed' => 'موقع',
                            'active' => 'نشط',
                            'on_hold' => 'معلق',
                            'completed' => 'مكتمل',
                            'terminated' => 'منتهي',
                            'closed' => 'مغلق'
                        ];
                    @endphp
                    <span style="display: inline-block; padding: 6px 16px; border-radius: 12px; font-size: 13px; font-weight: 600; color: white; background: {{ $statusColors[$contract->contract_status] ?? '#86868b' }};">
                        {{ $statusLabels[$contract->contract_status] ?? $contract->contract_status }}
                    </span>
                </div>
                <p style="color: #86868b; margin: 0; font-size: 16px;">{{ $contract->contract_title }}</p>
            </div>
            <div style="display: flex; gap: 12px;">
                @if(in_array($contract->contract_status, ['draft', 'under_negotiation']))
                <a href="{{ route('contracts.edit', $contract) }}" style="background: #34c759; color: white; text-decoration: none; padding: 12px 24px; border-radius: 10px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
                    <i data-lucide="edit" style="width: 18px; height: 18px;"></i>
                    تعديل
                </a>
                @endif
                <form method="POST" action="{{ route('contracts.clone', $contract) }}" style="display: inline;">
                    @csrf
                    <button type="submit" style="background: #f5f5f7; color: #1d1d1f; border: none; padding: 12px 24px; border-radius: 10px; font-weight: 600; cursor: pointer; display: inline-flex; align-items: center; gap: 8px;">
                        <i data-lucide="copy" style="width: 18px; height: 18px;"></i>
                        نسخ
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Overview Cards -->
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px;">
        <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <div style="color: #86868b; font-size: 13px; margin-bottom: 8px;">القيمة الأصلية</div>
            <div style="font-size: 24px; font-weight: 700; color: #1d1d1f;">{{ number_format($contract->original_contract_value, 2) }}</div>
            <div style="color: #86868b; font-size: 12px; margin-top: 4px;">{{ $contract->currency->code }}</div>
        </div>

        <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <div style="color: #86868b; font-size: 13px; margin-bottom: 8px;">القيمة الحالية</div>
            <div style="font-size: 24px; font-weight: 700; color: #0071e3;">{{ number_format($contract->current_contract_value, 2) }}</div>
            <div style="color: #86868b; font-size: 12px; margin-top: 4px;">{{ $contract->currency->code }}</div>
        </div>

        <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <div style="color: #86868b; font-size: 13px; margin-bottom: 8px;">التغييرات</div>
            <div style="font-size: 24px; font-weight: 700; color: {{ $contract->total_change_orders_value >= 0 ? '#34c759' : '#ff3b30' }};">
                {{ $contract->total_change_orders_value >= 0 ? '+' : '' }}{{ number_format($contract->total_change_orders_value, 2) }}
            </div>
            <div style="color: #86868b; font-size: 12px; margin-top: 4px;">{{ $contract->changeOrders->count() }} أمر تغيير</div>
        </div>

        <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
            <div style="color: #86868b; font-size: 13px; margin-bottom: 8px;">الأيام المتبقية</div>
            <div style="font-size: 24px; font-weight: 700; color: #1d1d1f;">{{ $contract->days_remaining ?? 0 }}</div>
            <div style="color: #86868b; font-size: 12px; margin-top: 4px;">من {{ $contract->contract_duration_days }} يوم</div>
        </div>
    </div>

    <!-- Main Content -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
        <!-- Left Column - Details -->
        <div>
            <!-- Contract Details -->
            <div style="background: white; border-radius: 12px; padding: 30px; margin-bottom: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <h2 style="font-size: 20px; font-weight: 700; color: #1d1d1f; margin: 0 0 24px 0;">تفاصيل العقد</h2>
                
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px;">
                    <div>
                        <label style="display: block; font-size: 13px; color: #86868b; margin-bottom: 6px;">رقم العقد</label>
                        <div style="font-size: 15px; font-weight: 600; color: #1d1d1f;">{{ $contract->contract_number }}</div>
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; color: #86868b; margin-bottom: 6px;">العميل</label>
                        <div style="font-size: 15px; font-weight: 600; color: #1d1d1f;">{{ $contract->client->name }}</div>
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; color: #86868b; margin-bottom: 6px;">نوع العقد</label>
                        <div style="font-size: 15px; font-weight: 600; color: #1d1d1f;">{{ ucfirst(str_replace('_', ' ', $contract->contract_type)) }}</div>
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; color: #86868b; margin-bottom: 6px;">فئة العقد</label>
                        <div style="font-size: 15px; font-weight: 600; color: #1d1d1f;">{{ ucfirst(str_replace('_', ' ', $contract->contract_category)) }}</div>
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; color: #86868b; margin-bottom: 6px;">تاريخ التوقيع</label>
                        <div style="font-size: 15px; font-weight: 600; color: #1d1d1f;">{{ $contract->signing_date->format('Y-m-d') }}</div>
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; color: #86868b; margin-bottom: 6px;">تاريخ البدء</label>
                        <div style="font-size: 15px; font-weight: 600; color: #1d1d1f;">{{ $contract->commencement_date->format('Y-m-d') }}</div>
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; color: #86868b; margin-bottom: 6px;">تاريخ الانتهاء</label>
                        <div style="font-size: 15px; font-weight: 600; color: #1d1d1f;">{{ $contract->completion_date->format('Y-m-d') }}</div>
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; color: #86868b; margin-bottom: 6px;">مدة العقد</label>
                        <div style="font-size: 15px; font-weight: 600; color: #1d1d1f;">{{ $contract->contract_duration_days }} يوم</div>
                    </div>

                    <div>
                        <label style="display: block; font-size: 13px; color: #86868b; margin-bottom: 6px;">نسبة الاستبقاء</label>
                        <div style="font-size: 15px; font-weight: 600; color: #1d1d1f;">{{ $contract->retention_percentage }}%</div>
                    </div>

                    @if($contract->advance_payment_percentage)
                    <div>
                        <label style="display: block; font-size: 13px; color: #86868b; margin-bottom: 6px;">الدفعة المقدمة</label>
                        <div style="font-size: 15px; font-weight: 600; color: #1d1d1f;">{{ $contract->advance_payment_percentage }}%</div>
                    </div>
                    @endif
                </div>

                @if($contract->scope_of_work)
                <div style="margin-top: 24px; padding-top: 24px; border-top: 1px solid #e5e5ea;">
                    <label style="display: block; font-size: 13px; color: #86868b; margin-bottom: 8px;">نطاق العمل</label>
                    <div style="font-size: 14px; color: #1d1d1f; line-height: 1.6;">{{ $contract->scope_of_work }}</div>
                </div>
                @endif
            </div>

            <!-- Change Orders -->
            <div style="background: white; border-radius: 12px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 style="font-size: 20px; font-weight: 700; color: #1d1d1f; margin: 0;">أوامر التغيير</h2>
                    <span style="background: #f5f5f7; color: #1d1d1f; padding: 4px 12px; border-radius: 12px; font-size: 13px; font-weight: 600;">
                        {{ $contract->changeOrders->count() }}
                    </span>
                </div>

                @if($contract->changeOrders->count() > 0)
                    <div style="space-y: 12px;">
                        @foreach($contract->changeOrders->take(5) as $changeOrder)
                        <div style="padding: 16px; background: #f9f9f9; border-radius: 8px; margin-bottom: 12px;">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 8px;">
                                <div>
                                    <div style="font-weight: 600; color: #1d1d1f; font-size: 15px;">{{ $changeOrder->title }}</div>
                                    <div style="font-size: 13px; color: #86868b; margin-top: 4px;">{{ $changeOrder->change_order_code }}</div>
                                </div>
                                <span style="padding: 4px 10px; border-radius: 8px; font-size: 12px; font-weight: 600; color: white; background: {{ $changeOrder->status == 'approved' ? '#34c759' : ($changeOrder->status == 'rejected' ? '#ff3b30' : '#f5a623') }};">
                                    {{ $changeOrder->status }}
                                </span>
                            </div>
                            <div style="display: flex; gap: 20px; font-size: 13px; color: #86868b;">
                                <div>قيمة التغيير: <strong style="color: {{ $changeOrder->value_change >= 0 ? '#34c759' : '#ff3b30' }};">{{ $changeOrder->value_change >= 0 ? '+' : '' }}{{ number_format($changeOrder->value_change, 2) }}</strong></div>
                                <div>الأيام: <strong>{{ $changeOrder->days_change }}</strong></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p style="text-align: center; color: #86868b; padding: 30px 0;">لا توجد أوامر تغيير</p>
                @endif
            </div>
        </div>

        <!-- Right Column - Info Cards -->
        <div>
            <!-- Management -->
            <div style="background: white; border-radius: 12px; padding: 24px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <h3 style="font-size: 16px; font-weight: 700; color: #1d1d1f; margin: 0 0 16px 0;">الإدارة</h3>
                
                <div style="margin-bottom: 16px;">
                    <label style="display: block; font-size: 13px; color: #86868b; margin-bottom: 6px;">مدير العقد</label>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #0071e3, #00c4cc); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 14px;">
                            {{ $contract->contractManager->initials }}
                        </div>
                        <div>
                            <div style="font-weight: 600; color: #1d1d1f; font-size: 14px;">{{ $contract->contractManager->name }}</div>
                            <div style="font-size: 12px; color: #86868b;">{{ $contract->contractManager->email }}</div>
                        </div>
                    </div>
                </div>

                @if($contract->projectManager)
                <div>
                    <label style="display: block; font-size: 13px; color: #86868b; margin-bottom: 6px;">مدير المشروع</label>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <div style="width: 36px; height: 36px; border-radius: 50%; background: linear-gradient(135deg, #667eea, #764ba2); display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 14px;">
                            {{ $contract->projectManager->initials }}
                        </div>
                        <div>
                            <div style="font-weight: 600; color: #1d1d1f; font-size: 14px;">{{ $contract->projectManager->name }}</div>
                            <div style="font-size: 12px; color: #86868b;">{{ $contract->projectManager->email }}</div>
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Milestones Summary -->
            <div style="background: white; border-radius: 12px; padding: 24px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <h3 style="font-size: 16px; font-weight: 700; color: #1d1d1f; margin: 0 0 16px 0;">المعالم</h3>
                
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                    <span style="font-size: 13px; color: #86868b;">الإجمالي</span>
                    <span style="font-size: 15px; font-weight: 700; color: #1d1d1f;">{{ $contract->milestones->count() }}</span>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                    <span style="font-size: 13px; color: #86868b;">مكتمل</span>
                    <span style="font-size: 15px; font-weight: 700; color: #34c759;">{{ $contract->milestones->where('status', 'completed')->count() }}</span>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 13px; color: #86868b;">قيد التنفيذ</span>
                    <span style="font-size: 15px; font-weight: 700; color: #0071e3;">{{ $contract->milestones->where('status', 'in_progress')->count() }}</span>
                </div>
            </div>

            <!-- Documents -->
            @if($contract->attachment_path)
            <div style="background: white; border-radius: 12px; padding: 24px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <h3 style="font-size: 16px; font-weight: 700; color: #1d1d1f; margin: 0 0 16px 0;">المرفقات</h3>
                
                <a href="{{ Storage::url($contract->attachment_path) }}" target="_blank" style="display: flex; align-items: center; gap: 12px; padding: 12px; background: #f9f9f9; border-radius: 8px; text-decoration: none; transition: all 0.2s;">
                    <div style="width: 40px; height: 40px; background: #0071e3; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <i data-lucide="file-text" style="width: 20px; height: 20px; color: white;"></i>
                    </div>
                    <div style="flex: 1;">
                        <div style="font-size: 14px; font-weight: 600; color: #1d1d1f;">ملف العقد</div>
                        <div style="font-size: 12px; color: #86868b;">PDF</div>
                    </div>
                    <i data-lucide="download" style="width: 18px; height: 18px; color: #0071e3;"></i>
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
    lucide.createIcons();
</script>
@endsection
