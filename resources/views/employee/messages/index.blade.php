@extends('employee.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Messages et Annonces</h1>
    </div>
    
    <div class="card">
        <div class="card-header bg-light">
            <ul class="nav nav-tabs card-header-tabs" id="messagesTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="all-tab" data-bs-toggle="tab" href="#all" role="tab" aria-controls="all" aria-selected="true">Tous</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="announcements-tab" data-bs-toggle="tab" href="#announcements" role="tab" aria-controls="announcements" aria-selected="false">Annonces</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="personal-tab" data-bs-toggle="tab" href="#personal" role="tab" aria-controls="personal" aria-selected="false">Messages personnels</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="unread-tab" data-bs-toggle="tab" href="#unread" role="tab" aria-controls="unread" aria-selected="false">Non lus</a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="messagesTabContent">
                <div class="tab-pane fade show active" id="all" role="tabpanel" aria-labelledby="all-tab">
                    @if($messages->count() > 0)
                        <div class="list-group">
                            @foreach($messages as $message)
                                <a href="{{ route('employee.messages.show', $message->id) }}" class="list-group-item list-group-item-action {{ !$message->read_at && $message->user_id == $employee->id ? 'list-group-item-warning' : '' }} {{ $message->is_announcement ? 'border-start border-info border-3' : '' }}">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $message->title }}</h6>
                                        <small>{{ $message->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1">{{ Str::limit($message->content, 100) }}</p>
                                    <small>
                                        <span class="text-muted">De: {{ $message->sender->name }}</span>
                                        @if($message->is_announcement)
                                            <span class="badge bg-info ms-2">Annonce</span>
                                        @endif
                                        @if(!$message->read_at && $message->user_id == $employee->id)
                                            <span class="badge bg-warning ms-2">Non lu</span>
                                        @endif
                                    </small>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>Aucun message trouvé.
                        </div>
                    @endif
                </div>
                
                <div class="tab-pane fade" id="announcements" role="tabpanel" aria-labelledby="announcements-tab">
                    @php
                        $announcements = $messages->where('is_announcement', true);
                    @endphp
                    
                    @if($announcements->count() > 0)
                        <div class="list-group">
                            @foreach($announcements as $announcement)
                                <a href="{{ route('employee.messages.show', $announcement->id) }}" class="list-group-item list-group-item-action border-start border-info border-3">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $announcement->title }}</h6>
                                        <small>{{ $announcement->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1">{{ Str::limit($announcement->content, 100) }}</p>
                                    <small>
                                        <span class="text-muted">De: {{ $announcement->sender->name }}</span>
                                        <span class="badge bg-info ms-2">Annonce</span>
                                    </small>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>Aucune annonce trouvée.
                        </div>
                    @endif
                </div>
                
                <div class="tab-pane fade" id="personal" role="tabpanel" aria-labelledby="personal-tab">
                    @php
                        $personalMessages = $messages->where('user_id', $employee->id)->where('is_announcement', false);
                    @endphp
                    
                    @if($personalMessages->count() > 0)
                        <div class="list-group">
                            @foreach($personalMessages as $message)
                                <a href="{{ route('employee.messages.show', $message->id) }}" class="list-group-item list-group-item-action {{ !$message->read_at ? 'list-group-item-warning' : '' }}">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $message->title }}</h6>
                                        <small>{{ $message->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1">{{ Str::limit($message->content, 100) }}</p>
                                    <small>
                                        <span class="text-muted">De: {{ $message->sender->name }}</span>
                                        @if(!$message->read_at)
                                            <span class="badge bg-warning ms-2">Non lu</span>
                                        @endif
                                    </small>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>Aucun message personnel trouvé.
                        </div>
                    @endif
                </div>
                
                <div class="tab-pane fade" id="unread" role="tabpanel" aria-labelledby="unread-tab">
                    @php
                        $unreadMessages = $messages->where('user_id', $employee->id)->whereNull('read_at');
                    @endphp
                    
                    @if($unreadMessages->count() > 0)
                        <div class="list-group">
                            @foreach($unreadMessages as $message)
                                <a href="{{ route('employee.messages.show', $message->id) }}" class="list-group-item list-group-item-action list-group-item-warning {{ $message->is_announcement ? 'border-start border-info border-3' : '' }}">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">{{ $message->title }}</h6>
                                        <small>{{ $message->created_at->diffForHumans() }}</small>
                                    </div>
                                    <p class="mb-1">{{ Str::limit($message->content, 100) }}</p>
                                    <small>
                                        <span class="text-muted">De: {{ $message->sender->name }}</span>
                                        @if($message->is_announcement)
                                            <span class="badge bg-info ms-2">Annonce</span>
                                        @endif
                                        <span class="badge bg-warning ms-2">Non lu</span>
                                    </small>
                                </a>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle me-2"></i>Vous n'avez pas de messages non lus.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection