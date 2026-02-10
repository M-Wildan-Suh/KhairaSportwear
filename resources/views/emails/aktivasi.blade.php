<h2>Halo {{ $user->name }}</h2>

<p>Terima kasih sudah mendaftar di SportWear.</p>
<p>Apakah kamu ingin mengaktifkan akun?</p>

<br>

<div style="display: flex; width: 100%; gap: 12px">
    <a href="{{ $yesLink }}" 
    style="width:100%; text-align:center; padding:12px 20px;background:green;color:white;text-decoration:none;border-radius:6px;">
    YA, Aktifkan Akun
    </a>
    
    <a href="{{ $noLink }}" 
    style="width:100%; text-align:center; padding:12px 20px;background:red;color:white;text-decoration:none;border-radius:6px;">
    TIDAK
    </a>
</div>

<p>Link berlaku 24 jam</p>
