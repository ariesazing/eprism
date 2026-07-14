<button {{ $attributes->merge(['type' => 'button', 'class' => 'btn-secondary-ui uppercase tracking-widest text-xs disabled:opacity-40 disabled:cursor-not-allowed']) }}>
    {{ $slot }}
</button>
