<!DOCTYPE html>
<html>
<head><meta charset="UTF-8"></head>
<body style="font-family:Arial,sans-serif;background:#f4f4f4;padding:20px">
<div style="max-width:600px;margin:auto;background:white;border-radius:10px;padding:30px">
    <h2 style="color:#16a34a">✅ Votre commande est confirmée !</h2>
    <p>Bonjour <strong>{{ $order->customer_name }}</strong>,</p>
    <p>Bonne nouvelle ! Votre commande a été confirmée et est en cours de préparation.</p>
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
            <td style="padding:10px;border:1px solid #eee"><strong>Total</strong></td>
            <td style="padding:10px;border:1px solid #eee;color:#16a34a;font-weight:bold">{{ $order->formatted_total }}</td>
        </tr>
    </table>
    <p>Vous serez notifié dès que votre commande sera prise en charge par un livreur.</p>
    <p style="margin-top:30px;color:#999;font-size:12px">Ecommerce Dakar — Notification automatique</p>
</div>
</body>
</html>