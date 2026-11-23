<?php

// ==========================================
// CORS CONFIGURATION
// ==========================================

$allowedOrigins = [
    'http://localhost:8001',
    'http://127.0.0.1:5500',
    'http://localhost:5500'
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if (in_array($origin, $allowedOrigins, true)) {
    header("Access-Control-Allow-Origin: $origin");
    header("Vary: Origin");
}

header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// ==========================================
// LOAD DEPENDENCIES
// ==========================================

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/Database.php';

// ==========================================
// LOAD CONFIGURATION
// ==========================================

$appConfig = require __DIR__ . '/config/config.php';

// ==========================================
// LOAD SERVICES
// ==========================================

require_once __DIR__ . '/services/UserService.php';
require_once __DIR__ . '/services/VenueService.php';
require_once __DIR__ . '/services/GameService.php';
require_once __DIR__ . '/services/ReservationService.php';
require_once __DIR__ . '/services/GameParticipantService.php';
require_once __DIR__ . '/services/ReviewService.php';
require_once __DIR__ . '/services/WeatherService.php';

// ==========================================
// REGISTER SERVICES
// ==========================================

Flight::register('user_service', 'UserService');
Flight::register('venue_service', 'VenueService');
Flight::register('game_service', 'GameService');
Flight::register('reservation_service', 'ReservationService');
Flight::register('game_participant_service', 'GameParticipantService');
Flight::register('review_service', 'ReviewService');

Flight::register(
    'weather_service',
    'WeatherService',
    [$appConfig['weather']['api_key']]
);

// ==========================================
// HELPER FUNCTIONS
// ==========================================

// Read JSON request body
function getRequestData(): array
{
    $body = Flight::request()->getBody();
    $data = json_decode($body, true);

    if (!is_array($data)) {
        throw new InvalidArgumentException('Invalid JSON request body.');
    }

    return $data;
}

// Handle API errors
function handleApiError(Throwable $e): void
{
    if ($e instanceof InvalidArgumentException) {
        $statusCode = 400;
    } elseif ($e instanceof RuntimeException) {
        $statusCode = 404;
    } else {
        $statusCode = 500;
    }

    Flight::json([
        'status' => 'error',
        'message' => $statusCode === 500
            ? 'Internal server error.'
            : $e->getMessage()
    ], $statusCode);
}

// ==========================================
// BASIC API ROUTES
// ==========================================

// GET /
Flight::route('GET /', function () {
    Flight::json([
        'status' => 'success',
        'message' => 'Termin.ba API is running!'
    ], 200);
});

// GET /test-db
Flight::route('GET /test-db', function () {
    try {
        Database::connect();

        Flight::json([
            'status' => 'success',
            'message' => 'Database connection successful!'
        ], 200);

    } catch (Throwable $e) {
        Flight::json([
            'status' => 'error',
            'message' => 'Database connection failed!'
        ], 500);
    }
});

// ==========================================
// WEATHER API ROUTE
// ==========================================

// GET /weather?city=Sarajevo
Flight::route('GET /weather', function () {
    $city = Flight::request()->query->city;

    if (empty($city)) {
        Flight::json([
            'status' => 'error',
            'message' => 'City parameter is required.'
        ], 400);
        return;
    }

    try {
        $weather = Flight::weather_service()->getCurrentWeather($city);

        Flight::json([
            'status' => 'success',
            'data' => $weather
        ], 200);

    } catch (InvalidArgumentException $e) {
        Flight::json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 400);

    } catch (Throwable $e) {
        error_log('Weather API error: ' . $e->getMessage());

        Flight::json([
            'status' => 'error',
            'message' => 'Weather service is currently unavailable.'
        ], 502);
    }
});

// ==========================================
// USERS CRUD ROUTES
// ==========================================

// GET /users
Flight::route('GET /users', function () {
    try {
        $users = Flight::user_service()->getAll();
        Flight::json($users, 200);
    } catch (Throwable $e) {
        handleApiError($e);
    }
});

// GET /users/@id
Flight::route('GET /users/@id', function ($id) {
    try {
        $user = Flight::user_service()->getById((int) $id);
        Flight::json($user, 200);
    } catch (Throwable $e) {
        handleApiError($e);
    }
});

// POST /users
Flight::route('POST /users', function () {
    try {
        $data = getRequestData();
        $user = Flight::user_service()->create($data);

        Flight::json([
            'status' => 'success',
            'message' => 'User created successfully.',
            'data' => $user
        ], 201);
    } catch (Throwable $e) {
        handleApiError($e);
    }
});

// PUT /users/@id
Flight::route('PUT /users/@id', function ($id) {
    try {
        $data = getRequestData();
        $user = Flight::user_service()->update((int) $id, $data);

        Flight::json([
            'status' => 'success',
            'message' => 'User updated successfully.',
            'data' => $user
        ], 200);
    } catch (Throwable $e) {
        handleApiError($e);
    }
});

// DELETE /users/@id
Flight::route('DELETE /users/@id', function ($id) {
    try {
        Flight::user_service()->delete((int) $id);

        Flight::json([
            'status' => 'success',
            'message' => 'User deleted successfully.'
        ], 200);
    } catch (Throwable $e) {
        handleApiError($e);
    }
});

// ==========================================
// VENUES CRUD ROUTES
// ==========================================

// GET /venues
Flight::route('GET /venues', function () {
    try {
        $venues = Flight::venue_service()->getAll();
        Flight::json($venues, 200);
    } catch (Throwable $e) {
        handleApiError($e);
    }
});

// GET /venues/location/@location
Flight::route('GET /venues/location/@location', function ($location) {
    try {
        $venues = Flight::venue_service()->getByLocation($location);
        Flight::json($venues, 200);
    } catch (Throwable $e) {
        handleApiError($e);
    }
});

// GET /venues/@id
Flight::route('GET /venues/@id', function ($id) {
    try {
        $venue = Flight::venue_service()->getById((int) $id);
        Flight::json($venue, 200);
    } catch (Throwable $e) {
        handleApiError($e);
    }
});

// POST /venues
Flight::route('POST /venues', function () {
    try {
        $data = getRequestData();
        $venue = Flight::venue_service()->create($data);

        Flight::json([
            'status' => 'success',
            'message' => 'Venue created successfully.',
            'data' => $venue
        ], 201);
    } catch (Throwable $e) {
        handleApiError($e);
    }
});

// PUT /venues/@id
Flight::route('PUT /venues/@id', function ($id) {
    try {
        $data = getRequestData();
        $venue = Flight::venue_service()->update((int) $id, $data);

        Flight::json([
            'status' => 'success',
            'message' => 'Venue updated successfully.',
            'data' => $venue
        ], 200);
    } catch (Throwable $e) {
        handleApiError($e);
    }
});

// DELETE /venues/@id
Flight::route('DELETE /venues/@id', function ($id) {
    try {
        Flight::venue_service()->delete((int) $id);

        Flight::json([
            'status' => 'success',
            'message' => 'Venue deleted successfully.'
        ], 200);
    } catch (Throwable $e) {
        handleApiError($e);
    }
});

// ==========================================
// GAMES CRUD ROUTES
// ==========================================

// GET /games
Flight::route('GET /games', function () {
    try {
        $games = Flight::game_service()->getAll();
        Flight::json($games, 200);
    } catch (Throwable $e) {
        handleApiError($e);
    }
});

// GET /games/venue/@venue_id
Flight::route('GET /games/venue/@venue_id', function ($venue_id) {
    try {
        $games = Flight::game_service()->getByVenueId((int) $venue_id);
        Flight::json($games, 200);
    } catch (Throwable $e) {
        handleApiError($e);
    }
});

// GET /games/creator/@creator_id
Flight::route('GET /games/creator/@creator_id', function ($creator_id) {
    try {
        $games = Flight::game_service()->getByCreatorId((int) $creator_id);
        Flight::json($games, 200);
    } catch (Throwable $e) {
        handleApiError($e);
    }
});

// GET /games/@id
Flight::route('GET /games/@id', function ($id) {
    try {
        $game = Flight::game_service()->getById((int) $id);
        Flight::json($game, 200);
    } catch (Throwable $e) {
        handleApiError($e);
    }
});

// POST /games
Flight::route('POST /games', function () {
    try {
        $data = getRequestData();
        $game = Flight::game_service()->create($data);

        Flight::json([
            'status' => 'success',
            'message' => 'Game created successfully.',
            'data' => $game
        ], 201);
    } catch (Throwable $e) {
        handleApiError($e);
    }
});

// PUT /games/@id
Flight::route('PUT /games/@id', function ($id) {
    try {
        $data = getRequestData();
        $game = Flight::game_service()->update((int) $id, $data);

        Flight::json([
            'status' => 'success',
            'message' => 'Game updated successfully.',
            'data' => $game
        ], 200);
    } catch (Throwable $e) {
        handleApiError($e);
    }
});

// DELETE /games/@id
Flight::route('DELETE /games/@id', function ($id) {
    try {
        Flight::game_service()->delete((int) $id);

        Flight::json([
            'status' => 'success',
            'message' => 'Game deleted successfully.'
        ], 200);
    } catch (Throwable $e) {
        handleApiError($e);
    }
});

// ==========================================
// RESERVATIONS CRUD ROUTES
// ==========================================

// GET /reservations
Flight::route('GET /reservations', function () {
    try {
        $reservations = Flight::reservation_service()->getAll();
        Flight::json($reservations, 200);
    } catch (Throwable $e) {
        handleApiError($e);
    }
});

// GET /reservations/user/@user_id
Flight::route('GET /reservations/user/@user_id', function ($user_id) {
    try {
        $reservations = Flight::reservation_service()->getByUserId((int) $user_id);
        Flight::json($reservations, 200);
    } catch (Throwable $e) {
        handleApiError($e);
    }
});

// GET /reservations/venue/@venue_id
Flight::route('GET /reservations/venue/@venue_id', function ($venue_id) {
    try {
        $reservations = Flight::reservation_service()->getByVenueId((int) $venue_id);
        Flight::json($reservations, 200);
    } catch (Throwable $e) {
        handleApiError($e);
    }
});

// GET /reservations/@id
Flight::route('GET /reservations/@id', function ($id) {
    try {
        $reservation = Flight::reservation_service()->getById((int) $id);
        Flight::json($reservation, 200);
    } catch (Throwable $e) {
        handleApiError($e);
    }
});

// POST /reservations
Flight::route('POST /reservations', function () {
    try {
        $data = getRequestData();
        $reservation = Flight::reservation_service()->create($data);

        Flight::json([
            'status' => 'success',
            'message' => 'Reservation created successfully.',
            'data' => $reservation
        ], 201);
    } catch (Throwable $e) {
        handleApiError($e);
    }
});

// PUT /reservations/@id
Flight::route('PUT /reservations/@id', function ($id) {
    try {
        $data = getRequestData();
        $reservation = Flight::reservation_service()->update((int) $id, $data);

        Flight::json([
            'status' => 'success',
            'message' => 'Reservation updated successfully.',
            'data' => $reservation
        ], 200);
    } catch (Throwable $e) {
        handleApiError($e);
    }
});

// DELETE /reservations/@id
Flight::route('DELETE /reservations/@id', function ($id) {
    try {
        Flight::reservation_service()->delete((int) $id);

        Flight::json([
            'status' => 'success',
            'message' => 'Reservation deleted successfully.'
        ], 200);
    } catch (Throwable $e) {
        handleApiError($e);
    }
});

// ==========================================
// REVIEWS CRUD ROUTES
// ==========================================

// GET /reviews
Flight::route('GET /reviews', function () {
    try {
        $reviews = Flight::review_service()->getAll();
        Flight::json($reviews, 200);
    } catch (Throwable $e) {
        handleApiError($e);
    }
});

// GET /reviews/venue/@venue_id
Flight::route('GET /reviews/venue/@venue_id', function ($venue_id) {
    try {
        $reviews = Flight::review_service()->getByVenueId((int) $venue_id);
        Flight::json($reviews, 200);
    } catch (Throwable $e) {
        handleApiError($e);
    }
});

// GET /reviews/user/@user_id
Flight::route('GET /reviews/user/@user_id', function ($user_id) {
    try {
        $reviews = Flight::review_service()->getByUserId((int) $user_id);
        Flight::json($reviews, 200);
    } catch (Throwable $e) {
        handleApiError($e);
    }
});

// GET /reviews/@id
Flight::route('GET /reviews/@id', function ($id) {
    try {
        $review = Flight::review_service()->getById((int) $id);
        Flight::json($review, 200);
    } catch (Throwable $e) {
        handleApiError($e);
    }
});

// POST /reviews
Flight::route('POST /reviews', function () {
    try {
        $data = getRequestData();
        $review = Flight::review_service()->create($data);

        Flight::json([
            'status' => 'success',
            'message' => 'Review created successfully.',
            'data' => $review
        ], 201);
    } catch (Throwable $e) {
        handleApiError($e);
    }
});

// PUT /reviews/@id
Flight::route('PUT /reviews/@id', function ($id) {
    try {
        $data = getRequestData();
        $review = Flight::review_service()->update((int) $id, $data);

        Flight::json([
            'status' => 'success',
            'message' => 'Review updated successfully.',
            'data' => $review
        ], 200);
    } catch (Throwable $e) {
        handleApiError($e);
    }
});

// DELETE /reviews/@id
Flight::route('DELETE /reviews/@id', function ($id) {
    try {
        Flight::review_service()->delete((int) $id);

        Flight::json([
            'status' => 'success',
            'message' => 'Review deleted successfully.'
        ], 200);
    } catch (Throwable $e) {
        handleApiError($e);
    }
});

// ==========================================
// START FLIGHTPHP
// ==========================================

Flight::start();
