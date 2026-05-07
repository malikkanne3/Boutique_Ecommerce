<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="font-family:Arial,sans-serif;background:#f4f4f4;padding:20px">
<div style="max-width:600px;margin:auto;background:white;border-radius:10px;padding:30px">
    <h2 style="color:#0891b2">📦 Commande livrée avec succès !</h2>
    <p>Bonjour <strong>Admin</strong>,</p>
    <p>La commande suivante a été livrée avec succès.</p>
    <table style="width:100%;border-collapse:collapse;margin:20px 0">
        <tr style="background:#f0f9ff">
            <td style="padding:10px;border:1px solid #eee"><strong>N° Commande</strong></td>
            <td style="padding:10px;border:1px solid #eee">{{ $order->order_number }}</td>
        </tr>
        <tr>
            <td style="padding:10px;border:1px solid #eee"><strong>Client</strong></td>
            <td style="padding:10px;border:1px solid #eee">{{ $order->customer_name }}</td>
        </tr>
        <tr style="background:#f0f9ff">
            <td style="padding:10px;border:1px solid #eee"><strong>Livreur</strong></td>
            <td style="padding:10px;border:1px solid #eee">{{ $order->livreur->name }}</td>
        </tr>
        <tr>
            <td style="padding:10px;border:1px solid #eee"><strong>Total</strong></td>
            <td style="padding:10px;border:1px solid #eee;color:#0891b2;font-weight:bold">{{ $order->formatted_total }}</td>
        </tr>
    </table>
    <p style="margin-top:30px;color:#999;font-size:12px">Ecommerce Dakar — Notification automatique</p>
</div>
</body>
</html>