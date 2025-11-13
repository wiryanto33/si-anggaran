<?php

namespace App\Livewire\Settings;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use App\Models\Setting;

class Appearance extends Component
{
    use WithFileUploads;

    public $background; // uploaded file
    public $currentBackground; // path from settings (relative to storage)
    public int $overlay = 30; // 0-80 darkness percentage
    public array $loginImages = []; // new uploads
    public array $currentLoginImages = []; // stored list
    public int $loginOverlay = 50;

    private function ensureSuperAdmin(): void
    {
        if (!auth()->check() || !auth()->user()->hasAnyRole(['Super Admin'])) {
            abort(403);
        }
    }

    public function mount(): void
    {
        $this->ensureSuperAdmin();
        $this->currentBackground = Setting::get('background_image');
        $this->overlay = (int) (Setting::get('background_overlay', 30));
        $this->currentLoginImages = json_decode((string) Setting::get('login_carousel_images', '[]'), true) ?: [];
        $this->loginOverlay = (int) (Setting::get('login_carousel_overlay', 50));
    }

    public function save(): void
    {
        $this->ensureSuperAdmin();
        $this->validate([
            'background' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $old = Setting::get('background_image');

        $path = $this->background->store('backgrounds', 'public');
        Setting::set('background_image', $path);

        // cleanup old file if it exists and different
        if ($old && $old !== $path && Storage::disk('public')->exists($old)) {
            Storage::disk('public')->delete($old);
        }

        $this->currentBackground = $path;
        $this->background = null;
        session()->flash('message', 'Background updated.');
    }

    public function clear(): void
    {
        $this->ensureSuperAdmin();
        $old = Setting::get('background_image');
        if ($old && Storage::disk('public')->exists($old)) {
            Storage::disk('public')->delete($old);
        }
        Setting::set('background_image', null);
        $this->currentBackground = null;
        session()->flash('message', 'Background cleared.');
    }

    public function saveOverlay(): void
    {
        $this->ensureSuperAdmin();
        $this->validate([
            'overlay' => 'required|integer|min:0|max:80',
        ]);
        Setting::set('background_overlay', (string) $this->overlay);
        session()->flash('message', 'Overlay saved.');
    }

    public function saveLoginImages(): void
    {
        $this->ensureSuperAdmin();
        $this->validate([
            'loginImages.*' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        foreach ($this->loginImages as $file) {
            $path = $file->store('login-carousel', 'public');
            $this->currentLoginImages[] = $path;
        }
        Setting::set('login_carousel_images', json_encode(array_values($this->currentLoginImages)));
        $this->loginImages = [];
        session()->flash('message', 'Login carousel images updated.');
    }

    public function removeLoginImage(int $index): void
    {
        $this->ensureSuperAdmin();
        if (!isset($this->currentLoginImages[$index])) return;
        $path = $this->currentLoginImages[$index];
        unset($this->currentLoginImages[$index]);
        $this->currentLoginImages = array_values($this->currentLoginImages);
        Setting::set('login_carousel_images', json_encode($this->currentLoginImages));
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
        session()->flash('message', 'Image removed.');
    }

    public function saveLoginOverlay(): void
    {
        $this->ensureSuperAdmin();
        $this->validate([
            'loginOverlay' => 'required|integer|min:0|max:80',
        ]);
        Setting::set('login_carousel_overlay', (string) $this->loginOverlay);
        session()->flash('message', 'Login overlay saved.');
    }

    public function moveLoginImageUp(int $index): void
    {
        $this->ensureSuperAdmin();
        if ($index <= 0 || $index >= count($this->currentLoginImages)) return;
        [$this->currentLoginImages[$index - 1], $this->currentLoginImages[$index]] = [
            $this->currentLoginImages[$index], $this->currentLoginImages[$index - 1]
        ];
        $this->currentLoginImages = array_values($this->currentLoginImages);
        Setting::set('login_carousel_images', json_encode($this->currentLoginImages));
    }

    public function moveLoginImageDown(int $index): void
    {
        $this->ensureSuperAdmin();
        if ($index < 0 || $index >= count($this->currentLoginImages) - 1) return;
        [$this->currentLoginImages[$index + 1], $this->currentLoginImages[$index]] = [
            $this->currentLoginImages[$index], $this->currentLoginImages[$index + 1]
        ];
        $this->currentLoginImages = array_values($this->currentLoginImages);
        Setting::set('login_carousel_images', json_encode($this->currentLoginImages));
    }

    public function render()
    {
        $this->ensureSuperAdmin();
        return view('livewire.settings.appearance', [
            'bgUrl' => $this->currentBackground ? asset('storage/' . $this->currentBackground) : null,
        ]);
    }
}
