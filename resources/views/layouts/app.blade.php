<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistem Screening & Tes Psikotes - AI-Powered Recruitment">
    <title>@yield('title', 'Screening & Tes Psikotes')</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    
    <!-- App CSS -->
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    
    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <a href="{{ route('landing') }}" class="navbar-brand">
            <div class="navbar-brand-icon">
                <i data-lucide="brain"></i>
            </div>
            <span class="lg:block hidden">ScreeningAI</span>
        </a>

        <button class="navbar-toggle" onclick="toggleMobileMenu()" aria-label="Toggle menu">
            <i data-lucide="menu"></i>
        </button>

        <div class="navbar-nav" id="navbarNav">
            <a href="{{ route('landing') }}" class="navbar-link {{ request()->routeIs('landing') ? 'active' : '' }}">
                <i data-lucide="home" style="width:18px;height:18px"></i>
                <span>Beranda</span>
            </a>
            <a href="{{ route('jobs.public') }}" class="navbar-link {{ request()->routeIs('jobs.public') ? 'active' : '' }}">
                <i data-lucide="briefcase" style="width:18px;height:18px"></i>
                <span>Lowongan</span>
            </a>
            @auth
            @if(auth()->user()->role === 'admin')
            <a href="{{ route('dashboard') }}" class="navbar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i data-lucide="layout-dashboard" style="width:18px;height:18px"></i>
                <span>Dashboard</span>
            </a>
            @elseif(auth()->user()->role === 'candidate')
            <a href="{{ route('applications.index') }}" class="navbar-link {{ request()->routeIs('applications.*') ? 'active' : '' }}">
                <i data-lucide="file-text" style="width:18px;height:18px"></i>
                <span>Lamaran Saya</span>
            </a>
            @endif
            @endauth
        </div>
    </nav>

    @if((request()->routeIs('dashboard') || request()->routeIs('jobs.*') || request()->routeIs('applications.*')) && !request()->routeIs('jobs.public'))
    <!-- Sidebar for Admin -->
    <aside class="sidebar" id="sidebar">
        <nav class="sidebar-nav">
            @if(auth()->user()->role === 'admin')
            <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span class="sidebar-link-icon"><i data-lucide="layout-dashboard"></i></span>
                Dashboard
            </a>
            @endif
            
            <div class="sidebar-section">
                @if(auth()->user()->role === 'admin')
                <div class="sidebar-section-title">Manajemen</div>
                
                <a href="{{ route('jobs.index') }}" class="sidebar-link {{ request()->routeIs('jobs.*') ? 'active' : '' }}">
                    <span class="sidebar-link-icon"><i data-lucide="briefcase"></i></span>
                    Lowongan Kerja
                </a>
                
                <a href="{{ route('applications.index') }}" class="sidebar-link {{ request()->routeIs('applications.*') ? 'active' : '' }}">
                    <span class="sidebar-link-icon"><i data-lucide="file-text"></i></span>
                    Lamaran Masuk
                </a>

                <div class="sidebar-section-title mt-4">Quick Actions</div>
                
                <a href="{{ route('jobs.create') }}" class="sidebar-link">
                    <span class="sidebar-link-icon"><i data-lucide="plus-circle"></i></span>
                    Buat Lowongan
                </a>
                
                <a href="{{ route('applications.create') }}" class="sidebar-link">
                    <span class="sidebar-link-icon"><i data-lucide="upload"></i></span>
                    Input Lamaran
                </a>
                @else
                <div class="sidebar-section-title">Menu Kandidat</div>
                <a href="{{ route('applications.index') }}" class="sidebar-link {{ request()->routeIs('applications.*') ? 'active' : '' }}">
                    <span class="sidebar-link-icon"><i data-lucide="file-text"></i></span>
                    Lamaran Saya
                </a>
                @endif
            </div>
        </nav>

        <div class="sidebar-footer" style="padding: 16px; border-top: 1px solid var(--color-gray-200); margin-top: auto;">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="sidebar-link w-full text-left" style="color: var(--color-danger); justify-content: flex-start;">
                    <span class="sidebar-link-icon"><i data-lucide="log-out"></i></span>
                    Logout
                </button>
            </form>
        </div>
    </aside>
    @endif

    <!-- Main Content -->
    <main class="{{ (request()->routeIs('dashboard') || request()->routeIs('jobs.*') || request()->routeIs('applications.*')) && !request()->routeIs('jobs.public') ? 'main-content' : '' }}">
        @if(session('success'))
        <div class="alert alert-success animate-fadeIn">
            <i data-lucide="check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger animate-fadeIn">
            <i data-lucide="alert-circle"></i>
            <span>{{ session('error') }}</span>
        </div>
        @endif

        @yield('content')
    </main>

    <!-- Initialize Lucide Icons -->
    <script>
        lucide.createIcons();

        function toggleMobileMenu() {
            const nav = document.getElementById('navbarNav');
            const sidebar = document.getElementById('sidebar');
            
            if (nav) nav.classList.toggle('active');
            if (sidebar) sidebar.classList.toggle('active');
        }

        // Close mobile menu on window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1024) {
                const nav = document.getElementById('navbarNav');
                const sidebar = document.getElementById('sidebar');
                if (nav) nav.classList.remove('active');
                if (sidebar) sidebar.classList.remove('active');
            }
        });
    </script>

    @stack('scripts')
</body>
</html>
