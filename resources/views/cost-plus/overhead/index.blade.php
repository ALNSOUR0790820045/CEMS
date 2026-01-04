@extends('layouts.app')

@section('content')
<div style="padding: 80px 20px 40px; max-width: 1400px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h1 style="font-size: 32px; font-weight: 700; margin-bottom: 10px;">المصاريف غير المباشرة</h1>
            <p style="color: #666; font-size: 16px;">توزيع التكاليف الإدارية على المشاريع</p>
        </div>
        <button onclick="document.getElementById('allocateModal').style.display='block'" style="background: var(--accent); color: white; padding: 12px 24px; border-radius: 8px; border: none; font-weight: 600; cursor: pointer; font-family: 'Cairo', sans-serif;">
            + توزيع مصاريف
        </button>
    </div>

    <div style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                    <th style="padding: 16px; text-align: right; font-weight: 600;">الفترة</th>
                    <th style="padding: 16px; text-align: right; font-weight: 600;">المشروع</th>
                    <th style="padding: 16px; text-align: right; font-weight: 600;">نوع المصروف</th>
                    <th style="padding: 16px; text-align: right; font-weight: 600;">الوصف</th>
                    <th style="padding: 16px; text-align: right; font-weight: 600;">الإجمالي</th>
                    <th style="padding: 16px; text-align: right; font-weight: 600;">نسبة التوزيع</th>
                    <th style="padding: 16px; text-align: right; font-weight: 600;">المبلغ الموزع</th>
                    <th style="padding: 16px; text-align: center; font-weight: 600;">قابل للاسترداد</th>
                </tr>
            </thead>
            <tbody>
                @forelse($allocations as $allocation)
                <tr style="border-bottom: 1px solid #dee2e6;">
                    <td style="padding: 16px;">{{ $allocation->year }}-{{ str_pad($allocation->month, 2, '0', STR_PAD_LEFT) }}</td>
                    <td style="padding: 16px;">{{ $allocation->project->name }}</td>
                    <td style="padding: 16px;">
                        @switch($allocation->overhead_type)
                            @case('admin_salaries') رواتب إدارية @break
                            @case('office_rent') إيجار مكتب @break
                            @case('utilities') مرافق @break
                            @case('insurance') تأمين @break
                            @case('depreciation') إهلاك @break
                            @case('other') أخرى @break
                        @endswitch
                    </td>
                    <td style="padding: 16px;">{{ $allocation->description }}</td>
                    <td style="padding: 16px; font-weight: 600;">{{ number_format($allocation->total_overhead, 2) }}</td>
                    <td style="padding: 16px; color: var(--accent); font-weight: 600;">{{ number_format($allocation->allocation_percentage, 2) }}%</td>
                    <td style="padding: 16px; font-weight: 700; color: #28a745;">{{ number_format($allocation->allocated_amount, 2) }}</td>
                    <td style="padding: 16px; text-align: center;">
                        @if($allocation->is_reimbursable)
                            <span style="color: #28a745; font-weight: 600;">✓ نعم</span>
                        @else
                            <span style="color: #dc3545; font-weight: 600;">✗ لا</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="padding: 40px; text-align: center; color: #666;">
                        لا توجد توزيعات للمصاريف غير المباشرة
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Allocate Modal -->
<div id="allocateModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 9999; padding: 20px; overflow-y: auto;">
    <div style="max-width: 700px; margin: 50px auto; background: white; border-radius: 12px; padding: 32px; position: relative;">
        <button onclick="document.getElementById('allocateModal').style.display='none'" style="position: absolute; top: 16px; left: 16px; background: none; border: none; font-size: 24px; cursor: pointer; color: #666;">×</button>
        
        <h2 style="margin-bottom: 24px; font-size: 24px; font-weight: 700;">توزيع مصاريف غير مباشرة</h2>
        
        <form action="{{ route('cost-plus.overhead.allocate') }}" method="POST">
            @csrf
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">السنة</label>
                    <input type="number" name="year" value="{{ date('Y') }}" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">الشهر</label>
                    <select name="month" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                        @for($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ date('m') == $i ? 'selected' : '' }}>{{ $i }}</option>
                        @endfor
                    </select>
                </div>
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">عقد Cost Plus</label>
                <select name="cost_plus_contract_id" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                    <option value="">اختر العقد</option>
                    @foreach($contracts as $contract)
                        <option value="{{ $contract->id }}">{{ $contract->contract->contract_number }} - {{ $contract->project->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">المشروع</label>
                <select name="project_id" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                    <option value="">اختر المشروع</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                    @endforeach
                </select>
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">نوع المصروف</label>
                <select name="overhead_type" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                    <option value="admin_salaries">رواتب إدارية</option>
                    <option value="office_rent">إيجار مكتب</option>
                    <option value="utilities">مرافق</option>
                    <option value="insurance">تأمين</option>
                    <option value="depreciation">إهلاك</option>
                    <option value="other">أخرى</option>
                </select>
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">الوصف</label>
                <input type="text" name="description" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">إجمالي المصروف</label>
                    <input type="number" name="total_overhead" step="0.01" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">نسبة التوزيع (%)</label>
                    <input type="number" name="allocation_percentage" step="0.01" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                </div>
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: 600;">المبلغ الموزع</label>
                <input type="number" name="allocated_amount" step="0.01" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: flex; align-items: center; cursor: pointer;">
                    <input type="checkbox" name="is_reimbursable" value="1" checked style="margin-left: 8px;">
                    <span style="font-weight: 600;">قابل للاسترداد</span>
                </label>
            </div>
            
            <button type="submit" style="width: 100%; background: var(--accent); color: white; padding: 14px; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-family: 'Cairo', sans-serif;">
                حفظ التوزيع
            </button>
        </form>
    </div>
</div>
@endsection
