@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'error-ui space-y-1']) }}>
        @foreach ((array) $messages as $message)
            <li>{{ $message }}</li>
        @endforeach
    </ul>
@endif
