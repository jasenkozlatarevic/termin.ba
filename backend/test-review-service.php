<?php

require_once __DIR__ . '/services/ReviewService.php';

$service = new ReviewService();

echo "========== REVIEW SERVICE TESTS ==========\n\n";

// 1. GET ALL
echo "1. GET ALL REVIEWS:\n";
$reviews = $service->getAll();
print_r($reviews);

// 2. GET BY ID
echo "\n2. GET REVIEW BY ID:\n";
$review = $service->getById(1);
print_r($review);

// 3. GET BY VENUE ID
echo "\n3. GET REVIEWS BY VENUE ID:\n";
$venueReviews = $service->getByVenueId(1);
print_r($venueReviews);

// 4. GET BY USER ID
echo "\n4. GET REVIEWS BY USER ID:\n";
$userReviews = $service->getByUserId(1);
print_r($userReviews);

// 5. CREATE
echo "\n5. CREATE REVIEW:\n";
$newReviewId = $service->create([
    'user_id' => 1,
    'venue_id' => 1,
    'rating' => 5,
    'comment' => 'Temporary test review'
]);

$createdReviewId = (int) $newReviewId['id'];

echo "Created review ID: " . $createdReviewId . "\n";
print_r($newReviewId);

// 6. VALIDATION
echo "\n6. TEST INVALID RATING:\n";

try {
    $service->create([
        'user_id' => 1,
        'venue_id' => 1,
        'rating' => 6,
        'comment' => 'This should fail'
    ]);

    echo "ERROR: Invalid rating was accepted!\n";
} catch (InvalidArgumentException $e) {
    echo "SUCCESS: Invalid rating rejected.\n";
    echo "Message: " . $e->getMessage() . "\n";
}

// 7. UPDATE
echo "\n7. UPDATE REVIEW:\n";

$updatedReview = $service->update($createdReviewId, [
    'rating' => 4,
    'comment' => 'Updated test review'
]);

print_r($updatedReview);

// 8. DELETE
echo "\n8. DELETE REVIEW:\n";

$deleted = $service->delete($createdReviewId);

echo $deleted
    ? "SUCCESS: Review deleted.\n"
    : "ERROR: Review was not deleted.\n";

echo "\n========== TESTS FINISHED ==========\n";
