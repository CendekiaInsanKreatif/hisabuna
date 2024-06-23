@props([
    'name',
    'field' => [],
    'show' => false,
    'maxWidth' => '2xl',
])

@php
$maxWidth = [
    'sm' => 'sm:max-w-sm',
    'md' => 'sm:max-w-md',
    'lg' => 'sm:max-w-lg',
    'xl' => 'sm:max-w-xl',
    '2xl' => 'sm:max-w-2xl',
][$maxWidth];
@endphp

<div
    x-data="{
        show: @js($show),
        title: '',
        data: {},
        route: '',
        method: '',
        name: '',
        type: '',
        focusables() {
            let selector = 'a, button, input:not([type=\'hidden\']), textarea, select, details, [tabindex]:not([tabindex=\'-1\'])';
            return [...$el.querySelectorAll(selector)]
                .filter(el => ! el.hasAttribute('disabled'));
        },
        firstFocusable() { return this.focusables()[0]; },
        lastFocusable() { return this.focusables().slice(-1)[0]; },
        nextFocusable() { return this.focusables()[this.nextFocusableIndex()] || this.firstFocusable(); },
        prevFocusable() { return this.focusables()[this.prevFocusableIndex()] || this.lastFocusable(); },
        nextFocusableIndex() { return (this.focusables().indexOf(document.activeElement) + 1) % (this.focusables().length + 1); },
        prevFocusableIndex() { return Math.max(0, this.focusables().indexOf(document.activeElement)) - 1; },
    }"
    x-init="$watch('show', value => {
        if (value) {
            document.body.classList.add('overflow-y-hidden');
            {{ $attributes->has('focusable') ? 'setTimeout(() => firstFocusable().focus(), 100)' : '' }}
        } else {
            document.body.classList.remove('overflow-y-hidden');
        }
    })"
    x-on:open-modal.window="method = $event.detail.method; route = $event.detail.route; data = $event.detail.data; title = $event.detail.title; show = true; name = $event.detail.name; type = $event.detail.type"
    x-on:close-modal.window="show = false"
    x-on:close.stop="show = false"
    x-on:keydown.escape.window="show = false"
    x-on:keydown.tab.prevent="$event.shiftKey || nextFocusable().focus()"
    x-on:keydown.shift.tab.prevent="prevFocusable().focus()"
    x-show="show"
    class="fixed inset-0 overflow-y-auto px-4 py-6 sm:px-0 z-50"
    style="display: {{ $show ? 'block' : 'none' }};"
>
    <div
        x-show="show"
        class="fixed inset-0 transform transition-all"
        x-on:click="show = false"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
    >
        <div class="absolute inset-0 bg-gray-500 dark:bg-gray-900 opacity-75"></div>
    </div>

    <div
        x-show="show"
        class="mb-6 bg-white dark:bg-gray-800 rounded-lg overflow-hidden shadow-xl transform transition-all sm:w-full {{ $maxWidth }} sm:mx-auto"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
        x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
    >
        <template x-if="type == 'custom'">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Import Excel</h2>
                <form method="POST" x-bind:action="route" enctype="multipart/form-data">
                    @csrf
                    <input type="file" name="file" id="file-input-button" style="margin-top: 0.25rem; display: block; width: 100%; border: 1px solid #10B981; border-radius: 0.375rem; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); focus:border-color: #10B981; focus:ring: 0.5rem; focus:ring-color: #A7F3D0; focus:ring-opacity: 0.5;">
                    <div class="mt-6 flex justify-end space-x-2">
                        <x-primary-button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-2 px-4 rounded">
                            <span>Import</span>
                        </x-primary-button>
                        <x-secondary-button x-on:click="$dispatch('close')" class="bg-gray-500 hover:bg-gray-600 text-gray-800 font-bold py-2 px-4 rounded">
                            {{ __('Cancel') }}
                        </x-secondary-button>
                    </div>
                </form>
            </div>
        </template>
        <template x-if="type == 'form'">
            <form method="POST" x-bind:action="route" class="p-6">
                @csrf
                <input type="hidden" name="_method" x-bind:value="method">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100" x-text="title"></h2>
                <div class="mt-6" x-data="data">
                    @foreach ($field as $field)
                        <div class="mt-6">
                            <x-input-label for="{{ $field['name'] }}" value="{{ __($field['label']) }}" class="sr-only" />
                            <x-text-input
                                id="{{ $field['name'] }}"
                                name="{{ $field['name'] }}"
                                type="{{ $field['type'] }}"
                                class="mt-1 block w-3/4"
                                x-bind:readonly="name.includes('show')"
                                x-bind:disabled="name.includes('show')"
                                placeholder="{{ __($field['label']) }}"
                                x-model="name.includes('create') ? '' : data.{{ $field['name'] }}"
                            />
                        </div>
                    @endforeach
                </div>
                <div class="mt-6 flex justify-end space-x-2">
                    <x-primary-button type="submit" x-show="!name.includes('show')" x-bind:class="name.includes('destroy') ? 'bg-red-500' : 'bg-emerald-500'">
                        <span x-text="name.includes('destroy') ? 'Hapus' : 'Simpan'"></span>
                    </x-primary-button>
                    <x-secondary-button x-on:click="$dispatch('close')" class="bg-gray-500 hover:bg-gray-600 text-gray-800 font-bold py-2 px-4 rounded">
                        {{ __('Cancel') }}
                    </x-secondary-button>
                </div>
            </form>
        </template>
    </div>
</div>
