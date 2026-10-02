<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Coffee POS System') - Artisan Coffee</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FontAwesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --primary: #6F4E37;
            --primary-hover: #543B29;
            --secondary: #D4A373;
            --accent: #E9EDC9;
            --dark-bg: #1C1917;
            --sidebar-bg: #262320;
            --card-bg: #FFFFFF;
            --text-main: #292524;
            --text-muted: #78716C;
            --border-color: #E7E5E4;
            --radius-lg: 16px;
            --radius-md: 12px;
            --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.04);
            --shadow-md: 0 6px 20px rgba(0, 0, 0, 0.08);
            --shadow-lg: 0 12px 32px rgba(0, 0, 0, 0.12);
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #F8F6F4;
            color: var(--text-main);
            min-height: 100vh;
            overflow-x: hidden;
        }

        h1, h2, h3, h4, h5, h6, .brand-font {
            font-family: 'Outfit', sans-serif;
        }

        /* Sidebar Styling */
        .sidebar {
            width: 260px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: var(--sidebar-bg);
            color: #E7E5E4;
            z-index: 1000;
            display: flex;
            flex-direction: column;
            box-shadow: 4px 0 20px rgba(0,0,0,0.15);
            transition: all 0.3s ease;
        }

        .sidebar-brand {
            padding: 24px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }

        .sidebar-brand .logo-icon {
            width: 42px;
            height: 42px;
            background: linear-gradient(135deg, var(--secondary), var(--primary));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 20px;
            box-shadow: 0 4px 12px rgba(212, 163, 115, 0.3);
        }

        .sidebar-brand .brand-title {
            font-size: 20px;
            font-weight: 700;
            color: #FFFFFF;
            letter-spacing: -0.5px;
            margin: 0;
        }

        .sidebar-menu {
            padding: 20px 12px;
            flex: 1;
            overflow-y: auto;
        }

        .nav-category {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #A8A29E;
            padding: 12px 14px 6px 14px;
        }

        .sidebar .nav-link {
            color: #D6D3D1;
            padding: 12px 16px;
            border-radius: var(--radius-md);
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 4px;
            transition: all 0.2s ease;
        }

        .sidebar .nav-link i {
            font-size: 18px;
            width: 22px;
            text-align: center;
            transition: transform 0.2s ease;
        }

        .sidebar .nav-link:hover {
            color: #FFFFFF;
            background: rgba(255, 255, 255, 0.08);
        }

        .sidebar .nav-link:hover i {
            transform: translateX(3px);
        }

        .sidebar .nav-link.active {
            color: #FFFFFF;
            background: linear-gradient(135deg, var(--primary), #8B5E3C);
            box-shadow: 0 4px 14px rgba(111, 78, 55, 0.4);
        }

        /* Main Content Wrapper */
        .main-wrapper {
            margin-left: 260px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Top Navbar Header */
        .top-header {
            height: 70px;
            background: #FFFFFF;
            border-bottom: 1px solid var(--border-color);
            padding: 0 32px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 900;
            box-shadow: var(--shadow-sm);
        }

        .user-pill {
            display: flex;
            align-items: center;
            gap: 12px;
            background: #F5F5F4;
            padding: 6px 16px;
            border-radius: 30px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .user-pill:hover {
            background: #E7E5E4;
        }

        .user-avatar {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, #6F4E37, #D4A373);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
        }

        /* Body Area */
        .content-body {
            padding: 32px;
            flex: 1;
        }

        /* Card Styling */
        .card-custom {
            background: #FFFFFF;
            border: 1px solid #E7E5E4;
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card-custom:hover {
            box-shadow: var(--shadow-md);
        }

        .btn-coffee {
            background: var(--primary);
            color: white;
            border: none;
            border-radius: var(--radius-md);
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        .btn-coffee:hover {
            background: var(--primary-hover);
            color: white;
            box-shadow: 0 4px 12px rgba(111, 78, 55, 0.3);
        }

        .badge-role {
            font-size: 11px;
            padding: 4px 10px;
            border-radius: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge-admin {
            background: #FEF3C7;
            color: #92400E;
        }

        .badge-cashier {
            background: #E0E7FF;
            color: #3730A3;
        }

        /* Responsive */
        @media (max-width: 991px) {
            .sidebar {
                margin-left: -260px;
            }
            .sidebar.show {
                margin-left: 0;
            }
            .main-wrapper {
                margin-left: 0;
            }
        }
    </style>
    @yield('styles')
</head>
<body>

    <!-- Sidebar Navigation -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-brand">
            <div class="logo-icon">
                <i class="fa-solid fa-mug-hot"></i>
            </div>
            <div>
                <h1 class="brand-title">Artisan POS</h1>
                <small style="color: #A8A29E; font-size: 11px;">v1.0.0 Coffee System</small>
            </div>
        </div>

        <div class="sidebar-menu">
            <div class="nav-category">Main Menu</div>
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-line"></i>
                <span>Dashboard</span>
            </a>

            <a href="{{ route('pos.index') }}" class="nav-link {{ request()->routeIs('pos.index') ? 'active' : '' }}">
                <i class="fa-solid fa-cash-register"></i>
                <span>POS Terminal</span>
            </a>

            <a href="{{ route('orders.index') }}" class="nav-link {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                <i class="fa-solid fa-receipt"></i>
                <span>Orders</span>
            </a>

            <div class="nav-category">Management</div>

            <a href="{{ route('coffees.index') }}" class="nav-link {{ request()->routeIs('coffees.*') ? 'active' : '' }}">
                <i class="fa-solid fa-mug-saucer"></i>
                <span>Coffee Menu</span>
            </a>

            <a href="{{ route('categories.index') }}" class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                <i class="fa-solid fa-tags"></i>
                <span>Categories</span>
            </a>

            <a href="{{ route('tables.index') }}" class="nav-link {{ request()->routeIs('tables.*') ? 'active' : '' }}">
                <i class="fa-solid fa-chair"></i>
                <span>Tables</span>
            </a>

            <a href="{{ route('customers.index') }}" class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}">
                <i class="fa-solid fa-users"></i>
                <span>Customers</span>
            </a>

            <a href="{{ route('reports.index') }}" class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <i class="fa-solid fa-file-invoice-dollar"></i>
                <span>Sales Reports</span>
            </a>

            @if(Auth::check() && Auth::user()->isAdmin())
                <div class="nav-category">System Admin</div>
                <a href="{{ route('users.index') }}" class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-user-gear"></i>
                    <span>Users</span>
                </a>

                <a href="{{ route('settings.index') }}" class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                    <i class="fa-solid fa-sliders"></i>
                    <span>System Settings</span>
                </a>
            @endif
        </div>
    </aside>

    <!-- Main Wrapper -->
    <div class="main-wrapper">

        <!-- Top Header -->
        <header class="top-header">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-light d-lg-none" onclick="document.getElementById('sidebar').classList.toggle('show')">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <h5 class="m-0 fw-bold brand-font text-dark">@yield('page_title', 'Dashboard')</h5>
            </div>

            <div class="d-flex align-items-center gap-3">
                @if(Auth::check())
                    <div class="dropdown">
                        <div class="user-pill" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-avatar">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <div class="d-none d-sm-block text-start">
                                <div class="fw-bold text-dark fs-7" style="line-height: 1.2;">{{ Auth::user()->name }}</div>
                                <span class="badge-role {{ Auth::user()->isAdmin() ? 'badge-admin' : 'badge-cashier' }}">
                                    {{ ucfirst(Auth::user()->role) }}
                                </span>
                            </div>
                            <i class="fa-solid fa-chevron-down ms-1 text-muted fs-7"></i>
                        </div>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 mt-2 rounded-3">
                            <li class="px-3 py-2 border-bottom">
                                <small class="text-muted d-block">Signed in as</small>
                                <strong>{{ Auth::user()->email }}</strong>
                            </li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger fw-semibold py-2">
                                        <i class="fa-solid fa-right-from-bracket me-2"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @endif
            </div>
        </header>

        <!-- Content Body -->
        <main class="content-body">
            <!-- Flash Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                    <div class="fw-bold mb-1"><i class="fa-solid fa-circle-xmark me-2"></i> Please fix the following errors:</div>
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/bootstrap.bundle.min.js"></script>

    @yield('scripts')
</body>
</html>
