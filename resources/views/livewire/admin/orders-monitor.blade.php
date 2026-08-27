<div>
    <h1 class="text-xl sm:text-2xl font-black text-gray-900 mb-6">Orders & Payments</h1>

    @php($filters = ['under_review' => 'Proofs to Review', 'unpaid' => 'Unpaid', 'paid' => 'Paid', '' => 'All'])
    <div class="mb-4 flex flex-wrap gap-2">
        @foreach ($filters as $key => $label)
            <button wire:click="$set('statusFilter', '{{ $key }}')"
                class="rounded-full px-3.5 py-1.5 text-xs font-semibold transition {{ $statusFilter === $key ? 'bg-gray-900 text-white' : 'bg-white border border-gray-200 text-gray-500 hover:border-amber-300' }}">
                {{ $label }}
            </button>
        @endforeach
    </div>

    <div class="space-y-4">
        @forelse ($orders as $order)
            <article class="rounded-2xl bg-white border border-gray-200 overflow-hidden">
                <header class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 p-5">
                    <div>
                        <p class="font-black text-gray-900">{{ $order->order_number }}</p>
                        <p class="text-xs text-gray-400">{{ $order->user?->name }} · {{ $order->created_at->format('d M Y, H:i') }} · {{ strtoupper($order->payment_method) }}</p>
                    </div>
                    <div class="flex flex-wrap items-center gap-2">
                        <x-status-badge :status="$order->status" />
                        <x-status-badge :status="$order->payment_status" />
                        <span class="font-black">${{ number_format((float) $order->total, 2) }}</span>
                    </div>
                </header>

                <ul class="divide-y divide-gray-50 text-sm px-5">
                    @foreach ($order->items as $item)
                        <li class="flex flex-wrap items-center gap-x-4 gap-y-1 py-2.5">
                            <span class="font-medium min-w-40 line-clamp-1">{{ $item->product_name }} × {{ $item->quantity }}</span>
                            <span class="text-xs text-gray-400">{{ $item->seller?->store_name }}</span>
                            <x-status-badge :status="$item->shipment_status === 'awaiting' ? 'awaiting' : $item->shipment_status" />
                            @if ($item->tracking_number)
                                <span class="rounded bg-gray-100 px-2 py-0.5 font-mono text-[11px]">{{ $item->tracking_number }}</span>
                            @endif
                            <span class="ml-auto font-semibold">${{ number_format((float) $item->subtotal, 2) }}
                                <span class="text-[10px] font-normal text-gray-400">comm. ${{ number_format((float) $item->commission_amount, 2) }}</span></span>
                        </li>
                    @endforeach
                </ul>

                <footer class="flex flex-wrap items-center justify-end gap-3 bg-gray-50 px-5 py-4">
                    @if ($order->payment_proof_path)
                        <a href="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($order->payment_proof_path) }}" target="_blank"
                            class="rounded-lg bg-blue-50 px-3.5 py-2 text-xs font-bold text-blue-700 hover:bg-blue-100">View Transfer Proof ↗</a>
                    @endif
                    @if ($order->payment_status === 'under_review')
                        <button wire:click="approvePayment({{ $order->id }})" wire:loading.attr="disabled" wire:target="approvePayment"
                            class="rounded-lg bg-emerald-600 px-4 py-2 text-xs font-bold text-white hover:bg-emerald-700">✓ Mark as Paid → Trigger Earnings</button>
                    @elseif ($order->payment_status === 'paid')
                        <span class="text-[11px] font-semibold text-emerald-600">Paid {{ optional($order->paid_at)->format('d M, H:i') }}</span>
                    @else
                        <span class="text-[11px] text-gray-400">Awaiting buyer payment</span>
                    @endif
                </footer>
            </article>
        @empty
            <div class="rounded-2xl bg-white border border-gray-200"><x-empty-state title="No orders in this state" /></div>
        @endforelse
    </div>

    <div class="mt-4">{{ $orders->links() }}</div>
</div>
