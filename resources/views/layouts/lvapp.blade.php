<!-- resources/views/components/layouts/app.blade.php -->

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>{{ $title ?? 'Livewire Page Layout' }}</title>

    <link rel="stylesheet" href="{{ asset('css/livewire.css') }}">
</head>

<body>
    <nav>
        <a href="/">Skimostats</a>
        <a href="/hello">Hello</a>
        <a href="/todos">Todos</a>
        <a href="/counter">Counter</a>
        <a href="/posts">Posts</a>
        <a href="/races">Races</a>
        <a href="/create-race-event">Create Race Event</a>
        <a href="/sprint-knockouts-admin">Sprint Knockouts Admin</a>
        <a href="/knockouts-admin">Knockouts Admin</a>
    </nav>
    {{ $slot }}
</body>

</html>
