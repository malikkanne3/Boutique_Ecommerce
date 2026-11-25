{{-- Fenêtre de transfert : $forwardTargets --}}
<div class="modal fade" id="forwardModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" method="POST" action="{{ route('messages.forward') }}" style="border-radius:20px;background:var(--surface);color:var(--text)">
            @csrf
            <input type="hidden" name="kind" id="fw-kind"><input type="hidden" name="message_id" id="fw-id">
            <div class="modal-header border-0"><h5 class="modal-title fw-bold"><i class="bi bi-reply-fill text-accent me-2" style="transform:scaleX(-1);display:inline-block"></i>Transférer ce message</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button></div>
            <div class="modal-body">
                <label class="form-label">Envoyer à…</label>
                <select name="target" class="form-select" required>
                    <option value="">Choisir un contact ou un groupe</option>
                    @foreach($forwardTargets as $label => $options)
                        @if(count($options))
                            <optgroup label="{{ $label }}">
                                @foreach($options as $o)<option value="{{ $o['value'] }}">{{ $o['label'] }}</option>@endforeach
                            </optgroup>
                        @endif
                    @endforeach
                </select>
                <small class="text-muted d-block mt-2">Le message (texte, photo, fichier ou position) sera copié tel quel avec la mention « Transféré ».</small>
            </div>
            <div class="modal-footer border-0"><button type="button" class="btn btn-ghost" data-bs-dismiss="modal">Annuler</button><button class="btn btn-primary"><i class="bi bi-send me-1"></i>Transférer</button></div>
        </form>
    </div>
</div>
<script>
document.addEventListener('click', function (e) {
    var b = e.target.closest('[data-forward]'); if (!b) return;
    document.getElementById('fw-kind').value = b.getAttribute('data-kind');
    document.getElementById('fw-id').value = b.getAttribute('data-id');
    bootstrap.Modal.getOrCreateInstance(document.getElementById('forwardModal')).show();
});
</script>
