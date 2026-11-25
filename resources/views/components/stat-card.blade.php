@props(['icon' => 'graph-up', 'label', 'value', 'hint' => null, 'tone' => ''])
<div class="stat rise">
    <div class="stat-ico {{ $tone }}"><i class="bi bi-{{ $icon }}"></i></div>
    <div class="min-w-0">
        <div class="stat-label">{{ $label }}</div>
        <div class="stat-value">{{ $value }}</div>
        @if($hint)<div class="stat-hint">{{ $hint }}</div>@endif
    </div>
</div>
