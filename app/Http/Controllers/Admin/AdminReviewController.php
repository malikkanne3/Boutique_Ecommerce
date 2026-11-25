<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;

/** Modération des avis (avis abusifs). */
class AdminReviewController extends Controller
{
    public function index()
    {
        $query = Review::with(['user', 'product', 'seller'])->latest();

        if (request('state') === 'hidden') {
            $query->where('is_hidden', true);
        } elseif (request('rating')) {
            $query->where('rating', (int) request('rating'));
        }

        $reviews = $query->paginate(15)->withQueryString();
        $hiddenCount = Review::where('is_hidden', true)->count();

        return view('admin.reviews.index', compact('reviews', 'hiddenCount'));
    }

    public function toggle(Review $review)
    {
        $review->update(['is_hidden' => ! $review->is_hidden]);
        return back()->with('success', $review->is_hidden ? 'Avis masqué.' : 'Avis de nouveau visible.');
    }

    public function destroy(Review $review)
    {
        $review->delete();
        return back()->with('success', 'Avis supprimé.');
    }
}
