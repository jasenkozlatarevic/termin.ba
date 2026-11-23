<?php

require_once __DIR__ . '/services/GameService.php';

$gameService = new GameService();

try {
    // READ - all games
    echo "Testing GET ALL:\n";
    print_r($gameService->getAll());

    // READ - by ID
    echo "\nTesting GET BY ID:\n";
    print_r($gameService->getById(1));

    // READ - by venue
    echo "\nTesting GET BY VENUE ID:\n";
    print_r($gameService->getByVenueId(1));

    // READ - by creator
    echo "\nTesting GET BY CREATOR ID:\n";
    print_r($gameService->getByCreatorId(1));

    // CREATE
    echo "\nTesting CREATE:\n";

    $newGame = $gameService->create([
        'creator_id' => 1,
        'venue_id' => 1,
        'game_name' => 'M3 Test Game',
        'game_date' => date('Y-m-d', strtotime('+7 days')),
        'game_time' => '20:00:00',
        'max_players' => 10,
        'skill_level' => 'Intermediate',
        'description' => 'Temporary game for testing.'
    ]);

    print_r($newGame);

    $gameId = (int) $newGame['id'];
    echo "PASS: Game created with ID $gameId\n";

    // UPDATE
    echo "\nTesting UPDATE:\n";

    $updatedGame = $gameService->update($gameId, [
        'game_name' => 'Updated M3 Test Game',
        'max_players' => 12
    ]);

    print_r($updatedGame);
    echo "PASS: Game updated successfully.\n";

    // DELETE
    echo "\nTesting DELETE:\n";

    $deleted = $gameService->delete($gameId);

    if ($deleted) {
        echo "PASS: Game deleted successfully.\n";
    }

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
