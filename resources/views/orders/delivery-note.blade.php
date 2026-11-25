<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bon de livraison {{ $order->order_number }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; } body { font-family: 'Plus Jakarta Sans', Arial, sans-serif; color: #1d1437; margin: 0; background: #f3f0fa; }
        .toolbar { max-width: 820px; margin: 18px auto 0; display: flex; gap: .6rem; justify-content: flex-end; padding: 0 12px; }
        .toolbar button, .toolbar a { padding: .6rem 1.1rem; border-radius: 10px; border: 0; background: #ea580c; color: #fff; font-weight: 700; cursor: pointer; text-decoration: none; font-size: .9rem; font-family: inherit; }
        .toolbar a.alt { background: #fff; color: #ea580c; border: 1px solid #fed7aa; }
        .sheet { max-width: 820px; margin: 14px auto 40px; background: #fff; padding: 44px; border-radius: 16px; box-shadow: 0 10px 40px rgba(0,0,0,.08); }
        .head { display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 3px solid #ea580c; padding-bottom: 18px; } .head img { height: 44px; }
        h1 { margin: 0; font-size: 1.5rem; text-align: right; } .muted { color: #6c6590; font-size: .85rem; }
        .box { border: 2px solid #1d1437; border-radius: 14px; padding: 18px 22px; margin: 24px 0; } .box h4 { margin: 0 0 8px; font-size: .72rem; text-transform: uppercase; letter-spacing: .1em; color: #ea580c; }
        .big { font-size: 1.1rem; font-weight: 800; } .row { display: flex; gap: 26px; } .row > div { flex: 1; }
        table { width: 100%; border-collapse: collapse; font-size: .92rem; } th { text-align: left; font-size: .72rem; text-transform: uppercase; color: #6c6590; padding: 9px 8px; border-bottom: 2px solid #fed7aa; }
        td { padding: 11px 8px; border-bottom: 1px solid #f5ede4; } .r { text-align: right; } .chip { display: inline-block; background: #ffedd5; color: #c2410c; border-radius: 99px; padding: 1px 8px; font-size: .72rem; font-weight: 700; }
        .collect { background: #fff7ed; border: 2px dashed #ea580c; border-radius: 14px; padding: 16px 22px; margin: 22px 0; display: flex; justify-content: space-between; align-items: center; font-weight: 700; } .collect b { font-size: 1.5rem; color: #c2410c; }
        .sign { display: flex; gap: 30px; margin-top: 30px; } .sign > div { flex: 1; border: 1px solid #d6d3d1; border-radius: 12px; height: 110px; padding: 10px 14px; font-size: .8rem; color: #6c6590; }
        @media print { body { background: #fff; } .toolbar { display: none; } .sheet { box-shadow: none; margin: 0; padding: 0; max-width: none; } @page { size: A4; margin: 16mm; } }
    </style>
</head>
<body>
<div class="toolbar"><a class="alt" href="javascript:history.back()">← Retour</a><button onclick="window.print()">🖨️ Imprimer / Enregistrer en PDF</button></div>
<div class="sheet">
    <div class="head"><img src="{{ asset('images/logo.svg') }}" alt="E-Shop SN"><div><h1>BON DE LIVRAISON</h1><div class="muted" style="text-align:right">{{ $order->order_number }}<br>{{ $order->created_at->format('d/m/Y') }}</div></div></div>

    <div class="box">
        <div class="row">
            <div><h4>Destinataire</h4><div class="big">{{ $order->customer_name }}</div><div>📞 {{ $order->customer_phone }}</div></div>
            <div><h4>Adresse de livraison</h4><div class="big">{{ $order->shipping_address }}</div>@if($order->delivery_note)<div class="muted">Note : {{ $order->delivery_note }}</div>@endif @if($order->has_exact_location)<div class="muted">GPS : {{ $order->delivery_lat }}, {{ $order->delivery_lng }}</div>@endif</div>
        </div>
    </div>
    <div class="row muted" style="margin-bottom:14px"><div>Expéditeur : <b>{{ $order->seller_name }}</b></div><div>Livreur : <b>{{ $order->livreur ? $order->livreur->name : 'non assigné' }}</b></div></div>

    <table>
        <thead><tr><th>Article</th><th>Options</th><th class="r">Quantité</th></tr></thead>
        <tbody>
        @foreach($order->products as $p)
            <tr><td><b>{{ $p->name }}</b></td><td>@if($p->pivot->size)<span class="chip">{{ $p->pivot->size }}</span> @endif @if($p->pivot->color)<span class="chip">{{ $p->pivot->color }}</span>@endif</td><td class="r"><b>{{ $p->pivot->quantity }}</b></td></tr>
        @endforeach
        </tbody>
    </table>

    <div class="collect"><span>À encaisser à la livraison<br><small class="muted">{{ $order->payment_label }} — {{ $order->payment_status === 'paid' ? 'déjà payé' : 'paiement à la réception' }}</small></span><b>{{ $order->payment_status === 'paid' ? '0 FCFA' : $order->formatted_total }}</b></div>

    <div class="sign"><div>Signature du livreur</div><div>Signature du client (réception conforme)</div></div>
</div>
</body>
</html>
