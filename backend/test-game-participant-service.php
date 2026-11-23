<?php

require_once __DIR__ . '/services/GameParticipantService.php';

$participantService = new GameParticipantService();

try {
    // READ - all participants
    echo "Testing GET ALL:\n";
    print_r($participantService->getAll());

    // READ - by game
    echo "\nTesting GET BY GAME ID:\n";
    print_r($participantService->getByGameId(1));

    // READ - by user
    echo "\nTesting GET BY USER ID:\n";
    print_r($participantService->getByUserId(3));

    // CREATE
    echo "\nTesting CREATE:\n";

    $newParticipant = $participantService->create([
        'game_id' => 1,
        'user_id' => 3
    ]);

    print_r($newParticipant);

    $participantId = (int) $newParticipant['id'];

    echo "PASS: Participant created with ID $participantId\n";

    // DUPLICATE PARTICIPATION
    echo "\nTesting DUPLICATE PARTICIPATION:\n";

    try {
        $participantService->create([
            'game_id' => 1,
            'user_id' => 3
        ]);

        echo "FAIL: Duplicate participation was allowed.\n";

    } catch (InvalidArgumentException $e) {
        echo "PASS: Duplicate participation rejected.\n";
        echo "Message: " . $e->getMessage() . "\n";
    }

    // UPDATE
    echo "\nTesting UPDATE:\n";

    $updatedParticipant = $participantService->update(
        $participantId,
        [
            'game_id' => 2,
            'user_id' => 1
        ]
    );

    print_r($updatedParticipant);

    echo "PASS: Participant updated successfully.\n";

    // DELETE
    echo "\nTesting DELETE:\n";

    $deleted = $participantService->delete($participantId);

    if ($deleted) {
        echo "PASS: Participant deleted successfully.\n";
    }

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
