<?php
namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use App\Services\Notifier;

/** Avis et notes : uniquement après une commande livrée. */
class ReviewController extends Controller
{
    private function deliveredOrder(int $orderId): ?Order
    {
        return Order::where('id', $orderId)->where('user_id', auth()->id())->where('status', 'delivered')->first();
    }

    public function product(Product $product)
    {
        $data = request()->validate([
            'order_id' => 'required|integer',
            'rating'   => 'required|integer|between:1,5',
            'comment'  => 'nullable|string|max:1000',
        ]);

        $order = $this->deliveredOrder((int) $data['order_id']);
        if (! $order || ! $order->products()->where('products.id', $product->id)->exists()) {
            return back()->with('error', 'Vous ne pouvez noter que les produits que vous avez reçus.');
        }

        $review = Review::firstOrNew(['user_id' => auth()->id(), 'product_id' => $product->id]);
        $isNew = ! $review->exists;
        $review->fill(['seller_id' => $product->seller_id, 'order_id' => $order->id, 'rating' => $data['rating'], 'comment' => $data['comment'] ?? null])->save();

        if ($isNew && $product->seller_id) {
            Notifier::notify($product->seller_id, '⭐ Nouvel avis', auth()->user()->name . ' a noté « ' . $product->name . ' » : ' . $data['rating'] . '/5.', 'star-fill', 'warning', route('shop.show', $product, false), auth()->id());
        }

        return back()->with('success', 'Merci pour votre avis !');
    }

    public function seller(User $seller)
    {
        abort_unless($seller->isVendeur(), 404);

        $data = request()->validate([
            'order_id' => 'required|integer',
            'rating'   => 'required|integer|between:1,5',
            'comment'  => 'nullable|string|max:1000',
        ]);

        $order = $this->deliveredOrder((int) $data['order_id']);
        if (! $order || (int) $order->seller_id !== $seller->id) {
            return back()->with('error', 'Vous ne pouvez noter que les vendeurs dont vous avez reçu une commande.');
        }

        $review = Review::firstOrNew(['user_id' => auth()->id(), 'seller_id' => $seller->id, 'order_id' => $order->id, 'product_id' => null]);
        $review->fill(['rating' => $data['rating'], 'comment' => $data['comment'] ?? null])->save();

        return back()->with('success', 'Merci d\'avoir noté ce vendeur !');
    }

    /** Le vendeur répond publiquement à un avis sur l'un de ses produits. */
    public function reply(Review $review)
    {
        abort_unless((int) $review->seller_id === (int) auth()->id(), 403);

        $data = request()->validate(['reply' => 'required|string|max:1000']);
        $review->update(['reply' => $data['reply']]);

        Notifier::notify($review->user_id, '💬 Le vendeur a répondu à votre avis', mb_strimwidth($data['reply'], 0, 100, '…'), 'chat-quote', 'info', null, auth()->id());

        return back()->with('success', 'Réponse publiée.');
    }
}
