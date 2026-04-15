<?php

namespace App\Livewire\Inventory;

use Livewire\Component;
use App\Models\Category;
use App\Services\CategoryService;
use Livewire\WithPagination;

class CategoryManager extends Component
{
    use WithPagination;

    public $name, $description, $categoryId;
    public $isEditing = false;
    public $showForm = false;
    public $searchTerm = '';

    protected $rules = [
        'name' => 'required|min:3|unique:categories,name',
        'description' => 'nullable',
    ];

    public function render(CategoryService $service)
    {
        $categories = Category::where('name', 'like', '%' . $this->searchTerm . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.inventory.category-manager', [
            'categories' => $categories
        ]);
    }

    public function resetFields()
    {
        $this->name = '';
        $this->description = '';
        $this->categoryId = null;
        $this->isEditing = false;
    }

    public function store(CategoryService $service)
    {
        $this->validate();

        $service->createCategory([
            'name' => $this->name,
            'description' => $this->description,
        ]);

        session()->flash('success', 'Category created successfully.');
        $this->resetFields();
    }

    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $this->categoryId = $id;
        $this->name = $category->name;
        $this->description = $category->description;
        $this->isEditing = true;
    }

    public function update(CategoryService $service)
    {
        $this->validate([
            'name' => 'required|min:3|unique:categories,name,' . $this->categoryId,
            'description' => 'nullable',
        ]);

        $category = Category::findOrFail($this->categoryId);
        $service->updateCategory($category, [
            'name' => $this->name,
            'description' => $this->description,
        ]);

        session()->flash('success', 'Category updated successfully.');
        $this->resetFields();
    }

    public function delete($id, CategoryService $service)
    {
        $category = Category::findOrFail($id);
        $service->deleteCategory($category);
        session()->flash('success', 'Category deleted successfully.');
    }
}
