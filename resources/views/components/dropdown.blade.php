@props(['trigger'])

<div class="relative  lg:inline-flex bg-gray-100 rounded-xl">

    <div x-data="{ show: false }" @click.away="show = false">
        {{-- Trigger --}}

        <div @click="show = ! show ">
            {{ $trigger }}

        </div>

        {{-- Links --}}
        <div x-show="show" class="py-2 absolute bg-gray-100  mt-2 rounded-xl w-full z-50" style="display: none">
            {{ $slot }}
        </div>
    </div>
