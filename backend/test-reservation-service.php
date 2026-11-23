<?php

require_once __DIR__ . '/services/ReservationService.php';

$reservationService = new ReservationService();

try {
    // READ - all reservations
    echo "Testing GET ALL:\n";
    print_r($reservationService->getAll());

    // READ - by ID
    echo "\nTesting GET BY ID:\n";
    print_r($reservationService->getById(1));

    // READ - by user
    echo "\nTesting GET BY USER ID:\n";
    print_r($reservationService->getByUserId(1));

    // READ - by venue
    echo "\nTesting GET BY VENUE ID:\n";
    print_r($reservationService->getByVenueId(1));

    // CREATE
    echo "\nTesting CREATE:\n";

    $testDate = date('Y-m-d', strtotime('+10 days'));

    $newReservation = $reservationService->create([
        'user_id' => 1,
        'venue_id' => 1,
        'reservation_date' => $testDate,
        'start_time' => '20:00:00',
        'end_time' => '21:00:00',
        'number_of_players' => 10
    ]);

    print_r($newReservation);

    $reservationId = (int) $newReservation['id'];
    echo "PASS: Reservation created with ID $reservationId\n";

    // OVERLAPPING RESERVATION TEST
    echo "\nTesting OVERLAPPING RESERVATION:\n";

    try {
        $reservationService->create([
            'user_id' => 2,
            'venue_id' => 1,
            'reservation_date' => $testDate,
            'start_time' => '20:30:00',
            'end_time' => '21:30:00',
            'number_of_players' => 8
        ]);

        echo "FAIL: Overlapping reservation was allowed.\n";

    } catch (InvalidArgumentException $e) {
        echo "PASS: Overlapping reservation rejected.\n";
        echo "Message: " . $e->getMessage() . "\n";
    }

    // UPDATE
    echo "\nTesting UPDATE:\n";

    $updatedReservation = $reservationService->update($reservationId, [
        'number_of_players' => 12
    ]);

    print_r($updatedReservation);
    echo "PASS: Reservation updated successfully.\n";

    // DELETE
    echo "\nTesting DELETE:\n";

    $deleted = $reservationService->delete($reservationId);

    if ($deleted) {
        echo "PASS: Reservation deleted successfully.\n";
    }

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
