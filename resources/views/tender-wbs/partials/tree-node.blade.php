<div class="wbs-node level-{{ $level }}">
    <div class="wbs-item">
        @if($node->children->count() > 0)
            <div class="wbs-toggle">
                <i data-lucide="chevron-down" style="width: 16px; height: 16px;"></i>
            </div>
        @else
            <div style="width: 24px; margin-left: 10px;"></div>
        @endif

        @if($node->is_summary)
            <i data-lucide="folder" class="wbs-icon"></i>
        @else
            <i data-lucide="file-text" class="wbs-icon"></i>
        @endif

        <span class="wbs-code">{{ $node->wbs_code }}</span>
        <span class="wbs-name">{{ $node->name }}</span>

        @if($node->weight_percentage > 0)
            <span class="wbs-badge">{{ $node->weight_percentage }}%</span>
        @endif

        @if($node->estimated_cost > 0)
            <span class="wbs-badge">{{ number_format($node->estimated_cost, 0) }} ريال</span>
        @endif

        @if($node->estimated_duration_days)
            <span class="wbs-badge">{{ $node->estimated_duration_days }} يوم</span>
        @endif

        <div class="wbs-actions">
            <a href="{{ route('tender-wbs.edit', [$node->tender_id, $node->id]) }}" class="wbs-btn wbs-btn-edit">
                تعديل
            </a>
            <form method="POST" action="{{ route('tender-wbs.destroy', [$node->tender_id, $node->id]) }}" style="display: inline;" onsubmit="return confirm('هل أنت متأكد من حذف هذا العنصر؟');">
                @csrf
                @method('DELETE')
                <button type="submit" class="wbs-btn wbs-btn-delete">حذف</button>
            </form>
        </div>
    </div>

    @if($node->children->count() > 0)
        <div class="wbs-children">
            @foreach($node->children as $child)
                @include('tender-wbs.partials.tree-node', ['node' => $child, 'level' => $level + 1])
            @endforeach
        </div>
    @endif
</div>
