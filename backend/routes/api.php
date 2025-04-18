<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\DeezerController;

Route::get('/deezer/song', [DeezerController::class, 'getSong']);


Route::get('/ping', function () {
    return response()->json(['message' => 'pong']);
});

Route::get('/songs/random', function (\Illuminate\Http\Request $request) {
    $genre = $request->query('genre');

    $query = DB::table('songs');

    if ($genre) {
        $query->where('genre', $genre);
    }

    $song = $query->inRandomOrder()->first();

    return response()->json($song);
});

Route::post('/game/start', function (\Illuminate\Http\Request $request) {
    $genre = $request->input('genre');

    $id = DB::table('game_sessions')->insertGetId([
        'genre' => $genre,
        'score' => 0,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    return response()->json(['game_session_id' => $id]);
});

Route::post('/guess', function (\Illuminate\Http\Request $request) {
    $data = $request->all();

    if (!isset($data['song_id'])) {
        return response()->json(['error' => 'Missing song_id'], 400);
    }

    $song = DB::table('songs')->find($data['song_id']);

    if (!$song) {
        return response()->json(['error' => 'Song not found'], 404);
    }

    $duration = isset($data['snippet_duration']) ? (int) $data['snippet_duration'] : 60;

    $multiplier = match ($duration) {
        10 => 1.5,
        30 => 1.25,
        60 => 1,
        default => 1
    };

    $points = 0;

    if (strtolower(trim($data['guessed_title'])) === strtolower($song->title)) {
        $points += 1 * $multiplier;
    }
    if (strtolower(trim($data['guessed_artist'])) === strtolower($song->artist)) {
        $points += 1 * $multiplier;
    }
    if (strtolower(trim($data['guessed_album'])) === strtolower($song->album)) {
        $points += 1 * $multiplier;
    }
    if ((int) $data['guessed_year'] === (int) $song->release_year) {
        $points += 1 * $multiplier;
    }

    $points = round($points);

    DB::table('guesses')->insert([
        'game_session_id' => $data['game_session_id'],
        'song_id' => $data['song_id'],
        'guessed_title' => $data['guessed_title'],
        'guessed_artist' => $data['guessed_artist'],
        'guessed_album' => $data['guessed_album'],
        'guessed_year' => $data['guessed_year'],
        'points_awarded' => $points,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('game_sessions')
        ->where('id', $data['game_session_id'])
        ->increment('score', $points);

    return response()->json([
        'points_awarded' => $points,
        'correct' => [
            'title' => $song->title,
            'artist' => $song->artist,
            'album' => $song->album,
            'release_year' => $song->release_year,
        ],
    ]);
});

Route::get('/game/score/{game_session_id}', function ($game_session_id) {
    $score = DB::table('game_sessions')
        ->where('id', $game_session_id)
        ->value('score');

    return response()->json(['score' => $score]);
});


