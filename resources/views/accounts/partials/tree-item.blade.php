<div class="account-row" style="padding-right: {{ $level * 30 + 15 }}px;">
    <div style="font-family: 'Courier New', monospace; font-weight: 600; color: #0071e3;">
        {{ $account->code }}
    </div>
    <div class="account-name">
        @if($account->is_parent)
            <i data-lucide="folder" style="width: 16px; height: 16px; display: inline-block; vertical-align: middle; color: #f57c00;"></i>
        @else
            <i data-lucide="file-text" style="width: 16px; height: 16px; display: inline-block; vertical-align: middle; color: #666;"></i>
        @endif
        {{ $account->name }}
        @if($account->name_en)
            <span style="color: #999; font-size: 0.85rem;">({{ $account->name_en }})</span>
        @endif
    </div>
    <div>
        <span class="badge badge-{{ $account->type }}">
            @if($account->type === 'asset')
                أصول
            @elseif($account->type === 'liability')
                خصوم
            @elseif($account->type === 'equity')
                حقوق ملكية
            @elseif($account->type === 'revenue')
                إيرادات
            @else
                مصروفات
            @endif
        </span>
    </div>
    <div>
        <span class="badge badge-{{ $account->nature }}">
            {{ $account->nature === 'debit' ? 'مدين' : 'دائن' }}
        </span>
    </div>
    <div style="text-align: center; font-weight: 600;">
        {{ $account->level }}
    </div>
    <div>
        <span class="badge badge-{{ $account->is_active ? 'active' : 'inactive' }}">
            {{ $account->is_active ? 'نشط' : 'غير نشط' }}
        </span>
    </div>
    <div style="text-align: center;">
        <a href="{{ route('accounts.show', $account->id) }}" class="action-btn" title="عرض">
            <i data-lucide="eye" style="width: 16px; height: 16px;"></i>
        </a>
        <a href="{{ route('accounts.edit', $account->id) }}" class="action-btn" title="تعديل">
            <i data-lucide="edit" style="width: 16px; height: 16px;"></i>
        </a>
        <form id="delete-form-{{ $account->id }}" action="{{ route('accounts.destroy', $account->id) }}" method="POST" style="display: inline;">
            @csrf
            @method('DELETE')
            <button type="button" class="action-btn delete" onclick="confirmDelete({{ $account->id }}, '{{ $account->name }}')" title="حذف">
                <i data-lucide="trash-2" style="width: 16px; height: 16px;"></i>
            </button>
        </form>
    </div>
</div>

@if($account->children && $account->children->count() > 0)
    @foreach($account->children as $child)
        @include('accounts.partials.tree-item', ['account' => $child, 'level' => $level + 1])
    @endforeach
@endif
