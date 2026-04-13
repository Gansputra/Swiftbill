<?php

namespace App\Livewire\Inventory;

use Livewire\Component;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Services\ProductService;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class ProductManager extends Component
{
    use WithPagination, WithFileUploads;

    public $name, $sku, $description, $category_id, $supplier_id, $buy_price, $sell_price, $stock, $min_stock, $productId;
    public $image;
    public $isEditing = false;
    public $searchTerm = '';

    protected $rules = [
        'name' => 'required|min:3',
        'sku' => 'nullable|unique:products,sku',
        'category_id' => 'required|exists:categories,id',
        'supplier_id' => 'nullable|exists:suppliers,id',
        'buy_price' => 'required|numeric|min:0',
        'sell_price' => 'required|numeric|min:0',
        'stock' => 'required|integer|min:0',
        'min_stock' => 'required|integer|min:0',
        'image' => 'nullable|image|max:1024',
    ];

    public function render()
    {
        $products = Product::with(['category', 'supplier'])
            ->where('name', 'like', '%' . $this->searchTerm . '%')
            ->orWhere('sku', 'like', '%' . $this->searchTerm . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.inventory.product-manager', [
            'products' => $products,
            'categories' => Category::all(),
            'suppliers' => Supplier::all(),
        ]);
    }

    public function resetFields()
    {
        $this->name = '';
        $this->sku = '';
        $this->description = '';
        $this->category_id = '';
        $this->supplier_id = '';
        $this->buy_price = '';
        $this->sell_price = '';
        $this->stock = '';
        $this->min_stock = 5;
        $this->image = null;
        $this->productId = null;
        $this->isEditing = false;
    }

    public function store(ProductService $service)
    {
        $this->validate();

        $imagePath = $this->image ? $this->image->store('products', 'public') : null;

        $service->createProduct([
            'name' => $this->name,
            'sku' => $this->sku,
            'description' => $this->description,
            'category_id' => $this->category_id,
            'supplier_id' => $this->supplier_id ?: null,
            'buy_price' => $this->buy_price,
            'sell_price' => $this->sell_price,
            'stock' => $this->stock,
            'min_stock' => $this->min_stock,
            'image' => $imagePath,
        ]);

        session()->flash('success', 'Product created successfully.');
        $this->resetFields();
    }

    public function edit($id)
    {
        $product = Product::findOrFail($id);
        $this->productId = $id;
        $this->name = $product->name;
        $this->sku = $product->sku;
        $this->description = $product->description;
        $this->category_id = $product->category_id;
        $this->supplier_id = $product->supplier_id;
        $this->buy_price = $product->buy_price;
        $this->sell_price = $product->sell_price;
        $this->stock = $product->stock;
        $this->min_stock = $product->min_stock;
        $this->isEditing = true;
    }

    public function update(ProductService $service)
    {
        $this->validate([
            'name' => 'required|min:3',
            'sku' => 'nullable|unique:products,sku,' . $this->productId,
            'category_id' => 'required|exists:categories,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'buy_price' => 'required|numeric|min:0',
            'sell_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'min_stock' => 'required|integer|min:0',
            'image' => 'nullable|image|max:1024',
        ]);

        $product = Product::findOrFail($this->productId);
        
        $data = [
            'name' => $this->name,
            'sku' => $this->sku,
            'description' => $this->description,
            'category_id' => $this->category_id,
            'supplier_id' => $this->supplier_id ?: null,
            'buy_price' => $this->buy_price,
            'sell_price' => $this->sell_price,
            'stock' => $this->stock,
            'min_stock' => $this->min_stock,
        ];

        if ($this->image) {
            $data['image'] = $this->image->store('products', 'public');
        }

        $service->updateProduct($product, $data);

        session()->flash('success', 'Product updated successfully.');
        $this->resetFields();
    }

    public function delete($id, ProductService $service)
    {
        $product = Product::findOrFail($id);
        $service->deleteProduct($product);
        session()->flash('success', 'Product deleted successfully.');
    }
}
