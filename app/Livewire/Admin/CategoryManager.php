<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.admin')]
class CategoryManager extends Component
{
    public string $newName = '';
    public string $newIcon = '';

    public ?int $editingId = null;
    public string $editName = '';
    public string $editIcon = '';

    public function addCategory(): void
    {
        $validated = $this->validate([
            'newName' => 'required|string|max:150|unique:categories,name',
            'newIcon' => 'nullable|string|max:20',
        ], [
            'newName.required' => 'Category name is required.',
            'newName.unique' => 'This category name already exists.',
        ]);

        Category::create([
            'name' => trim($validated['newName']),
            'slug' => Str::slug($validated['newName']).Str::lower(Str::random(4)),
            'icon' => trim($validated['newIcon']) ?: null,
        ]);

        $this->newName = '';
        $this->newIcon = '';
        session()->flash('success', 'Category added.');
    }

    public function edit(int $id): void
    {
        $cat = Category::findOrFail($id);
        $this->editingId = $id;
        $this->editName = $cat->name;
        $this->editIcon = (string) $cat->icon;
    }

    public function saveEdit(): void
    {
        $validated = $this->validate([
            'editName' => 'required|string|max:150|unique:categories,name,'.$this->editingId,
            'editIcon' => 'nullable|string|max:20',
        ]);

        Category::findOrFail($this->editingId)->update([
            'name' => trim($validated['editName']),
            'icon' => trim($validated['editIcon']) ?: null,
        ]);

        $this->editingId = null;
        session()->flash('success', 'Category updated.');
    }

    public function deleteCategory(int $id): void
    {
        // Prevent deleting categories that have active products
        $cat = Category::findOrFail($id);
        if ($cat->products()->where('status', 'active')->exists()) {
            session()->flash('error', 'Cannot delete a category that has active products.');
            return;
        }

        $cat->delete();
        session()->flash('success', 'Category deleted.');
    }

    public function render()
    {
        return view('livewire.admin.category-manager', [
            'categories' => Category::orderBy('name')->get(),
        ]);
    }
}