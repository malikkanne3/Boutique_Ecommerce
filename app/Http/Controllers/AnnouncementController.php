<?php
namespace App\Http\Controllers;

use App\Models\Announcement;

/** Annonces de l'administration, en lecture seule, pour client / vendeur / livreur. */
class AnnouncementController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.announcements.index');
        }

        $audience = $user->isVendeur() ? 'vendeur' : ($user->isLivreur() ? 'livreur' : 'client');

        $announcements = Announcement::whereIn('audience', ['all', $audience])->latest()->paginate(10);

        return view('announcements.index', compact('announcements'));
    }
}
