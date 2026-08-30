<?php

namespace App\Livewire\Admin;

use App\Mail\ProductRejectedMail;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class ProductsModeration extends Component
{
    use WithPagination;

    public string $statusFilter = 'pending';

    public bool $showRejectModal = false;

    public ?int $rejectingId = null;

    public string $rejectionReason = '';

    public function approve(int $id): void
    {
        $product = Product::findOrFail($id);

        DB::transaction(function () use ($product) {
            $product->update([
                'status' => 'active',
                'published_at' => now(),
                'rejection_reason' => null,
            ]);
        });

        session()->flash('success', "Product [{$product->name}] approved and live.");
    }

    public function openRejectModal(int $id): void
    {
        $this->rejectingId = $id;
        $this->rejectionReason = '';
        $this->showRejectModal = true;
    }

    public function confirmReject(): void
    {
        $validated = $this->validate([
            'rejectionReason' => 'required|string|min:5|max:500',
            'rejectingId' => 'required|integer',
        ]);

        $product = Product::with('seller.user')->findOrFail($validated['rejectingId']);
        $product->update(['status' => 'rejected', 'rejection_reason' => trim($this->rejectionReason)]);

        if ($product->seller?->user) {
            Mail::to($product->seller->user->email)->queue(new ProductRejectedMail($product));
        }

        session()->flash('success', "Product [{$product->name}] rejected. Seller notified with reason.");
        $this->showRejectModal = false;
    }

    public function render()
    {
        return view('livewire.admin.products-moderation', [
            'products' => Product::query()
                ->with(['category:id,name', 'seller:id,store_name', 'media'])
                ->when($this->statusFilter !== '', fn ($q) => $q->where('status', $this->statusFilter))
                ->latest()
                ->paginate(10),
        ]);
    }
}
