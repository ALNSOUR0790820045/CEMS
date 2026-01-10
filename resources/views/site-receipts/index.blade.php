@extends('layouts.app')

@section('content')
<div style="padding: 40px; max-width: 1400px; margin: 0 auto;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 style="font-size: 28px; font-weight: 700;">استلامات الموقع</h1>
        <a href="{{ route('site-receipts.create') }}" style="background: #0071e3; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600;">
            + إضافة استلام جديد
        </a>
    </div>

    <!-- Filters -->
    <div style="background: white; padding: 20px; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
        <form method="GET" action="{{ route('site-receipts.index') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px;">المشروع</label>
                <select name="project_id" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: 'Cairo', sans-serif;">
                    <option value="">جميع المشاريع</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ request('project_id') == $project->id ? 'selected' : '' }}>
                            {{ $project->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px;">المورد</label>
                <select name="supplier_id" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: 'Cairo', sans-serif;">
                    <option value="">جميع الموردين</option>
                    @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ request('supplier_id') == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px;">الحالة</label>
                <select name="status" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: 'Cairo', sans-serif;">
                    <option value="">جميع الحالات</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>مسودة</option>
                    <option value="pending_verification" {{ request('status') == 'pending_verification' ? 'selected' : '' }}>بانتظار التحقق</option>
                    <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>تم التحقق</option>
                    <option value="grn_created" {{ request('status') == 'grn_created' ? 'selected' : '' }}>تم إنشاء GRN</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                </select>
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px;">من تاريخ</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: 'Cairo', sans-serif;">
            </div>

            <div>
                <label style="display: block; margin-bottom: 5px; font-weight: 600; font-size: 14px;">إلى تاريخ</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-family: 'Cairo', sans-serif;">
            </div>

            <div style="display: flex; align-items: flex-end; gap: 10px;">
                <button type="submit" style="background: #0071e3; color: white; padding: 10px 20px; border: none; border-radius: 6px; cursor: pointer; font-family: 'Cairo', sans-serif; font-weight: 600;">
                    بحث
                </button>
                <a href="{{ route('site-receipts.index') }}" style="padding: 10px 20px; border: 1px solid #ddd; border-radius: 6px; text-decoration: none; color: #666; font-weight: 600;">
                    إعادة تعيين
                </a>
            </div>
        </form>
    </div>

    <!-- View Toggle -->
    <div style="background: white; padding: 15px; border-radius: 12px; margin-bottom: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); display: flex; gap: 10px;">
        <button onclick="showListView()" id="listViewBtn" style="padding: 8px 16px; border: 1px solid #0071e3; background: #0071e3; color: white; border-radius: 6px; cursor: pointer; font-family: 'Cairo', sans-serif;">
            عرض قائمة
        </button>
        <button onclick="showMapView()" id="mapViewBtn" style="padding: 8px 16px; border: 1px solid #ddd; background: white; color: #666; border-radius: 6px; cursor: pointer; font-family: 'Cairo', sans-serif;">
            عرض الخريطة
        </button>
    </div>

    <!-- List View -->
    <div id="listView" style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f5f5f7; text-align: right;">
                    <th style="padding: 15px; font-weight: 600;">رقم الاستلام</th>
                    <th style="padding: 15px; font-weight: 600;">التاريخ والوقت</th>
                    <th style="padding: 15px; font-weight: 600;">المشروع</th>
                    <th style="padding: 15px; font-weight: 600;">المورد</th>
                    <th style="padding: 15px; font-weight: 600;">عدد البنود</th>
                    <th style="padding: 15px; font-weight: 600;">الحالة</th>
                    <th style="padding: 15px; font-weight: 600;">التوقيعات</th>
                    <th style="padding: 15px; font-weight: 600;">GRN</th>
                    <th style="padding: 15px; font-weight: 600;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($siteReceipts as $receipt)
                <tr style="border-bottom: 1px solid #f0f0f0;">
                    <td style="padding: 15px;">{{ $receipt->receipt_number }}</td>
                    <td style="padding: 15px;">
                        {{ $receipt->receipt_date->format('Y-m-d') }}<br>
                        <small style="color: #666;">{{ $receipt->receipt_time }}</small>
                    </td>
                    <td style="padding: 15px;">{{ $receipt->project->name }}</td>
                    <td style="padding: 15px;">{{ $receipt->supplier->name }}</td>
                    <td style="padding: 15px; text-align: center;">{{ $receipt->items->count() }}</td>
                    <td style="padding: 15px;">
                        @php
                            $statusColors = [
                                'draft' => '#999',
                                'pending_verification' => '#ff9500',
                                'verified' => '#34c759',
                                'grn_created' => '#007aff',
                                'rejected' => '#ff3b30'
                            ];
                            $statusLabels = [
                                'draft' => 'مسودة',
                                'pending_verification' => 'بانتظار التحقق',
                                'verified' => 'تم التحقق',
                                'grn_created' => 'تم إنشاء GRN',
                                'rejected' => 'مرفوض'
                            ];
                        @endphp
                        <span style="padding: 5px 12px; border-radius: 12px; font-size: 12px; font-weight: 600; background: {{ $statusColors[$receipt->status] }}22; color: {{ $statusColors[$receipt->status] }};">
                            {{ $statusLabels[$receipt->status] }}
                        </span>
                    </td>
                    <td style="padding: 15px;">
                        <div style="display: flex; gap: 5px;">
                            <span style="font-size: 20px;" title="مهندس الموقع">{{ $receipt->engineer_signature ? '✅' : '⭕' }}</span>
                            <span style="font-size: 20px;" title="أمين المخزن">{{ $receipt->storekeeper_signature ? '✅' : '⭕' }}</span>
                            <span style="font-size: 20px;" title="السائق">{{ $receipt->driver_signature ? '✅' : '⭕' }}</span>
                        </div>
                    </td>
                    <td style="padding: 15px;">
                        @if($receipt->grn)
                            <a href="#" style="color: #0071e3; text-decoration: none; font-weight: 600;">{{ $receipt->grn->grn_number }}</a>
                        @else
                            <span style="color: #999;">-</span>
                        @endif
                    </td>
                    <td style="padding: 15px;">
                        <a href="{{ route('site-receipts.show', $receipt) }}" style="color: #0071e3; text-decoration: none; font-weight: 600;">عرض</a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="padding: 40px; text-align: center; color: #999;">
                        لا توجد استلامات
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <div style="margin-top: 20px;">
            {{ $siteReceipts->links() }}
        </div>
    </div>

    <!-- Map View -->
    <div id="mapView" style="display: none; background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);">
        <div id="map" style="height: 600px; border-radius: 8px;"></div>
    </div>
</div>

<script>
function showListView() {
    document.getElementById('listView').style.display = 'block';
    document.getElementById('mapView').style.display = 'none';
    document.getElementById('listViewBtn').style.background = '#0071e3';
    document.getElementById('listViewBtn').style.color = 'white';
    document.getElementById('mapViewBtn').style.background = 'white';
    document.getElementById('mapViewBtn').style.color = '#666';
}

function showMapView() {
    document.getElementById('listView').style.display = 'none';
    document.getElementById('mapView').style.display = 'block';
    document.getElementById('listViewBtn').style.background = 'white';
    document.getElementById('listViewBtn').style.color = '#666';
    document.getElementById('mapViewBtn').style.background = '#0071e3';
    document.getElementById('mapViewBtn').style.color = 'white';
    initMap();
}

function initMap() {
    // Google Maps initialization would go here
    // For now, showing placeholder
    const receiptsWithGps = @json($receiptsWithGps);
    console.log('Receipts with GPS:', receiptsWithGps);
    
    // TODO: Initialize Google Maps with markers for each receipt
    document.getElementById('map').innerHTML = '<div style="display: flex; align-items: center; justify-content: center; height: 100%; color: #999;"><p>عرض الخريطة (يتطلب Google Maps API Key)</p></div>';
}
</script>
@endsection
