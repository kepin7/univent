@extends('layouts.app')

@section('title', 'Event List')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/browseevents.css') }}">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <link rel="stylesheet" href="{{ asset('css/eventlist.css') }}">
@endpush

@section('content')
    <div class="admin-container">

        <div class="admin-header">
            <h1>Event List Management</h1>
            <p>Kelola event yang disubmit oleh pengguna dan tentukan status penayangannya.</p>
        </div>

        {{-- Pesan Notifikasi --}}
        @if (session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert-danger">{{ session('error') }}</div>
        @endif

        <div class="filter-bar">
        </div>

        <div class="admin-nav-tabs">
            <button onclick="showTab(this, 'pending')" id="tab-pending">Menunggu ({{ $pendingEvents->count() }})</button>
            <button onclick="showTab(this, 'approved')" id="tab-approved">Disetujui
                ({{ $approvedEvents->count() }})</button>
            <button onclick="showTab(this, 'rejected')" id="tab-rejected">Ditolak ({{ $rejectedEvents->count() }})</button>
            <button onclick="showTab(this, 'all')" id="tab-all">Semua ({{ $allEvents->count() }})</button>

        </div>

        <div id="pending" class="tab-content">
            <h3 class="mt-4">Event Menunggu Persetujuan</h3>
            @include('partials.event_table', ['events' => $pendingEvents, 'showActions' => true])
        </div>


        <div id="approved" class="tab-content">
            <h3 class="mt-4">Event yang Sudah Disetujui</h3>
            @include('partials.event_table', [
                'events' => $approvedEvents,
                'showActions' => false,
                'showRevert' => true,
            ])
        </div>

        <div id="rejected" class="tab-content">
            <h3 class="mt-4">Event yang Ditolak</h3>
            @include('partials.event_table', [
                'events' => $rejectedEvents,
                'showActions' => false,
                'showRevert' => true,
            ])
        </div>

        <div id="all" class="tab-content">
            <h3 class="mt-4">Semua Event</h3>
            @include('partials.event_table', [
                'events' => $allEvents,
                'showActions' => false,
                'showRevert' => true,
            ])
        </div>

    </div>
@endsection
@push('scripts')
    <script src="{{ asset('js/event-list.js') }}"></script>
@endpush
