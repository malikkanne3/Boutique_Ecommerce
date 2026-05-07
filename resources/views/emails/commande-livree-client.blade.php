<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="font-family:Arial,sans-serif;background:#f4f4f4;padding:20px">
<div style="max-width:600px;margin:auto;background:white;border-radius:10px;padding:30px">

    <div style="text-align:center;margin-bottom:25px">
        <div style="font-size:60px">✅</div>
        <h2 style="color:#16a34a;margin:10px 0">Votre commande est livrée !</h2>
    </div>

    <p>Bonjour <strong>{{ $order->customer_name }}</strong>,</p>
    <p>Nous avons le plaisir de vous informer que votre commande a été livrée avec succès.</p>

    <table style="width:100%;border-collapse:collapse;margin:20px 0">
        <tr style="background:#f0fdf4">
            <td style="padding:10px;border:1px solid #eee"><strong>N° Commande</strong></td>
            <td style="padding:10px;border:1px solid #eee">{{ $order->order_number }}</td>
        </tr>
        <tr>
            <td style="padding:10px;border:1px solid #eee"><strong>Adresse de livraison</strong></td>
            <td style="padding:10px;border:1px solid #eee">{{ $order->shipping_address }}</td>
        </tr>
        <tr style="background:#f0fdf4">
            <td style="padding:10px;border:1px solid #eee"><strong>Montant total</strong></td>
            <td style="padding:10px;border:1px solid #eee;color:#16a34a;font-weight:bold">
                {{ $order->formatted_total }}
            </td>
        </tr>
        <tr>
            <td style="padding:10px;border:1px solid #eee"><strong>Livreur</strong></td>
            <td style="padding:10px;border:1px solid #eee">{{ $order->livreur->name ?? 'N/A' }}</td>
        </tr>
    </table>

    <div style="background:#f0fdf4;border-left:4px solid #16a34a;padding:15px;margin:20px 0;border-radius:4px">
        <p style="margin:0;color:#15803d">
            <strong>Articles commandés :</strong><br>
            @foreach($order->products as $produit)
                • {{ $produit->name }} × {{ $produit->pivot->quantity }}<br>
            @endforeach
        </p>
    </div>

    <p>Merci pour votre confiance et votre achat chez nous. Nous espérons vous revoir bientôt !</p>

    <a href="{{ url('/my-orders') }}"
       style="background:#16a34a;color:white;padding:12px 25px;border-radius:8px;text-decoration:none;display:inline-block;margin-top:10px">
        Voir mes commandes
    </a>

    <p style="margin-top:30px;color:#999;font-size:12px">
        Ecommerce Dakar — Notification automatique<br>
        Si vous avez des questions, contactez notre support.
    </p>
</div>
</body>
</html>
