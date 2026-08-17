@props(['disabled' => false])

<input {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => 'block w-full mt-1 text-sm   focus:border-purple-400 focus:outline-none focus:shadow-outline-purple  :shadow-outline-gray form-input']) !!}>