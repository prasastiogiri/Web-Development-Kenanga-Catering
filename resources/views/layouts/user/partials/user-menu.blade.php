<button class="dropdown-toggle text-light" type="button" id="userMenu" data-bs-toggle="dropdown" aria-expanded="false">
    {{ auth()->user()->nama }}
</button>
<ul class="dropdown-menu" aria-labelledby="userMenu" data-bs-proper="false">
    <li>
        <a class="dropdown-item" href="{{ route('user.profile') }}">Profil Saya</a>
    </li>
    <li>
        <a class="dropdown-item" href="{{ route('pemesanan.riwayatPemesanan') }}">Riwayat
            Pesanan</a>
    </li>
    <li>
        <a href="#" class="dropdown-item"
            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            Logout
        </a>
        <form id="logout-form" action="{{ route('user.logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </li>
</ul>
