<button {{ $attributes->merge(['type' => 'submit', 'class' => 'btn-danger-ui uppercase tracking-widest text-xs disabled:opacity-40 disabled:cursor-not-allowed']) }}>
    {{ $slot }}
</button>
