<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Struk Pembayaran</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 py-10">

    <div class="max-w-md mx-auto bg-white shadow-lg rounded-lg p-6">
        <!-- Header -->
        <div class="text-center border-b pb-4 mb-4">
            <h1 class="text-xl font-bold uppercase">Khaira SportWear</h1>
            <p class="text-sm text-gray-500">Kp. Margahayu Rt. 001 Rw. 009 Desa Cicalengka Kulon, Kecamatan Cicalengka, Kabupaten Bandung. 40395.</p>
            <p class="text-sm text-gray-500">Telp: (021) 1234-5678</p>
        </div>

        <!-- Info Transaksi -->
        <div class="text-sm mb-4">
            <div class="flex justify-between">
                <span class="text-gray-500">Kode Transaksi</span>
                <span class="font-medium">{{$data->transaksi->kode_transaksi}}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Tanggal</span>
                <span class="font-medium">{{$data->transaksi->tanggal_pembayaran}}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Pelanggan</span>
                <span class="font-medium">{{$data->user->name}}</span>
            </div>
        </div>

        <!-- Detail Item -->
        <div class="border-t border-b py-3 mb-4 text-sm">
            <div class="flex justify-between mb-1">
                <span>Sewa {{$data->produk->nama}}</span>
                <span>Rp 
                    {{ number_format(($data->durasi == 'harian' ? $data->produk->harga_sewa_harian : ($data->durasi == 'mingguan' ? $data->produk->harga_sewa_mingguan : $data->produk->harga_sewa_bulanan)), 0, ',', '.') }}
                    /{{$data->durasi == 'harian' ? 'Hari' : ($data->durasi == 'mingguan' ? 'Minggu' : 'Bulan')}}
                </span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Durasi</span>
                <span>{{$data->jumlah_hari}} Hari</span>
            </div>
        </div>

        <!-- Total -->
        <div class="text-sm mb-4">
            <div class="flex justify-between">
                <span>Subtotal</span>
                <span>Rp {{ number_format($data->total_harga, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between">
                <span>Diskon</span>
                <span>- Rp 0</span>
            </div>
            <div class="flex justify-between font-bold text-base mt-2">
                <span>Total</span>
                <span>Rp {{ number_format($data->total_harga, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Status -->
        <div class="text-center mb-4">
            <span class="inline-block px-4 py-1 rounded-full bg-green-100 text-green-700 text-sm font-medium">
                LUNAS
            </span>
        </div>

        <!-- Footer -->
        <div class="text-center text-xs text-gray-500">
            <p>Terima kasih atas kepercayaan Anda</p>
            <p>Struk ini sah tanpa tanda tangan</p>
        </div>
    </div>

</body>
</html>
