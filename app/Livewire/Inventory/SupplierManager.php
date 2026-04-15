<?php

namespace App\Livewire\Inventory;

use Livewire\Component;
use App\Models\Supplier;
use App\Services\SupplierService;
use Livewire\WithPagination;

class SupplierManager extends Component
{
    use WithPagination;

    public $name, $email, $phone, $address, $supplierId;
    public $isEditing = false;
    public $showForm = false;
    public $searchTerm = '';

    protected $rules = [
        'name' => 'required|min:3',
        'email' => 'nullable|email',
        'phone' => 'nullable',
        'address' => 'nullable',
    ];

    public function render(SupplierService $service)
    {
        $suppliers = Supplier::where('name', 'like', '%' . $this->searchTerm . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.inventory.supplier-manager', [
            'suppliers' => $suppliers
        ]);
    }

    public function resetFields()
    {
        $this->name = '';
        $this->email = '';
        $this->phone = '';
        $this->address = '';
        $this->supplierId = null;
        $this->isEditing = false;
    }

    public function store(SupplierService $service)
    {
        $this->validate();

        $service->createSupplier([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
        ]);

        session()->flash('success', 'Supplier created successfully.');
        $this->resetFields();
    }

    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);
        $this->supplierId = $id;
        $this->name = $supplier->name;
        $this->email = $supplier->email;
        $this->phone = $supplier->phone;
        $this->address = $supplier->address;
        $this->isEditing = true;
    }

    public function update(SupplierService $service)
    {
        $this->validate();

        $supplier = Supplier::findOrFail($this->supplierId);
        $service->updateSupplier($supplier, [
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
        ]);

        session()->flash('success', 'Supplier updated successfully.');
        $this->resetFields();
    }

    public function delete($id, SupplierService $service)
    {
        $supplier = Supplier::findOrFail($id);
        $service->deleteSupplier($supplier);
        session()->flash('success', 'Supplier deleted successfully.');
    }
}
