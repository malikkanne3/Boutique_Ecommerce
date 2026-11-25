@props(['id', 'current' => 0])
<div class="star-input">
    @for($i = 5; $i >= 1; $i--)
        <input type="radio" name="rating" id="{{ $id }}-{{ $i }}" value="{{ $i }}" @checked((int) $current === $i) required>
        <label for="{{ $id }}-{{ $i }}" title="{{ $i }} / 5"><i class="bi bi-star-fill"></i></label>
    @endfor
</div>
