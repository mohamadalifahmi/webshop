<?php

namespace App\Livewire\Admin;

use App\Mail\SellerApprovedMail;
use App\Models\Seller;
use App\Services\SettingsService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class SellersManager extends Component
{
    use WithPagination;

    public string $statusFilter = 'pending';

    public bool $showRejectModal = false;

    public ?int $rejectingId = null;

    public string $rejectionReason = '';

    /** @var array<int, string> */
    public array $overrides = [];

    public function approve(int $id): void
    {
        $seller = Seller::with('user')->findOrFail($id);

        DB::transaction(function () use ($seller) {
            $seller->update(['status' => 'approved', 'approved_at' => now(), 'rejection_reason' => null]);
        });

        if ($seller->user) {
            Mail::to($seller->user->email)->queue(new SellerApprovedMail($seller));
        }

        session()->flash('success', "Seller [{$seller->store_name}] approved.");
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

        $seller = Seller::findOrFail($validated['rejectingId']);
        $seller->update(['status' => 'rejected', 'rejection_reason' => trim($this->rejectionReason)]);

        session()->flash('success', "Seller [{$seller->store_name}] rejected.");
        $this->showRejectModal = false;
    }

    public function toggleSuspend(int $id): void
    {
        $seller = Seller::findOrFail($id);

        if ($seller->status === 'suspended') {
            $seller->update(['status' => 'approved']);
            session()->flash('success', "Seller [{$seller->store_name}] reactivated.");
        } else {
            $seller->update(['status' => 'suspended']);
            session()->flash('success', "Seller [{$seller->store_name}] suspended.");
        }
    }

    public function saveOverride(int $id): void
    {
        $value = trim((string) ($this->overrides[$id] ?? ''));

        $seller = Seller::findOrFail($id);
        $seller->update([
            'commission_override' => $value === '' ? null : number_format(min(100, max(0, (float) $value)), 2, '.', ''),
        ]);

        session()->flash('success', "Commission override updated for [{$seller->store_name}].");
    }

    public function render()
    {
        return view('livewire.admin.sellers-manager', [
            'sellers' => Seller::query()
                ->with('user:id,name,email')
                ->when($this->statusFilter !== '', fn ($q) => $q->where('status', $this->statusFilter))
                ->latest()
                ->paginate(10),
            'globalRate' => SettingsService::globalCommissionRate(),
        ]);
    }
}
