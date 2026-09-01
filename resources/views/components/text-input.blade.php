@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'input-cosmic rounded-lg shadow-sm placeholder:text-white/25']) }}>
