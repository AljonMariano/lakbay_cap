@props(['id', 'maxWidth'])

<div x-data="{ show: @entangle($attributes->wire('model')) }" x-show="show" id="{{ $id }}" class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50" style="display: none;">
    <div x-show="show" class="fixed inset-0 transform transition-all" x-on:click="show = false">
        <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
    </div>

    <div x-show="show" class="bg-white rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full {{ $maxWidth ?? 'sm:max-w-lg' }} sm:mx-auto">
        {{ $slot }}
    </div>
</div>
