<nav class="sidebar sidebar-offcanvas" id="sidebar">
    <ul class="nav">
        <li class="nav-item">
            <a class="nav-link" href="{{ route(name: 'home') }}">
                <i class="icon-grid menu-icon"></i>
                <span class="menu-title">Dashboard</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route(name: 'pedagang') }}">
                <i class="menu-icon icon-head"></i>
                <span class="menu-title">Pedagang</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route(name: 'tagihan') }}">
                <i class="icon-layout menu-icon"></i>
                <span class="menu-title">Tagihan</span>
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route(name: 'transaksi') }}">
                <i class="icon-paper menu-icon"></i>
                <span class="menu-title">Transaksi</span>
            </a>
        </li>
    </ul>
</nav>