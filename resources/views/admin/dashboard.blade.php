@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $totalUsers }}</h3>
                    <p>Total Users</p>
                </div>
                <div class="icon"><i class="fas fa-users"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $totalNotes }}</h3>
                    <p>Total Catatan</p>
                </div>
                <div class="icon"><i class="fas fa-sticky-note"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $newUsers }}</h3>
                    <p>User Baru (Bulan Ini)</p>
                </div>
                <div class="icon"><i class="fas fa-user-plus"></i></div>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>{{ $newNotes }}</h3>
                    <p>Catatan Baru (Bulan Ini)</p>
                </div>
                <div class="icon"><i class="fas fa-plus-square"></i></div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h3 class="card-title">User Terbaru</h3></div>
                <div class="card-body p-0">
                    <table class="table table-sm">
                        @foreach($recentUsers as $u)
                        <tr>
                            <td>{{ $u->name }}</td>
                            <td>{{ $u->email }}</td>
                            <td><span class="badge {{ $u->is_admin ? 'badge-warning' : 'badge-info' }}">{{ $u->is_admin ? 'Admin' : 'User' }}</span></td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Catatan Terbaru</h3></div>
                <div class="card-body p-0">
                    <table class="table table-sm">
                        @foreach($recentNotes as $n)
                        <tr>
                            <td>{{ Str::limit($n->content, 50) }}</td>
                            <td>{{ $n->user?->name ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop
