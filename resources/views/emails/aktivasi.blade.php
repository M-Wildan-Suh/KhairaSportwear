<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Terima Kasih - Khaira Sportwear</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body{
            margin:0;
            padding:0;
            font-family: Arial, Helvetica, sans-serif;
            background:#f5f5f5;
        }
        .wrapper{
            min-height:100vh;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:20px;
        }
        .card{
            background:#fff;
            max-width:500px;
            width:100%;
            padding:40px 30px;
            border-radius:12px;
            box-shadow:0 10px 25px rgba(0,0,0,0.08);
            text-align:center;
        }
        .title{
            font-size:24px;
            font-weight:bold;
            margin-bottom:15px;
            color:#222;
        }
        .text{
            color:#555;
            margin-bottom:10px;
            line-height:1.6;
        }
        .box{
            background:#e8f9ee;
            color:#1f8f4c;
            padding:15px;
            border-radius:8px;
            margin:20px 0;
            font-size:14px;
        }
        .btn{
            display:inline-block;
            margin-top:15px;
            padding:12px 25px;
            background:#16a34a;
            color:white !important;
            text-decoration:none;
            border-radius:8px;
            font-weight:bold;
        }
        .btn:hover{
            background:#15803d;
        }
    </style>
</head>
<body>

<div class="wrapper">
    <div class="card">

        <div class="title">
            Terima Kasih Sudah Mendaftar 🎉
        </div>

        <p class="text">
            Halo <b>{{ $user->name ?? 'Customer' }}</b>,
        </p>

        <p class="text">
            Terima kasih telah mendaftar di <b>Khaira Sportwear</b>.
            Akun kamu sudah berhasil dibuat.
        </p>

        <div class="box">
            Sekarang kamu sudah bisa login dan mulai berbelanja produk terbaik dari kami.
        </div>

        <a href="{{ route('login') }}" class="btn">
            Login Sekarang
        </a>

    </div>
</div>

</body>
</html>
