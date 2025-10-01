@php
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

$report = [
    [
        'label' => 'Neraca',
        'route' => '/report/neraca'
    ],
    [
        'label' => 'Neraca Perbandingan',
        'route' => '/report/neraca-perbandingan'
    ],
    [
        'label' => 'Laba / Rugi',
        'route' => '/report/labarugi'
    ],
    [
        'label' => 'Laporan Perubahan Modal',
        'route' => '/report/perubahanekuitas'
    ],
    [
        'label' => 'Laporan Arus Kas',
        'route' => '/report/aruskas'
    ],
    [
        'label' => 'Neraca Saldo',
        'route' => '/report/neraca-saldo'
    ],
    [
        'label' => 'Mutasi Saldo',
        'route' => '/report/mutasi-saldo'
    ],
    [
        'label' => 'Buku Besar',
        'route' => '/report/bukubesar'
    ]
];
@endphp

@extends('layouts.app')
@section('content')
<x-modal :field="$fieldSelect" :data="@$coa" maxWidth="2xl" focusable />
<div class="container w-full" x-data="">
    <h1 class="text-2xl font-bold">Semua Laporan</h1>
    <br>
    <div class="flex flex-wrap -mx-2">
        <div class="w-full md:w-1/2 px-2">
            <div class="mb-2">
                <label for="start_date" class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                <input type="text" id="start_date" name="start_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
            </div>
            {{-- <input type="checkbox" name="signature" id="signature"><span class="text-sm font-medium text-gray-700"> Need Signature ? (Neraca dan Laba/Rugi)</span>
            <div class="mb-2" id="signature1">
                <label for="text_input1" class="block text-sm font-medium text-gray-700">Ditandatangani oleh : </label>
                <input type="text" id="text_input1" name="text_input1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
            </div> --}}
        </div>
        <div class="w-full md:w-1/2 px-2">
            <div class="mb-2">
                <label for="end_date" class="block text-sm font-medium text-gray-700">Tanggal Selesai</label>
                <input type="text" id="end_date" name="end_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
            </div>
            {{-- <div class="mb-2" style="margin-top: 32px;" id="signature2">
                <label for="text_input2" class="block text-sm font-medium text-gray-700">Jabatan : </label>
                <input type="text" id="text_input2" name="text_input2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
            </div> --}}
        </div>
        <div class="w-full md:w-1/2 px-2">
            {{-- <div class="mb-2">
                <label for="jumlahLaman" class="block text-sm font-medium text-gray-700">Mulai dari Halaman</label>
                <input type="text" id="jumlahLaman" name="jumlahLaman" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
            </div> --}}
        </div>
    </div>

    <div class="flex flex-wrap -mx-2">
        @foreach ($report as $i)
        <div class="container-card-report mt-1 flex flex-row gap-5 w-full p-2">
            <div class="report-card p-2 bg-slate-50 border hover:bg-slate-100 border-slate-200 rounded-md flex justify-between items-center w-full cursor-pointer">
                <p class="text-xl">{{ $i['label'] }}</p>
                <div class="flex gap-3 items-center">
                    @if ($i['route'] == '/report/bukubesar')
                            <label for="akun" class="text-sm font-medium">Akun:</label>
                                <input type="text" id="akun" name="akun" placeholder="Filter Buku Besar by CoA" readonly class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                                <button type="button" class="h-fit text-sm border border-slate-500 py-1 px-2 rounded-md hover:bg-slate-200" @click.prevent="$dispatch('open-modal', { route: '{{ route('coas.index') }}', name: 'coas.index', title: 'Data Coa', type: 'select', isDetail: false })">
                                    Pilih
                                </button>
                    @endif
                    @if (in_array($i['route'], ['/report/neraca', '/report/neraca-perbandingan', '/report/labarugi', '/report/aruskas', '/report/perubahanekuitas']))
                        <button @click="showReport('{{ $loop->index }}', '{{ $i['route'] }}')" class="h-fit text-sm border border-slate-500 py-1 px-2 rounded-md hover:bg-slate-200">Show Options</button>
                    @endif
                    <button @click="downloadReport('{{ $i['route'] }}', {{ $loop->index }}, 0)" class="h-fit text-sm border border-slate-500 py-1 px-2 rounded-md hover:bg-slate-200">Preview</button>
                    {{-- <button @click="downloadReport('{{ $i['route'] }}', {{ $loop->index }}, 1)" class="h-fit text-sm border border-slate-500 py-1 px-2 rounded-md hover:bg-slate-200">Download (Excel)</button> --}}

                    {{-- <div class="flex items-center gap-1">
                        <button class="size-8 bg-slate-800 text-2xl text-white aspect-square rounded-md" @click="kurangiLaman('{{ $loop->index }}')">
                            -
                        </button>
                        <input type="text" id="jumlahLaman{{ $loop->index }}" class="h-8 w-10 px-2 text-center border rounded-md border-slate-300 p-2 bg-white" value="1" style="appearance: textfield;" oninput="this.value = this.value.replace(/[^0-9]/g, '')" />
                        <button class="size-8 bg-slate-800 text-2xl text-white aspect-square rounded-md" @click="tambahLaman('{{ $loop->index }}')">
                            +
                        </button>
                    </div> --}}

                </div>
            </div>
        </div>
        @if (in_array($i['route'], ['/report/neraca', '/report/neraca-perbandingan', '/report/labarugi', '/report/aruskas', '/report/perubahanekuitas']))
        @php
            $pecah = explode('/', $i['route']);
            $pecah = end($pecah);
        @endphp
            <div id="showReport{{ $loop->index }}" class="hidden container-card-report w-full p-2">
                <div class="flex justify-evenly p-2">
                    <div class="px-1">
                        <label for="alamat_{{$loop->index}}" class="block text-sm font-medium text-gray-700">Tempat</label>
                        <input type="text" id="alamat_{{$loop->index}}" oninput="funcState(this.value, 'alamat', {{ $loop->index }})" name="alamat_{{$loop->index}}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                    </div>
                    <div class="px-1">
                        <label for="tanggal_{{$loop->index}}" class="block text-sm font-medium text-gray-700">Tanggal</label>
                        <input type="text" id="tanggal_{{$loop->index}}" oninput="funcState(this.value, 'tanggal', {{ $loop->index }})" name="tanggal_{{$loop->index}}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                    </div>
                    <div class="px-1">
                        <label for="dibuat_{{$loop->index}}" class="block text-sm font-medium text-gray-700">Ditandatangani Oleh</label>
                        <input type="text" id="dibuat_{{$loop->index}}" oninput="funcState(this.value, 'dibuat', {{ $loop->index }})" name="dibuat_{{$loop->index}}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                    </div>
                    <div class="px-1">
                        <label for="jabatan_{{$loop->index}}" class="block text-sm font-medium text-gray-700">Jabatan</label>
                        <input type="text" id="jabatan_{{$loop->index}}" oninput="funcState(this.value, 'jabatan', {{ $loop->index }})" name="jabatan_{{$loop->index}}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                    </div>
                </div>
            </div>
        @endif
            {{-- <div class="p-2 w-full md:w-1/4">
                <div class="bg-emerald-300 shadow-lg overflow-hidden hover:bg-emerald-500 hover:shadow-2xl transition-shadow duration-300 cursor-pointer" style="border-radius: 0; height: 200px;" onclick="downloadReport('{{ $i['route'] }}')">
                    <div class="p-2 flex flex-col justify-between h-full">
                        <div class="flex justify-center items-center h-full">
                            <img src="{{ asset('images/icons/ic-download.svg') }}" alt="Download Icon" class="h-16 w-16">
                        </div>
                        <h2 class="text-xl font-semibold mb-2 text-gray-700">{{ $i['label'] }}</h2>
                    </div>
                </div>
            </div> --}}
        @endforeach
    </div>
</div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            let periode = @js(auth()->user()->periode);
            let val = 0;
            let defaultDateStart = new Date(new Date(new Date().getFullYear(), new Date().getMonth(), 1).setHours(0, 0, 0, 0))
            let defaultDateEnd = new Date(new Date(new Date().getFullYear(), new Date().getMonth() + 1, 0).setHours(23, 59, 59, 999))

            $('#signature1').toggleClass('hidden', !$('#signature').is(':checked'));
            $('#signature2').toggleClass('hidden', !$('#signature').is(':checked'));

            tambahLaman = (index) => {
                var jumlah = parseInt($('#jumlahLaman'+index).val());
                $('#jumlahLaman'+index).val(jumlah + 1);
            }

            kurangiLaman = (index) => {
                var jumlah = parseInt($('#jumlahLaman'+index).val());
                if (jumlah > 1) {
                    $('#jumlahLaman'+index).val(jumlah - 1);
                }
            }

            showReport = (index, route) => {
                if (route === '/report/neraca-perbandingan' || route === '/report/neraca' || route === '/report/labarugi' || route === '/report/aruskas' || route === '/report/perubahanekuitas') {
                    $('#showReport'+index).fadeToggle(300);
                }
            }

            funcState = (value, field, index) => {
                const containers = document.querySelectorAll('.container-card-report');
                const tanggal = flatpickr(`#tanggal_${index}`, {
                    dateFormat: 'd-m-Y',
                    allowInput: true,
                    minDate: `01-01-${periode}`,
                    maxDate: `31-12-${periode}`,
                    defaultDate: defaultDateStart
                });
                containers.forEach((container) => {
                    const input = container.querySelector(`#${field}_${index}`);
                    if (input) {
                        const inputsToUpdate = document.querySelectorAll(`input[id^="${field}_"]:not(#${field}_${index-1})`);
                        inputsToUpdate.forEach(inputToUpdate => {
                            inputToUpdate.value = value;
                        });
                    }
                });
            }

            const currentYear   = new Date().getFullYear();
            const defaultDate   = `01-01-${currentYear}`;

            flatpickr('#start_date', {
                dateFormat: 'd-m-Y',
                allowInput: true,
                minDate: '01-01-1990',
                maxDate: '31-12-' + periode,
                defaultDate: defaultDate,
                onClose: function(selectedDates, dateStr, instance) {
                    instance.setDate(dateStr, true);
                }
            });

            flatpickr('#end_date', {
                dateFormat: 'd-m-Y',
                allowInput: true,
                minDate: '01-01-' + periode,
                maxDate: '31-12-' + periode,
                defaultDate: defaultDateEnd,
                onClose: function(selectedDates, dateStr, instance) {
                    instance.setDate(dateStr, true);
                    var startDate = $('#start_date').val();
                    if (startDate && new Date(dateStr.split('-').reverse().join('-')) < new Date(startDate.split('-').reverse().join('-'))) {
                        alert('Tanggal selesai tidak boleh kurang dari tanggal mulai');
                        instance.clear();
                    }
                }
            });

            window.$('#signature').on('change', function() {
                $('#signature1').toggleClass('hidden', !$(this).is(':checked'));
                $('#signature2').toggleClass('hidden', !$(this).is(':checked'));
            });


            downloadReport = function(route, index, jenis = 0) {
                let startDate = $('#start_date').val();
                let endDate = $('#end_date').val();
                let akun = $('#akun').val();
                let alamat = $('#alamat_'+index).val();
                let tanggal = $('#tanggal_'+index).val();
                let dibuat = $('#dibuat_'+index).val();
                let jabatan = $('#jabatan_'+index).val();
                let jumlahLaman = $('#jumlahLaman').val();
                let token = $('meta[name="csrf-token"]').attr("content");
                let routenya = '';

                if(jenis == 0){
                    routenya = `${route}?excel=0`
                }else{
                    routenya = `${route}?excel=1`
                }

                console.log(routenya)

                var form = $('<form>', {
                    'method': 'POST',
                    'action': routenya,
                    'target': '_blank'
                }).append($('<input>', {
                    'name': 'start_date',
                    'value': startDate,
                    'type': 'hidden'
                })).append($('<input>', {
                    'name': 'end_date',
                    'value': endDate,
                    'type': 'hidden'
                })).append($('<input>', {
                    'name': 'akun',
                    'value': akun,
                    'type': 'hidden'
                })).append($('<input>', {
                    'name': 'alamat',
                    'value': alamat,
                    'type': 'hidden'
                })).append($('<input>', {
                    'name': 'tanggal',
                    'value': tanggal,
                    'type': 'hidden'
                })).append($('<input>', {
                    'name': 'dibuat',
                    'value': dibuat,
                    'type': 'hidden'
                })).append($('<input>', {
                    'name': 'jabatan',
                    'value': jabatan,
                    'type': 'hidden'
                })).append($('<input>', {
                    'name': 'jumlahLaman',
                    'value': jumlahLaman,
                    'type': 'hidden'
                })).append($('<input>', {
                    'name': '_token',
                    'value': token,
                    'type': 'hidden'
                }));

                form.appendTo('body').submit().fail(function(jqXHR) {
                    if (jqXHR.status === 419) {
                        // Tangani error 419, misalnya dengan meminta token baru
                        alert('Session expired. Please refresh the page.');
                        location.reload(); // Reload halaman untuk mendapatkan token baru
                    }
                });
            };

        });

    </script>

@endpush
