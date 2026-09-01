    <!-- Footer -->
    <footer class="relative mt-auto border-t border-white/5">
        <!-- Newsletter Section -->
        <div class="relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-b from-deep via-cosmic-900/20 to-deep"></div>
            <div class="relative mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16 sm:py-20 text-center reveal">
                <p class="text-xs font-bold uppercase tracking-[0.3em] text-stargold mb-4">Stay Connected</p>
                <h2 class="font-serif text-3xl sm:text-4xl lg:text-5xl font-bold text-white mb-4">Join the Universe</h2>
                <p class="text-white/40 max-w-md mx-auto mb-8">New arrivals, limited drops, and seasonal deals — straight to your inbox.</p>
                <form method="POST" action="{{ route('newsletter') }}" class="flex flex-col sm:flex-row gap-3 max-w-md mx-auto">
                    @csrf
                    <input type="email" name="email" placeholder="your@email.com" required
                        class="flex-1 rounded-xl input-cosmic px-5 py-3.5 text-sm">
                    <button type="submit"
                        class="btn-glow rounded-xl px-8 py-3.5 text-sm font-bold text-white whitespace-nowrap">
                        Subscribe
                    </button>
                </form>
            </div>
        </div>

        <!-- Footer Links -->
        <div class="border-t border-white/5">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
                <div class="grid gap-10 md:grid-cols-4">
                    <!-- Brand -->
                    <div class="md:col-span-2">
                        <a href="{{ route('home') }}" wire:navigate class="flex items-center gap-2.5 mb-4 group">
                            <svg class="h-7 w-7 text-cosmic-500 group-hover:text-stargold transition-colors duration-300" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M16 2L19 12H29L21 18L24 28L16 22L8 28L11 18L3 12H13L16 2Z" fill="currentColor"/>
                                <circle cx="16" cy="19" r="4.5" stroke="#0A0A0F" stroke-width="2"/>
                                <path d="M11.5 19.5h9M16 15v9M12 16.5l3-4M20 16.5l-3-4M12 21.5l3 4M20 21.5l-3 4" stroke="#0A0A0F" stroke-width="1.4" stroke-linecap="round"/>
                            </svg>
                            <span class="text-lg font-black tracking-wider">
                                <span class="text-white">ASTRA</span><span class="text-cosmic-500">GO</span>
                                <span class="text-xs font-medium text-white/40 tracking-widest uppercase ml-1">Market</span>
                            </span>
                        </a>
                        <p class="text-sm text-white/55 max-w-sm leading-relaxed">A marketplace built in Lebanon. Local sellers, one secure checkout, and shipping handled by each store.</p>

                        <!-- Social Icons -->
                        <div class="flex items-center gap-3 mt-6">
                            <a href="mailto:support@astrago.market" aria-label="Email ASTRAGO" title="Email us"
                                class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-white/5 text-white/30 hover:border-cosmic-500/40 hover:text-cosmic-400 hover:bg-cosmic-500/10 transition-all duration-200">
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                            </a>
                            <a href="mailto:support@astrago.market" aria-label="Instagram — coming soon" title="Instagram"
                                class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-white/5 text-white/30 hover:border-cosmic-500/40 hover:text-cosmic-400 hover:bg-cosmic-500/10 transition-all duration-200">
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                            </a>
                            <a href="mailto:support@astrago.market" aria-label="YouTube — coming soon" title="YouTube"
                                class="inline-flex h-10 w-10 items-center justify-center rounded-xl border border-white/5 text-white/30 hover:border-cosmic-500/40 hover:text-cosmic-400 hover:bg-cosmic-500/10 transition-all duration-200">
                                <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"/></svg>
                            </a>
                        </div>
                    </div>

                    <!-- Quick Links -->
                    <div>
                        <h3 class="text-sm font-bold text-white/80 uppercase tracking-wider mb-5">Explore</h3>
                        <ul class="space-y-3 text-sm">
                            <li><a href="{{ route('shop') }}" wire:navigate class="text-white/30 hover:text-cosmic-400 transition-colors duration-200">Shop All</a></li>
                            <li><a href="{{ route('shop') }}" wire:navigate class="text-white/30 hover:text-cosmic-400 transition-colors duration-200">New Arrivals</a></li>
                            <li><a href="{{ route('become-seller') }}" wire:navigate class="text-white/30 hover:text-cosmic-400 transition-colors duration-200">Sell on AstraGo</a></li>
                        </ul>
                    </div>

                    <!-- Support -->
                    <div>
                        <h3 class="text-sm font-bold text-white/80 uppercase tracking-wider mb-5">Support</h3>
                        <ul class="space-y-3 text-sm">
                            <li><a href="mailto:support@astrago.market" class="text-white/30 hover:text-cosmic-400 transition-colors duration-200">support@astrago.market</a></li>
                            <li><a href="{{ route('home') }}#categories" wire:navigate class="text-white/30 hover:text-cosmic-400 transition-colors duration-200">Browse Marketplace</a></li>
                            <li><a href="{{ route('shop') }}" wire:navigate class="text-white/30 hover:text-cosmic-400 transition-colors duration-200">Customer Service</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Bottom Bar -->
                <div class="mt-12 pt-8 border-t border-white/5 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <p class="text-xs text-white/20">&copy; {{ date('Y') }} ASTRAGO MARKET. All rights reserved.</p>
                    <p class="text-xs text-white/15">Beirut, Lebanon</p>
                </div>
            </div>
        </div>
    </footer>
