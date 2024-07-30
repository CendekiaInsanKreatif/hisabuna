@php
    $menus = getMenu();
@endphp


<div class="sidebar bg-gray-100 overflow-x-auto h-screen relative" id="sidebar">

    @foreach ($menus as $menu)
        <div class="px-2 py-2 flex flex-col bg-gray-100 rounded">
            <p class="text-sm font-medium text-zinc-400 tracking-widest">{{ $menu->name }}</p>
            @foreach ($menu->children as $child)
                <a href="{{ $child->route === '#' ? '#' : route($child->route) }}"
                    class="p-2 flex gap-2 rounded-md cursor-pointer items-center text-gray-800 hover:bg-emerald-200 {{ $child->is_active == 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                    {{ $child->is_active == 0 ? 'aria-disabled=true' : '' }}>
                    <div class="w-7 h-7 grid place-items-center relative">
                        <img src="{{ asset($child->icon) }}" alt="icon" class="w-full h-full" />
                    </div>
                    <p class="text-base tracking-normal leading-normal">{{ $child->name }}</p>
                </a>
            @endforeach
        </div>
    @endforeach

    <div class="px-2 py-2 flex flex-col bg-gray-100 rounded mb-12 items-center">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                class="bg-transparent hover:bg-red-500 text-red-700 hover:text-white p-2 rounded w-full sm:w-auto">
                Logout
            </button>
        </form>
    </div>
</div>
@push('script')
    <script type="module"></script>
@endpush
