<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Players</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-900 text-gray-100 font-sans min-h-screen p-6">
    <div class="max-w-lg mx-auto">
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-xl font-bold text-blue-500">Manage Players</h1>
            <a href="/" class="text-sm text-blue-400 underline">&larr; Back to Scoring</a>
        </div>
        <nav class="flex gap-4 mb-6 text-sm">
            <a href="/teams" class="text-gray-400 hover:text-blue-400">Teams</a>
            <a href="/players" class="text-blue-400 underline">Players</a>
            <a href="/tournaments" class="text-gray-400 hover:text-blue-400">Tournaments</a>
        </nav>

        @if (session('status'))
            <div class="bg-green-800 text-green-100 p-2 rounded mb-4 text-sm">{{ session('status') }}</div>
        @endif
        @if ($errors->any())
            <div class="bg-red-800 text-red-100 p-2 rounded mb-4 text-sm">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="/players" class="bg-gray-800 p-4 rounded-xl mb-6 space-y-3 border border-gray-700">
            @csrf
            <select name="team_id" required class="w-full p-2 bg-gray-700 border border-gray-600 rounded">
                <option value="">Select Team</option>
                @foreach ($teams as $team)
                    <option value="{{ $team->id }}">{{ $team->name }}</option>
                @endforeach
            </select>
            <input name="name" placeholder="Player name" required class="w-full p-2 bg-gray-700 border border-gray-600 rounded">
            <select name="role" required class="w-full p-2 bg-gray-700 border border-gray-600 rounded">
                <option value="Batter">Batter</option>
                <option value="Bowler">Bowler</option>
                <option value="All-Rounder">All-Rounder</option>
                <option value="Wicket-Keeper">Wicket-Keeper</option>
            </select>
            <button class="w-full bg-blue-600 py-2 rounded font-semibold">Add Player</button>
        </form>

        <div class="space-y-2">
            @foreach ($players as $player)
                <div class="bg-gray-800 p-3 rounded-lg flex justify-between items-center border border-gray-700">
                    <div>
                        <div class="font-medium">{{ $player->name }}</div>
                        <div class="text-xs text-gray-500">{{ $player->team->name }} &middot; {{ $player->role }}</div>
                    </div>
                    <form method="POST" action="/players/{{ $player->id }}">
                        @csrf @method('DELETE')
                        <button class="text-red-400 text-sm" onclick="return confirm('Delete this player?')">Delete</button>
                    </form>
                </div>
            @endforeach
        </div>
    </div>
</body>
</html>
