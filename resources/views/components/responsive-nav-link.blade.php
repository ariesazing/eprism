@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2 border-l-4 border-violet-600 text-start text-base font-semibold text-violet-700 bg-violet-50 focus:outline-none focus:text-violet-800 focus:bg-violet-100 focus:border-violet-700 transition duration-150 ease-in-out'
            : 'block w-full ps-3 pe-4 py-2 border-l-4 border-transparent text-start text-base font-medium text-gray-600 hover:text-violet-700 hover:bg-violet-50 hover:border-violet-200 focus:outline-none focus:text-violet-700 focus:bg-violet-50 focus:border-violet-200 transition duration-150 ease-in-out';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
