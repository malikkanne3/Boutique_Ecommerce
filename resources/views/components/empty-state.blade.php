@props(['icon' => 'inbox', 'title', 'text' => null])
<div class="empty">
    <i class="bi bi-{{ $icon }}"></i>
    <h5>{{ $title }}</h5>
    @if($text)<p class="mb-3">{{ $text }}</p>@endif
    {{ $slot }}
</div>
