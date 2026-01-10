@extends('layouts.app')

@section('content')
<div style="padding: 40px;">
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <div>
            <h1 style="font-size: 2rem; color: #1d1d1f; margin-bottom: 5px;">هيكل تقسيم العمل (WBS)</h1>
            <p style="color: #86868b;">{{ $tender->name }} - {{ $tender->reference_number }}</p>
        </div>
        <div style="display: flex; gap: 10px;">
            <a href="{{ route('tender-wbs.import', $tender->id) }}" style="background: #fff; color: #0071e3; padding: 12px 24px; border: 1px solid #0071e3; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
                <i data-lucide="upload" style="width: 18px; height: 18px;"></i>
                استيراد
            </a>
            <a href="{{ route('tender-wbs.create', $tender->id) }}" style="background: linear-gradient(135deg, #0071e3 0%, #00c4cc 100%); color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
                <i data-lucide="plus" style="width: 18px; height: 18px;"></i>
                إضافة عنصر WBS
            </a>
        </div>
    </div>

    <!-- Success/Error Messages -->
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

    <!-- WBS Tree -->
    <div style="background: white; border-radius: 12px; padding: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
        @if($tender->wbsItems->count() > 0)
            <div class="wbs-tree">
                @foreach($tender->wbsItems as $wbs)
                    @include('tender-wbs.partials.tree-node', ['node' => $wbs, 'level' => 1])
                @endforeach
            </div>
        @else
        <div style="padding: 60px; text-align: center;">
            <i data-lucide="folder-tree" style="width: 64px; height: 64px; color: #d2d2d7; margin-bottom: 20px;"></i>
            <h3 style="color: #86868b; margin-bottom: 10px;">لا توجد عناصر WBS</h3>
            <p style="color: #d2d2d7;">ابدأ بإضافة عنصر WBS جديد أو استيراد من قالب</p>
        </div>
        @endif
    </div>

    <!-- Summary Statistics -->
    @if($tender->wbsItems->count() > 0)
    <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-top: 30px;">
        <div style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 5px;">إجمالي العناصر</div>
            <div style="font-size: 2rem; font-weight: 700; color: #1d1d1f;">{{ $tender->wbsItems->count() }}</div>
        </div>
        <div style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 5px;">المستويات</div>
            <div style="font-size: 2rem; font-weight: 700; color: #1d1d1f;">{{ $tender->wbsItems->max('level') }}</div>
        </div>
        <div style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 5px;">التكلفة المقدرة</div>
            <div style="font-size: 1.5rem; font-weight: 700; color: #0071e3;">{{ number_format($tender->wbsItems->sum('estimated_cost'), 2) }} ريال</div>
        </div>
        <div style="background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
            <div style="color: #86868b; font-size: 0.85rem; margin-bottom: 5px;">المدة المقدرة</div>
            <div style="font-size: 1.5rem; font-weight: 700; color: #34c759;">{{ $tender->wbsItems->sum('estimated_duration_days') }} يوم</div>
        </div>
    </div>
    @endif
</div>

<style>
    .wbs-tree {
        font-family: 'Cairo', sans-serif;
    }

    .wbs-node {
        margin-bottom: 5px;
    }

    .wbs-item {
        display: flex;
        align-items: center;
        padding: 12px;
        border-radius: 8px;
        transition: all 0.2s;
        background: #f5f5f7;
        margin-bottom: 5px;
    }

    .wbs-item:hover {
        background: #e8e8ea;
        transform: translateX(-5px);
    }

    .wbs-toggle {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: white;
        border: 1px solid #d2d2d7;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        margin-left: 10px;
        transition: all 0.2s;
    }

    .wbs-toggle:hover {
        background: #0071e3;
        border-color: #0071e3;
        color: white;
    }

    .wbs-toggle.collapsed {
        transform: rotate(-90deg);
    }

    .wbs-icon {
        width: 20px;
        height: 20px;
        margin-left: 10px;
        color: #0071e3;
    }

    .wbs-code {
        font-weight: 700;
        color: #0071e3;
        margin-left: 15px;
        font-family: 'SF Mono', monospace;
    }

    .wbs-name {
        flex: 1;
        font-weight: 600;
        color: #1d1d1f;
    }

    .wbs-badge {
        background: white;
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 0.75rem;
        margin-left: 10px;
        color: #86868b;
    }

    .wbs-actions {
        display: flex;
        gap: 5px;
        margin-right: 10px;
    }

    .wbs-btn {
        padding: 6px 12px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 0.75rem;
        font-weight: 600;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
        font-family: 'Cairo', sans-serif;
    }

    .wbs-btn-edit {
        background: #0071e3;
        color: white;
    }

    .wbs-btn-edit:hover {
        background: #0051a8;
    }

    .wbs-btn-delete {
        background: #ff3b30;
        color: white;
    }

    .wbs-btn-delete:hover {
        background: #c91a0f;
    }

    .wbs-children {
        margin-right: 40px;
        margin-top: 5px;
    }

    .wbs-children.collapsed {
        display: none;
    }

    .level-1 .wbs-item { background: #f5f5f7; }
    .level-2 .wbs-item { background: #e8f5e9; }
    .level-3 .wbs-item { background: #e3f2fd; }
    .level-4 .wbs-item { background: #fff3e0; }
    .level-5 .wbs-item { background: #fce4ec; }
</style>

@push('scripts')
<script>
    lucide.createIcons();

    // Toggle tree nodes
    document.querySelectorAll('.wbs-toggle').forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.stopPropagation();
            const children = this.closest('.wbs-node').querySelector('.wbs-children');
            if (children) {
                children.classList.toggle('collapsed');
                this.classList.toggle('collapsed');
            }
        });
    });
</script>
@endpush
@endsection
