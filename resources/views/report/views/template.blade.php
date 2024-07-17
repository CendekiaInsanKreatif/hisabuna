<x-app-layout>
    @php
        // dd(Route::currentRouteName());
        $route = Route::currentRouteName();
        $route = explode('.', $route);
        $title = ucfirst($route[0]).' '.ucwords($route[2]);
        // da($title);
        // dd($route);
    @endphp
    @section('content')
        <div class="container mx-auto p-4 max-w-2xl">
            <h1 class="text-2xl font-bold mb-4">{{ $title }}</h1>
            <form action="{{ route('report.labarugi') }}" method="POST" class="space-y-4">
                @csrf
                @method('POST')
                <div class="form-group flex flex-col md:flex-row md:items-center md:space-x-4">
                    <label for="start_date" class="block text-sm font-medium text-gray-700 md:w-1/4">Tanggal Mulai:</label>
                    <input type="text" id="start_date" name="start_date" class="mt-1 block w-full md:w-3/4 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm datepicker-input">
                </div>
                <div class="form-group flex flex-col md:flex-row md:items-center md:space-x-4">
                    <label for="end_date" class="block text-sm font-medium text-gray-700 md:w-1/4">Tanggal Selesai:</label>
                    <input type="text" id="end_date" name="end_date" class="mt-1 block w-full md:w-3/4 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm datepicker-input">
                </div>
                @if($route[2] == 'neraca' || $route[2] == 'labarugi')
                    <div class="form-group flex flex-col md:flex-row md:items-center md:space-x-4">
                        <label for="text_input1" class="block text-sm font-medium text-gray-700 md:w-1/4">Nama Kiri:</label>
                        <input type="text" id="text_input1" name="text_input1" class="mt-1 block w-full md:w-3/4 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                    <div class="form-group flex flex-col md:flex-row md:items-center md:space-x-4">
                        <label for="text_input2" class="block text-sm font-medium text-gray-700 md:w-1/4">Nama Kanan:</label>
                        <input type="text" id="text_input2" name="text_input2" class="mt-1 block w-full md:w-3/4 border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    </div>
                @endif
                <div class="flex justify-center md:justify-start">
                    <button type="submit" class="inline-flex items-center justify-center px-2 py-1 bg-emerald-500 dark:bg-emerald-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-emerald-800 uppercase tracking-widest hover:bg-emerald-700 dark:hover:bg-white focus:bg-emerald-700 dark:focus:bg-white active:bg-emerald-900 dark:active:bg-emerald-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-emerald-800 transition ease-in-out duration-150 shadow-custom-strong py-2 px-4">Submit</button>
                </div>
            </form>
        </div>
    @endsection

    @push('script')
        <script type="module">
            $(document).ready(function() {
                $('#start_date').datepicker({
                    dateFormat: 'dd-mm-yy',
                    changeMonth: true,
                    changeYear: true,
                    showButtonPanel: true,
                    onClose: function(dateText, inst) { 
                        $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, inst.selectedDay));
                    }
                });
                $('#end_date').datepicker({
                    dateFormat: 'dd-mm-yy',
                    changeMonth: true,
                    changeYear: true,
                    showButtonPanel: true,
                    onClose: function(dateText, inst) { 
                        $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, inst.selectedDay));
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
