@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1400px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 style="margin: 0;">دليل الحسابات</h1>
        <a href="{{ route('accounts.create') }}" style="background: #0071e3; color: white; padding: 12px 30px; text-decoration: none; border-radius: 8px; font-weight: 600;">
            <i data-lucide="plus" style="width: 18px; height: 18px; display: inline-block; vertical-align: middle;"></i>
            إضافة حساب
        </a>
    </div>

    @if(session('success'))
        <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
            {{ session('error') }}
        </div>
    @endif

    <div style="background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
        <div style="padding: 20px;">
            <div style="display: grid; grid-template-columns: 150px 2fr 1fr 120px 120px 120px 150px; gap: 15px; padding: 15px; background: #f8f9fa; border-radius: 8px; font-weight: 600; margin-bottom: 15px;">
                <div>الكود</div>
                <div>اسم الحساب</div>
                <div>النوع</div>
                <div>الطبيعة</div>
                <div>المستوى</div>
                <div>الحالة</div>
                <div style="text-align: center;">الإجراءات</div>
            </div>

            @forelse($accounts as $account)
                @include('accounts.partials.tree-item', ['account' => $account, 'level' => 0])
            @empty
                <div style="padding: 40px; text-align: center; color: #666;">
                    <i data-lucide="folder-open" style="width: 48px; height: 48px; margin-bottom: 10px; opacity: 0.3;"></i>
                    <p>لا توجد حسابات حالياً</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<style>
    .account-row {
        display: grid;
        grid-template-columns: 150px 2fr 1fr 120px 120px 120px 150px;
        gap: 15px;
        padding: 15px;
        border-bottom: 1px solid #eee;
        align-items: center;
        transition: background 0.2s;
    }

    .account-row:hover {
        background: #f8f9fa;
    }

    .account-name {
        font-weight: 500;
        color: #1d1d1f;
    }

    .badge {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .badge-asset { background: #e3f2fd; color: #1976d2; }
    .badge-liability { background: #fff3e0; color: #f57c00; }
    .badge-equity { background: #f3e5f5; color: #7b1fa2; }
    .badge-revenue { background: #e8f5e9; color: #388e3c; }
    .badge-expense { background: #ffebee; color: #d32f2f; }

    .badge-debit { background: #e3f2fd; color: #1976d2; }
    .badge-credit { background: #fff3e0; color: #f57c00; }

    .badge-active { background: #d4edda; color: #155724; }
    .badge-inactive { background: #f8d7da; color: #721c24; }

    .action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 6px;
        border: 1px solid #ddd;
        background: white;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        color: #666;
        margin: 0 2px;
    }

    .action-btn:hover {
        background: #f8f9fa;
        border-color: #0071e3;
        color: #0071e3;
    }

    .action-btn.delete {
        border-color: #dc3545;
        color: #dc3545;
    }

    .action-btn.delete:hover {
        background: #dc3545;
        color: white;
    }
</style>

@push('scripts')
<script>
    lucide.createIcons();

    function confirmDelete(accountId, accountName) {
        if (confirm('هل أنت متأكد من حذف الحساب: ' + accountName + '؟')) {
            document.getElementById('delete-form-' + accountId).submit();
        }
    }
</script>
@endpush
@endsection
