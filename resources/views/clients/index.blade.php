@extends('layouts.app')

@section('content')
<div style="padding: 40px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 style="margin: 0;">العملاء</h1>
        <a href="{{ route('clients.create') }}" style="background: #0071e3; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600;">
            <i data-lucide="plus" style="width: 18px; height: 18px; margin-left: 5px;"></i>
            إضافة عميل جديد
        </a>
    </div>

    @if(session('success'))
    <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
        {{ session('success') }}
    </div>
    @endif

    <!-- Filters -->
    <div style="background: white; padding: 20px; border-radius: 10px; margin-bottom: 20px;">
        <form method="GET" action="{{ route('clients.index') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <div>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="بحث (الكود، الاسم، الرقم الضريبي...)" 
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
            </div>
            
            <div>
                <select name="client_type" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="">جميع الأنواع</option>
                    <option value="government" {{ request('client_type') == 'government' ? 'selected' : '' }}>حكومي</option>
                    <option value="semi_government" {{ request('client_type') == 'semi_government' ? 'selected' : '' }}>شبه حكومي</option>
                    <option value="private_sector" {{ request('client_type') == 'private_sector' ? 'selected' : '' }}>قطاع خاص</option>
                    <option value="individual" {{ request('client_type') == 'individual' ? 'selected' : '' }}>فرد</option>
                </select>
            </div>
            
            <div>
                <select name="client_category" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="">جميع الفئات</option>
                    <option value="strategic" {{ request('client_category') == 'strategic' ? 'selected' : '' }}>استراتيجي</option>
                    <option value="preferred" {{ request('client_category') == 'preferred' ? 'selected' : '' }}>مفضل</option>
                    <option value="regular" {{ request('client_category') == 'regular' ? 'selected' : '' }}>عادي</option>
                    <option value="one_time" {{ request('client_category') == 'one_time' ? 'selected' : '' }}>لمرة واحدة</option>
                </select>
            </div>
            
            <div>
                <select name="is_active" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px; font-family: 'Cairo', sans-serif;">
                    <option value="">جميع الحالات</option>
                    <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>نشط</option>
                    <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>غير نشط</option>
                </select>
            </div>
            
            <div style="display: flex; gap: 10px;">
                <button type="submit" style="flex: 1; background: #0071e3; color: white; padding: 10px; border: none; border-radius: 5px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">
                    بحث
                </button>
                <a href="{{ route('clients.index') }}" style="flex: 1; background: #f5f5f7; color: #666; padding: 10px; border: none; border-radius: 5px; text-decoration: none; text-align: center; font-family: 'Cairo', sans-serif; font-weight: 600;">
                    إعادة تعيين
                </a>
            </div>
        </form>
    </div>

    <!-- Clients Table -->
    <div style="background: white; border-radius: 10px; overflow: hidden;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f5f5f7; border-bottom: 1px solid #ddd;">
                    <th style="padding: 15px; text-align: right; font-weight: 600;">كود العميل</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600;">الاسم</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600;">النوع</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600;">الفئة</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600;">الهاتف</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600;">البريد</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600;">التقييم</th>
                    <th style="padding: 15px; text-align: right; font-weight: 600;">الحالة</th>
                    <th style="padding: 15px; text-align: center; font-weight: 600;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $client)
                <tr style="border-bottom: 1px solid #f0f0f0;">
                    <td style="padding: 15px;">{{ $client->client_code }}</td>
                    <td style="padding: 15px; font-weight: 600;">{{ $client->name }}</td>
                    <td style="padding: 15px;">
                        @php
                            $typeLabels = [
                                'government' => ['text' => 'حكومي', 'color' => '#0071e3'],
                                'semi_government' => ['text' => 'شبه حكومي', 'color' => '#5856d6'],
                                'private_sector' => ['text' => 'قطاع خاص', 'color' => '#34c759'],
                                'individual' => ['text' => 'فرد', 'color' => '#ff9500'],
                            ];
                            $type = $typeLabels[$client->client_type] ?? ['text' => $client->client_type, 'color' => '#999'];
                        @endphp
                        <span style="background: {{ $type['color'] }}15; color: {{ $type['color'] }}; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; font-weight: 600;">
                            {{ $type['text'] }}
                        </span>
                    </td>
                    <td style="padding: 15px;">
                        @php
                            $categoryLabels = [
                                'strategic' => ['text' => 'استراتيجي', 'color' => '#ff2d55'],
                                'preferred' => ['text' => 'مفضل', 'color' => '#5856d6'],
                                'regular' => ['text' => 'عادي', 'color' => '#34c759'],
                                'one_time' => ['text' => 'لمرة واحدة', 'color' => '#999'],
                            ];
                            $category = $categoryLabels[$client->client_category] ?? ['text' => $client->client_category, 'color' => '#999'];
                        @endphp
                        <span style="background: {{ $category['color'] }}15; color: {{ $category['color'] }}; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; font-weight: 600;">
                            {{ $category['text'] }}
                        </span>
                    </td>
                    <td style="padding: 15px;">{{ $client->phone ?? '-' }}</td>
                    <td style="padding: 15px;">{{ $client->email ?? '-' }}</td>
                    <td style="padding: 15px;">
                        @if($client->rating)
                            @php
                                $ratingStars = [
                                    'excellent' => '⭐⭐⭐⭐⭐',
                                    'good' => '⭐⭐⭐⭐',
                                    'average' => '⭐⭐⭐',
                                    'poor' => '⭐⭐',
                                ];
                            @endphp
                            <span title="{{ $client->rating }}">{{ $ratingStars[$client->rating] ?? '-' }}</span>
                        @else
                            -
                        @endif
                    </td>
                    <td style="padding: 15px;">
                        @if($client->is_active)
                            <span style="background: #34c75915; color: #34c759; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; font-weight: 600;">نشط</span>
                        @else
                            <span style="background: #ff2d5515; color: #ff2d55; padding: 4px 12px; border-radius: 12px; font-size: 0.85rem; font-weight: 600;">غير نشط</span>
                        @endif
                    </td>
                    <td style="padding: 15px; text-align: center;">
                        <div style="display: flex; gap: 10px; justify-content: center;">
                            <a href="{{ route('clients.show', $client) }}" title="عرض" style="color: #0071e3; text-decoration: none;">
                                <i data-lucide="eye" style="width: 18px; height: 18px;"></i>
                            </a>
                            <a href="{{ route('clients.edit', $client) }}" title="تعديل" style="color: #ff9500; text-decoration: none;">
                                <i data-lucide="edit" style="width: 18px; height: 18px;"></i>
                            </a>
                            <form method="POST" action="{{ route('clients.destroy', $client) }}" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذا العميل؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" title="حذف" style="background: none; border: none; color: #ff2d55; cursor: pointer; padding: 0;">
                                    <i data-lucide="trash-2" style="width: 18px; height: 18px;"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="padding: 40px; text-align: center; color: #999;">
                        لا توجد عملاء
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($clients->hasPages())
    <div style="margin-top: 20px; display: flex; justify-content: center;">
        {{ $clients->links() }}
    </div>
    @endif
</div>

<script>
    lucide.createIcons();
</script>
@endsection
