<span @class([
    'absolute -top-0.5 -right-0.5 flex h-5 min-w-5 items-center justify-center rounded-full bg-amber-600 px-1 text-[10px] font-bold text-white',
    'hidden' => empty($count),
])>
    {{ $count }}
</span>
