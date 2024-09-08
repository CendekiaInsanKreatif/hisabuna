<x-app-layout>
    @php
        $route = Route::currentRouteName();
        $route = explode('.', $route);
        $title = ucfirst($route[0]).' '.ucwords($route[2]);

        switch($route[2]) {
            case 'neraca':
                $title = 'Neraca';
                break;
            case 'labarugi':
                $title = 'Laba Rugi';
                break;
            case 'perubahanekuitas':
                $title = 'Perubahan Ekuitas';
                break;
            case 'aruskas':
                $title = 'Arus Kas';
                break;
            case 'neracasaldo':
                $title = 'Neraca Saldo';
                break;
            case 'bukubesar':
                $title = 'Buku Besar';
                break;
        }

        $fieldSelect = [
                [
                    'name' => 'nomor_akun',
                    'type' => 'text',
                    'label' => 'Nomor Akun',
                    'required' => true,
                ],
                [
                    'name' => 'nama_akun',
                    'type' => 'text',
                    'label' => 'Nama Akun',
                    'required' => true,
                ],
            ];
    @endphp
    @section('content')
    <x-modal :field="$fieldSelect" :data="@$coa" maxWidth="2xl" focusable />
        <div class="container mx-auto p-4 max-w-2xl" x-data="">
            <h1 class="text-2xl font-bold mb-4">{{ 'Report '.$title }}</h1>
            <form action="{{ route($route[0].'.'.$route[2]) }}" method="POST" class="space-y-4">
                @csrf
                @method('POST')
                @if($route[2] == 'neraca')
                    <div class="form-group flex flex-col md:flex-row md:items-center md:space-x-4">
                        <label for="end_date" class="block text-sm font-medium text-gray-700 md:w-1/4">Neraca Per Tanggal:</label>
                        <input type="text" id="end_date" name="end_date" class="mt-1 block w-full md:w-3/4 border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm datepicker-input">
                    </div>
                @else
                <div class="form-group flex flex-col md:flex-row md:items-center md:space-x-4">
                    <label for="start_date" class="block text-sm font-medium text-gray-700 md:w-1/4">Tanggal Mulai:</label>
                    <input type="text" id="start_date" name="start_date" class="mt-1 block w-full md:w-3/4 border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm datepicker-input">
                </div><div class="form-group flex flex-col md:flex-row md:items-center md:space-x-4">
                    <label for="end_date" class="block text-sm font-medium text-gray-700 md:w-1/4">Tanggal Selesai:</label>
                    <input type="text" id="end_date" name="end_date" class="mt-1 block w-full md:w-3/4 border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm datepicker-input">
                </div>
                @endif
                @if($route[2] == 'neraca' || $route[2] == 'labarugi')
                    <div class="form-group flex flex-col md:flex-row md:items-center md:space-x-4">
                        <label for="text_input1" class="block text-sm font-medium text-gray-700 md:w-1/4">Dibuat Oleh:</label>
                        <input type="text" id="text_input1" name="text_input1" class="mt-1 block w-full md:w-3/4 border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                    </div>
                    <div class="form-group flex flex-col md:flex-row md:items-center md:space-x-4">
                        <label for="text_input2" class="block text-sm font-medium text-gray-700 md:w-1/4">Disetujui Oleh:</label>
                        <input type="text" id="text_input2" name="text_input2" class="mt-1 block w-full md:w-3/4 border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                    </div>
                @endif
                @if($route[2] == 'bukubesar')
                    <div class="form-group flex flex-col md:flex-row md:items-center md:space-x-4">
                        <label for="akun" class="block text-sm font-medium text-gray-700 md:w-1/4">Akun:</label>
                        <input type="text" id="akun" name="akun" class="mt-1 block w-full md:w-3/4 border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                        <button type="button" class="inline-flex items-center justify-center px-2 py-1 bg-emerald-500 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-custom-strong" x-on:click.prevent="$dispatch('open-modal', { route: '{{ route('coas.index') }}', name: 'coas.index', title: 'Data Coa', type: 'select', isDetail: false })">
                            Pilih
                        </button>
                    </div>
                @endif
                <div class="flex justify-center md:justify-start gap-1">
                    <button type="button" id="popup" class="inline-flex items-center justify-center px-2 py-1 bg-emerald-500 dark:bg-emerald-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-emerald-800 uppercase tracking-widest hover:bg-emerald-700 dark:hover:bg-white focus:bg-emerald-700 dark:focus:bg-white active:bg-emerald-900 dark:active:bg-emerald-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-emerald-800 transition ease-in-out duration-150 shadow-custom-strong py-2 px-4">View</button>
                </div>
            </form>
        </div>
    @endsection

    @push('script')
        <script type="module">
            
            let route = @js($route[2]);
            let periode = @js(auth()->user()->periode);

            $(function() {
                $('#popup').click(function() {
                    var form = $('<form>', {
                        'method': 'POST',
                        'action': '{{ route('report.'.$route[2]) }}',
                        'target': '_blank'
                    }).append($('<input>', {
                        'name': '_token',
                        'value': '{{ csrf_token() }}',
                        'type': 'hidden'
                    })).append($('<input>', {
                        'name': 'start_date',
                        'value': $('#start_date').val(),
                        'type': 'hidden'
                    })).append($('<input>', {
                        'name': 'end_date',
                        'value': $('#end_date').val(),
                        'type': 'hidden'
                    }));

                    if (route == 'neraca' || route == 'labarugi') {
                        form.append($('<input>', {
                            'name': 'text_input1',
                            'value': $('#text_input1').val(),
                            'type': 'hidden'
                        })).append($('<input>', {
                            'name': 'text_input2',
                            'value': $('#text_input2').val(),
                            'type': 'hidden'
                        }));
                    }

                    if (route == 'bukubesar') {
                        form.append($('<input>', {
                            'name': 'akun',
                            'value': $('#akun').val(),
                            'type': 'hidden'
                        }));
                    }

                    form.appendTo('body').submit();
                });

                
                flatpickr('#start_date', {
                    dateFormat: 'd-m-Y',
                    allowInput: true,
                    minDate: '01-01-' + periode,
                    maxDate: '31-12-' + periode,
                    onClose: function(selectedDates, dateStr, instance) {
                        instance.setDate(dateStr, true);
                    }
                });

                flatpickr('#end_date', {
                    dateFormat: 'd-m-Y',
                    allowInput: true,
                    minDate: '01-01-' + periode,
                    maxDate: '31-12-' + periode,
                    onClose: function(selectedDates, dateStr, instance) {
                        instance.setDate(dateStr, true);
                        var startDate = $('#start_date').val();
                        if (startDate && new Date(dateStr.split('-').reverse().join('-')) < new Date(startDate.split('-').reverse().join('-'))) {
                            alert('Tanggal selesai tidak boleh kurang dari tanggal mulai');
                            instance.clear();
                        }
                    }
                });

                $('#view').click(function() {
                    alert('Lukman');
                })
            });
        </script>
    @endpush
</x-app-layout>
