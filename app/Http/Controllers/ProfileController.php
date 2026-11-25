<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', ['user' => $request->user()]);
    }

    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();
        unset($data['avatar'], $data['shop_logo'], $data['shop_banner'], $data['hours'], $data['use_hours'], $data['vacation_mode'], $data['vacation_until'], $data['vacation_message']);

        $user->fill($data);

        // Photo de profil
        if ($request->hasFile('avatar')) {
            $this->deletePhoto($user->avatar);
            $user->avatar = $request->file('avatar')->store('avatars', 'public');
        } elseif ($request->boolean('remove_avatar')) {
            $this->deletePhoto($user->avatar);
            $user->avatar = null;
        }

        // Photo / logo de la boutique (vendeur)
        if ($user->isVendeur()) {
            if ($request->hasFile('shop_logo')) {
                $this->deletePhoto($user->shop_logo);
                $user->shop_logo = $request->file('shop_logo')->store('shops', 'public');
            } elseif ($request->boolean('remove_shop_logo')) {
                $this->deletePhoto($user->shop_logo);
                $user->shop_logo = null;
            }
        }

        // Page boutique : bannière, horaires, vacances
        if ($user->isVendeur()) {
            if ($request->hasFile('shop_banner')) {
                $this->deletePhoto($user->shop_banner);
                $user->shop_banner = $request->file('shop_banner')->store('shops', 'public');
            } elseif ($request->boolean('remove_shop_banner')) {
                $this->deletePhoto($user->shop_banner);
                $user->shop_banner = null;
            }

            if ($request->boolean('use_hours')) {
                $hours = [];
                foreach (range(1, 7) as $day) {
                    $row = $request->input("hours.$day", []);
                    $hours[$day] = [
                        'closed' => ! empty($row['closed']),
                        'open'   => $row['open'] ?: '08:00',
                        'close'  => $row['close'] ?: '19:00',
                    ];
                }
                $user->shop_hours = $hours;
            } else {
                $user->shop_hours = null;
            }

            $user->vacation_mode    = $request->boolean('vacation_mode');
            $user->vacation_until   = $request->input('vacation_until') ?: null;
            $user->vacation_message = $request->input('vacation_message') ?: null;
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    private function deletePhoto(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
