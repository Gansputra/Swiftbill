@props(['align' => 'right', 'width' => '64', 'contentClasses' => 'py-1 bg-white dark:bg-slate-900'])

@php
$alignmentClasses = match ($align) {
    'left' => 'ltr:origin-top-left rtl:origin-top-right start-0',
    'top' => 'origin-top',
    default => 'ltr:origin-top-right rtl:origin-top-left end-0',
};

$widthClass = match ($width) {
    '48' => 'w-48',
    '56' => 'w-56',
    '64' => 'w-64',
    '72' => 'w-72',
    default => $width,
};
@endphp

<div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
    <div @click="open = ! open" class="cursor-pointer">
        {{ $trigger }}
    </div>

    <div x-show="open"
            x-transition:enter="transition ease-out duration-250 transform"
            x-transition:enter-start="opacity-0 translate-y-2 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-150 transform"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-2 scale-95"
            class="absolute z-50 mt-2.5 {{ $widthClass }} rounded-[16px] shadow-2xl shadow-slate-900/10 dark:shadow-black/50 border border-slate-200/80 dark:border-slate-800 {{ $alignmentClasses }}"
            style="display: none;"
            @click="open = false">
        <div class="rounded-[16px] overflow-hidden {{ $contentClasses }}">
            {{ $content }}
        </div>
    </div>
</div>

