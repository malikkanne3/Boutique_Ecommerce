<div class="commande-card">
    <div class="commande-header">
        <div>
            <span class="fw-bold" style="font-family:monospace;font-size:.95rem">{{ $cmd->order_number }}</span>
            <span class="text-muted ms-2" style="font-size:.78rem">{{ $cmd->created_at->format('d/m/Y à H:i') }}</span>
        </div>
        <span class="badge-{{ $cmd->status }}">
            @if($cmd->status === 'confirmed') ⏳ À accepter
            @elseif($cmd->status === 'delivering') 🚚 En livraison
            @elseif($cmd->status === 'delivered') ✅ Livrée
            @endif
        </span>
    </div>

    <div class="commande-body">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="info-item"><i class="bi bi-person-fill"></i> <strong>{{ $cmd->customer_name }}</strong></div>
                <div class="info-item"><i class="bi bi-telephone-fill"></i> {{ $cmd->customer_phone }}</div>
                <div class="info-item"><i class="bi bi-geo-alt-fill"></i> {{ $cmd->shipping_address }}</div>
                <div class="info-item"><i class="bi bi-cash-stack"></i> <strong style="color:#7c3aed">{{ $cmd->formatted_total }}</strong></div>
            </div>
            <div class="col-md-6">
                <p class="fw-bold mb-2" style="font-size:.85rem">Articles :</p>
                @foreach($cmd->products as $produit)
                <div class="article-item">
                    <span class="article-qty">×{{ $produit->pivot->quantity }}</span>
                    <span style="font-size:.85rem">{{ $produit->name }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="commande-footer">
        @if($cmd->status === 'confirmed')
        <form method="POST" action="{{ route('livreur.accepter', $cmd) }}">
            @csrf
            <button class="btn btn-success btn-sm px-3" style="border-radius:10px;font-weight:600">
                <i class="bi bi-check-circle me-1"></i>Accepter
            </button>
        </form>
        <form method="POST" action="{{ route('livreur.refuser', $cmd) }}">
            @csrf
            <button class="btn btn-outline-danger btn-sm px-3" style="border-radius:10px;font-weight:600">
                <i class="bi bi-x-circle me-1"></i>Refuser
            </button>
        </form>
        @elseif($cmd->status === 'delivering')
        <form method="POST" action="{{ route('livreur.livree', $cmd) }}">
            @csrf
            <button class="btn btn-primary btn-sm px-3" style="border-radius:10px;font-weight:600">
                <i class="bi bi-check2-all me-1"></i>Confirmer la livraison
            </button>
        </form>
        @elseif($cmd->status === 'delivered')
        <span class="text-success fw-bold" style="font-size:.85rem">
            <i class="bi bi-check-circle-fill me-1"></i>Livrée avec succès
        </span>
        @endif
    </div>
</div>