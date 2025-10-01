@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-6">
    <h2 class="text-2xl font-bold mb-4">Pilih Paket Langganan</h2>
    <div class="flex flex-col md:flex-row gap-6">
        @php
            $plans = [
                ['name' => 'Standard', 'price' => 2000000, 'features' => []],
                // ['name' => 'Pro', 'price' => 5000000, 'features' => ['All Standar', 'Multiple users', 'Export PDF']],
                // ['name' => 'Enterprise', 'price' => 0, 'features' => ['Custom support', 'Dedicated manager'], 'note' => 'Hubungi Kami'],
            ];
        @endphp

        @foreach($plans as $plan)
            <div class="border p-4 rounded-lg shadow-md bg-white flex flex-col justify-between w-full md:w-1/3">
                <div>
                    <h3 class="text-xl font-semibold">{{ $plan['name'] }}</h3>
                    <p class="text-emerald-600 font-bold text-lg mt-2">
                        @if($plan['price'] > 0)
                            Rp{{ number_format($plan['price'], 0, ',', '.') }}/tahun
                        @else
                            {{ $plan['note'] }}
                        @endif
                    </p>
                    <ul class="mt-3 text-sm space-y-1">
                        @foreach($plan['features'] as $feature)
                            <li>✔ {{ $feature }}</li>
                        @endforeach
                    </ul>
                </div>
                @if($plan['price'] > 0)
                    <button onclick="payWithMidtrans('{{ strtolower($plan['name']) }}')"
                        class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-2 rounded mt-4">
                            Pilih {{ $plan['name'] }}
                    </button>
                @else
                    <a href="https://wa.me/6285155330630" class="mt-4 block text-center bg-emerald-600 hover:bg-emerald-700 text-white py-2 rounded">
                        Hubungi Kami
                    </a>
                @endif
            </div>
        @endforeach
    </div>
</div>
@endsection
@push('script')
<script type="text/javascript"
            src="https://app.midtrans.com/snap/snap.js"
            data-client-key="{{ config('midtrans.client_key') }}">
    </script>
    <script>
        function payWithMidtrans(plan) {
            const type = 'upgrade';
            fetch(`/subscription/token?type=${type}&plan=${plan}`)
                .then(response => response.json())
                .then(data => {
                    snap.pay(data.token, {
                       onSuccess: function(result) {
                         console.log('Transaction success:', result);
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
                })

                .catch(error => {
                    console.error('Error:', error);
                    alert('Error: ' + error.message);
                    document.getElementById('loading-'+plan).classList.add('d-none');
                });
        }
    </script>

@endpush
