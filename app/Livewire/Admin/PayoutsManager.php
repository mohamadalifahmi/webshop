<?php

namespace App\Livewire\Admin;

use App\Models\Payout;
use App\Services\PayoutService;
use DomainException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
class PayoutsManager extends Component
{
    use WithPagination;

    public string $statusFilter = 'pending';

    public bool $showRejectModal = false;

    public ?int $rejectingId = null;

    public string $adminNote = '';

    public function markPaid(int $id): void
    {
        $payout = Payout::findOrFail($id);

        try {
            PayoutService::markPaid($payout, auth()->user());
            session()->flash('success', "Payout #{$payout->id} marked paid. Seller balance deducted & notified.");
        } catch (DomainException $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function openRejectModal(int $id): void
    {
        $this->rejectingId = $id;
        $this->adminNote = '';
        $this->showRejectModal = true;
    }

    public function confirmReject(): void
    {
        $validated = $this->validate([
            'adminNote' => 'required|string|min:5|max:500',
            'rejectingId' => 'required|integer',
        ]);

        $payout = Payout::findOrFail($validated['rejectingId']);

        try {
            PayoutService::reject($payout, auth()->user(), trim($this->adminNote));
            session()->flash('success', "Payout #{$payout->id} rejected.");
        } catch (DomainException $e) {
            session()->flash('error', $e->getMessage());
        }

        $this->showRejectModal = false;
    }

    public function render()
    {
        return view('livewire.admin.payouts-manager', [
            'payouts' => Payout::query()
                ->with(['seller.user:id,name,email'])
                ->when($this->statusFilter !== '', fn ($q) => $q->where('status', $this->statusFilter))
                ->latest('requested_at')
                ->paginate(10),
        ]);
    }
}
