{{-- Zone d'écriture : $action --}}
<form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="chat-form-wrap" id="chat-form" data-no-loading>
    @csrf
    <div id="chat-chip" class="chat-chip d-none"><i class="bi bi-paperclip"></i><span id="chip-text"></span><button type="button" id="chip-x" aria-label="Retirer">&times;</button></div>
    <div class="chat-form">
        <input type="file" name="file" id="c-file" class="d-none" accept=".jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.zip,image/*">
        <input type="hidden" name="lat" id="c-lat"><input type="hidden" name="lng" id="c-lng">
        <button type="button" class="icon-btn" id="btn-attach" title="Joindre un fichier" data-no-loading><i class="bi bi-paperclip"></i></button>
        <button type="button" class="icon-btn" id="btn-photo" title="Photo" data-no-loading><i class="bi bi-camera"></i></button>
        <button type="button" class="icon-btn" id="btn-geo" title="Partager ma position exacte" data-no-loading><i class="bi bi-geo-alt"></i></button>
        <input type="text" name="body" id="c-body" class="form-control" placeholder="Écrire un message…" maxlength="2000" autocomplete="off">
        <button class="btn btn-primary px-4" title="Envoyer"><i class="bi bi-send-fill"></i></button>
    </div>
</form>
<script>
(function () {
    var f = document.getElementById('c-file'), lat = document.getElementById('c-lat'), lng = document.getElementById('c-lng');
    var chip = document.getElementById('chat-chip'), txt = document.getElementById('chip-text'), body = document.getElementById('c-body');
    function reset() { f.value = ''; lat.value = ''; lng.value = ''; chip.classList.add('d-none'); }
    function show(t, icon) { txt.textContent = t; chip.querySelector('i').className = 'bi bi-' + icon; chip.classList.remove('d-none'); }

    document.getElementById('btn-attach').onclick = function () { f.removeAttribute('capture'); f.accept = '.jpg,.jpeg,.png,.gif,.webp,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.csv,.zip,image/*'; f.click(); };
    document.getElementById('btn-photo').onclick  = function () { f.accept = 'image/*'; f.setAttribute('capture', 'environment'); f.click(); };
    f.onchange = function () {
        if (!f.files.length) { return; }
        if (f.files[0].size > 10 * 1024 * 1024) { alert('Fichier trop volumineux (10 Mo maximum).'); reset(); return; }
        lat.value = ''; lng.value = ''; show(f.files[0].name, f.files[0].type.indexOf('image/') === 0 ? 'image' : 'paperclip');
    };
    document.getElementById('btn-geo').onclick = function () {
        if (!navigator.geolocation) { alert('La géolocalisation n\'est pas disponible sur cet appareil.'); return; }
        show('Recherche de votre position…', 'geo-alt');
        navigator.geolocation.getCurrentPosition(function (p) {
            f.value = ''; lat.value = p.coords.latitude.toFixed(7); lng.value = p.coords.longitude.toFixed(7);
            show('Position exacte jointe (± ' + Math.round(p.coords.accuracy) + ' m)', 'geo-alt-fill');
        }, function () { reset(); alert('Position introuvable. Autorisez la localisation (elle exige une connexion https).'); }, { enableHighAccuracy: true, timeout: 15000 });
    };
    document.getElementById('chip-x').onclick = reset;
    document.getElementById('chat-form').addEventListener('submit', function (e) {
        if (!body.value.trim() && !f.files.length && !lat.value) { e.preventDefault(); body.focus(); }
    });
})();
</script>
