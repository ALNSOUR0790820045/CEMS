@extends('layouts.app')

@section('content')
<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    .page-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: var(--text);
    }

    .btn-primary {
        background: linear-gradient(135deg, #0071e3, #00a0e3);
        color: white;
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-family: 'Cairo', sans-serif;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s;
        box-shadow: 0 2px 8px rgba(0, 113, 227, 0.2);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 113, 227, 0.3);
    }

    .search-bar {
        background: white;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 20px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .search-input {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-family: 'Cairo', sans-serif;
        font-size: 0.95rem;
    }

    .table-container {
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    thead {
        background: linear-gradient(135deg, #f5f5f7, #e8e8ea);
    }

    th {
        padding: 16px;
        text-align: right;
        font-weight: 600;
        color: var(--text);
        font-size: 0.9rem;
        border-bottom: 2px solid #ddd;
    }

    td {
        padding: 16px;
        text-align: right;
        border-bottom: 1px solid #f0f0f0;
        color: var(--text);
    }

    tr:hover {
        background: #f9f9f9;
    }

    .badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .badge-base {
        background: linear-gradient(135deg, #ffd700, #ffed4e);
        color: #8b6914;
        box-shadow: 0 2px 4px rgba(255, 215, 0, 0.3);
    }

    .badge-active {
        background: #d4edda;
        color: #155724;
    }

    .badge-inactive {
        background: #f8d7da;
        color: #721c24;
    }

    .btn-edit, .btn-delete {
        padding: 8px 16px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 0.85rem;
        font-weight: 500;
        transition: all 0.2s;
        display: inline-block;
        margin-left: 5px;
    }

    .btn-edit {
        background: #0071e3;
        color: white;
    }

    .btn-edit:hover {
        background: #0056b3;
    }

    .btn-delete {
        background: #ff3b30;
        color: white;
        border: none;
        cursor: pointer;
        font-family: 'Cairo', sans-serif;
    }

    .btn-delete:hover {
        background: #cc2f26;
    }

    .alert {
        padding: 16px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-weight: 500;
    }

    .alert-success {
        background: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .alert-error {
        background: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
</style>

<div style="padding: 40px; max-width: 1400px; margin: 0 auto;">
    <div class="page-header">
        <h1 class="page-title">إدارة العملات</h1>
        <a href="{{ route('currencies.create') }}" class="btn-primary">
            <i data-lucide="plus" style="width: 18px; height: 18px;"></i>
            إضافة عملة جديدة
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-error">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="search-bar">
        <form method="GET" action="{{ route('currencies.index') }}">
            <div style="display: grid; grid-template-columns: 2fr 1fr auto; gap: 15px;">
                <input type="text" name="search" placeholder="البحث بالاسم أو الكود..." 
                       value="{{ request('search') }}" class="search-input">
                
                <select name="status" class="search-input">
                    <option value="">جميع الحالات</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>نشطة</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>غير نشطة</option>
                </select>
                
                <button type="submit" class="btn-primary">بحث</button>
            </div>
        </form>
    </div>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>الاسم</th>
                    <th>الاسم بالإنجليزية</th>
                    <th>الكود</th>
                    <th>الرمز</th>
                    <th>سعر الصرف</th>
                    <th>عملة أساسية</th>
                    <th>الحالة</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($currencies as $currency)
                    <tr>
                        <td>{{ $currency->id }}</td>
                        <td style="font-weight: 600;">{{ $currency->name }}</td>
                        <td>{{ $currency->name_en }}</td>
                        <td><strong>{{ $currency->code }}</strong></td>
                        <td>{{ $currency->symbol }}</td>
                        <td>{{ $currency->getFormattedExchangeRate() }}</td>
                        <td>
                            @if($currency->is_base)
                                <span class="badge badge-base">عملة أساسية ⭐</span>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if($currency->is_active)
                                <span class="badge badge-active">نشطة</span>
                            @else
                                <span class="badge badge-inactive">غير نشطة</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('currencies.edit', $currency) }}" class="btn-edit">تعديل</a>
                            <form method="POST" action="{{ route('currencies.destroy', $currency) }}" 
                                  style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete" 
                                        onclick="return confirm('هل أنت متأكد من حذف هذه العملة؟')">
                                    حذف
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 40px; color: #999;">
                            لا توجد عملات مضافة
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
    lucide.createIcons();
</script>
@endsection
