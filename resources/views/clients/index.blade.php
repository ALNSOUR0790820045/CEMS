@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1600px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 style="font-size:   2rem; font-weight: 700;">إدارة العملاء</h1>
        <a href="{{ route('clients.create') }}" style="background: #0071e3; color: white; padding: 12px 30px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap:   8px;">
            <i data-lucide="plus" style="width: 18px; height: 18px;"></i>
            إضافة عميل جديد
        </a>
    </div>

    @if(session('success'))
    <div style="background: #d1f4e0; border-radius: 8px; padding:  15px; margin-bottom: 20px; color: #0c6b3f;">
        {{ session('success') }}
    </div>
    @endif

    <!-- Filters -->
    <div style="background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
        <form method="GET" action="{{ route('clients.index') }}" style="display:   grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث (الكود، الاسم، الرقم الضريبي... )" 
                style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius:  8px; font-family: 'Cairo', sans-serif;">
            
            <select name="client_type" style="width: 100%; padding:   10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                <option value="">جميع الأنواع</option>
                <option value="government" {{ request('client_type') == 'government' ? 'selected' : '' }}>حكومي</option>
                <option value="semi_government" {{ request('client_type') == 'semi_government' ? 'selected' : '' }}>شبه حكومي</option>
                <option value="private_sector" {{ request('client_type') == 'private_sector' ? 'selected' : '' }}>قطاع خاص</option>
                <option value="private" {{ request('client_type') == 'private' ? 'selected' : '' }}>خاص</option>
                <option value="individual" {{ request('client_type') == 'individual' ? 'selected' :  '' }}>فرد</option>
            </select>
            
            <select name="client_category" style="width: 100%; padding:  10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                <option value="">جميع الفئات</option>
                <option value="strategic" {{ request('client_category') == 'strategic' ? 'selected' :  '' }}>استراتيجي</option>
                <option value="preferred" {{ request('client_category') == 'preferred' ? 'selected' : '' }}>مفضل</option>
                <option value="regular" {{ request('client_category') == 'regular' ? 'selected' : '' }}>عادي</option>
                <option value="one_time" {{ request('client_category') == 'one_time' ? 'selected' : '' }}>لمرة واحدة</option>
            </select>
            
            <select name="is_active" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; font-family: 'Cairo', sans-serif;">
                <option value="">جميع الحالات</option>
                <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>نشط</option>
                <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>غير نشط</option>
            </select>
            
            <div style="display: flex; gap:   10px;">
                <button type="submit" style="flex: 1; background: #0071e3; color: white; padding: 10px 25px; border: none; border-radius: 8px; cursor:  pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">
                    بحث
                </button>
                <a href="{{ route('clients.index') }}" style="flex: 1; background: #f5f5f7; color:  #666; padding: 10px 25px; border:  1px solid #ddd; border-radius: 8px; text-decoration: none; text-align: center; font-family: 'Cairo', sans-serif; font-weight: 600; display: inline-block;">
                    إعادة تعيين
                </a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div style="background:  white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.05); overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; min-width: 1000px;">
            <thead>
                <tr style="background:  #f5f5f7; border-bottom: 2px solid #e5e5e7;">
                    <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f; white-space: nowrap;">كود العميل</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">الاسم</th>
                    <th style="padding:  15px; text-align:  right; font-weight: 600; color: #1d1d1f;">النوع</th>
                    <th style="padding: 15px; text-align: right; font-weight:  600; color: #1d1d1f;">الفئة</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">الهاتف</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">البريد</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">التقييم</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600; color: #1d1d1f;">الحالة</th>
                    <th style="padding: 15px; text-align: center; font-weight:  600; color: #1d1d1f;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $client)
                <tr style="border-bottom: 1px solid #f5f5f7;">
                    <td style="padding: 15px; color: #0071e3; font-weight: 600; white-space: nowrap;">{{ $client->client_code }}</td>
                    <td style="padding: 15px; font-weight: 600;">{{ $client->name }}</td>
                    <td style="padding:  15px;">
                        @php
                            $typeLabels = [
                                'government' => ['text' => 'حكومي', 'color' => '#1976d2'],
                                'semi_government' => ['text' => 'شبه حكومي', 'color' => '#7b1fa2'],
                                'private_sector' => ['text' => 'قطاع خاص', 'color' => '#f57c00'],
                                'private' => ['text' => 'خاص', 'color' => '#f57c00'],
                                'individual' => ['text' => 'فرد', 'color' => '#00796b'],
                            ];
                            $type = $typeLabels[$client->client_type] ?? ['text' => $client->client_type, 'color' => '#999'];
                        @endphp
                        <span style="background: {{ $type['color'] }}15; color: {{ $type['color'] }}; padding: 4px 12px; border-radius:  12px; font-size:  0.85rem; font-weight: 600; white-space: nowrap;">
                            {{ $type['text'] }}
                        </span>
                    </td>
                    <td style="padding: 15px;">
                        @if($client->client_category)
                            @php
                                $categoryLabels = [
                                    'strategic' => ['text' => 'استراتيجي', 'color' => '#c62828'],
                                    'preferred' => ['text' => 'مفضل', 'color' => '#7b1fa2'],
                                    'regular' => ['text' => 'عادي', 'color' => '#00796b'],
                                    'one_time' => ['text' => 'لمرة واحدة', 'color' => '#999'],
                                ];
                                $category = $categoryLabels[$client->client_category] ?? ['text' => $client->client_category, 'color' => '#999'];
                            @endphp
                            <span style="background: {{ $category['color'] }}15; color:  {{ $category['color'] }}; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; font-weight: 600; white-space: nowrap;">
                                {{ $category['text'] }}
                            </span>
                        @else
                            -
                        @endif
                    </td>
                    <td style="padding: 15px; white-space: nowrap; direction: ltr; text-align: right;">{{ $client->phone ?? '-' }}</td>
                    <td style="padding: 15px; color: #666;">{{ $client->email ?? '-' }}</td>
                    <td style="padding:  15px; text-align: center;">
                        @if($client->rating)
                            @php
                                $ratingStars = [
                                    'excellent' => '⭐⭐⭐⭐⭐',
                                    'good' => '⭐⭐⭐⭐',
                                    'average' => '⭐⭐⭐',
                                    'poor' => '⭐⭐',
                                ];
                            @endphp
                            <span title="{{ $client->rating }}" style="white-space: nowrap;">{{ $ratingStars[$client->rating] ??  '-' }}</span>
                        @else
                            -
                        @endif
                    </td>
                    <td style="padding: 15px;">
                        @if($client->is_active)
                            <span style="background: #d1f4e0; color: #0c6b3f; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; font-weight: 600; white-space: nowrap;">نشط</span>
                        @else
                            <span style="background: #ffebee; color: #c62828; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; font-weight: 600; white-space: nowrap;">غير نشط</span>
                        @endif
                    </td>
                    <td style="padding: 15px; text-align: center;">
                        <div style="display: flex; gap: 8px; justify-content: center;">
                            <a href="{{ route('clients. show', $client) }}" style="background: #e0f2f1; color: #00796b; padding: 8px 12px; border-radius: 6px; text-decoration: none; font-size: 0.85rem; font-weight: 600; white-space: nowrap;">عرض</a>
                            <a href="{{ route('clients.edit', $client) }}" style="background:  #f5f5f7; color: #0071e3; padding: 8px 12px; border-radius: 6px; text-decoration: none; font-size: 0.85rem; font-weight: 600; white-space:  nowrap;">تعديل</a>
                            <form method="POST" action="{{ route('clients.destroy', $client) }}" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذا العميل؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background: #ffebee; color: #c62828; padding: 8px 12px; border:  none; border-radius: 6px; cursor: pointer; font-size: 0.85rem; font-weight: 600; font-family: 'Cairo', sans-serif; white-space: nowrap;">حذف</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="padding: 40px; text-align: center; color: #999;">لا توجد عملاء</td>
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
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
</script>
@endpush
@endsection