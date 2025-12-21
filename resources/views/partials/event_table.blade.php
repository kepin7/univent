@props(['events', 'showActions' => false, 'showRevert' => false])

<table class="event-table">
    <thead>
        <tr>
            <th>No</th>
            <th>Event</th>

            {{-- DIUBAH MENJADI .text-center --}}
            <th class="text-center">Status</th>

            {{-- DIUBAH MENJADI .text-center --}}
            <th class="text-center">Aksi</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($events as $index => $event)
            <tr>
                <td>{{ $index + 1 }}</td>

                <td style="display:flex; align-items:center; gap:12px;">
                    <img src="data:image/jpeg;base64,{{ $event->event_poster }}" alt="Event Poster"
                        style="width:55px; height:55px; object-fit:cover; border-radius:8px;">
                    <div class="event-title">{{ $event->event_title }}</div>
                </td>

                {{-- DIUBAH MENJADI .text-center --}}
                <td class="text-center">
                    <span class="status-tag status-{{ $event->status }}">{{ ucfirst($event->status) }}</span>
                </td>

                {{-- DIUBAH MENJADI .text-center --}}
                <td class="action-links text-center">
                    <div class="action-container">

                        @if ($showActions)
                            {{-- Aksi PENDING --}}
                            <form action="{{ route('admin.events.approve', $event->id) }}" method="POST"
                                style="display: inline;">
                                @csrf
                                <button type="submit" class="action-button action-accept">
                                    Accept
                                </button>
                            </form>
                            <form action="{{ route('admin.events.reject', $event->id) }}" method="POST"
                                style="display: inline;">
                                @csrf
                                <button type="submit" class="action-button action-reject">
                                    Reject
                                </button>
                            </form>
                        @endif

                        @if ($showRevert)
                            {{-- Aksi REVERT --}}
                            @if ($event->status === 'approved')
                                <form action="{{ route('admin.events.reject', $event->id) }}" method="POST"
                                    style="display: inline;">
                                    @csrf
                                    <button type="submit" class="action-button action-reject">
                                        Reject
                                    </button>
                                </form>
                            @elseif ($event->status === 'rejected')
                                <form action="{{ route('admin.events.approve', $event->id) }}" method="POST"
                                    style: "display: inline;">
                                    @csrf
                                    <button type="submit" class="action-button action-accept">
                                        Accept
                                    </button>
                                </form>
                            @endif
                        @endif

                        {{-- Link Detail (Selalu tampil) --}}
                        <a href="{{ route('admin.events.detail', $event->id) }}" class="action-view">View</a>
                        <form action="{{ route('admin.events.delete', $event->id) }}" method="POST"
                            class="delete-form" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="action-button action-delete btn-delete-sw">
                                Delete
                            </button>

                        </form>


                    </div> {{-- Penutup .action-container --}}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" style="text-align:center; padding:20px;">Tidak ada event dengan status
                    {{ $events->isEmpty() ? 'ini' : ucfirst($events[0]->status) }}.</td>
            </tr>
        @endforelse
    </tbody>
</table>
