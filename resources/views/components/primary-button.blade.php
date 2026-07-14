<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn-primary-ui uppercase tracking-widest text-xs disabled:opacity-40 disabled:cursor-not-allowed']) }}>
    {{ $slot }}
</button>
