<?php

namespace App\Livewire\Storefront;

use App\Models\Category;
use App\Models\Product;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.storefront')]
class Catalog extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public string $q = '';

    #[Url(history: true)]
    public ?int $category = null;

    #[Url(history: true)]
    public ?float $minPrice = null;

    #[Url(history: true)]
    public ?float $maxPrice = null;

    #[Url(history: true)]
    public string $sort = 'newest';

    public function updatedQ(): void
    {
        $this->resetPage();
    }

    public function suggestions()
    {
        if (trim($this->q) === '') {
            return collect();
        }

        $term = '%'.trim($this->q).'%';

        return Product::query()
            ->with(['category:id,name'])
            ->where('status', 'active')
            ->where(fn ($word) => $word
                ->where('name', 'like', $term)
                ->orWhere('description', 'like', $term)
                ->orWhere('sku', 'like', $term))
            ->latest('published_at')
            ->limit(5)
            ->get();
    }

    public function resetFilters(): void
    {
        $this->reset('q', 'category', 'minPrice', 'maxPrice', 'sort');
        $this->resetPage();
    }

    public function render()
    {
        $products = Product::query()
            ->with(['category', 'seller:id,store_name,slug'])
            ->where('status', 'active')
            ->when($this->q !== '', function ($query) {
                $term = '%'.$this->q.'%';
                $query->where(fn ($w) => $w
                    ->where('name', 'like', $term)
                    ->orWhere('description', 'like', $term)
                    ->orWhere('sku', 'like', $term));
            })
            ->when($this->category, fn ($query) => $query->where('category_id', $this->category))
            ->when($this->minPrice !== null, fn ($query) => $query->where('price', '>=', $this->minPrice))
            ->when($this->maxPrice !== null && $this->maxPrice > 0, fn ($query) => $query->where('price', '<=', $this->maxPrice))
            ->when($this->sort === 'newest', fn ($query) => $query->latest('published_at'))
            ->when($this->sort === 'price_asc', fn ($query) => $query->orderBy('price'))
            ->when($this->sort === 'price_desc', fn ($query) => $query->orderByDesc('price'))
            ->paginate(12);

        return view('livewire.storefront.catalog', [
            'products' => $products,
            'categories' => Category::orderBy('name')->get(),
            'suggestions' => $this->suggestions(),
        ]);
    }
}
