<?php

require_once __DIR__ . '/services/VenueService.php';

$venueService = new VenueService();

try {
    // READ - all venues
    echo "Testing GET ALL:\n";
    print_r($venueService->getAll());

    // READ - by ID
    echo "\nTesting GET BY ID:\n";
    print_r($venueService->getById(1));

    // SEARCH - by location
    echo "\nTesting GET BY LOCATION:\n";
    print_r($venueService->getByLocation('Sarajevo'));

    // CREATE
    echo "\nTesting CREATE:\n";

    $newVenue = $venueService->create([
        'name' => 'M3 Test Venue',
        'location' => 'Sarajevo, Test',
        'sport' => 'Football',
        'price_per_hour' => 50,
        'description' => 'Temporary venue for testing.'
    ]);

    print_r($newVenue);

    $venueId = (int) $newVenue['id'];
    echo "PASS: Venue created with ID $venueId\n";

    // UPDATE
    echo "\nTesting UPDATE:\n";

    $updatedVenue = $venueService->update($venueId, [
        'name' => 'Updated M3 Test Venue',
        'price_per_hour' => 60
    ]);

    print_r($updatedVenue);

    echo "PASS: Venue updated successfully.\n";

    // DELETE
    echo "\nTesting DELETE:\n";

    $deleted = $venueService->delete($venueId);

    if ($deleted) {
        echo "PASS: Venue deleted successfully.\n";
    }

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
