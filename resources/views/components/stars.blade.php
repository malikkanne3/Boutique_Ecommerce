@props(['value' => 0, 'count' => null, 'size' => '1rem'])
@php $v = (float) $value; @endphp
<span class="stars" style="font-size:{{ $size }}" title="{{ number_format($v, 1) }} / 5">
    @for($i = 1; $i <= 5; $i++)
        @if($v >= $i)<i class="bi bi-star-fill"></i>@elseif($v >= $i - 0.5)<i class="bi bi-star-half"></i>@else<i class="bi bi-star"></i>@endif
    @endfor
    @if($count !== null)<small class="text-muted ms-1">({{ $count }})</small>@endif
</span>
