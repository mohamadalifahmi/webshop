<?php

namespace App\Livewire\Storefront;

use App\Services\CartService;
use App\Services\OrderService;
use App\Services\PaymentService;
use App\Services\ShippingService;
use DomainException;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.storefront')]
class Checkout extends Component
{
    use WithFileUploads;

    public string $name = '';

    public string $phone = '';

    public string $governorate = '';

    public string $address = '';

    public string $note = '';

    public string $paymentMethod = 'manual';

    public $proof;

    public function mount(): void
    {
        $user = auth()->user();
        $this->name = $user->name;
    }

    protected function rules(): array
    {
        return [
            'name' => 'required|string|max:120',
            'phone' => 'required|string|max:30',
            'governorate' => ['required', Rule::in(ShippingService::governorates())],
            'address' => 'required|string|max:1000',
            'paymentMethod' => ['required', Rule::in($this->availableMethods())],
            'proof' => 'required_if:paymentMethod,manual|image|max:4096',
        ];
    }

    protected function availableMethods(): array
    {
        return config('services.stripe.key') && config('services.stripe.secret')
            ? ['manual', 'stripe']
            : ['manual'];
    }

    public function updatedGovernorate(): void
    {
        // Changing the governorate re-renders the component, which recomputes
        // the shipping fee below. No extra event needed.
    }

    public function placeOrder()
    {
        if (CartService::items(auth()->user())->isEmpty()) {
            return redirect()->route('cart');
        }

        $validated = $this->validate();

        try {
            $order = OrderService::place(
                auth()->user(),
                $validated['paymentMethod'],
                [
                    'name' => $validated['name'],
                    'phone' => $validated['phone'],
                    'governorate' => $validated['governorate'],
                    'address' => $validated['address'],
                    'note' => $this->note ?: null,
                ],
            );

            if ($validated['paymentMethod'] === 'manual') {
                $path = $this->proof->store('payment-proofs', 'public');
                PaymentService::submitManualProof($order, $path);
            }

            return redirect()
                ->route('account.orders.show', $order->order_number)
                ->with('success', "Order {$order->order_number} placed successfully!");
        } catch (DomainException $e) {
            session()->flash('error', $e->getMessage());

            return null;
        }
    }

    public function render()
    {
        view()->share([
            'pageTitle' => 'Checkout — ASTRAGO MARKET',
            'pageDescription' => 'Secure checkout. One payment, auto-split to sellers.',
            'pageRobots' => 'noindex, nofollow',
        ]);

        $items = CartService::items(auth()->user());

        return view('livewire.storefront.checkout', [
            'items' => $items,
            'subtotal' => CartService::subtotal(auth()->user()),
            'shippingFee' => $this->governorate ? ShippingService::feeFor($this->governorate) : 0,
            'governorates' => ShippingService::governorates(),
        ]);
    }
}
