@extends('layouts.app')

@section('content')
<style>
    .container { max-width: 1600px; margin: 0 auto; padding: 20px; }
    .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
    .header h1 { font-size: 1.8rem; font-weight: 700; margin: 0; }
    .btn { padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.2s; display: inline-flex; align-items: center; gap: 8px; }
    .btn-primary { background: #0071e3; color: white; }
    .btn-secondary { background: #f5f5f7; color: #1d1d1f; }
    .kanban-board { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; overflow-x: auto; }
    .kanban-column { background: #f5f5f7; padding: 20px; border-radius: 12px; min-height: 400px; }
    .kanban-header { font-weight: 700; font-size: 0.95rem; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; }
    .kanban-count { background: #0071e3; color: white; padding: 3px 8px; border-radius: 12px; font-size: 0.8rem; }
    .kanban-card { background: white; padding: 15px; border-radius: 8px; margin-bottom: 12px; cursor: pointer; transition: all 0.2s; border-right: 3px solid #0071e3; }
    .kanban-card:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
    .card-title { font-weight: 600; font-size: 0.9rem; margin-bottom: 8px; }
    .card-meta { font-size: 0.8rem; color: #666; display: flex; flex-direction: column; gap: 5px; }
    .card-value { font-weight: 700; color: #0071e3; margin-top: 8px; }
    .empty-column { text-align: center; padding: 40px 20px; color: #999; font-size: 0.85rem; }
</style>

<div class="container">
    <div class="header">
        <h1>Pipeline المناقصات</h1>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('tenders.index') }}" class="btn btn-secondary">
                <i data-lucide="list" style="width: 18px; height: 18px;"></i>
                عرض القائمة
            </a>
            <a href="{{ route('tenders.create') }}" class="btn btn-primary">
                <i data-lucide="plus" style="width: 18px; height: 18px;"></i>
                إضافة مناقصة
            </a>
        </div>
    </div>

    <div class="kanban-board">
        @foreach($statuses as $status => $label)
        <div class="kanban-column">
            <div class="kanban-header">
                <span>{{ $label }}</span>
                <span class="kanban-count">{{ $tenders->get($status)?->count() ?? 0 }}</span>
            </div>
            
            @if($tenders->has($status) && $tenders->get($status)->count() > 0)
                @foreach($tenders->get($status) as $tender)
                <a href="{{ route('tenders.show', $tender) }}" style="text-decoration: none; color: inherit;">
                    <div class="kanban-card">
                        <div class="card-title">{{ $tender->name }}</div>
                        <div class="card-meta">
                            <div>
                                <i data-lucide="building-2" style="width: 14px; height: 14px;"></i>
                                {{ $tender->client?->name ?? $tender->client_name ?? '-' }}
                            </div>
                            <div>
                                <i data-lucide="calendar" style="width: 14px; height: 14px;"></i>
                                {{ $tender->submission_deadline?->format('Y-m-d') ?? '-' }}
                            </div>
                            @if($tender->assignedTo)
                            <div>
                                <i data-lucide="user" style="width: 14px; height: 14px;"></i>
                                {{ $tender->assignedTo->name }}
                            </div>
                            @endif
                        </div>
                        @if($tender->estimated_value)
                        <div class="card-value">
                            {{ number_format($tender->estimated_value, 0) }} {{ $tender->currency }}
                        </div>
                        @endif
                    </div>
                </a>
                @endforeach
            @else
                <div class="empty-column">
                    <i data-lucide="inbox" style="width: 32px; height: 32px;"></i>
                    <p>لا توجد مناقصات</p>
                </div>
            @endif
        </div>
        @endforeach
    </div>
</div>

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
@endsection
