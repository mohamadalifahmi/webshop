<?php

namespace App\Livewire\Seller;

use App\Models\Category;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.seller')]
class ProductsManager extends Component
{
    use WithFileUploads, WithPagination;

    public string $statusFilter = '';

    public bool $showForm = false;

    public ?int $editingId = null;

    public string $name = '';

    public string $description = '';

    public string $price = '';

    public string $stock = '0';

    public ?int $categoryId = null;

    public ?string $sku = null;

    /** @var array<int, TemporaryUploadedFile> */
    public array $images = [];

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:180',
            'description' => 'required|string|max:5000',
            'price' => 'required|numeric|min:0.01|max:999999',
            'stock' => 'required|integer|min:0|max:1000000',
            'categoryId' => 'required|exists:categories,id',
            'sku' => 'nullable|string|max:64|unique:products,sku,'.($this->editingId ?? 'NULL'),
            'images' => 'sometimes|array|max:6',
            'images.*' => 'image|max:4096',
        ];
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(int $id): void
    {
        $product = auth()->user()->seller->products()->findOrFail($id);

        $this->editingId = $id;
        $this->name = $product->name;
        $this->description = (string) $product->description;
        $this->price = (string) $product->price;
        $this->stock = (string) $product->stock;
        $this->categoryId = $product->category_id;
        $this->sku = $product->sku;
        $this->showForm = true;
    }

    public function save(): void
    {
        $validated = $this->validate();

        $seller = auth()->user()->seller;
        $optimized = [];

        DB::transaction(function () use ($validated, $seller, &$optimized) {
            $data = [
                'name' => $validated['name'],
                'description' => $validated['description'],
                'price' => number_format((float) $validated['price'], 2, '.', ''),
                'stock' => $validated['stock'],
                'category_id' => $validated['categoryId'],
                'status' => 'pending',
                'rejection_reason' => null,
            ];

            if ($this->editingId) {
                $product = $seller->products()->lockForUpdate()->findOrFail($this->editingId);
                $product->update($data + ['slug' => $product->slug]);
            } else {
                $product = $seller->products()->create($data + [
                    'slug' => Str::slug($validated['name']).'-'.Str::lower(Str::random(5)),
                    'sku' => $validated['sku'] ?: strtoupper('SK-'.Str::random(8)),
                ]);
            }

            foreach ($this->images as $image) {
                $originalBytes = (int) $image->getSize();

                $media = $product->addMedia($image->getRealPath())
                    ->usingFileName(Str::random(16).'.'.$image->extension())
                    ->toMediaCollection('images');

                // WebP conversion is generated synchronously (nonQueued) — measure it now.
                $webpPath = $media->getPath('webp');
                if (is_file($webpPath)) {
                    $webpBytes = (int) filesize($webpPath);
                    if ($originalBytes > 0 && $webpBytes > 0 && $webpBytes < $originalBytes) {
                        $optimized[] = $this->formatBytes($originalBytes).' → '.$this->formatBytes($webpBytes);
                    }
                }
            }
        });

        $this->resetForm();
        $this->showForm = false;

        $message = $this->editingId
            ? 'Product updated and resubmitted for approval.'
            : 'Product submitted for admin approval.';

        if ($optimized !== []) {
            session()->flash('success', $message.' Optimized images: '.implode(', ', $optimized).'.');
        } else {
            session()->flash('success', $message);
        }
    }

    private function formatBytes(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2).' MB';
        }

        return number_format($bytes / 1024, 0).' KB';
    }

    public function delete(int $id): void
    {
        $product = auth()->user()->seller->products()->findOrFail($id);
        $this->authorize('delete', $product);
        $product->delete();

        session()->flash('success', 'Product deleted.');
    }

    private function resetForm(): void
    {
        $this->reset(['name', 'description', 'price', 'stock', 'categoryId', 'sku', 'images', 'editingId']);
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.seller.products-manager', [
            'products' => auth()->user()->seller->products()
                ->with(['category', 'media'])
                ->when($this->statusFilter !== '', fn ($q) => $q->where('status', $this->statusFilter))
                ->latest()
                ->paginate(10),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }
}
