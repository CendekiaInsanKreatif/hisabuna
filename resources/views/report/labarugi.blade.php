<x-app-layout>
    @section('content')
    <div class="container mx-auto mt-5">
        <h1>Laporan Laba Rugi</h1>
        <form method="GET" action="{{ route('report.labarugi') }}">
            <div class="form-group">
                <label for="tanggal_mulai">Tanggal Mulai:</label>
                <input type="date" id="tanggal_mulai" name="tanggal_mulai" value="{{ $tanggalMulai }}" class="form-control">
            </div>
            <div class="form-group">
                <label for="tanggal_selesai">Tanggal Selesai:</label>
                <input type="date" id="tanggal_selesai" name="tanggal_selesai" value="{{ $tanggalSelesai }}" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary mt-3">Tampilkan</button>
        </form>

        <div class="mt-5">
            <h2>Periode: {{ $tanggalMulai }} - {{ $tanggalSelesai }}</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Deskripsi</th>
                        <th>Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Pendapatan</td>
                        <td>{{ number_format($pendapatan, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Beban</td>
                        <td>{{ number_format($beban, 2) }}</td>
                    </tr>
                    <tr>
                        <th>Laba Rugi</th>
                        <th>{{ number_format($labaRugi, 2) }}</th>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    @endsection
</x-app-layout>
