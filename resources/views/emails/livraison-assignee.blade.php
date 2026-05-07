<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="font-family:Arial,sans-serif;background:#f4f4f4;padding:20px">
<div style="max-width:600px;margin:auto;background:white;border-radius:10px;padding:30px">
    <h2 style="color:#d97706">🚀 Nouvelle livraison assignée !</h2>
    <p>Bonjour <strong>{{ $order->livreur->name }}</strong>,</p>
    <p>Une nouvelle commande vous a été assignée pour livraison.</p>
    <table style="width:100%;border-collapse:collapse;margin:20px 0">
        <tr style="background:#fffbeb">
            <td style="padding:10px;border:1px solid #eee"><strong>N° Commande</strong></td>
            <td style="padding:10px;border:1px solid #eee">{{ $order->order_number }}</td>
        </tr>
        <tr>
            <td style="padding:10px;border:1px solid #eee"><strong>Client</strong></td>
            <td style="padding:10px;border:1px solid #eee">{{ $order->customer_name }}</td>
        </tr>
        <tr style="background:#fffbeb">
            <td style="padding:10px;border:1px solid #eee"><strong>Téléphone</strong></td>
            <td style="padding:10px;border:1px solid #eee">{{ $order->customer_phone }}</td>
        </tr>
        <tr>
            <td style="padding:10px;border:1px solid #eee"><strong>Adresse</strong></td>
            <td style="padding:10px;border:1px solid #eee">{{ $order->shipping_address }}</td>
        </tr>
        <tr style="background:#fffbeb">
            <td style="padding:10px;border:1px solid #eee"><strong>Total</strong></td>
            <td style="padding:10px;border:1px solid #eee;color:#d97706;font-weight:bold">{{ $order->formatted_total }}</td>
        </tr>
    </table>
    <a href="{{ url('/livreur') }}"
       style="background:#d97706;color:white;padding:12px 25px;border-radius:8px;text-decoration:none;display:inline-block">
        Voir mon dashboard
    </a>
    <p style="margin-top:30px;color:#999;font-size:12px">Ecommerce Dakar — Notification automatique</p>
</div>
</body>
</html>