<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DeezerController extends Controller
{
    public function getSong(Request $request)
    {
        $genreId = $request->input('genre_id', '152'); // Default: Rock

        // Get artists for the genre
        $artistsResponse = Http::get("https://api.deezer.com/genre/{$genreId}/artists");

        if (!$artistsResponse->ok() || empty($artistsResponse['data'])) {
            return response()->json(['error' => 'No artists found'], 404);
        }

        $artists = $artistsResponse['data'];
        $randomArtist = $artists[array_rand($artists)];

        // Get top tracks from the artist
        $tracksResponse = Http::get("https://api.deezer.com/artist/{$randomArtist['id']}/top?limit=10");

        if (!$tracksResponse->ok() || empty($tracksResponse['data'])) {
            return response()->json(['error' => 'No tracks found'], 404);
        }

        $tracks = $tracksResponse['data'];
        $randomTrack = $tracks[array_rand($tracks)];

        return response()->json([
            'id' => $randomTrack['id'],
            'title' => $randomTrack['title'],
            'artist' => $randomTrack['artist']['name'],
            'album' => $randomTrack['album']['title'],
            'preview' => $randomTrack['preview'],
            'release_date' => $randomTrack['release_date'] ?? null,
        ]);
    }
}

