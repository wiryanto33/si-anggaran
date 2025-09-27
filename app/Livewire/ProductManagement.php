<?php
// app/Livewire/ProductManagement.php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use App\Models\Category;

class ProductManagement extends Component
{
    use WithPagination;

    public $showModal = false;
    public $productId;
    public $name;
    public $description;
    public $price;
    public $stock;
    public $category_id;
    public $isEditing = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'price' => 'required|numeric|min:0',
        'stock' => 'required|integer|min:0',
        'category_id' => 'nullable|exists:categories,id',
    ];

    public function create()
    {
        $this->authorize('product.create');
        $this->resetForm();
        $this->showModal = true;
    }

    public function edit($id)
    {
        $this->authorize('product.edit');
        $product = Product::findOrFail($id);
        $this->productId = $product->id;
        $this->name = $product->name;
        $this->description = $product->description;
        $this->price = $product->price;
        $this->stock = $product->stock;
        $this->category_id = $product->category_id;
        $this->isEditing = true;
        $this->showModal = true;
    }

    public function save()
    {
        if ($this->isEditing) {
            $this->authorize('product.edit');
        } else {
            $this->authorize('product.create');
        }

        $this->validate();

        $productData = [
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'stock' => $this->stock,
            'category_id' => $this->category_id,
            'user_id' => auth()->id(),
        ];

        if ($this->isEditing) {
            Product::findOrFail($this->productId)->update($productData);
        } else {
            Product::create($productData);
        }

        $this->resetForm();
        $this->showModal = false;
        session()->flash('message', 'Product saved successfully!');
    }

    public function delete($id)
    {
        $this->authorize('product.delete');
        Product::findOrFail($id)->delete();
        session()->flash('message', 'Product deleted successfully!');
    }

    public function resetForm()
    {
        $this->productId = null;
        $this->name = '';
        $this->description = '';
        $this->price = '';
        $this->stock = '';
        $this->category_id = '';
        $this->isEditing = false;
        $this->resetValidation();
    }
    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function render()
    {
        $this->authorize('product.view');
        
        return view('livewire.product-management', [
            'products' => Product::with(['user', 'category'])->paginate(10),
            'categories' => Category::where('is_active', true)->get(),
        ]);
    }
}