@props(['value'])

<label {{ $attributes->merge(['class' => 'text-gray-700 ']) }}>
    {{ $value ?? $slot }}
</label>