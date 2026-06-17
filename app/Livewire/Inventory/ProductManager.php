<?php

namespace App\Livewire\Inventory;

use Livewire\Component;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Services\ProductService;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\WithFileUploads;

class ProductManager extends Component
{
    use WithPagination, WithFileUploads;

    public $name, $sku, $description, $category_id, $supplier_id, $buy_price, $sell_price, $stock, $min_stock, $productId, $skuSuffix;
    public $image;
    public $isEditing = false;
    public $showForm = false;
    public $searchTerm = '';

    #[Computed]
    public function categories()
    {
        return Category::all();
    }

    #[Computed]
    public function suppliers()
    {
        return Supplier::all();
    }

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
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('sku', 'like', '%' . $this->searchTerm . '%');
            })
            ->latest()
            ->paginate(10);

        $lowStockCount = Product::whereColumn('stock', '<=', 'min_stock')->count();

        return view('livewire.inventory.product-manager', [
            'products' => $products,
            'categories' => $this->categories,
            'suppliers' => $this->suppliers,
            'lowStockProducts' => $lowStockCount
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
        $this->showForm = false;
        $this->skuSuffix = null;
    }

    public function updatedName($value)
    {
        if (!$this->isEditing) {
            $this->sku = $this->generateSku($value);
        }
    }

    private function generateSku($name)
    {
        if (empty($name)) {
            return '';
        }

        // Split name into words, clean them to be uppercase alphanumeric
        $rawWords = preg_split('/\s+/', trim($name));
        $words = [];
        foreach ($rawWords as $word) {
            $cleanWord = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $word));
            if (!empty($cleanWord)) {
                $words[] = $cleanWord;
            }
        }

        $initials = '';
        $wordCount = count($words);

        if ($wordCount >= 3) {
            // Take first letter of the first 3 words (e.g. Susu Kental Manis -> SKM)
            $initials = substr($words[0], 0, 1) . substr($words[1], 0, 1) . substr($words[2], 0, 1);
        } elseif ($wordCount === 2) {
            // Take first 2 letters of first word + first letter of second word (e.g. Bakso Bakar -> BAB)
            $initials = substr($words[0], 0, 2) . substr($words[1], 0, 1);
        } elseif ($wordCount === 1) {
            // Take first 3 letters of the single word (e.g. Bakso -> BAK)
            $initials = substr($words[0], 0, 3);
        }

        // Final cleanup & fallback
        $initials = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $initials));
        if (empty($initials)) {
            $initials = 'PROD';
        }

        // Ensure initials is padded to at least 3 chars if somehow shorter
        $initials = str_pad($initials, 3, 'X');

        if (!$this->skuSuffix) {
            $this->skuSuffix = rand(1000, 9999);
        }

        return $initials . '-' . $this->skuSuffix;
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
        $this->showForm = true;
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
