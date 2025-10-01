@php
    $menus = getMenu();
    $currentRoute = request()->route()->getName();
@endphp

<aside class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-secondary-200 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out shadow-strong lg:shadow-medium" id="sidebar">
    <!-- Navigation Content -->
    <div class="flex flex-col h-full">
        <!-- Brand/Logo Section -->
        <div class="flex-shrink-0 px-4 py-6 border-b border-secondary-200">
            <div class="flex items-center justify-center">
                <div class="text-center">
                    <img src="{{ asset('images/brand/logo-hisabuna-color.svg') }}" alt="Hisabuna" class="h-8 w-auto mx-auto mb-2">
                    {{-- <h2 class="text-lg font-bold text-secondary-800">Hisabuna</h2> --}}
                    <p class="text-xs text-secondary-500 font-medium">Accounting System</p>
                </div>
            </div>
        </div>

        <!-- Menu Items -->
        <nav class="flex-1 px-4 py-4 space-y-2 overflow-y-auto">
            @if(auth()->user()->roles == 'superadmin')
                @foreach ($menus as $menu)
                    <div class="mb-4">
                        <!-- Menu Category Header -->
                        <div class="px-2 py-2 mb-3">
                            <h3 class="text-xs font-bold text-secondary-600 uppercase tracking-wider flex items-center">
                                <div class="w-2 h-2 bg-primary-500 rounded-full mr-2"></div>
                                {{ $menu->name }}
                            </h3>
                        </div>

                        <!-- Menu Items -->
                        <div class="space-y-1">
                            @foreach ($menu->children as $child)
                                @if($child->is_show == 1)
                                    @php
                                        $isActive = $child->route !== '#' && $currentRoute === $child->route;
                                        $isDisabled = $child->is_active == 0;
                                    @endphp
                                    <a href="{{ $child->route === '#' ? '#' : route($child->route) }}"
                                        class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-300 {{ $isActive ? 'bg-gradient-to-r from-primary-500 to-primary-600 text-white shadow-emerald' : 'text-secondary-700 hover:text-primary-600 hover:bg-primary-50 hover:shadow-soft' }} {{ $isDisabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer hover:scale-[1.02]' }}"
                                        {{ $isDisabled ? 'aria-disabled=true' : '' }}>

                                        <div class="flex-shrink-0 w-5 h-5 mr-3 flex items-center justify-center">
                                            <img src="{{ asset($child->icon) }}"
                                                 alt="{{ $child->name }}"
                                                 class="w-4 h-4 {{ $isActive ? 'filter brightness-0 invert' : 'opacity-70 group-hover:opacity-100 transition-opacity duration-200' }}" />
                                        </div>

                                        <span class="flex-1 truncate font-medium">{{ $child->name }}</span>

                                        @if($isActive)
                                            <div class="flex-shrink-0">
                                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @else
                @foreach ($menus as $menu)
                    @if($menu->roles == auth()->user()->roles)
                        <div class="mb-4">
                            <!-- Menu Category Header -->
                            <div class="px-2 py-2 mb-3">
                                <h3 class="text-xs font-bold text-secondary-600 uppercase tracking-wider flex items-center">
                                    <div class="w-2 h-2 bg-primary-500 rounded-full mr-2"></div>
                                    {{ $menu->name }}
                                </h3>
                            </div>

                            <!-- Menu Items -->
                            <div class="space-y-1">
                                @foreach ($menu->children as $child)
                                    @if($child->is_show == 1)
                                        @php
                                            $isActive = $child->route !== '#' && $currentRoute === $child->route;
                                            $isDisabled = $child->is_active == 0;
                                        @endphp
                                        <a href="{{ $child->route === '#' ? '#' : route($child->route) }}"
                                            class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-300 {{ $isActive ? 'bg-gradient-to-r from-primary-500 to-primary-600 text-white shadow-emerald' : 'text-secondary-700 hover:text-primary-600 hover:bg-primary-50 hover:shadow-soft' }} {{ $isDisabled ? 'opacity-50 cursor-not-allowed' : 'cursor-pointer hover:scale-[1.02]' }}"
                                            {{ $isDisabled ? 'aria-disabled=true' : '' }}>

                                            <div class="flex-shrink-0 w-5 h-5 mr-3 flex items-center justify-center">
                                                <img src="{{ asset($child->icon) }}"
                                                     alt="{{ $child->name }}"
                                                     class="w-4 h-4 {{ $isActive ? 'filter brightness-0 invert' : 'opacity-70 group-hover:opacity-100 transition-opacity duration-200' }}" />
                                            </div>

                                            <span class="flex-1 truncate font-medium">{{ $child->name }}</span>

                                            @if($isActive)
                                                <div class="flex-shrink-0">
                                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                    </svg>
                                                </div>
                                            @endif
                                        </a>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            @endif
        </nav>

        <!-- Additional Links Section -->
        <div class="px-4 mb-4">
            <div class="border-t border-secondary-200 pt-4">
                <div class="mb-3">
                    <h3 class="text-xs font-bold text-secondary-600 uppercase tracking-wider flex items-center">
                        <div class="w-2 h-2 bg-info-500 rounded-full mr-2"></div>
                        Lainnya
                    </h3>
                </div>
                <div class="space-y-1">
                    @php $isInvoiceActive = $currentRoute === 'invoices.index'; @endphp
                    <a href="{{ route('invoices.index') }}"
                       class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-300 hover:scale-[1.02] {{ $isInvoiceActive ? 'bg-gradient-to-r from-info-500 to-info-600 text-white shadow-blue' : 'text-secondary-700 hover:text-info-600 hover:bg-info-50 hover:shadow-soft' }}">
                        <div class="flex-shrink-0 w-5 h-5 mr-3 flex items-center justify-center">
                            <svg class="w-4 h-4 {{ $isInvoiceActive ? 'text-white' : 'text-secondary-400 group-hover:text-info-500' }} transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <span class="flex-1 truncate font-medium">Riwayat Invoice</span>
                        @if($isInvoiceActive)
                            <div class="flex-shrink-0">
                                <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        @endif
                    </a>
                </div>
            </div>
        </div>

        <!-- Logout Section -->
        <div class="px-4 pb-4">
            <div class="border-t border-secondary-200 pt-4">
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <button type="submit"
                            class="group flex items-center w-full px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-300 hover:scale-[1.02] text-danger-700 hover:text-danger-600 hover:bg-danger-50 hover:shadow-soft">
                        <div class="flex-shrink-0 w-5 h-5 mr-3 flex items-center justify-center">
                            <svg class="w-4 h-4 text-danger-500 group-hover:text-danger-600 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </div>
                        <span class="flex-1 text-left truncate font-medium">Logout</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</aside>
