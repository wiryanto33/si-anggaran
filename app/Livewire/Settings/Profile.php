<?php

namespace App\Livewire\Settings;

use App\Models\User;
use App\Models\Satuan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class Profile extends Component
{
    use WithFileUploads;

    public string $name = '';
    public string $email = '';
    public $satuan_id = null;
    public $avatar = null;

    public function mount(): void
    {
        $this->name = Auth::user()->name;
        $this->email = Auth::user()->email;
        $this->satuan_id = Auth::user()->satuan_id;
    }

    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($user->id),
            ],
            'satuan_id' => ['nullable', 'exists:satuans,id'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:5120'],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        if ($this->avatar) {
            $path = $this->avatar->store('avatars', 'public');

            if (!empty($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $user->avatar = $path;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    public function resendVerificationNotification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));
            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }

    public function render()
    {
        return view('livewire.settings.profile', [
            'satuans' => Satuan::orderBy('nama')->get(),
        ]);
    }
}
