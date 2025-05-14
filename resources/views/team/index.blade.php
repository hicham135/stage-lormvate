@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gestion de l'Équipe</h1>
    </div>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nom</th>
                            <th>Email</th>
                            <th>Poste</th>
                            <th>Tâches en cours</th>
                            <th>Dernière évaluation</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($team as $member)
                        <tr>
                            <td>{{ $member->name }}</td>
                            <td>{{ $member->email }}</td>
                            <td>{{ $member->role == 'employee' ? 'Employé' : $member->role }}</td>
                            <td>{{ $member->tasks->where('status', 'in_progress')->count() }}</td>
                            <td>
                                @if($member->evaluations->count() > 0)
                                    {{ $member->evaluations->sortByDesc('created_at')->first()->created_at->format('d/m/Y') }}
                                @else
                                    Aucune
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('team.show', $member->id) }}" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('evaluations.create', ['user_id' => $member->id]) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-star"></i> Évaluer
                                </a>
                                <a href="{{ route('tasks.create', ['user_id' => $member->id]) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-tasks"></i> Assigner
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection