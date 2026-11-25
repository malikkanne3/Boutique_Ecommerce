{{-- Messages flash affichés en "toasts" --}}
<div class="toast-zone">
    @foreach(['success' => ['check-circle-fill', 'text-bg-success'], 'error' => ['exclamation-triangle-fill', 'text-bg-danger']] as $key => [$icon, $cls])
        @if(session($key))
            <div class="toast align-items-center {{ $cls }}" role="alert" aria-live="assertive">
                <div class="d-flex">
                    <div class="toast-body d-flex gap-2 align-items-center fw-semibold">
                        <i class="bi bi-{{ $icon }}"></i><span>{{ session($key) }}</span>
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Fermer"></button>
                </div>
            </div>
        @endif
    @endforeach
    @if($errors->any())
        <div class="toast align-items-center text-bg-danger" role="alert">
            <div class="d-flex">
                <div class="toast-body"><i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $errors->first() }}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    @endif
</div>
