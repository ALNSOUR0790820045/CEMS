@extends('layouts.app')

@section('content')
<div style="max-width: 1400px; margin: 0 auto;">
    <!-- Page Header -->
    <div style="background: white; border-radius: 12px; padding: 30px; margin-bottom: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h1 style="font-size: 28px; font-weight: 700; color: #1d1d1f; margin: 0 0 8px 0;">إدارة العقود</h1>
                <p style="color: #86868b; margin: 0;">إدارة عقود المشاريع والتعديلات والمراجعات</p>
            </div>
            <a href="{{ route('contracts.create') }}" style="background: linear-gradient(135deg, #0071e3, #0077ed); color: white; text-decoration: none; padding: 14px 28px; border-radius: 10px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px; transition: all 0.2s; box-shadow: 0 4px 12px rgba(0, 113, 227, 0.3);">
                <i data-lucide="plus" style="width: 20px; height: 20px;"></i>
                عقد جديد
            </a>
        </div>
    </div>

    <!-- KPI Cards -->
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px;">
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 12px; padding: 24px; color: white; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                <div style="background: rgba(255,255,255,0.2); padding: 10px; border-radius: 8px;">
                    <i data-lucide="file-text" style="width: 24px; height: 24px;"></i>
                </div>
                <span style="font-size: 14px; opacity: 0.9;">العقود النشطة</span>
            </div>
            <div style="font-size: 32px; font-weight: 700;">{{ $activeContracts }}</div>
        </div>

        <div style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border-radius: 12px; padding: 24px; color: white; box-shadow: 0 4px 12px rgba(240, 147, 251, 0.3);">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                <div style="background: rgba(255,255,255,0.2); padding: 10px; border-radius: 8px;">
                    <i data-lucide="dollar-sign" style="width: 24px; height: 24px;"></i>
                </div>
                <span style="font-size: 14px; opacity: 0.9;">إجمالي قيمة العقود</span>
            </div>
            <div style="font-size: 28px; font-weight: 700;">{{ number_format($totalContractValue, 0) }}</div>
        </div>

        <div style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); border-radius: 12px; padding: 24px; color: white; box-shadow: 0 4px 12px rgba(79, 172, 254, 0.3);">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                <div style="background: rgba(255,255,255,0.2); padding: 10px; border-radius: 8px;">
                    <i data-lucide="file-edit" style="width: 24px; height: 24px;"></i>
                </div>
                <span style="font-size: 14px; opacity: 0.9;">أوامر التغيير المعلقة</span>
            </div>
            <div style="font-size: 32px; font-weight: 700;">{{ $pendingChangeOrders }}</div>
        </div>

        <div style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); border-radius: 12px; padding: 24px; color: white; box-shadow: 0 4px 12px rgba(250, 112, 154, 0.3);">
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;">
                <div style="background: rgba(255,255,255,0.2); padding: 10px; border-radius: 8px;">
                    <i data-lucide="clock" style="width: 24px; height: 24px;"></i>
                </div>
                <span style="font-size: 14px; opacity: 0.9;">عقود قريبة الانتهاء</span>
            </div>
            <div style="font-size: 32px; font-weight: 700;">{{ $expiringSoon }}</div>
        </div>
    </div>

    <!-- Filters and Table -->
    <div style="background: white; border-radius: 12px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
        <!-- Filters -->
        <form method="GET" style="margin-bottom: 24px;">
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px;">
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 600; color: #1d1d1f; margin-bottom: 8px;">البحث</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="رقم العقد أو العنوان..." style="width: 100%; padding: 10px 14px; border: 1px solid #d2d2d7; border-radius: 8px; font-size: 14px;">
                </div>
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 600; color: #1d1d1f; margin-bottom: 8px;">الحالة</label>
                    <select name="status" style="width: 100%; padding: 10px 14px; border: 1px solid #d2d2d7; border-radius: 8px; font-size: 14px;">
                        <option value="">الكل</option>
                        <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                        <option value="under_negotiation" {{ request('status') == 'under_negotiation' ? 'selected' : '' }}>قيد التفاوض</option>
                        <option value="signed" {{ request('status') == 'signed' ? 'selected' : '' }}>موقع</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; font-size: 14px; font-weight: 600; color: #1d1d1f; margin-bottom: 8px;">نوع العقد</label>
                    <select name="contract_type" style="width: 100%; padding: 10px 14px; border: 1px solid #d2d2d7; border-radius: 8px; font-size: 14px;">
                        <option value="">الكل</option>
                        <option value="lump_sum" {{ request('contract_type') == 'lump_sum' ? 'selected' : '' }}>مبلغ إجمالي</option>
                        <option value="unit_price" {{ request('contract_type') == 'unit_price' ? 'selected' : '' }}>سعر الوحدة</option>
                        <option value="cost_plus" {{ request('contract_type') == 'cost_plus' ? 'selected' : '' }}>التكلفة الإضافية</option>
                    </select>
                </div>
                <div style="display: flex; align-items: end; gap: 8px;">
                    <button type="submit" style="flex: 1; background: #0071e3; color: white; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer;">
                        <i data-lucide="search" style="width: 16px; height: 16px; display: inline-block; vertical-align: middle;"></i>
                        بحث
                    </button>
                    <a href="{{ route('contracts.index') }}" style="background: #f5f5f7; color: #1d1d1f; border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-block;">
                        إعادة تعيين
                    </a>
                </div>
            </div>
        </form>

        <!-- Contracts Table -->
        @if($contracts->count() > 0)
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background: #f5f5f7; border-bottom: 2px solid #e5e5ea;">
                        <th style="padding: 14px; text-align: right; font-size: 13px; font-weight: 600; color: #1d1d1f;">رمز العقد</th>
                        <th style="padding: 14px; text-align: right; font-size: 13px; font-weight: 600; color: #1d1d1f;">رقم العقد</th>
                        <th style="padding: 14px; text-align: right; font-size: 13px; font-weight: 600; color: #1d1d1f;">العنوان</th>
                        <th style="padding: 14px; text-align: right; font-size: 13px; font-weight: 600; color: #1d1d1f;">العميل</th>
                        <th style="padding: 14px; text-align: right; font-size: 13px; font-weight: 600; color: #1d1d1f;">القيمة الحالية</th>
                        <th style="padding: 14px; text-align: right; font-size: 13px; font-weight: 600; color: #1d1d1f;">تاريخ البدء</th>
                        <th style="padding: 14px; text-align: right; font-size: 13px; font-weight: 600; color: #1d1d1f;">تاريخ الانتهاء</th>
                        <th style="padding: 14px; text-align: right; font-size: 13px; font-weight: 600; color: #1d1d1f;">الحالة</th>
                        <th style="padding: 14px; text-align: center; font-size: 13px; font-weight: 600; color: #1d1d1f;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($contracts as $contract)
                    <tr style="border-bottom: 1px solid #e5e5ea; transition: background 0.2s;" onmouseover="this.style.background='#f9f9f9'" onmouseout="this.style.background='white'">
                        <td style="padding: 14px; font-size: 14px; font-weight: 600; color: #0071e3;">{{ $contract->contract_code }}</td>
                        <td style="padding: 14px; font-size: 14px; color: #1d1d1f;">{{ $contract->contract_number }}</td>
                        <td style="padding: 14px; font-size: 14px; color: #1d1d1f;">{{ $contract->contract_title }}</td>
                        <td style="padding: 14px; font-size: 14px; color: #1d1d1f;">{{ $contract->client->name }}</td>
                        <td style="padding: 14px; font-size: 14px; font-weight: 600; color: #1d1d1f;">{{ number_format($contract->current_contract_value, 2) }} {{ $contract->currency->code }}</td>
                        <td style="padding: 14px; font-size: 14px; color: #86868b;">{{ $contract->commencement_date->format('Y-m-d') }}</td>
                        <td style="padding: 14px; font-size: 14px; color: #86868b;">{{ $contract->completion_date->format('Y-m-d') }}</td>
                        <td style="padding: 14px;">
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
                            <span style="display: inline-block; padding: 4px 12px; border-radius: 12px; font-size: 12px; font-weight: 600; color: white; background: {{ $statusColors[$contract->contract_status] ?? '#86868b' }};">
                                {{ $statusLabels[$contract->contract_status] ?? $contract->contract_status }}
                            </span>
                        </td>
                        <td style="padding: 14px; text-align: center;">
                            <div style="display: flex; gap: 8px; justify-content: center;">
                                <a href="{{ route('contracts.show', $contract) }}" style="background: #0071e3; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 13px; display: inline-flex; align-items: center; gap: 4px;">
                                    <i data-lucide="eye" style="width: 14px; height: 14px;"></i>
                                    عرض
                                </a>
                                @if(in_array($contract->contract_status, ['draft', 'under_negotiation']))
                                <a href="{{ route('contracts.edit', $contract) }}" style="background: #34c759; color: white; padding: 6px 12px; border-radius: 6px; text-decoration: none; font-size: 13px; display: inline-flex; align-items: center; gap: 4px;">
                                    <i data-lucide="edit" style="width: 14px; height: 14px;"></i>
                                    تعديل
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div style="margin-top: 24px; display: flex; justify-content: center;">
            {{ $contracts->links() }}
        </div>
        @else
        <div style="text-align: center; padding: 60px 20px;">
            <i data-lucide="inbox" style="width: 64px; height: 64px; color: #d2d2d7; margin-bottom: 16px;"></i>
            <p style="color: #86868b; font-size: 16px; margin: 0;">لا توجد عقود حالياً</p>
            <a href="{{ route('contracts.create') }}" style="display: inline-block; margin-top: 16px; background: #0071e3; color: white; padding: 10px 24px; border-radius: 8px; text-decoration: none; font-weight: 600;">
                إضافة عقد جديد
            </a>
        </div>
        @endif
    </div>
</div>

<script>
    lucide.createIcons();
</script>
@endsection
