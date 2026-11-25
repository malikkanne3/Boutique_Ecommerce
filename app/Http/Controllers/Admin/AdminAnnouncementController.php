<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Str;

/** L'admin envoie des annonces en lecture seule : personne ne peut lui répondre. */
class AdminAnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::latest()->paginate(10);
        return view('admin.announcements.index', compact('announcements'));
    }

    public function store()
    {
        $data = request()->validate([
            'title'    => 'required|string|max:150',
            'body'     => 'required|string|max:3000',
            'audience' => 'required|in:all,client,vendeur,livreur',
        ]);

        $announcement = Announcement::create($data + ['sender_id' => auth()->id()]);

        $recipients = User::where('status', 'active')->where('role', '!=', 'admin');
        if ($data['audience'] === 'client') {
            $recipients = User::where('status', 'active')->whereNotIn('role', ['admin', 'vendeur', 'livreur']);
        } elseif ($data['audience'] !== 'all') {
            $recipients = $recipients->where('role', $data['audience']);
        }

        $count = 0;
        $now = now();
        $recipients->select('id')->chunkById(200, function ($users) use ($announcement, $now, &$count) {
            $rows = [];
            foreach ($users as $u) {
                $rows[] = [
                    'user_id'    => $u->id,
                    'title'      => '📢 ' . $announcement->title,
                    'body'       => Str::limit($announcement->body, 110),
                    'icon'       => 'megaphone',
                    'color'      => 'warning',
                    'link'       => '/annonces',
                    'is_read'    => false,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            Notification::insert($rows);
            $count += count($rows);
        });

        return back()->with('success', "Annonce envoyée à {$count} utilisateur(s).");
    }

    public function destroy(Announcement $announcement)
    {
        $announcement->delete();
        return back()->with('success', 'Annonce supprimée.');
    }
}
