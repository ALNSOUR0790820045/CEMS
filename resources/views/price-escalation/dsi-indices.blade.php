@extends('layouts.app')

@section('content')
<style>
    .pe-container {
        max-width: 1400px;
        margin: 0 auto;
    }
    
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }
    
    .page-title {
        font-size: 1.8rem;
        font-weight: 700;
        color: #1d1d1f;
    }
    
    .btn-primary {
        background: #0071e3;
        color: white;
        padding: 12px 24px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
    }
    
    .btn-primary:hover {
        background: #0077ed;
        transform: translateY(-2px);
    }
    
    .btn-secondary {
        background: #f5f5f7;
        color: #1d1d1f;
        padding: 12px 24px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
    }
    
    .chart-card {
        background: white;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        margin-bottom: 30px;
    }
    
    .table-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        overflow: hidden;
    }
    
    .table-header {
        padding: 20px 30px;
        border-bottom: 1px solid #f5f5f7;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .table-title {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1d1d1f;
    }
    
    table {
        width: 100%;
        border-collapse: collapse;
    }
    
    thead {
        background: #f5f5f7;
    }
    
    th {
        padding: 15px 20px;
        text-align: right;
        font-size: 0.85rem;
        font-weight: 600;
        color: #86868b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    td {
        padding: 20px;
        border-bottom: 1px solid #f5f5f7;
        font-size: 0.95rem;
    }
    
    tbody tr:hover {
        background: #fafafa;
    }
    
    .index-value {
        font-weight: 600;
        color: #1d1d1f;
        font-size: 1.1rem;
    }
    
    .change-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 0.85rem;
        font-weight: 600;
    }
    
    .change-positive {
        background: #d1f4dd;
        color: #047857;
    }
    
    .change-negative {
        background: #fee;
        color: #dc2626;
    }
    
    .change-neutral {
        background: #f5f5f7;
        color: #86868b;
    }
    
    .btn-action {
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.8rem;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
    }
    
    .btn-edit {
        background: #f5f5f7;
        color: #1d1d1f;
    }
    
    .btn-edit:hover {
        background: #e8e8ed;
    }
    
    .btn-delete {
        background: #fee;
        color: #dc2626;
    }
    
    .btn-delete:hover {
        background: #fecaca;
    }
    
    .pagination {
        padding: 20px;
        display: flex;
        justify-content: center;
        gap: 10px;
    }
    
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.5);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }
    
    .modal.active {
        display: flex;
    }
    
    .modal-content {
        background: white;
        border-radius: 12px;
        padding: 30px;
        max-width: 500px;
        width: 90%;
        max-height: 90vh;
        overflow-y: auto;
    }
    
    .modal-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 20px;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-label {
        display: block;
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 8px;
    }
    
    .form-control {
        width: 100%;
        padding: 12px 16px;
        border: 1px solid #d1d1d6;
        border-radius: 8px;
        font-size: 1rem;
        font-family: 'Cairo', sans-serif;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #0071e3;
    }
    
    .form-actions {
        display: flex;
        gap: 10px;
        justify-content: flex-end;
        margin-top: 20px;
    }
</style>

<div class="pe-container">
    <div class="page-header">
        <h1 class="page-title">مؤشرات DSI</h1>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('price-escalation.import-dsi') }}" class="btn-secondary">
                <i data-lucide="upload" style="width: 18px; height: 18px;"></i>
                استيراد Excel/CSV
            </a>
            <button onclick="openAddModal()" class="btn-primary">
                <i data-lucide="plus" style="width: 18px; height: 18px;"></i>
                إضافة مؤشر
            </button>
        </div>
    </div>
    
    @if(session('success'))
        <div style="background: #d1f4dd; color: #047857; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            {{ session('success') }}
        </div>
    @endif
    
    <!-- Chart -->
    <div class="chart-card">
        <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 20px;">اتجاه المؤشرات</h3>
        <canvas id="dsiTrendChart"></canvas>
    </div>
    
    <!-- Table -->
    <div class="table-card">
        <div class="table-header">
            <h3 class="table-title">سجل المؤشرات</h3>
        </div>
        
        <table>
            <thead>
                <tr>
                    <th>التاريخ</th>
                    <th>مؤشر المواد (L)</th>
                    <th>التغير %</th>
                    <th>مؤشر العمالة (P)</th>
                    <th>التغير %</th>
                    <th>المصدر</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($indices as $index)
                    <tr>
                        <td>
                            <strong>{{ $index->year }}/{{ str_pad($index->month, 2, '0', STR_PAD_LEFT) }}</strong>
                        </td>
                        <td>
                            <span class="index-value">{{ number_format($index->materials_index, 4) }}</span>
                        </td>
                        <td>
                            @if($index->materials_change_percent !== null)
                                <span class="change-badge {{ $index->materials_change_percent > 0 ? 'change-positive' : ($index->materials_change_percent < 0 ? 'change-negative' : 'change-neutral') }}">
                                    @if($index->materials_change_percent > 0) +@endif{{ number_format($index->materials_change_percent, 2) }}%
                                </span>
                            @else
                                <span class="change-badge change-neutral">--</span>
                            @endif
                        </td>
                        <td>
                            <span class="index-value">{{ number_format($index->labor_index, 4) }}</span>
                        </td>
                        <td>
                            @if($index->labor_change_percent !== null)
                                <span class="change-badge {{ $index->labor_change_percent > 0 ? 'change-positive' : ($index->labor_change_percent < 0 ? 'change-negative' : 'change-neutral') }}">
                                    @if($index->labor_change_percent > 0) +@endif{{ number_format($index->labor_change_percent, 2) }}%
                                </span>
                            @else
                                <span class="change-badge change-neutral">--</span>
                            @endif
                        </td>
                        <td>{{ $index->source }}</td>
                        <td>
                            <div style="display: flex; gap: 5px;">
                                <button onclick="openEditModal({{ $index->id }}, {{ json_encode($index) }})" class="btn-action btn-edit">
                                    <i data-lucide="edit" style="width: 14px; height: 14px;"></i>
                                    تعديل
                                </button>
                                <form method="POST" action="{{ route('price-escalation.dsi-indices.destroy', $index) }}" style="display: inline;" onsubmit="return confirm('هل أنت متأكد؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn-action btn-delete">
                                        <i data-lucide="trash-2" style="width: 14px; height: 14px;"></i>
                                        حذف
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px; color: #86868b;">
                            لا توجد مؤشرات حتى الآن
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        
        <div class="pagination">
            {{ $indices->links() }}
        </div>
    </div>
</div>

<!-- Add Modal -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <h3 class="modal-title">إضافة مؤشر DSI</h3>
        <form method="POST" action="{{ route('price-escalation.dsi-indices.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">السنة *</label>
                <input type="number" name="year" class="form-control" min="2000" max="2100" required>
            </div>
            <div class="form-group">
                <label class="form-label">الشهر *</label>
                <input type="number" name="month" class="form-control" min="1" max="12" required>
            </div>
            <div class="form-group">
                <label class="form-label">مؤشر المواد *</label>
                <input type="number" step="0.0001" name="materials_index" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">مؤشر العمالة *</label>
                <input type="number" step="0.0001" name="labor_index" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">المؤشر العام</label>
                <input type="number" step="0.0001" name="general_index" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">المصدر</label>
                <input type="text" name="source" class="form-control" value="DOS Jordan">
            </div>
            <div class="form-actions">
                <button type="button" onclick="closeModal('addModal')" class="btn-secondary">إلغاء</button>
                <button type="submit" class="btn-primary">إضافة</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <h3 class="modal-title">تعديل مؤشر DSI</h3>
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label class="form-label">مؤشر المواد *</label>
                <input type="number" step="0.0001" name="materials_index" id="edit_materials_index" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">مؤشر العمالة *</label>
                <input type="number" step="0.0001" name="labor_index" id="edit_labor_index" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">المؤشر العام</label>
                <input type="number" step="0.0001" name="general_index" id="edit_general_index" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">المصدر</label>
                <input type="text" name="source" id="edit_source" class="form-control">
            </div>
            <div class="form-actions">
                <button type="button" onclick="closeModal('editModal')" class="btn-secondary">إلغاء</button>
                <button type="submit" class="btn-primary">تحديث</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    lucide.createIcons();
    
    // Chart
    const indices = @json($indices->items());
    const ctx = document.getElementById('dsiTrendChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: indices.map(i => `${i.year}/${String(i.month).padStart(2, '0')}`),
            datasets: [{
                label: 'مؤشر المواد',
                data: indices.map(i => i.materials_index),
                borderColor: '#0071e3',
                backgroundColor: 'rgba(0, 113, 227, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'مؤشر العمالة',
                data: indices.map(i => i.labor_index),
                borderColor: '#00c4cc',
                backgroundColor: 'rgba(0, 196, 204, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'top',
                    rtl: true
                }
            }
        }
    });
    
    // Modal functions
    function openAddModal() {
        document.getElementById('addModal').classList.add('active');
    }
    
    function openEditModal(id, data) {
        document.getElementById('editForm').action = `/price-escalation/dsi-indices/${id}`;
        document.getElementById('edit_materials_index').value = data.materials_index;
        document.getElementById('edit_labor_index').value = data.labor_index;
        document.getElementById('edit_general_index').value = data.general_index || '';
        document.getElementById('edit_source').value = data.source;
        document.getElementById('editModal').classList.add('active');
        lucide.createIcons();
    }
    
    function closeModal(modalId) {
        document.getElementById(modalId).classList.remove('active');
    }
    
    // Close modal on outside click
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal(this.id);
            }
        });
    });
</script>
@endsection
