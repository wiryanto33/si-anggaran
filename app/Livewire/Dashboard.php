<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Product;
use Livewire\Component;
use App\Models\Category;

class Dashboard extends Component
{
    public function render()
    {
        $stats = [];
        
        if (auth()->user()->can('user.view')) {
            $stats['users'] = User::count();
        }
        
        if (auth()->user()->can('product.view')) {
            $stats['products'] = Product::count();
            $stats['low_stock'] = Product::where('stock', '<', 10)->count();
        }
        
        if (auth()->user()->can('category.view')) {
            $stats['categories'] = Category::where('is_active', true)->count();
        }

        return view('livewire.dashboard', [
            'stats' => $stats
        ]);
    }
}
