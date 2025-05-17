<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Système de Gestion - Chef de Département</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>
<body>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <div class="bg-primary" id="sidebar-wrapper">
            <div class="sidebar-heading text-white text-center py-4">
                <h4>Chef de Département</h4>
            </div>
            <div class="list-group list-group-flush">
                <a href="{{ route('dashboard') }}" class="list-group-item list-group-item-action bg-transparent text-white {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt me-2"></i>Tableau de bord
                </a>
                <a href="{{ route('team.index') }}" class="list-group-item list-group-item-action bg-transparent text-white {{ request()->routeIs('team*') ? 'active' : '' }}">
                    <i class="fas fa-users me-2"></i>Gestion d'Équipe
                </a>
                <a href="{{ route('attendance.index') }}" class="list-group-item list-group-item-action bg-transparent text-white {{ request()->routeIs('attendance*') ? 'active' : '' }}">
                    <i class="fas fa-calendar-check me-2"></i>Présences
                </a>
                <a href="{{ route('tasks.index') }}" class="list-group-item list-group-item-action bg-transparent text-white {{ request()->routeIs('tasks*') ? 'active' : '' }}">
                    <i class="fas fa-tasks me-2"></i>Tâches
                </a>
                <a href="{{ route('evaluations.index') }}" class="list-group-item list-group-item-action bg-transparent text-white {{ request()->routeIs('evaluations*') ? 'active' : '' }}">
                    <i class="fas fa-star me-2"></i>Évaluations
                </a>
                <a href="{{ route('reports.index') }}" class="list-group-item list-group-item-action bg-transparent text-white {{ request()->routeIs('reports*') ? 'active' : '' }}">
                    <i class="fas fa-chart-line me-2"></i>Rapports
                </a>
                <a href="{{ route('requests.index') }}" class="list-group-item list-group-item-action bg-transparent text-white {{ request()->routeIs('requests*') ? 'active' : '' }}">
                    <i class="fas fa-envelope me-2"></i>Demandes
                </a>
            </div>
        </div>
        <!-- Page Content -->
        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
                <div class="container-fluid">
                    <button class="btn btn-primary" id="menu-toggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <div class="ms-auto">
                        <div class="dropdown">
                            <button class="btn btn-light dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user me-2"></i>Admin
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                <li><a class="dropdown-item" href="#"><i class="fas fa-user-cog me-2"></i>Profil</a></li>
                                <li><hr class="dropdown-divider"></li>
<<<<<<< HEAD
                                <li>
                                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                                        </button>
                                    </form>
                                </li>
=======
                                <li><a class="dropdown-item" href="#"><i class="fas fa-sign-out-alt me-2"></i>Déconnexion</a></li>
>>>>>>> c20c1856788050a6e6e89bca26b992efb1776b00
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>

            <div class="container-fluid p-4">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ asset('js/main.js') }}"></script>
    @yield('scripts')
</body>
</html>