@extends('layouts.dashboard')
@php
    $editing = $product->exists;
    $isAdmin = $prefix === 'admin';
    $variantRows = old('variants', $product->exists ? $product->variants->map(fn ($v) => ['size' => $v->size, 'color' => $v->color, 'stock' => $v->stock])->values()->all() : []);
    $dt = fn ($d) => $d ? $d->format('Y-m-d\TH:i') : '';
@endphp
@section('title', $editing ? 'Modifier le produit' : 'Nouveau produit')

@section('content')
<x-page-head :title="$editing ? 'Modifier « '.$product->name.' »' : 'Nouveau produit'" subtitle="Ces informations sont visibles par vos clients.">
    <x-slot:actions><a href="{{ route($prefix.'.products.index') }}" class="btn btn-ghost"><i class="bi bi-arrow-left me-1"></i>Retour</a></x-slot:actions>
</x-page-head>

<form method="POST" enctype="multipart/form-data" action="{{ $editing ? route($prefix.'.products.update', $product) : route($prefix.'.products.store') }}" class="row g-4">
    @csrf
    @if($editing) @method('PUT') @endif

    <div class="col-lg-8">
        {{-- Informations --}}
        <div class="ui-card mb-4 rise"><div class="ui-card-head">Informations générales</div>
        <div class="ui-card-body row g-3">
            <div class="col-12"><label class="form-label">Nom du produit</label>
                <input type="text" name="name" value="{{ old('name', $product->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-8"><label class="form-label">Catégorie</label>
                <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                    <option value="">Choisir…</option>
                    @foreach($categories as $c)<option value="{{ $c->id }}" @selected((int) old('category_id', $product->category_id) === $c->id)>{{ $c->name }}</option>@endforeach
                </select>@error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-4"><label class="form-label">Prix (FCFA)</label>
                <input type="number" step="1" min="0" name="price" value="{{ old('price', $product->exists ? (int) $product->price : '') }}" class="form-control @error('price') is-invalid @enderror" required>
                @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-12"><label class="form-label">Description</label>
                <textarea name="description" rows="5" class="form-control" placeholder="Matière, dimensions, garantie, entretien…">{{ old('description', $product->description) }}</textarea></div>
            @if($isAdmin)
                <div class="col-12"><label class="form-label">Vendeur propriétaire</label>
                    <select name="seller_id" class="form-select">
                        <option value="">E-Shop SN (boutique officielle)</option>
                        @foreach($sellers as $s)<option value="{{ $s->id }}" @selected((int) old('seller_id', $product->seller_id) === $s->id)>{{ $s->display_name }} ({{ $s->email }})</option>@endforeach
                    </select></div>
            @endif
        </div></div>

        {{-- Photos --}}
        <div class="ui-card mb-4 rise"><div class="ui-card-head">Photos <small>jusqu'à 6 · la première est la photo principale</small></div>
        <div class="ui-card-body">
            @if($editing && $product->images->isNotEmpty())
                <div class="photo-grid mb-3">
                    @foreach($product->images as $img)
                        <div class="ph"><img src="{{ $img->url }}" alt=""><label><input type="checkbox" name="remove_images[]" value="{{ $img->id }}" class="form-check-input me-1">Supprimer</label></div>
                    @endforeach
                </div>
            @elseif($editing && $product->image)
                <div class="photo-grid mb-3"><div class="ph"><img src="{{ $product->image_url }}" alt=""></div></div>
            @endif
            <input type="file" name="images[]" accept="image/*" multiple class="form-control @error('images.*') is-invalid @enderror">
            @error('images')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            @error('images.*')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            <small class="text-muted d-block mt-1">JPG / PNG, 3 Mo maximum par photo.</small>
            <label class="form-label mt-3">…ou lien d'image <small class="text-muted">(si aucune photo envoyée)</small></label>
            <input type="url" name="image_url" value="{{ old('image_url') }}" class="form-control @error('image_url') is-invalid @enderror" placeholder="https://…">
            @error('image_url')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div></div>

        {{-- Stock & variantes (vendeur uniquement) --}}
        @if($isAdmin)
            <div class="ui-card mb-4 rise"><div class="ui-card-body d-flex gap-3 align-items-center">
                <i class="bi bi-lock-fill fs-3 text-accent"></i>
                <div><b>Stock, tailles et couleurs</b><div class="small text-muted">Ces éléments sont gérés uniquement par le vendeur du produit. L'administrateur n'y a pas accès.</div></div>
            </div></div>
        @else
            <div class="ui-card mb-4 rise"><div class="ui-card-head">Tailles &amp; couleurs <small>facultatif — idéal pour les vêtements et chaussures</small></div>
            <div class="ui-card-body">
                <div class="row g-3 mb-3">
                    <div class="col-md-5"><label class="form-label">Tailles <small class="text-muted">(séparées par des virgules)</small></label>
                        <input type="text" id="sizes-input" class="form-control" placeholder="S, M, L, XL"></div>
                    <div class="col-md-5"><label class="form-label">Couleurs <small class="text-muted">(séparées par des virgules)</small></label>
                        <input type="text" id="colors-input" class="form-control" placeholder="Noir, Blanc, Rouge"></div>
                    <div class="col-md-2 d-flex align-items-end"><button type="button" class="btn btn-soft w-100" id="gen-btn" data-no-loading><i class="bi bi-magic me-1"></i>Générer</button></div>
                </div>
                <div id="variants-empty" class="text-muted small"><i class="bi bi-info-circle me-1"></i>Sans variantes, le produit utilise le stock simple ci-dessous. Avec des variantes, indiquez le stock de chaque combinaison.</div>
                <div class="table-responsive"><table class="table table-ui" id="variants-table" style="display:none">
                    <thead><tr><th>Taille</th><th>Couleur</th><th style="width:130px">Stock</th><th></th></tr></thead>
                    <tbody id="variants-body"></tbody>
                </table></div>
            </div></div>
        @endif

        {{-- Tarifs --}}
        <div class="ui-card rise"><div class="ui-card-head">Promotions &amp; tarifs dégressifs</div>
        <div class="ui-card-body row g-3">
            <div class="col-12"><b class="small text-muted text-uppercase">Réduction sur une période</b></div>
            <div class="col-md-4"><label class="form-label">Réduction (%)</label>
                <input type="number" min="1" max="90" name="discount_percent" value="{{ old('discount_percent', $product->discount_percent) }}" class="form-control @error('discount_percent') is-invalid @enderror" placeholder="Ex : 20">
                @error('discount_percent')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-4"><label class="form-label">Début</label>
                <input type="datetime-local" name="discount_starts_at" value="{{ old('discount_starts_at', $dt($product->discount_starts_at)) }}" class="form-control"></div>
            <div class="col-md-4"><label class="form-label">Fin</label>
                <input type="datetime-local" name="discount_ends_at" value="{{ old('discount_ends_at', $dt($product->discount_ends_at)) }}" class="form-control @error('discount_ends_at') is-invalid @enderror">
                @error('discount_ends_at')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-12"><small class="text-muted">Laissez la réduction vide pour ne pas en appliquer. Sans dates, elle s'applique tout de suite et sans limite.</small></div>
            <div class="col-12 mt-3"><b class="small text-muted text-uppercase">Tarif dégressif</b></div>
            <div class="col-md-4"><label class="form-label">À partir de (unités)</label>
                <input type="number" min="2" name="bulk_min_qty" value="{{ old('bulk_min_qty', $product->bulk_min_qty) }}" class="form-control @error('bulk_min_qty') is-invalid @enderror" placeholder="Ex : 5">
                @error('bulk_min_qty')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
            <div class="col-md-4"><label class="form-label">Réduction supplémentaire (%)</label>
                <input type="number" min="1" max="90" name="bulk_percent" value="{{ old('bulk_percent', $product->bulk_percent) }}" class="form-control @error('bulk_percent') is-invalid @enderror" placeholder="Ex : 10">
                @error('bulk_percent')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        </div></div>
    </div>

    <div class="col-lg-4">
        <div class="ui-card mb-4 rise"><div class="ui-card-head">Publication</div>
        <div class="ui-card-body">
            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" role="switch" name="is_active" value="1" id="is_active" @checked(old('is_active', $product->is_active))>
                <label class="form-check-label fw-bold" for="is_active">Visible dans la boutique</label>
            </div>
            @unless($isAdmin)
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" role="switch" name="is_available" value="1" id="is_available" @checked(old('is_available', $product->is_available))>
                    <label class="form-check-label fw-bold" for="is_available">Disponible à la vente</label>
                    <div class="small text-muted">Décochez pour marquer « en rupture ».</div>
                </div>
                <div id="simple-stock">
                    <label class="form-label">Stock (produit simple)</label>
                    <input type="number" min="0" name="stock" id="stock-input" value="{{ old('stock', $product->stock) }}" class="form-control @error('stock') is-invalid @enderror">
                    @error('stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="small text-muted mt-1" id="stock-hint">Quantité disponible.</div>
                </div>
            @endunless
            <button class="btn btn-primary btn-lg w-100 mt-4"><i class="bi bi-check2-circle me-1"></i>{{ $editing ? 'Enregistrer' : 'Publier le produit' }}</button>
        </div></div>
    </div>
</form>
@endsection

@unless($isAdmin)
@push('scripts')
<script>
(function () {
    var rows = @json($variantRows);
    var body = document.getElementById('variants-body'), table = document.getElementById('variants-table'), empty = document.getElementById('variants-empty');
    var stockInput = document.getElementById('stock-input'), hint = document.getElementById('stock-hint');

    function esc(v) { return String(v == null ? '' : v).replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;'); }

    function render() {
        body.innerHTML = '';
        rows.forEach(function (r, i) {
            var tr = document.createElement('tr');
            tr.innerHTML = '<td><input class="form-control form-control-sm" name="variants[' + i + '][size]" value="' + esc(r.size) + '" placeholder="—"></td>'
                + '<td><input class="form-control form-control-sm" name="variants[' + i + '][color]" value="' + esc(r.color) + '" placeholder="—"></td>'
                + '<td><input type="number" min="0" class="form-control form-control-sm" name="variants[' + i + '][stock]" value="' + esc(r.stock) + '"></td>'
                + '<td class="text-end"><button type="button" class="btn btn-sm btn-outline-danger" data-i="' + i + '" data-no-loading><i class="bi bi-x-lg"></i></button></td>';
            body.appendChild(tr);
        });
        var has = rows.length > 0;
        table.style.display = has ? '' : 'none';
        empty.style.display = has ? 'none' : '';
        if (stockInput) { stockInput.disabled = has; hint.textContent = has ? 'Le stock total est la somme des variantes.' : 'Quantité disponible.'; }
    }

    function split(v) { return v.split(',').map(function (x) { return x.trim(); }).filter(Boolean); }

    document.getElementById('gen-btn').addEventListener('click', function () {
        var sizes = split(document.getElementById('sizes-input').value), colors = split(document.getElementById('colors-input').value);
        if (!sizes.length && !colors.length) { return; }
        var combos = [];
        (sizes.length ? sizes : [null]).forEach(function (s) { (colors.length ? colors : [null]).forEach(function (c) { combos.push({ size: s, color: c }); }); });
        combos.forEach(function (cb) {
            var exists = rows.some(function (r) { return (r.size || '').toLowerCase() === (cb.size || '').toLowerCase() && (r.color || '').toLowerCase() === (cb.color || '').toLowerCase(); });
            if (!exists) { rows.push({ size: cb.size, color: cb.color, stock: 0 }); }
        });
        render();
    });
    body.addEventListener('click', function (e) {
        var b = e.target.closest('button[data-i]'); if (!b) return;
        // conserve les valeurs saisies avant de supprimer une ligne
        rows = Array.prototype.map.call(body.querySelectorAll('tr'), function (tr) { var i = tr.querySelectorAll('input'); return { size: i[0].value, color: i[1].value, stock: i[2].value }; });
        rows.splice(parseInt(b.getAttribute('data-i'), 10), 1);
        render();
    });
    render();
})();
</script>
@endpush
@endunless
