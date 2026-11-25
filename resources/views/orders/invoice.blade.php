<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Facture {{ $order->order_number }} — E-Shop SN</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; } body { font-family: 'Plus Jakarta Sans', Arial, sans-serif; color: #1d1437; margin: 0; background: #f3f0fa; }
        .toolbar { max-width: 820px; margin: 18px auto 0; display: flex; gap: .6rem; justify-content: flex-end; padding: 0 12px; }
        .toolbar button, .toolbar a { padding: .6rem 1.1rem; border-radius: 10px; border: 0; background: #7c3aed; color: #fff; font-weight: 700; cursor: pointer; text-decoration: none; font-size: .9rem; font-family: inherit; }
        .toolbar a.alt { background: #fff; color: #7c3aed; border: 1px solid #ddd6fe; }
        .sheet { max-width: 820px; margin: 14px auto 40px; background: #fff; padding: 44px; border-radius: 16px; box-shadow: 0 10px 40px rgba(76,29,149,.12); }
        .head { display: flex; justify-content: space-between; gap: 20px; align-items: flex-start; border-bottom: 3px solid #7c3aed; padding-bottom: 20px; }
        .head img { height: 46px; } h1 { margin: 0; font-size: 1.6rem; letter-spacing: -.02em; text-align: right; } .muted { color: #6c6590; font-size: .85rem; }
        .cols { display: flex; gap: 30px; margin: 26px 0; } .cols > div { flex: 1; } .cols h4 { margin: 0 0 6px; font-size: .72rem; text-transform: uppercase; letter-spacing: .1em; color: #7c3aed; }
        table { width: 100%; border-collapse: collapse; font-size: .9rem; } th { text-align: left; font-size: .72rem; text-transform: uppercase; letter-spacing: .08em; color: #6c6590; padding: 10px 8px; border-bottom: 2px solid #e6ddfb; }
        td { padding: 12px 8px; border-bottom: 1px solid #f0ebfa; vertical-align: top; } .r { text-align: right; white-space: nowrap; } .chip { display: inline-block; background: #ede9fe; color: #6d28d9; border-radius: 99px; padding: 1px 8px; font-size: .72rem; font-weight: 700; margin-right: 3px; }
        .total { margin-left: auto; width: 300px; margin-top: 16px; } .total div { display: flex; justify-content: space-between; padding: 6px 0; font-size: .92rem; }
        .total .grand { border-top: 2px solid #1d1437; margin-top: 6px; padding-top: 10px; font-size: 1.15rem; font-weight: 800; color: #7c3aed; }
        .foot { margin-top: 34px; padding-top: 16px; border-top: 1px solid #e6ddfb; font-size: .78rem; color: #6c6590; text-align: center; }
        .badge { display: inline-block; padding: 3px 12px; border-radius: 99px; font-weight: 700; font-size: .75rem; background: #fef3c7; color: #b45309; } .badge.paid { background: #d1fae5; color: #047857; }
        @media print { body { background: #fff; } .toolbar { display: none; } .sheet { box-shadow: none; margin: 0; padding: 0; max-width: none; border-radius: 0; } @page { size: A4; margin: 16mm; } }
    </style>
</head>
<body>
<div class="toolbar"><a class="alt" href="javascript:history.back()">← Retour</a><button onclick="window.print()">🖨️ Imprimer / Enregistrer en PDF</button></div>
<div class="sheet">
    <div class="head">
        <img src="{{ asset('images/logo.svg') }}" alt="E-Shop SN">
        <div><h1>FACTURE</h1><div class="muted" style="text-align:right">N° FAC-{{ $order->order_number }}<br>Date : {{ $order->created_at->format('d/m/Y') }}</div></div>
    </div>

    <div class="cols">
        <div><h4>Vendeur</h4><b>{{ $order->seller_name }}</b><div class="muted">@if($order->seller){{ $order->seller->address }}<br>{{ $order->seller->phone }}<br>{{ $order->seller->email }}@else Plateforme E-Shop SN — Dakar, Sénégal @endif</div></div>
        <div><h4>Facturé à</h4><b>{{ $order->customer_name }}</b><div class="muted">{{ $order->customer_email }}<br>{{ $order->customer_phone }}<br>{{ $order->shipping_address }}</div></div>
        <div><h4>Commande</h4><b>{{ $order->order_number }}</b><div class="muted">{{ $order->status_label }}<br>Paiement : {{ $order->payment_label }}<br><span class="badge {{ $order->payment_status === 'paid' ? 'paid' : '' }}">{{ $order->payment_status === 'paid' ? 'Payée' : 'À régler à la livraison' }}</span></div></div>
    </div>

    <table>
        <thead><tr><th>Désignation</th><th class="r">Qté</th><th class="r">Prix unitaire</th><th class="r">Total</th></tr></thead>
        <tbody>
        @foreach($order->products as $p)
            <tr><td><b>{{ $p->name }}</b>@if($p->pivot->size || $p->pivot->color)<br>@if($p->pivot->size)<span class="chip">Taille {{ $p->pivot->size }}</span>@endif @if($p->pivot->color)<span class="chip">{{ $p->pivot->color }}</span>@endif @endif</td>
                <td class="r">{{ $p->pivot->quantity }}</td><td class="r">{{ number_format($p->pivot->price, 0, ',', ' ') }} FCFA</td><td class="r">{{ number_format($p->pivot->price * $p->pivot->quantity, 0, ',', ' ') }} FCFA</td></tr>
        @endforeach
        </tbody>
    </table>
    <div class="total">
        <div><span>Sous-total</span><span>{{ $order->formatted_total }}</span></div>
        <div><span>Livraison</span><span>Selon accord</span></div>
        <div class="grand"><span>Total TTC</span><span>{{ $order->formatted_total }}</span></div>
    </div>

    <div class="foot">Facture générée par E-Shop SN le {{ now()->format('d/m/Y à H:i') }} · Merci pour votre confiance.</div>
</div>
</body>
</html>
