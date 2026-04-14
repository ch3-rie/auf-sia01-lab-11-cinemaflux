<?php
declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use Algolia\AlgoliaSearch\Api\SearchClient;

// MySQL config (localhost/phpMyAdmin)
$dbHost = '127.0.0.1';
$dbName = 'moviedb';
$dbUser = 'root';
$dbPass = '';

// Algolia config
$algoliaAppId = 'NAVX0XCPG4';   
$algoliaAdminApiKey = 'd5a66017843cc4cf6c33c8afd737bf59'; // Replace with your Algolia Admin API key
$algoliaIndexName = 'movies';

function firstNonEmpty(array $row, array $keys, mixed $default = null): mixed
{
    foreach ($keys as $key) {
        if (array_key_exists($key, $row) && $row[$key] !== null && $row[$key] !== '') {
            return $row[$key];
        }
    }

    return $default;
}

try {
    // Removed Admin API key check to allow real key usage

    $pdo = new PDO(
        "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4",
        $dbUser,
        $dbPass,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );


    $client = SearchClient::create($algoliaAppId, $algoliaAdminApiKey);

    $rows = $pdo->query('SELECT * FROM moviedb')->fetchAll();

    $records = [];
    foreach ($rows as $movie) {
        $id = (string) firstNonEmpty($movie, ['id', 'movie_id', 'tmdb_id'], uniqid('movie_', true));
        $title = (string) firstNonEmpty($movie, ['title', 'movie_title', 'name'], 'Untitled');
        $overview = (string) firstNonEmpty($movie, ['overview', 'description', 'summary'], '');
        $genreRaw = firstNonEmpty($movie, ['genre', 'genres', 'category'], 'Unknown');
        $ratingRaw = firstNonEmpty($movie, ['rating', 'vote_average', 'score'], null);
        $poster = (string) firstNonEmpty($movie, ['poster', 'poster_url', 'poster_path', 'image', 'image_url'], '');
        $yearRaw = firstNonEmpty($movie, ['year_released', 'release_year', 'year'], null);
        $releaseDate = isset($movie['release_date']) ? $movie['release_date'] : null;

        $genres = is_string($genreRaw)
            ? array_values(array_filter(array_map('trim', explode(',', $genreRaw))))
            : (is_array($genreRaw) ? $genreRaw : ['Unknown']);

        $rating = is_numeric($ratingRaw) ? (float) $ratingRaw : null;
        // Prefer explicit year, else extract from release_date
        if (is_numeric($yearRaw)) {
            $yearReleased = (int)$yearRaw;
        } elseif ($releaseDate && preg_match('/^(\d{4})-/', $releaseDate, $m)) {
            $yearReleased = (int)$m[1];
        } else {
            $yearReleased = null;
        }

        $records[] = [
            'objectID' => $id,
            'id' => $id,
            'title' => $title,
            'overview' => $overview,
            'genre' => implode(', ', $genres),
            'genres' => $genres,
            'rating' => $rating,
            'poster' => $poster,
            'year_released' => $yearReleased,
            'searchable_text' => trim($title . ' ' . $overview . ' ' . implode(' ', $genres)),
        ];
    }



    // Set index settings
    $client->setSettings($algoliaIndexName, [
        'searchableAttributes' => ['title', 'overview', 'genres', 'searchable_text'],
        'attributesForFaceting' => ['searchable(genres)', 'year_released'],
        'customRanking' => ['desc(rating)'],
    ]);

    // Save objects
    if (!empty($records)) {
        $client->saveObjects($algoliaIndexName, $records);
    }

    header('Content-Type: text/plain; charset=utf-8');
    echo 'Synced ' . count($records) . " movie records to Algolia index '{$algoliaIndexName}'.";
} catch (Throwable $e) {
    http_response_code(500);
    header('Content-Type: text/plain; charset=utf-8');
    echo 'Sync failed: ' . $e->getMessage();
}