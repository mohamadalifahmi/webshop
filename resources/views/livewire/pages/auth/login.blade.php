<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div>
    <h1 class="text-xl font-bold text-white">Welcome back</h1>
    <p class="mt-1 mb-6 text-sm text-white/40">Sign in to continue shopping and selling.</p>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form wire:submit="login" class="space-y-4">
        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="form.email" id="email" class="block mt-1.5 w-full" type="email" name="email" required autofocus autocomplete="username" placeholder="you@example.com" />
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between">
                <x-input-label for="password" :value="__('Password')" />
                @if (Route::has('password.request'))
                    <a class="text-xs font-semibold text-cosmic-400 hover:text-cosmic-300" href="{{ route('password.request') }}" wire:navigate>
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
            </div>

            <x-text-input wire:model="form.password" id="password" class="block mt-1.5 w-full"
                            type="password"
                            name="password"
                            required autocomplete="current-password" placeholder="••••••••" />

            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <label for="remember" class="inline-flex cursor-pointer items-center">
            <input wire:model="form.remember" id="remember" type="checkbox" class="rounded border-white/20 text-cosmic-500 bg-space-800 shadow-sm focus:ring-cosmic-500" name="remember">
            <span class="ms-2 text-sm text-white/50">{{ __('Remember me') }}</span>
        </label>

        <x-primary-button class="w-full !py-3 !justify-center">
            {{ __('Log in') }}
        </x-primary-button>
    </form>

    <p class="mt-6 border-t border-white/5 pt-5 text-center text-sm text-white/40">
        {{ __('New to ASTRAGO?') }}
        <a href="{{ route('register') }}" wire:navigate class="font-bold text-cosmic-400 hover:text-cosmic-300">{{ __('Create an account') }}</a>
    </p>
</div>
