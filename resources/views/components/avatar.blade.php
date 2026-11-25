@props(['user' => null, 'size' => 'md'])
@php
    $px  = ['xs' => 26, 'sm' => 32, 'md' => 40, 'lg' => 58, 'xl' => 88][$size] ?? 40;
    $url = $user ? $user->photo_url : null;
@endphp
@if($url)
    <img src="{{ $url }}" alt="{{ $user->name }}" class="avatar-img" style="width:{{ $px }}px;height:{{ $px }}px" loading="lazy">
@else
    <span class="avatar" style="width:{{ $px }}px;height:{{ $px }}px;font-size:{{ round($px * .4) }}px" title="{{ $user ? $user->name : '' }}">{{ $user ? $user->initial : '?' }}</span>
@endif
