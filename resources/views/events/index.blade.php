<!DOCTYPE html>
<html>
<head>
    <title>Events</title>
</head>
<body>
    <h1>Events List</h1>
    <a href="{{ route('events.create') }}">Create Event</a>
    <ul>
        @foreach ($events as $event)
            <li>
                {{ $event->title }} ({{ $event->start_time }} - {{ $event->end_time }})
                <form action="{{ route('events.destroy', $event->id) }}" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit">Delete</button>
                </form>
            </li>
        @endforeach
    </ul>
</body>
</html>
