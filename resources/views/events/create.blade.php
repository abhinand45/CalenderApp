<!DOCTYPE html>
<html>
<head>
    <title>Create Event</title>
</head>
<body>
    <h1>Create Event</h1>
    <form action="{{ route('events.store') }}" method="POST">
        @csrf
        <label>Title:</label>
        <input type="text" name="title" required><br>
        <label>Description:</label>
        <textarea name="description"></textarea><br>
        <label>Start Time:</label>
        <input type="datetime-local" name="start_time" required><br>
        <label>End Time:</label>
        <input type="datetime-local" name="end_time" required><br>
        <button type="submit">Create Event</button>
    </form>
</body>
</html>
