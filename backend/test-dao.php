
<?php

require_once __DIR__ . '/dao/UserDao.php';
require_once __DIR__ . '/dao/VenueDao.php';
require_once __DIR__ . '/dao/GameDao.php';
require_once __DIR__ . '/dao/ReservationDao.php';
require_once __DIR__ . '/dao/GameParticipantDao.php';
require_once __DIR__ . '/dao/ReviewDao.php';

try {
    echo "=== Termin.ba DAO tests ===" . PHP_EOL;

    // READ: Get all users
    $userDao = new UserDao();
    $users = $userDao->getAll();

    echo "Users found: " . count($users) . PHP_EOL;

    // READ: Get user by ID
    $user = $userDao->getById(1);

    echo "User with ID 1: "
        . ($user ? $user['full_name'] : 'Not found')
        . PHP_EOL;

    // READ: Get all venues
    $venueDao = new VenueDao();
    $venues = $venueDao->getAll();

    echo "Venues found: " . count($venues) . PHP_EOL;

    // READ: Get all games
    $gameDao = new GameDao();
    $games = $gameDao->getAll();

    echo "Games found: " . count($games) . PHP_EOL;

    // READ: Get all reservations
    $reservationDao = new ReservationDao();
    $reservations = $reservationDao->getAll();

    echo "Reservations found: " . count($reservations) . PHP_EOL;

    // READ: Get game participants
    $participantDao = new GameParticipantDao();
    $participants = $participantDao->getAll();

    echo "Game participants found: " . count($participants) . PHP_EOL;

    // READ: Get reviews
    $reviewDao = new ReviewDao();
    $reviews = $reviewDao->getAll();

    echo "Reviews found: " . count($reviews) . PHP_EOL;

    echo PHP_EOL . "All DAO read tests completed." . PHP_EOL;

} catch (Throwable $exception) {
    echo "DAO test failed: " . $exception->getMessage() . PHP_EOL;
    exit(1);
}
