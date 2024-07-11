<div id="ledgerContent" style="width: 100%; font-family: 'Arial', sans-serif;">
    <div style="text-align: center;">
        <h1 style="font-size: 1.5em; font-weight: bold; color: #2f855a; margin-bottom: 16px;">BUKU BESAR (LEDGER)</h1>
    </div>
    @foreach ($ledgers as $coaAkun => $transactions)
        <h2 style="font-size: 1.25em; font-weight: 600; color: #4a5568;">{{ $coaAkun }}</h2>
        <table style="width: 100%; border-collapse: collapse; margin-top: 16px; margin-bottom: 24px;">
            <thead style="background-color: #f7fafc;">
                <tr>
                    <th scope="col" style="width: 20%; padding: 8px; text-align: left; font-size: 0.75rem; font-weight: 500; color: #718096; text-transform: uppercase;">Tanggal</th>
                    <th scope="col" style="width: 40%; padding: 8px; text-align: left; font-size: 0.75rem; font-weight: 500; color: #718096; text-transform: uppercase;">Keterangan</th>
                    <th scope="col" style="width: 16.66%; padding: 8px; text-align: left; font-size: 0.75rem; font-weight: 500; color: #718096; text-transform: uppercase;">Debit<div style="color: #e53e3e;">Bertambah</div></th>
                    <th scope="col" style="width: 16.66%; padding: 8px; text-align: left; font-size: 0.75rem; font-weight: 500; color: #718096; text-transform: uppercase;">Kredit<div style="color: #e53e3e;">Berkurang</div></th>
                    <th scope="col" style="width: 16.66%; padding: 8px; text-align: left; font-size: 0.75rem; font-weight: 500; color: #718096; text-transform: uppercase;">Saldo</th>
                </tr>
            </thead>
            <tbody style="background-color: #ffffff; border-top: 1px solid #e2e8f0;">
                @foreach ($transactions as $transaction)
                    <tr>
                        <td style="padding: 8px; white-space: nowrap; font-size: 0.875rem; color: #718096;">{{ \Carbon\Carbon::parse($transaction->jurnal_tgl)->format('F d') }}</td>
                        <td style="padding: 8px; white-space: nowrap; font-size: 0.875rem; color: #718096;">
                            @php
                                $keterangan = $transaction->keterangan;
                                $max_length = 50;
                                $output = '';
                                while (strlen($keterangan) > $max_length) {
                                    $output .= substr($keterangan, 0, $max_length) . '<br>';
                                    $keterangan = substr($keterangan, $max_length);
                                }
                                $output .= $keterangan;
                                echo $output;
                            @endphp
                        </td>
                        <td style="padding: 8px; white-space: nowrap; font-size: 0.875rem; color: #718096;">{{ number_format($transaction->debit, 0, ',', '.') }}</td>
                        <td style="padding: 8px; white-space: nowrap; font-size: 0.875rem; color: #718096;">{{ number_format($transaction->credit, 0, ',', '.') }}</td>
                        <td style="padding: 8px; white-space: nowrap; font-size: 0.875rem; color: #718096;">{{ number_format($transaction->saldo, 0, ',', '.') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endforeach
</div>
