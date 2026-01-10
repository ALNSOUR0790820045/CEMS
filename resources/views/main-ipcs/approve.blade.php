@extends('layouts.app')

@section('content')
<div style="max-width: 1200px; margin: 0 auto;">
    <div style="margin-bottom: 30px;">
        <h1 style="font-size: 2rem; font-weight: 700; margin-bottom: 5px;">مراجعة والموافقة</h1>
        <p style="color: #86868b; font-size: 0.9rem;">{{ $mainIpc->ipc_number }} - {{ $mainIpc->project->name }}</p>
    </div>

    <form method="POST" action="{{ route('main-ipcs.process-approval', $mainIpc) }}" style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
        @csrf

        <!-- Main Content -->
        <div>
            <!-- IPC Summary -->
            <div style="background: white; padding: 25px; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                <h3 style="font-size: 1.2rem; font-weight: 600; margin-bottom: 20px;">ملخص المستخلص</h3>
                
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; margin-bottom: 20px;">
                    <div style="background: #f5f5f7; padding: 15px; border-radius: 8px;">
                        <div style="font-size: 0.8rem; color: #86868b; margin-bottom: 5px;">الفترة</div>
                        <div style="font-weight: 600;">{{ $mainIpc->period_from->format('Y/m/d') }} - {{ $mainIpc->period_to->format('Y/m/d') }}</div>
                    </div>
                    
                    <div style="background: #f5f5f7; padding: 15px; border-radius: 8px;">
                        <div style="font-size: 0.8rem; color: #86868b; margin-bottom: 5px;">تاريخ التقديم</div>
                        <div style="font-weight: 600;">{{ $mainIpc->submission_date->format('Y/m/d') }}</div>
                    </div>
                    
                    <div style="background: #f5f5f7; padding: 15px; border-radius: 8px;">
                        <div style="font-size: 0.8rem; color: #86868b; margin-bottom: 5px;">القيمة التراكمية</div>
                        <div style="font-weight: 700; color: var(--accent);">{{ number_format($mainIpc->current_cumulative, 2) }} ر.س</div>
                    </div>
                    
                    <div style="background: #f5f5f7; padding: 15px; border-radius: 8px;">
                        <div style="font-size: 0.8rem; color: #86868b; margin-bottom: 5px;">الصافي للدفع</div>
                        <div style="font-weight: 700; color: #34c759;">{{ number_format($mainIpc->net_payable, 2) }} ر.س</div>
                    </div>
                </div>

                <!-- Items Summary -->
                <div>
                    <h4 style="font-size: 1rem; font-weight: 600; margin-bottom: 15px;">بنود الأعمال</h4>
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; font-size: 0.85rem;">
                            <thead>
                                <tr style="background: #f5f5f7;">
                                    <th style="padding: 8px; text-align: right; font-weight: 600; border: 1px solid #e5e5e7;">كود البند</th>
                                    <th style="padding: 8px; text-align: right; font-weight: 600; border: 1px solid #e5e5e7;">الوصف</th>
                                    <th style="padding: 8px; text-align: right; font-weight: 600; border: 1px solid #e5e5e7;">الكمية الحالية</th>
                                    <th style="padding: 8px; text-align: right; font-weight: 600; border: 1px solid #e5e5e7;">القيمة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($mainIpc->items as $item)
                                    <tr style="border-bottom: 1px solid #e5e5e7;">
                                        <td style="padding: 8px; border: 1px solid #e5e5e7;">{{ $item->item_code }}</td>
                                        <td style="padding: 8px; border: 1px solid #e5e5e7;">{{ $item->description }}</td>
                                        <td style="padding: 8px; border: 1px solid #e5e5e7;">{{ number_format($item->current_quantity, 3) }} {{ $item->unit }}</td>
                                        <td style="padding: 8px; border: 1px solid #e5e5e7; font-weight: 600;">{{ number_format($item->current_amount, 2) }} ر.س</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Decision Form -->
            <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                <h3 style="font-size: 1.2rem; font-weight: 600; margin-bottom: 20px;">القرار</h3>
                
                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 10px; font-weight: 600;">اختر القرار *</label>
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px;">
                        <label style="padding: 15px; border: 2px solid #e5e5e7; border-radius: 8px; cursor: pointer; display: flex; align-items: center; gap: 10px; transition: all 0.2s;">
                            <input type="radio" name="decision" value="approved" required style="width: 18px; height: 18px; accent-color: #34c759;">
                            <div>
                                <div style="font-weight: 600; color: #34c759;">موافقة ✅</div>
                                <div style="font-size: 0.75rem; color: #86868b;">اعتماد المستخلص</div>
                            </div>
                        </label>
                        
                        <label style="padding: 15px; border: 2px solid #e5e5e7; border-radius: 8px; cursor: pointer; display: flex; align-items: center; gap: 10px; transition: all 0.2s;">
                            <input type="radio" name="decision" value="rejected" required style="width: 18px; height: 18px; accent-color: #ff3b30;">
                            <div>
                                <div style="font-weight: 600; color: #ff3b30;">رفض ❌</div>
                                <div style="font-size: 0.75rem; color: #86868b;">رفض المستخلص</div>
                            </div>
                        </label>
                        
                        <label style="padding: 15px; border: 2px solid #e5e5e7; border-radius: 8px; cursor: pointer; display: flex; align-items: center; gap: 10px; transition: all 0.2s;">
                            <input type="radio" name="decision" value="revision_required" required style="width: 18px; height: 18px; accent-color: #ff9500;">
                            <div>
                                <div style="font-weight: 600; color: #ff9500;">مراجعة 🔄</div>
                                <div style="font-size: 0.75rem; color: #86868b;">طلب تعديل</div>
                            </div>
                        </label>
                    </div>
                </div>

                @if(in_array($mainIpc->status, ['pending_consultant', 'pending_client']))
                    <div style="margin-bottom: 20px;" id="approved_amount_div">
                        <label style="display: block; margin-bottom: 8px; font-weight: 600;">المبلغ المعتمد (اختياري)</label>
                        <input type="number" name="approved_amount" step="0.01" min="0" 
                               placeholder="{{ number_format($mainIpc->net_payable, 2) }}"
                               style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                        <div style="font-size: 0.8rem; color: #86868b; margin-top: 5px;">
                            اترك فارغاً لاعتماد المبلغ الكامل: {{ number_format($mainIpc->net_payable, 2) }} ر.س
                        </div>
                    </div>
                @endif

                <div style="margin-bottom: 20px;">
                    <label style="display: block; margin-bottom: 8px; font-weight: 600;">التعليقات *</label>
                    <textarea name="comments" rows="4" required 
                              placeholder="أدخل تعليقاتك هنا..."
                              style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif; resize: vertical;"></textarea>
                </div>

                <div style="display: flex; gap: 15px; justify-content: flex-end;">
                    <a href="{{ route('main-ipcs.show', $mainIpc) }}" style="padding: 12px 30px; background: #f5f5f7; color: #1d1d1f; border-radius: 8px; text-decoration: none; font-weight: 600;">
                        إلغاء
                    </a>
                    <button type="submit" style="padding: 12px 30px; background: var(--accent); color: white; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">
                        تأكيد القرار
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Current Stage -->
            <div style="background: white; padding: 20px; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                <h4 style="font-size: 0.9rem; color: #86868b; margin-bottom: 15px;">المرحلة الحالية</h4>
                <div style="padding: 15px; background: rgba(0, 113, 227, 0.1); border-radius: 8px; border-right: 4px solid var(--accent);">
                    <div style="font-weight: 700; font-size: 1.1rem; color: var(--accent); margin-bottom: 5px;">
                        {{ $mainIpc->status_badge['label'] }}
                    </div>
                    <div style="font-size: 0.85rem; color: #86868b;">
                        @if($mainIpc->consultant_days_remaining !== null && $mainIpc->status === 'pending_consultant')
                            @if($mainIpc->consultant_days_remaining > 0)
                                متبقي {{ $mainIpc->consultant_days_remaining }} يوم
                            @elseif($mainIpc->consultant_days_remaining === 0)
                                آخر يوم للمراجعة
                            @else
                                متأخر {{ abs($mainIpc->consultant_days_remaining) }} يوم
                            @endif
                        @elseif($mainIpc->client_days_remaining !== null && $mainIpc->status === 'pending_client')
                            @if($mainIpc->client_days_remaining > 0)
                                متبقي {{ $mainIpc->client_days_remaining }} يوم
                            @elseif($mainIpc->client_days_remaining === 0)
                                آخر يوم للمراجعة
                            @else
                                متأخر {{ abs($mainIpc->client_days_remaining) }} يوم
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            <!-- Previous Reviews -->
            @if($mainIpc->technical_reviewed_at)
                <div style="background: white; padding: 20px; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                    <h4 style="font-size: 0.9rem; color: #86868b; margin-bottom: 15px;">المراجعة الفنية</h4>
                    <div style="margin-bottom: 10px;">
                        <span style="padding: 4px 10px; background: rgba(52, 199, 89, 0.1); color: #34c759; border-radius: 6px; font-size: 0.8rem; font-weight: 600;">
                            {{ $mainIpc->technical_decision === 'approved' ? 'تمت الموافقة' : $mainIpc->technical_decision }}
                        </span>
                    </div>
                    <div style="font-size: 0.85rem; margin-bottom: 8px;">
                        <strong>المراجع:</strong> {{ $mainIpc->technicalReviewer->name ?? 'N/A' }}
                    </div>
                    <div style="font-size: 0.8rem; color: #86868b; margin-bottom: 8px;">
                        {{ $mainIpc->technical_reviewed_at->format('Y/m/d H:i') }}
                    </div>
                    @if($mainIpc->technical_comments)
                        <div style="background: #f5f5f7; padding: 10px; border-radius: 6px; font-size: 0.85rem;">
                            {{ $mainIpc->technical_comments }}
                        </div>
                    @endif
                </div>
            @endif

            <!-- Guidelines -->
            <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
                <h4 style="font-size: 0.9rem; color: #86868b; margin-bottom: 15px;">إرشادات المراجعة</h4>
                <ul style="font-size: 0.85rem; color: #1d1d1f; padding-right: 20px; line-height: 1.6;">
                    <li style="margin-bottom: 8px;">تحقق من دقة الكميات المنفذة</li>
                    <li style="margin-bottom: 8px;">راجع الأسعار المطبقة</li>
                    <li style="margin-bottom: 8px;">تأكد من صحة الحسابات</li>
                    <li style="margin-bottom: 8px;">راجع الخصومات المطبقة</li>
                    <li style="margin-bottom: 8px;">قدم تعليقات واضحة ومفصلة</li>
                </ul>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    lucide.createIcons();

    // Radio button styling
    document.querySelectorAll('input[name="decision"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.querySelectorAll('input[name="decision"]').forEach(r => {
                r.parentElement.style.borderColor = '#e5e5e7';
                r.parentElement.style.background = 'white';
            });
            this.parentElement.style.borderColor = this.value === 'approved' ? '#34c759' : (this.value === 'rejected' ? '#ff3b30' : '#ff9500');
            this.parentElement.style.background = this.value === 'approved' ? 'rgba(52, 199, 89, 0.05)' : (this.value === 'rejected' ? 'rgba(255, 59, 48, 0.05)' : 'rgba(255, 149, 0, 0.05)');
        });
    });
</script>
@endpush
@endsection
