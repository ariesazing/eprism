@props(['disabled' => false])

<input @disabled($disabled) {{ $attributes->merge(['class' => 'form-control-ui disabled:opacity-50 disabled:cursor-not-allowed']) }}>
