@props(['title', 'subtitle' => null])
<div class="page-head rise">
    <div><h2>{{ $title }}</h2>@if($subtitle)<p>{{ $subtitle }}</p>@endif</div>
    @if(isset($actions) && !$actions->isEmpty())<div class="d-flex gap-2 flex-wrap">{{ $actions }}</div>@endif
</div>
