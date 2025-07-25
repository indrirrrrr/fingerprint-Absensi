<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>@yield('title', 'Dashboard') - Jhonlin Absensi</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.4/css/sb-admin-2.min.css" rel="stylesheet">
    
    <style>
        /* [STYLES ANDA TIDAK DIUBAH] */
        #accordionSidebar { background: linear-gradient(180deg, #ffffff 0%, #00759c 50%, #ffffff 100%); }
        #accordionSidebar .sidebar-brand-icon img { height: 40px; }
        #accordionSidebar .sidebar-brand-text span.brand-main { font-size: 1rem; font-weight: bold; display: block; color: #131212; }
        #accordionSidebar .sidebar-brand-text span.brand-sub { font-size: 0.75rem; display: block; color: #3f3b3b; }
        #accordionSidebar .sidebar-heading { color: #212529; }
        #accordionSidebar .nav-link { color: #023577; }
        #accordionSidebar .nav-link i { color: #023e8a; }
        #accordionSidebar .nav-item.active .nav-link { font-weight: bold; background-color: rgba(2, 62, 138, 0.1); }
        #accordionSidebar .nav-item.active .nav-link i { color: #023e8a; }
        #sidebarToggle, #sidebarToggle:hover, #sidebarToggle:focus { background-color: #a1c4fd !important; }
        .table .thead-dark th { border-top: none; }
        .table .thead-dark th:first-child { border-top-left-radius: 0.35rem; border-left: none; }
        .table .thead-dark th:last-child { border-top-right-radius: 0.35rem; border-right: none; }
        .table tbody td { color: #212529; }
        .table-bordered, .table-bordered td, .table-bordered th { border-color: #cccccc; }
    </style>

    @stack('styles')
</head>

<body id="page-top">
    <div id="wrapper">
        <ul class="navbar-nav sidebar accordion" id="accordionSidebar">
            <a class="sidebar-brand d-flex align-items-center" href="{{ route('dashboard') }}">
                <div class="sidebar-brand-icon">
                    <img src="{{ asset('assets/logo.png') }}" alt="Logo Jhonlin">
                </div>
                <div class="sidebar-brand-text mx-2">
                    <span class="brand-main">JHONLIN</span>
                    <span class="brand-sub">Absensi</span>
                </div>
            </a>
            <hr class="sidebar-divider my-0">
            <li class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('dashboard') }}">
                    <i class="fas fa-fw fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <hr class="sidebar-divider">
            <div class="sidebar-heading">Master Data</div>
            <li class="nav-item {{ request()->routeIs('absensi.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('absensi.index') }}">
                    <i class="fas fa-fw fa-table"></i>
                    <span>Data Absensi</span>
                </a>
            </li>

            <li class="nav-item {{ request()->routeIs('karyawan.*') ? 'active' : '' }}">
                <a class="nav-link" href="{{ route('karyawan.index') }}">
                    <i class="fas fa-fw fa-users"></i>
                    <span>Data Karyawan</span>
                </a>
            </li>
            <hr class="sidebar-divider d-none d-md-block">
            <div class="text-center d-none d-md-inline">
                <button class="rounded-circle border-0" id="sidebarToggle"></button>
            </div>
        </ul>
        <div id="content-wrapper" class="d-flex flex-column">
            <div id="content">
                <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
                    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3"><i class="fa fa-bars"></i></button>
                    <ul class="navbar-nav ml-auto"></ul>
                </nav>
                <div class="container-fluid">
                    <div class="d-sm-flex align-items-center justify-content-between mb-4">
                        <h1 class="h3 mb-0 text-gray-800">@yield('title', 'Dashboard')</h1>
                    </div>
                    @yield('content')
                </div>
            </div>
            <footer class="sticky-footer bg-white">
                <div class="container my-auto">
                    <div class="copyright text-center my-auto">
                        <span>Copyright &copy; Jhonlin Group {{ date('Y') }}</span>
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.4.1/jquery.easing.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/startbootstrap-sb-admin-2/4.1.4/js/sb-admin-2.min.js"></script>

    @stack('scripts') 

</body>
</html>