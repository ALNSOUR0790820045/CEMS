@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1400px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 style="font-size: 2rem; font-weight: 700;">إدارة العملاء</h1>
        <a href="{{ route('clients.create') }}" style="background: #0071e3; color: white; padding: 12px 30px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
            <i data-lucide="plus" style="width: 18px; height: 18px;"></i>
            إضافة عميل جديد
        </a>
    </div>

    @if(session('success'))
    <div style="background: #d1f4e0; border-radius: 8px; padding: 15px; margin-bottom: 20px; color: #0c6b3f;">
        {{ session('success') }}
    </div>
    @endif

    <!-- Filters -->
    <div style="background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
        <form method="GET" action="{{ route('clients.index') }}" style="display: flex; gap: 15px; flex-wrap: wrap;">
            <input type="text" name="search" placeholder="بحث بالكود، الاسم، أو البريد..." value="{{ request('search') }}" style="flex: 1; min-width: 250px; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
            
            <select name="client_type" style="padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                <option value="">كل الأنواع</option>
                <option value="government" {{ request('client_type') == 'government' ? 'selected' : '' }}>حكومي</option>
                <option value="semi_government" {{ request('client_type') == 'semi_government' ? 'selected' : '' }}>شبه حكومي</option>
                <option value="private" {{ request('client_type') == 'private' ? 'selected' : '' }}>خاص</option>
                <option value="individual" {{ request('client_type') == 'individual' ? 'selected' : '' }}>فردي</option>
            </select>
            
            <button type="submit" style="background: #0071e3; color: white; padding: 10px 25px; border: none; border-radius: 8px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">بحث</button>
            <a href="{{ route('clients.index') }}" style="padding: 10px 25px; border: 1px solid #ddd; border-radius: 8px; text-decoration: none; color: #666; font-family: 'Cairo', sans-serif; font-weight: 600;">إعادة تعيين</a>
        </form>
    </div>

    <!-- Table -->
    <div style="background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f5f5f7; border-bottom: 2px solid #e5e5e7;">
                    <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">كود العميل</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">الاسم</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">النوع</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">البريد الإلكتروني</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">الهاتف</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">المدينة</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">الحالة</th>
                    <th style="padding: 15px; text-align: center; font-weight: 600; color: #1d1d1f;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $client)
                <tr style="border-bottom: 1px solid #f5f5f7;">
                    <td style="padding: 15px; color: #0071e3; font-weight: 600;">{{ $client->client_code }}</td>
                    <td style="padding: 15px;">{{ $client->name }}</td>
                    <td style="padding: 15px;">
                        @if($client->client_type == 'government')
                            <span style="background: #e3f2fd; color: #1976d2; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem;">حكومي</span>
                        @elseif($client->client_type == 'semi_government')
                            <span style="background: #f3e5f5; color: #7b1fa2; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem;">شبه حكومي</span>
                        @elseif($client->client_type == 'private')
                            <span style="background: #fff3e0; color: #f57c00; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem;">خاص</span>
                        @else
                            <span style="background: #e0f2f1; color: #00796b; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem;">فردي</span>
                        @endif
                    </td>
                    <td style="padding: 15px; color: #666;">{{ $client->email ?? '-' }}</td>
                    <td style="padding: 15px; direction: ltr; text-align: right;">{{ $client->phone ?? '-' }}</td>
                    <td style="padding: 15px;">{{ $client->city->name ?? '-' }}</td>
                    <td style="padding: 15px;">
                        @if($client->is_active)
                            <span style="background: #d1f4e0; color: #0c6b3f; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem;">نشط</span>
                        @else
                            <span style="background: #ffebee; color: #c62828; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem;">غير نشط</span>
                        @endif
                    </td>
                    <td style="padding: 15px; text-align: center;">
                        <div style="display: flex; gap: 8px; justify-content: center;">
                            <a href="{{ route('clients.edit', $client) }}" style="background: #f5f5f7; color: #0071e3; padding: 8px 12px; border-radius: 6px; text-decoration: none; font-size: 0.85rem; font-weight: 600;">تعديل</a>
                            <form method="POST" action="{{ route('clients.destroy', $client) }}" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: #ffebee; color: #c62828; padding: 8px 12px; border: none; border-radius: 6px; cursor: pointer; font-size: 0.85rem; font-weight: 600; font-family: 'Cairo', sans-serif;">حذف</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="padding: 40px; text-align: center; color: #999;">لا توجد عملاء</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div style="margin-top: 20px;">
        {{ $clients->links() }}
    </div>
</div>

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
@endsection
