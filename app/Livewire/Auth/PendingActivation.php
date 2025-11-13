<?php

namespace App\Livewire\Auth;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class PendingActivation extends Component
{
    public function render()
    {
        return view('livewire.auth.pending-activation');
    }
}

