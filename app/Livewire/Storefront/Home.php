<?php

namespace App\Livewire\Storefront;

use App\Models\Category;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.storefront')]
class Home extends Component
{
    public string $q = '';

    #[Url(history: true)]
    public ?int $category = null;

    #[Url(history: true)]
    public ?float $minPrice = null;

    #[Url(history: true)]
    public ?float $maxPrice = null;

    #[Url(history: true)]
    public string $sort = 'newest';

    private function searchQuery(): Builder
    {
        $term = trim($this->q);
        $like = '%'.$term.'%';

        return Product::query()
            ->with(['category:id,name', 'seller:id,store_name'])
            ->where('status', 'active')
            ->where(fn ($word) => $word
                ->where('name', 'like', $like)
                ->orWhere('description', 'like', $like)
                ->orWhere('sku', 'like', $like))
            ->latest('published_at');
    }

    public function resetFilters(): void
    {
        $this->reset('q', 'category', 'minPrice', 'maxPrice', 'sort');
    }

    public function suggestions(): Collection
    {
        if (trim($this->q) === '') {
            return new Collection();
        }

        return $this->searchQuery()->limit(5)->get();
    }

    public function results(): Collection
    {
        if (trim($this->q) === '') {
            return new Collection();
        }

        return $this->searchQuery()->limit(12)->get();
    }

    public function mostBought(int $limit = 10)
    {
        $rankedIds = OrderItem::query()
            ->whereNotNull('product_id')
            ->whereHas('order', fn ($query) => $query->whereIn('status', [
                'paid', 'partially_shipped', 'shipped', 'delivered', 'completed',
            ]))
            ->select('product_id')
            ->selectRaw('SUM(quantity) as sold_qty')
            ->groupBy('product_id')
            ->orderByDesc('sold_qty')
            ->orderByDesc('product_id')
            ->limit($limit)
            ->pluck('product_id');

        if ($rankedIds->isEmpty()) {
            $rankedIds = Product::active()
                ->latest('published_at')
                ->limit($limit)
                ->pluck('id');
        }

        $order = $rankedIds->values()->all();

        if ($order === []) {
            return collect();
        }

        return Product::query()
            ->with(['category:id,name', 'seller:id,store_name'])
            ->whereIn('id', $order)
            ->orderByRaw('FIELD(id, '.implode(',', array_map('intval', $order)).')')
            ->get();
    }

    public function featuredCategories(): Collection
    {
        return Category::query()
            ->withCount(['products' => fn ($q) => $q->where('status', 'active')])
            ->whereHas('products', fn ($q) => $q->where('status', 'active'))
            ->orderByDesc('products_count')
            ->limit(6)
            ->get();
    }

    public function sections()
    {
        $query = Category::query()
            ->withCount(['products' => fn ($q) => $q->where('status', 'active')])
            ->whereHas('products', fn ($q) => $q->where('status', 'active'))
            ->orderBy('name');

        if ($this->category) {
            $query->where('id', $this->category);
        }

        return $query->get()->map(function (Category $category) {
            $productQuery = Product::query()
                ->with(['category:id,name', 'seller:id,store_name'])
                ->where('status', 'active')
                ->where('category_id', $category->id);

            if ($this->minPrice !== null) {
                $productQuery->where('price', '>=', $this->minPrice);
            }
            if ($this->maxPrice !== null && $this->maxPrice > 0) {
                $productQuery->where('price', '<=', $this->maxPrice);
            }

            $productQuery = match ($this->sort) {
                'price_asc' => $productQuery->orderBy('price'),
                'price_desc' => $productQuery->orderByDesc('price'),
                default => $productQuery->latest('published_at'),
            };

            $category->section_products = $productQuery->limit(8)->get();
            return $category;
        });
    }

    public function render()
    {
        $searching = trim($this->q) !== '';

        view()->share([
            'pageTitle' => 'ASTRAGO MARKET — Shop Among Stars',
            'pageDescription' => 'The luxury space-tech marketplace. Vetted local sellers, one secure checkout — shop technology and taste among stars.',
            'pageRobots' => 'index, follow',
        ]);

        return view('livewire.storefront.home', [
            'searching' => $searching,
            'suggestions' => $searching ? $this->suggestions() : new Collection(),
            'results' => $searching ? $this->results() : new Collection(),
            'mostBought' => $searching ? new Collection() : $this->mostBought(),
            'sections' => $searching ? new Collection() : $this->sections(),
            'featuredCategories' => $searching ? new Collection() : $this->featuredCategories(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }
}