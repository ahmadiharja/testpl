@props([
    'id' => 'grid-table',
    'class' => '',
    'padded' => true,
])

{{--
    USAGE:
    <x-data-table id="users-grid" />

    Use this as a shared Grid.js mount wrapper.
    Page-specific grid initialization stays in the page script.
--}}

<div class="rounded-[2rem] overflow-hidden transition-colors duration-500 {{ $class }}"
     :class="theme === 'perfectlum' ? 'bento-lum' : 'bento-chroma'">
    <div class="{{ $padded ? 'p-1' : '' }}">
        <div id="{{ $id }}" class="w-full"></div>
        {{ $slot }}
    </div>
</div>
