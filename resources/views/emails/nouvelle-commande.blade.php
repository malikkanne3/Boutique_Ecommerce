<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="font-family:Arial,sans-serif;background:#f4f4f4;padding:20px">
<div style="max-width:600px;margin:auto;background:white;border-radius:10px;padding:30px">
    <h2 style="color:#7c3aed">🛒 Nouvelle commande reçue !</h2>
    <p>Bonjour <strong>Admin</strong>,</p>
    <p>Une nouvelle commande vient d'être passée sur votre boutique.</p>
    <table style="width:100%;border-collapse:collapse;margin:20px 0">
        <tr style="background:#f8f4ff">
            <td style="padding:10px;border:1px solid #eee"><strong>N° Commande</strong></td>
            <td style="padding:10px;border:1px solid #eee">{{ $order->order_number }}</td>
        </tr>
        <tr>
            <td style="padding:10px;border:1px solid #eee"><strong>Client</strong></td>
            <td style="padding:10px;border:1px solid #eee">{{ $order->customer_name }}</td>
        </tr>
        <tr style="background:#f8f4ff">
            <td style="padding:10px;border:1px solid #eee"><strong>Email</strong></td>
            <td style="padding:10px;border:1px solid #eee">{{ $order->customer_email }}</td>
        </tr>
        <tr>
            <td style="padding:10px;border:1px solid #eee"><strong>Téléphone</strong></td>
            <td style="padding:10px;border:1px solid #eee">{{ $order->customer_phone }}</td>
        </tr>
        <tr style="background:#f8f4ff">
            <td style="padding:10px;border:1px solid #eee"><strong>Adresse</strong></td>
            <td style="padding:10px;border:1px solid #eee">{{ $order->shipping_address }}</td>
        </tr>
        <tr>
            <td style="padding:10px;border:1px solid #eee"><strong>Total</strong></td>
            <td style="padding:10px;border:1px solid #eee;color:#7c3aed;font-weight:bold">{{ $order->formatted_total }}</td>
        </tr>
    </table>
    <a href="{{ url('/admin/orders/' . $order->id) }}"
       style="background:#7c3aed;color:white;padding:12px 25px;border-radius:8px;text-decoration:none;display:inline-block">
        Voir la commande
    </a>
    <p style="margin-top:30px;color:#999;font-size:12px">Ecommerce Dakar — Notification automatique</p>
</div>
</body>
</html>