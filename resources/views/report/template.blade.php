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

// $report = [
//     'Neraca / Perbandingan', 
//     'Laba / Rugi', 
//     'Arus Kas', 
//     'Laporan Perubahan Modal', 
//     'Laporan Arus Kas', 
//     'Neraca Saldo', 
//     'Buku Besar'
// ];

$report = [
    [
        'label' => 'Mutasi Saldo',
        'route' => '/report/mutasi-saldo'
    ],
    [
        'label' => 'Neraca',
        'route' => '/report/neraca-perbandingan'
    ],
    [
        'label' => 'Neraca Perbandingan',
        'route' => '/report/neraca'
    ],
    [
        'label' => 'Neraca Saldo',
        'route' => '/report/neraca-saldo'
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
        'label' => 'Buku Besar',
        'route' => '/report/bukubesar'
    ]
];
@endphp

@extends('layouts.app')
@section('content')
<x-modal :field="$fieldSelect" :data="@$coa" maxWidth="2xl" focusable />
<div class="container" x-data="">
    <h1 class="text-2xl font-bold">Semua Laporan</h1>
    <br>
    <div class="flex flex-wrap -mx-2">
        <div class="w-full md:w-1/2 px-2">
            <div class="mb-2">
                <label for="start_date" class="block text-sm font-medium text-gray-700">Tanggal Mulai</label>
                <input type="text" id="start_date" name="start_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
            </div>
            <input type="checkbox" name="signature" id="signature"><span class="text-sm font-medium text-gray-700"> Need Signature ? (Neraca dan Laba/Rugi)</span>
            <div class="mb-2" id="signature1">
                <label for="text_input1" class="block text-sm font-medium text-gray-700">Ditandatangani oleh : </label>
                <input type="text" id="text_input1" name="text_input1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
            </div>
        </div>
        <div class="w-full md:w-1/2 px-2">
            <div class="mb-2">
                <label for="end_date" class="block text-sm font-medium text-gray-700">Tanggal Selesai</label>
                <input type="text" id="end_date" name="end_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
            </div>
            <div class="mb-2" style="margin-top: 32px;" id="signature2">
                <label for="text_input2" class="block text-sm font-medium text-gray-700">Jabatan : </label>
                <input type="text" id="text_input2" name="text_input2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
            </div>
            
        </div>
    </div>
    <div class="mb-2 px-2">
        <label for="akun" class="block w-full text-sm font-medium text-gray-700">Akun:</label>
        <div class="mt-1 flex">
            <input type="text" id="akun" name="akun" placeholder="Filter Buku Besar by CoA" readonly class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
            <button type="button" class="ml-2 inline-flex items-center justify-center px-2 py-1 bg-emerald-500 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150 shadow-custom-strong" x-on:click.prevent="$dispatch('open-modal', { route: '{{ route('coas.index') }}', name: 'coas.index', title: 'Data Coa', type: 'select', isDetail: false })">
                Pilih
            </button>
        </div>
    </div>
    <div class="flex flex-wrap -mx-2">
        @foreach ($report as $i)
            <div class="p-2 w-full md:w-1/4">
                <div class="bg-emerald-300 shadow-lg overflow-hidden hover:bg-emerald-500 hover:shadow-2xl transition-shadow duration-300 cursor-pointer" style="border-radius: 0; height: 200px;" onclick="downloadReport('{{ $i['route'] }}')">
                    <div class="p-2 flex flex-col justify-between h-full">
                        <div class="flex justify-center items-center h-full">
                            <img src="{{ asset('images/icons/ic-download.svg') }}" alt="Download Icon" class="h-16 w-16">
                        </div>
                        <h2 class="text-xl font-semibold mb-2 text-gray-700">{{ $i['label'] }}</h2>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

@push('script')
    <script>
        $(document).ready(function() {
            let periode = @js(auth()->user()->periode);
            let defaultDateStart = new Date(new Date(new Date().getFullYear(), new Date().getMonth(), 1).setHours(0, 0, 0, 0))
            let defaultDateEnd = new Date(new Date(new Date().getFullYear(), new Date().getMonth() + 1, 0).setHours(23, 59, 59, 999))

            $('#signature1').toggleClass('hidden', !$('#signature').is(':checked'));
            $('#signature2').toggleClass('hidden', !$('#signature').is(':checked'));

            console.log(defaultDateStart);
            console.log(defaultDateEnd);
            flatpickr('#start_date', {
                dateFormat: 'd-m-Y',
                allowInput: true,
                minDate: '01-01-' + periode,
                maxDate: '31-12-' + periode,
                defaultDate: defaultDateStart,
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


            downloadReport = function(route) {
                let startDate = $('#start_date').val();
                let endDate = $('#end_date').val();
                let akun = $('#akun').val();
                let dibuatOleh = $('#text_input1').val();
                let disetujuiOleh = $('#text_input2').val();
                let token = $('meta[name="csrf-token"]').attr("content");

                var form = $('<form>', {
                    'method': 'POST',
                    'action': route,
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
                    'name': 'text_input1',
                    'value': dibuatOleh,
                    'type': 'hidden'
                })).append($('<input>', {
                    'name': 'text_input2',
                    'value': disetujuiOleh,
                    'type': 'hidden'
                })).append($('<input>', {
                    'name': '_token',
                    'value': token,
                    'type': 'hidden'
                }));

                form.appendTo('body').submit();
            };


            
        });
        
    </script>

@endpush