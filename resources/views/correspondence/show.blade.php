@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1200px; margin: 0 auto;">
    @if(session('success'))
        <div style="background: #34c75922; border: 1px solid #34c759; padding: 15px; border-radius: 8px; margin-bottom: 20px; color: #34c759;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: #ff3b3022; border: 1px solid #ff3b30; padding: 15px; border-radius: 8px; margin-bottom: 20px; color: #ff3b30;">
            {{ session('error') }}
        </div>
    @endif

    <!-- Header -->
    <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 20px;">
        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 20px;">
            <div>
                <h1 style="margin: 0 0 10px 0; font-size: 1.5rem;">{{ $correspondence->reference_number }}</h1>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <span style="display: inline-block; padding: 4px 12px; background: {{ $correspondence->type === 'incoming' ? '#e3f2fd' : '#e8f5e9' }}; color: {{ $correspondence->type === 'incoming' ? '#0071e3' : '#34c759' }}; border-radius: 12px; font-size: 0.85rem; font-weight: 500;">
                        {{ $correspondence->type === 'incoming' ? 'وارد' : 'صادر' }}
                    </span>
                    @php
                        $statusColors = [
                            'draft' => '#86868b',
                            'pending_approval' => '#ff9500',
                            'approved' => '#34c759',
                            'sent' => '#0071e3',
                            'received' => '#5856d6',
                            'pending_response' => '#ff9500',
                            'responded' => '#34c759',
                            'closed' => '#1d1d1f',
                            'cancelled' => '#ff3b30'
                        ];
                        $statusLabels = [
                            'draft' => 'مسودة',
                            'pending_approval' => 'بانتظار الاعتماد',
                            'approved' => 'معتمد',
                            'sent' => 'مرسل',
                            'received' => 'مستلم',
                            'pending_response' => 'بانتظار الرد',
                            'responded' => 'تم الرد',
                            'closed' => 'مغلق',
                            'cancelled' => 'ملغي'
                        ];
                        $priorityColors = [
                            'normal' => '#86868b',
                            'urgent' => '#ff9500',
                            'very_urgent' => '#ff3b30',
                            'confidential' => '#5856d6'
                        ];
                        $priorityLabels = [
                            'normal' => 'عادي',
                            'urgent' => 'عاجل',
                            'very_urgent' => 'عاجل جداً',
                            'confidential' => 'سري'
                        ];
                    @endphp
                    <span style="display: inline-block; padding: 4px 12px; background: {{ $statusColors[$correspondence->status] }}22; color: {{ $statusColors[$correspondence->status] }}; border-radius: 12px; font-size: 0.85rem; font-weight: 500;">
                        {{ $statusLabels[$correspondence->status] }}
                    </span>
                    <span style="display: inline-block; padding: 4px 12px; background: {{ $priorityColors[$correspondence->priority] }}22; color: {{ $priorityColors[$correspondence->priority] }}; border-radius: 12px; font-size: 0.85rem; font-weight: 500;">
                        {{ $priorityLabels[$correspondence->priority] }}
                    </span>
                    @if($correspondence->is_confidential)
                        <span style="display: inline-block; padding: 4px 12px; background: #5856d622; color: #5856d6; border-radius: 12px; font-size: 0.85rem; font-weight: 500;">
                            <i data-lucide="lock" style="width: 14px; height: 14px; vertical-align: middle;"></i> سري
                        </span>
                    @endif
                    @if($correspondence->isOverdue())
                        <span style="display: inline-block; padding: 4px 12px; background: #ff3b3022; color: #ff3b30; border-radius: 12px; font-size: 0.85rem; font-weight: 500;">
                            <i data-lucide="alert-circle" style="width: 14px; height: 14px; vertical-align: middle;"></i> متأخر
                        </span>
                    @endif
                </div>
            </div>
            
            <div style="display: flex; gap: 10px;">
                @if($correspondence->canBeEdited())
                    <a href="{{ route('correspondence.edit', $correspondence) }}" style="background: #f5f5f7; color: #1d1d1f; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600;">
                        <i data-lucide="edit" style="width: 18px; height: 18px; vertical-align: middle;"></i>
                        تعديل
                    </a>
                @endif
                
                @if($correspondence->status === 'pending_approval')
                    <form method="POST" action="{{ route('correspondence.approve', $correspondence) }}" style="display: inline; margin: 0;">
                        @csrf
                        <button type="submit" style="background: #34c759; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">
                            <i data-lucide="check" style="width: 18px; height: 18px; vertical-align: middle;"></i>
                            اعتماد
                        </button>
                    </form>
                @endif
                
                @if($correspondence->canBeSent())
                    <form method="POST" action="{{ route('correspondence.send', $correspondence) }}" style="display: inline; margin: 0;">
                        @csrf
                        <button type="submit" style="background: #0071e3; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">
                            <i data-lucide="send" style="width: 18px; height: 18px; vertical-align: middle;"></i>
                            إرسال
                        </button>
                    </form>
                @endif
                
                @if($correspondence->type === 'incoming' && $correspondence->requires_response && $correspondence->status !== 'responded')
                    <button onclick="document.getElementById('replyModal').style.display='block'" style="background: #5856d6; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">
                        <i data-lucide="reply" style="width: 18px; height: 18px; vertical-align: middle;"></i>
                        رد
                    </button>
                @endif
                
                <button onclick="document.getElementById('forwardModal').style.display='block'" style="background: #ff9500; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">
                    <i data-lucide="forward" style="width: 18px; height: 18px; vertical-align: middle;"></i>
                    تحويل
                </button>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
        <!-- Left Column -->
        <div>
            <!-- Subject & Content -->
            <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 20px;">
                <h2 style="margin: 0 0 15px 0; font-size: 1.2rem; color: #1d1d1f;">{{ $correspondence->subject }}</h2>
                
                @if($correspondence->summary)
                    <div style="background: #f5f5f7; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                        <p style="margin: 0; color: #1d1d1f; line-height: 1.6;">{{ $correspondence->summary }}</p>
                    </div>
                @endif
                
                @if($correspondence->content)
                    <div style="color: #1d1d1f; line-height: 1.8;">
                        {!! nl2br(e($correspondence->content)) !!}
                    </div>
                @endif
            </div>

            <!-- Correspondence Details -->
            <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 20px;">
                <h3 style="margin: 0 0 20px 0; font-size: 1.1rem;">تفاصيل المراسلة</h3>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div style="background: #f5f5f7; padding: 15px; border-radius: 8px;">
                        <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 5px;">من</div>
                        <div style="font-weight: 600; margin-bottom: 5px;">{{ $correspondence->from_entity }}</div>
                        @if($correspondence->from_person)
                            <div style="color: #1d1d1f; font-size: 0.9rem;">{{ $correspondence->from_person }}</div>
                        @endif
                        @if($correspondence->from_position)
                            <div style="color: #86868b; font-size: 0.85rem;">{{ $correspondence->from_position }}</div>
                        @endif
                    </div>
                    
                    <div style="background: #f5f5f7; padding: 15px; border-radius: 8px;">
                        <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 5px;">إلى</div>
                        <div style="font-weight: 600; margin-bottom: 5px;">{{ $correspondence->to_entity }}</div>
                        @if($correspondence->to_person)
                            <div style="color: #1d1d1f; font-size: 0.9rem;">{{ $correspondence->to_person }}</div>
                        @endif
                        @if($correspondence->to_position)
                            <div style="color: #86868b; font-size: 0.85rem;">{{ $correspondence->to_position }}</div>
                        @endif
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px;">
                    <div>
                        <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 3px;">تاريخ المستند</div>
                        <div style="font-weight: 600;">{{ $correspondence->document_date->format('Y-m-d') }}</div>
                    </div>
                    
                    @if($correspondence->received_date)
                    <div>
                        <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 3px;">تاريخ الاستلام</div>
                        <div style="font-weight: 600;">{{ $correspondence->received_date->format('Y-m-d') }}</div>
                    </div>
                    @endif
                    
                    @if($correspondence->sent_date)
                    <div>
                        <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 3px;">تاريخ الإرسال</div>
                        <div style="font-weight: 600;">{{ $correspondence->sent_date->format('Y-m-d') }}</div>
                    </div>
                    @endif
                    
                    @if($correspondence->response_required_date)
                    <div>
                        <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 3px;">تاريخ الرد المطلوب</div>
                        <div style="font-weight: 600; color: {{ $correspondence->isOverdue() ? '#ff3b30' : '#1d1d1f' }};">
                            {{ $correspondence->response_required_date->format('Y-m-d') }}
                        </div>
                    </div>
                    @endif
                    
                    @if($correspondence->response_date)
                    <div>
                        <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 3px;">تاريخ الرد الفعلي</div>
                        <div style="font-weight: 600;">{{ $correspondence->response_date->format('Y-m-d') }}</div>
                    </div>
                    @endif
                </div>

                @if($correspondence->their_reference)
                <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #f5f5f7;">
                    <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 3px;">رقمهم المرجعي</div>
                    <div style="font-weight: 600;">{{ $correspondence->their_reference }}</div>
                </div>
                @endif

                @if($correspondence->notes)
                <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #f5f5f7;">
                    <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 5px;">ملاحظات</div>
                    <div style="color: #1d1d1f;">{{ $correspondence->notes }}</div>
                </div>
                @endif
            </div>

            <!-- Actions History -->
            @if($correspondence->actions->isNotEmpty())
            <div style="background: white; padding: 30px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 20px;">
                <h3 style="margin: 0 0 20px 0; font-size: 1.1rem;">سجل الإجراءات</h3>
                <div>
                    @foreach($correspondence->actions as $action)
                    <div style="padding: 15px; border-bottom: 1px solid #f5f5f7; {{ $loop->last ? 'border-bottom: none;' : '' }}">
                        <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 5px;">
                            <div style="font-weight: 600;">{{ $action->user->name }}</div>
                            <div style="color: #86868b; font-size: 0.85rem;">{{ $action->created_at->format('Y-m-d H:i') }}</div>
                        </div>
                        <div style="color: #1d1d1f; margin-bottom: 5px;">
                            <span style="display: inline-block; padding: 2px 8px; background: #f5f5f7; border-radius: 4px; font-size: 0.85rem;">
                                {{ $action->action }}
                            </span>
                        </div>
                        @if($action->comments)
                            <div style="color: #86868b; font-size: 0.9rem;">{{ $action->comments }}</div>
                        @endif
                        @if($action->forwarded_to)
                            <div style="color: #0071e3; font-size: 0.9rem;">حول إلى: {{ $action->forwardedToUser->name }}</div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column -->
        <div>
            <!-- Quick Info -->
            <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 20px;">
                <h3 style="margin: 0 0 15px 0; font-size: 1rem;">معلومات سريعة</h3>
                
                <div style="margin-bottom: 15px;">
                    <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 3px;">أنشئ بواسطة</div>
                    <div style="font-weight: 500;">{{ $correspondence->creator->name }}</div>
                    <div style="color: #86868b; font-size: 0.85rem;">{{ $correspondence->created_at->format('Y-m-d H:i') }}</div>
                </div>

                @if($correspondence->approved_by)
                <div style="margin-bottom: 15px; padding-top: 15px; border-top: 1px solid #f5f5f7;">
                    <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 3px;">اعتمد بواسطة</div>
                    <div style="font-weight: 500;">{{ $correspondence->approver->name }}</div>
                </div>
                @endif

                @if($correspondence->assigned_to)
                <div style="margin-bottom: 15px; padding-top: 15px; border-top: 1px solid #f5f5f7;">
                    <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 3px;">مسند إلى</div>
                    <div style="font-weight: 500;">{{ $correspondence->assignedUser->name }}</div>
                </div>
                @endif

                @if($correspondence->replyTo)
                <div style="padding-top: 15px; border-top: 1px solid #f5f5f7;">
                    <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 5px;">رد على</div>
                    <a href="{{ route('correspondence.show', $correspondence->replyTo) }}" style="color: #0071e3; text-decoration: none; font-weight: 500;">
                        {{ $correspondence->replyTo->reference_number }}
                    </a>
                </div>
                @endif

                @if($correspondence->replies->isNotEmpty())
                <div style="padding-top: 15px; border-top: 1px solid #f5f5f7;">
                    <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 8px;">الردود</div>
                    @foreach($correspondence->replies as $reply)
                        <a href="{{ route('correspondence.show', $reply) }}" style="display: block; color: #0071e3; text-decoration: none; font-weight: 500; margin-bottom: 5px;">
                            {{ $reply->reference_number }}
                        </a>
                    @endforeach
                </div>
                @endif

                <div style="padding-top: 15px; border-top: 1px solid #f5f5f7;">
                    <a href="{{ route('correspondence.thread', $correspondence) }}" style="color: #0071e3; text-decoration: none; font-weight: 500;">
                        <i data-lucide="git-branch" style="width: 16px; height: 16px; vertical-align: middle;"></i>
                        عرض سلسلة المراسلات
                    </a>
                </div>
            </div>

            <!-- Attachments -->
            @if($correspondence->attachments->isNotEmpty())
            <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 20px;">
                <h3 style="margin: 0 0 15px 0; font-size: 1rem;">المرفقات</h3>
                @foreach($correspondence->attachments as $attachment)
                    <div style="padding: 12px; background: #f5f5f7; border-radius: 8px; margin-bottom: 10px;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <i data-lucide="file" style="width: 20px; height: 20px; color: #0071e3;"></i>
                            <div style="flex: 1;">
                                <div style="font-weight: 500; color: #1d1d1f; font-size: 0.9rem;">{{ $attachment->name }}</div>
                                <div style="color: #86868b; font-size: 0.75rem;">{{ number_format($attachment->file_size / 1024, 1) }} KB</div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @endif

            <!-- Distribution -->
            @if($correspondence->distributions->isNotEmpty())
            <div style="background: white; padding: 25px; border-radius: 12px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <h3 style="margin: 0 0 15px 0; font-size: 1rem;">التوزيع</h3>
                @foreach($correspondence->distributions as $distribution)
                    <div style="padding: 10px 0; border-bottom: 1px solid #f5f5f7; {{ $loop->last ? 'border-bottom: none;' : '' }}">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <div style="font-weight: 500;">{{ $distribution->user->name }}</div>
                                <div style="color: #86868b; font-size: 0.85rem;">{{ $distribution->action_type }}</div>
                            </div>
                            @if($distribution->is_read)
                                <i data-lucide="check-circle" style="width: 18px; height: 18px; color: #34c759;"></i>
                            @else
                                <i data-lucide="circle" style="width: 18px; height: 18px; color: #86868b;"></i>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Forward Modal -->
<div id="forwardModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;" onclick="if(event.target === this) this.style.display='none'">
    <div style="background: white; padding: 30px; border-radius: 12px; max-width: 500px; width: 90%;" onclick="event.stopPropagation()">
        <h3 style="margin: 0 0 20px 0;">تحويل المراسلة</h3>
        <form method="POST" action="{{ route('correspondence.forward', $correspondence) }}">
            @csrf
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">إلى المستخدم</label>
                <select name="user_id" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="">اختر المستخدم</option>
                    @foreach(\App\Models\User::all() as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">تعليقات</label>
                <textarea name="comments" rows="3" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif; resize: vertical;"></textarea>
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="submit" style="background: #0071e3; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">تحويل</button>
                <button type="button" onclick="document.getElementById('forwardModal').style.display='none'" style="background: #f5f5f7; color: #1d1d1f; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">إلغاء</button>
            </div>
        </form>
    </div>
</div>

<!-- Reply Modal -->
<div id="replyModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;" onclick="if(event.target === this) this.style.display='none'">
    <div style="background: white; padding: 30px; border-radius: 12px; max-width: 600px; width: 90%;" onclick="event.stopPropagation()">
        <h3 style="margin: 0 0 20px 0;">الرد على المراسلة</h3>
        <form method="POST" action="{{ route('correspondence.reply', $correspondence) }}">
            @csrf
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">الموضوع</label>
                <input type="text" name="subject" required value="رد على: {{ $correspondence->subject }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 5px; font-weight: 600;">المحتوى</label>
                <textarea name="content" required rows="5" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif; resize: vertical;"></textarea>
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="submit" style="background: #0071e3; color: white; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">إرسال الرد</button>
                <button type="button" onclick="document.getElementById('replyModal').style.display='none'" style="background: #f5f5f7; color: #1d1d1f; padding: 10px 20px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">إلغاء</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
@endsection
