<div class="sidebar bg-gray-100" id="sidebar">
    <div class="px-2 py-4 flex flex-col bg-gray-100 rounded">
        <p class="text-sm font-medium text-zinc-400 tracking-widest">MAIN APPLICATION</p>
        <a href="{{ route('coas.index') }}" class="p-2 flex gap-2 rounded-md cursor-pointer items-center text-gray-800 hover:bg-emerald-600">
            <div class="w-7 h-7 grid place-items-center relative">
                <img src="{{ asset('images/icons/ic-saldo-awal.svg') }}" alt="saldo-awal" class="w-full h-full" />
            </div>
            <p class="text-base tracking-normal leading-normal">COA</p>
        </a>
        <a href="{{ route('jurnal.index') }}" class="p-2 flex gap-2 rounded-md cursor-pointer items-center text-gray-800 hover:bg-emerald-600">
            <div class="w-7 h-7 grid place-items-center relative">
                <img src="{{ asset('images/icons/ic-laporan.svg') }}" alt="Laporan" class="w-full h-full" />
            </div>
            <p class="text-base tracking-normal leading-normal">Jurnal</p>
        </a>
    </div>
    <div class="px-2 py-4 flex flex-col bg-gray-100 rounded">
        <p class="text-sm font-medium text-zinc-400 tracking-widest">LAPORAN</p>
        <a href="#" class="p-2 flex gap-2 rounded-md cursor-pointer items-center text-gray-800 hover:bg-emerald-600">
            <div class="w-7 h-7 grid place-items-center relative">
                <img src="{{ asset('images/icons/ic-laporan.svg') }}" alt="Laporan" class="w-full h-full" />
            </div>
            <p class="text-base tracking-normal leading-normal">Buku Besar</p>
        </a>
        <a href="#" class="p-2 flex gap-2 rounded-md cursor-pointer items-center text-gray-800 hover:bg-emerald-600">
            <div class="w-7 h-7 grid place-items-center relative">
                <img src="{{ asset('images/icons/ic-laporan.svg') }}" alt="Laporan" class="w-full h-full" />
            </div>
            <p class="text-base tracking-normal leading-normal">Laba Rugi</p>
        </a>
        <a href="#" class="p-2 flex gap-2 rounded-md cursor-pointer items-center text-gray-800 hover:bg-emerald-600">
            <div class="w-7 h-7 grid place-items-center relative">
                <img src="{{ asset('images/icons/ic-laporan.svg') }}" alt="Laporan" class="w-full h-full" />
            </div>
            <p class="text-base tracking-normal leading-normal">Perubahan Ekuitas</p>
        </a>
        <a href="#" class="p-2 flex gap-2 rounded-md cursor-pointer items-center text-gray-800 hover:bg-emerald-600">
            <div class="w-7 h-7 grid place-items-center relative">
                <img src="{{ asset('images/icons/ic-laporan.svg') }}" alt="Laporan" class="w-full h-full" />
            </div>
            <p class="text-base tracking-normal leading-normal">Neraca</p>
        </a>
        <a href="#" class="p-2 flex gap-2 rounded-md cursor-pointer items-center text-gray-800 hover:bg-emerald-600">
            <div class="w-7 h-7 grid place-items-center relative">
                <img src="{{ asset('images/icons/ic-laporan.svg') }}" alt="Laporan" class="w-full h-full" />
            </div>
            <p class="text-base tracking-normal leading-normal">Neraca Saldo</p>
        </a>
        <a href="#" class="p-2 flex gap-2 rounded-md cursor-pointer items-center text-gray-800 hover:bg-emerald-600">
            <div class="w-7 h-7 grid place-items-center relative">
                <img src="{{ asset('images/icons/ic-laporan.svg') }}" alt="Laporan" class="w-full h-full" />
            </div>
            <p class="text-base tracking-normal leading-normal">Neraca Perbandingan</p>
        </a>
        <a href="#" class="p-2 flex gap-2 rounded-md cursor-pointer items-center text-gray-800 hover:bg-emerald-600">
            <div class="w-7 h-7 grid place-items-center relative">
                <img src="{{ asset('images/icons/ic-laporan.svg') }}" alt="Laporan" class="w-full h-full" />
            </div>
            <p class="text-base tracking-normal leading-normal">Laporan Arus Kas</p>
        </a>
    </div>

    <div class="flex-shrink-0">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="bg-transparent hover:bg-emerald-500 p-2 rounded w-20 sm:w-auto">
                Logout
            </button>
        </form>
    </div>
</div>
