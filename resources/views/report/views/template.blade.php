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
                    <label for="end_date" class="block text-sm font-medium text-gray-700 md:w-1/4">Neraca Selesai:</label>
                    <input type="text" id="end_date" name="end_date" class="mt-1 block w-full md:w-3/4 border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm datepicker-input">
                </div>
                @endif
                @if($route[2] == 'neraca' || $route[2] == 'labarugi')
                    <div class="form-group flex flex-col md:flex-row md:items-center md:space-x-4">
                        <label for="text_input1" class="block text-sm font-medium text-gray-700 md:w-1/4">Nama Kiri:</label>
                        <input type="text" id="text_input1" name="text_input1" class="mt-1 block w-full md:w-3/4 border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                    </div>
                    <div class="form-group flex flex-col md:flex-row md:items-center md:space-x-4">
                        <label for="text_input2" class="block text-sm font-medium text-gray-700 md:w-1/4">Nama Kanan:</label>
                        <input type="text" id="text_input2" name="text_input2" class="mt-1 block w-full md:w-3/4 border-gray-300 rounded-md shadow-sm focus:ring-emerald-500 focus:border-emerald-500 sm:text-sm">
                    </div>
                @endif
                <div class="flex justify-center md:justify-start gap-1">
                    <button type="submit" class="inline-flex items-center justify-center px-2 py-1 bg-emerald-500 dark:bg-emerald-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-emerald-800 uppercase tracking-widest hover:bg-emerald-700 dark:hover:bg-white focus:bg-emerald-700 dark:focus:bg-white active:bg-emerald-900 dark:active:bg-emerald-300 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 dark:focus:ring-offset-emerald-800 transition ease-in-out duration-150 shadow-custom-strong py-2 px-4">Submit</button>
                    @if($route[2] == 'neraca')
                        <div class="form-group flex items-center">
                            <input type="checkbox" id="checkbox" name="checkbox" class="h-4 w-4 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500">
                            <label for="checkbox" class="ml-2 block text-sm text-gray-900">Neraca Perbandingan</label>
                        </div>
                    @endif
                </div>
            </form>
        </div>
    @endsection

    @push('script')
        <script type="module">
            $(function() {
                flatpickr('#start_date', {
                    dateFormat: 'd-m-Y',
                    allowInput: true,
                    onClose: function(selectedDates, dateStr, instance) {
                        instance.setDate(dateStr, true);
                    }
                });

                flatpickr('#end_date', {
                    dateFormat: 'd-m-Y',
                    allowInput: true,
                    onClose: function(selectedDates, dateStr, instance) {
                        instance.setDate(dateStr, true);
                        var startDate = $('#start_date').val();
                        if (startDate && new Date(dateStr.split('-').reverse().join('-')) < new Date(startDate.split('-').reverse().join('-'))) {
                            alert('Tanggal selesai tidak boleh kurang dari tanggal mulai');
                            instance.clear();
                        }
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
