@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1000px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 style="margin: 0;">تفاصيل الحساب</h1>
        <div>
            <a href="{{ route('accounts.edit', $account->id) }}" style="background: #0071e3; color: white; padding: 12px 30px; text-decoration: none; border-radius: 8px; font-weight: 600; margin-left: 10px;">
                <i data-lucide="edit" style="width: 18px; height: 18px; display: inline-block; vertical-align: middle;"></i>
                تعديل
            </a>
            <a href="{{ route('accounts.index') }}" style="background: #f5f5f7; color: #1d1d1f; padding: 12px 30px; text-decoration: none; border-radius: 8px; font-weight: 600;">
                عودة للقائمة
            </a>
        </div>
    </div>

    <div style="background: white; border-radius: 10px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 20px;">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
            <div>
                <h3 style="color: #666; font-size: 0.9rem; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 1px;">كود الحساب</h3>
                <p style="font-size: 1.2rem; font-weight: 600; font-family: 'Courier New', monospace; color: #0071e3; margin: 0;">{{ $account->code }}</p>
            </div>

            <div>
                <h3 style="color: #666; font-size: 0.9rem; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 1px;">المستوى</h3>
                <p style="font-size: 1.2rem; font-weight: 600; margin: 0;">المستوى {{ $account->level }}</p>
            </div>
        </div>

        <div style="margin-top: 30px; padding-top: 30px; border-top: 1px solid #eee;">
            <h3 style="color: #666; font-size: 0.9rem; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 1px;">اسم الحساب</h3>
            <p style="font-size: 1.5rem; font-weight: 600; margin: 0;">{{ $account->name }}</p>
            @if($account->name_en)
                <p style="color: #999; margin-top: 5px;">{{ $account->name_en }}</p>
            @endif
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 30px; margin-top: 30px;">
            <div>
                <h3 style="color: #666; font-size: 0.9rem; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 1px;">نوع الحساب</h3>
                <span class="badge badge-{{ $account->type }}" style="display: inline-block; padding: 8px 16px; border-radius: 12px; font-size: 0.9rem; font-weight: 500;">
                    @if($account->type === 'asset')
                        أصول
                    @elseif($account->type === 'liability')
                        خصوم
                    @elseif($account->type === 'equity')
                        حقوق ملكية
                    @elseif($account->type === 'revenue')
                        إيرادات
                    @else
                        مصروفات
                    @endif
                </span>
            </div>

            <div>
                <h3 style="color: #666; font-size: 0.9rem; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 1px;">طبيعة الحساب</h3>
                <span class="badge badge-{{ $account->nature }}" style="display: inline-block; padding: 8px 16px; border-radius: 12px; font-size: 0.9rem; font-weight: 500;">
                    {{ $account->nature === 'debit' ? 'مدين' : 'دائن' }}
                </span>
            </div>

            <div>
                <h3 style="color: #666; font-size: 0.9rem; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 1px;">الحالة</h3>
                <span class="badge badge-{{ $account->is_active ? 'active' : 'inactive' }}" style="display: inline-block; padding: 8px 16px; border-radius: 12px; font-size: 0.9rem; font-weight: 500;">
                    {{ $account->is_active ? 'نشط' : 'غير نشط' }}
                </span>
            </div>
        </div>

        @if($account->parent)
            <div style="margin-top: 30px; padding-top: 30px; border-top: 1px solid #eee;">
                <h3 style="color: #666; font-size: 0.9rem; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 1px;">الحساب الأب</h3>
                <a href="{{ route('accounts.show', $account->parent->id) }}" style="color: #0071e3; text-decoration: none; font-weight: 500;">
                    {{ $account->parent->code }} - {{ $account->parent->name }}
                </a>
            </div>
        @endif

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-top: 30px; padding-top: 30px; border-top: 1px solid #eee;">
            <div>
                <h3 style="color: #666; font-size: 0.9rem; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 1px;">الرصيد الافتتاحي</h3>
                <p style="font-size: 1.5rem; font-weight: 600; color: #0071e3; margin: 0;">{{ number_format($account->opening_balance, 2) }} ريال</p>
            </div>

            <div>
                <h3 style="color: #666; font-size: 0.9rem; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 1px;">الرصيد الحالي</h3>
                <p style="font-size: 1.5rem; font-weight: 600; color: #0071e3; margin: 0;">{{ number_format($account->current_balance, 2) }} ريال</p>
            </div>
        </div>

        @if($account->description)
            <div style="margin-top: 30px; padding-top: 30px; border-top: 1px solid #eee;">
                <h3 style="color: #666; font-size: 0.9rem; margin-bottom: 10px; text-transform: uppercase; letter-spacing: 1px;">الوصف</h3>
                <p style="line-height: 1.6; color: #666; margin: 0;">{{ $account->description }}</p>
            </div>
        @endif

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-top: 30px; padding-top: 30px; border-top: 1px solid #eee;">
            <div>
                <h3 style="color: #666; font-size: 0.9rem; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 1px;">حساب أب</h3>
                <p style="margin: 0;">{{ $account->is_parent ? 'نعم' : 'لا' }}</p>
            </div>

            <div>
                <h3 style="color: #666; font-size: 0.9rem; margin-bottom: 5px; text-transform: uppercase; letter-spacing: 1px;">عدد الحسابات الفرعية</h3>
                <p style="margin: 0; font-weight: 600;">{{ $account->children->count() }}</p>
            </div>
        </div>
    </div>

    @if($account->children->count() > 0)
        <div style="background: white; border-radius: 10px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
            <h2 style="margin-bottom: 20px;">الحسابات الفرعية</h2>
            <div style="border: 1px solid #eee; border-radius: 8px; overflow: hidden;">
                @foreach($account->children as $child)
                    <div style="padding: 15px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <span style="font-family: 'Courier New', monospace; font-weight: 600; color: #0071e3; margin-left: 15px;">{{ $child->code }}</span>
                            <span style="font-weight: 500;">{{ $child->name }}</span>
                        </div>
                        <a href="{{ route('accounts.show', $child->id) }}" style="color: #0071e3; text-decoration: none; padding: 8px 16px; border: 1px solid #0071e3; border-radius: 6px; font-size: 0.9rem;">
                            عرض التفاصيل
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<style>
    .badge-asset { background: #e3f2fd; color: #1976d2; }
    .badge-liability { background: #fff3e0; color: #f57c00; }
    .badge-equity { background: #f3e5f5; color: #7b1fa2; }
    .badge-revenue { background: #e8f5e9; color: #388e3c; }
    .badge-expense { background: #ffebee; color: #d32f2f; }
    .badge-debit { background: #e3f2fd; color: #1976d2; }
    .badge-credit { background: #fff3e0; color: #f57c00; }
    .badge-active { background: #d4edda; color: #155724; }
    .badge-inactive { background: #f8d7da; color: #721c24; }
</style>

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
@endsection
