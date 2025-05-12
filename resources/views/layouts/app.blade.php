<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Système de Gestion RH</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    @yield('styles')
</head>
<body>
    <div class="d-flex flex-column min-vh-100">
        <!-- Header/Navbar -->
        <header class="navbar navbar-dark sticky-top bg-primary flex-md-nowrap p-0 shadow">
            <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 fs-5" href="{{ route('department_head.dashboard') }}">
                <i class="fas fa-building me-2"></i> Système GRH
            </a>
            <button class="navbar-toggler position-absolute d-md-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="w-100"></div>
            <div class="navbar-nav">
                <div class="nav-item text-nowrap d-flex align-items-center me-3">
                @auth
    <span class="text-white me-3">{{ Auth::user()->name }}</span>
    <a class="nav-link px-3" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
        <i class="fas fa-sign-out-alt me-1"></i> Déconnexion
    </a>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>
@endauth

@guest
    <a class="nav-link px-3 text-white" href="{{ route('login') }}">
        <i class="fas fa-sign-in-alt me-1"></i> Se connecter
    </a>
@endguest

                </div>
            </div>
        </header>

        <div class="container-fluid">
            <div class="row">
                <!-- Sidebar -->
                <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
                    <div class="position-sticky pt-3">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('department_head.dashboard') ? 'active' : '' }}" href="{{ route('department_head.dashboard') }}">
                                    <i class="fas fa-tachometer-alt me-2"></i> Tableau de bord
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('department_head.team_management') || request()->routeIs('department_head.employee_details') ? 'active' : '' }}" href="{{ route('department_head.team_management') }}">
                                    <i class="fas fa-users me-2"></i> Gestion d'équipe
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('department_head.attendance') || request()->routeIs('department_head.attendance_history') ? 'active' : '' }}" href="{{ route('department_head.attendance') }}">
                                    <i class="fas fa-clock me-2"></i> Présences
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('department_head.leave_requests') || request()->routeIs('department_head.overtime_requests') ? 'active' : '' }}" href="{{ route('department_head.leave_requests') }}">
                                    <i class="fas fa-calendar-check me-2"></i> Demandes
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('department_head.tasks') ? 'active' : '' }}" href="{{ route('department_head.tasks') }}">
                                    <i class="fas fa-tasks me-2"></i> Tâches
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('department_head.announcements') ? 'active' : '' }}" href="{{ route('department_head.announcements') }}">
                                    <i class="fas fa-bullhorn me-2"></i> Annonces
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('department_head.evaluations') || request()->routeIs('department_head.edit_evaluation') ? 'active' : '' }}" href="{{ route('department_head.evaluations') }}">
                                    <i class="fas fa-star me-2"></i> Évaluations
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('department_head.reports') || request()->routeIs('department_head.view_report') ? 'active' : '' }}" href="{{ route('department_head.reports') }}">
                                    <i class="fas fa-chart-bar me-2"></i> Rapports
                                </a>
                            </li>
                        </ul>
                    </div>
                </nav>

                <!-- Main Content -->
                <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @yield('content')
                </main>
            </div>
        </div>

        <!-- Footer -->
        <footer class="footer mt-auto py-3 bg-light">
            <div class="container text-center">
                <span class="text-muted">&copy; {{ date('Y') }} Système de Gestion RH. Tous droits réservés.</span>
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script src="{{ asset('js/script.js') }}"></script>
    @yield('scripts')
</body>
</html>