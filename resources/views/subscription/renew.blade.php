@extends('layouts.app')

@section('content')
<div class="flex justify-center items-center min-h-screen bg-gray-50">
    <div class="bg-white shadow-md rounded-xl p-6 w-full max-w-sm">
        <div class="flex items-center justify-center mb-4">
            <div class="bg-emerald-100 text-emerald-600 rounded-full p-3">
                <!-- Refresh Icon -->
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M4 4v6h6M20 20v-6h-6M4 14a8 8 0 0114.32-4.906M20 10a8 8 0 01-14.32 4.906"/>
                </svg>
            </div>
        </div>

        <h2 class="text-xl font-semibold text-center text-gray-800">Perpanjang Langganan</h2>
        <p class="text-gray-500 text-center text-sm mt-2 mb-6">
            Langganan Anda akan segera berakhir. Klik tombol di bawah untuk memperpanjang selama 1 tahun lagi.
        </p>

        <button onclick="payWithMidtrans('renew')"
                class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-2.5 rounded-lg font-semibold transition">
            🔁 Perpanjang Sekarang
        </button>
    </div>
</div>
@endsection
@push('script')
<script type="text/javascript"
            src="https://app.midtrans.com/snap/snap.js"
            data-client-key="{{ config('midtrans.client_key') }}">
    </script>
    <script>
        function payWithMidtrans(type) {
            fetch(`/subscription/token?type=${type}`)
                .then(response => response.json())
                .then(data => {
                    if (data.token) {
                        snap.pay(data.token, {
                            onSuccess: function(result) {
                               window.location.href = "/subscription/success?order_id=" + result.order_id;
                            },
                            onPending: function(result) {
                                alert("Pembayaran tertunda. Silakan cek dashboard.");
                            },
                            onError: function(result) {
                                alert("Pembayaran gagal. Silakan coba lagi.");
                            },
                            onClose: function() {
                                alert('Anda menutup pembayaran sebelum selesai.');
                            }
                        });
                    } else {
                        alert("Token pembayaran tidak ditemukan.");
                    }
                });
        }
    </script>

@endpush
