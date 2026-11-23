<?php

require_once __DIR__ . '/../dao/ReviewDao.php';
require_once __DIR__ . '/../dao/UserDao.php';
require_once __DIR__ . '/../dao/VenueDao.php';

class ReviewService
{
    private ReviewDao $reviewDao;
    private UserDao $userDao;
    private VenueDao $venueDao;

    public function __construct()
    {
        $this->reviewDao = new ReviewDao();
        $this->userDao = new UserDao();
        $this->venueDao = new VenueDao();
    }

    // Get all reviews
    public function getAll(): array
    {
        return $this->reviewDao->getAll();
    }

    // Get review by ID
    public function getById(int $id): array
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('Invalid review ID.');
        }

        $review = $this->reviewDao->getById($id);

        if (!$review) {
            throw new RuntimeException('Review not found.');
        }

        return $review;
    }

    // Get reviews by venue
    public function getByVenueId(int $venueId): array
    {
        if ($venueId <= 0 || !$this->venueDao->getById($venueId)) {
            throw new InvalidArgumentException('Invalid or nonexistent venue.');
        }

        return $this->reviewDao->getByVenueId($venueId);
    }

    // Get reviews by user
    public function getByUserId(int $userId): array
    {
        if ($userId <= 0 || !$this->userDao->getById($userId)) {
            throw new InvalidArgumentException('Invalid or nonexistent user.');
        }

        return $this->reviewDao->getByUserId($userId);
    }

    // Create review
    public function create(array $data): array
    {
        $userId = (int) ($data['user_id'] ?? 0);
        $venueId = (int) ($data['venue_id'] ?? 0);
        $rating = $data['rating'] ?? null;
        $comment = $data['comment'] ?? null;

        if (!$this->userDao->getById($userId)) {
            throw new InvalidArgumentException('User does not exist.');
        }

        if (!$this->venueDao->getById($venueId)) {
            throw new InvalidArgumentException('Venue does not exist.');
        }

        if (
            !filter_var($rating, FILTER_VALIDATE_INT) ||
            $rating < 1 ||
            $rating > 5
        ) {
            throw new InvalidArgumentException(
                'Rating must be an integer between 1 and 5.'
            );
        }

        $reviewId = $this->reviewDao->create([
            'user_id' => $userId,
            'venue_id' => $venueId,
            'rating' => $rating,
            'comment' => $comment
        ]);

        return $this->getById((int) $reviewId);
    }

    // Update review
    public function update(int $id, array $data): array
    {
        $existing = $this->getById($id);

        $updateData = [];

        if (isset($data['user_id'])) {
            $userId = (int) $data['user_id'];

            if (!$this->userDao->getById($userId)) {
                throw new InvalidArgumentException('User does not exist.');
            }

            $updateData['user_id'] = $userId;
        }

        if (isset($data['venue_id'])) {
            $venueId = (int) $data['venue_id'];

            if (!$this->venueDao->getById($venueId)) {
                throw new InvalidArgumentException('Venue does not exist.');
            }

            $updateData['venue_id'] = $venueId;
        }

        if (isset($data['rating'])) {
            if (
                !filter_var($data['rating'], FILTER_VALIDATE_INT) ||
                $data['rating'] < 1 ||
                $data['rating'] > 5
            ) {
                throw new InvalidArgumentException(
                    'Rating must be an integer between 1 and 5.'
                );
            }

            $updateData['rating'] = $data['rating'];
        }

        if (array_key_exists('comment', $data)) {
            $updateData['comment'] = $data['comment'];
        }

        if (empty($updateData)) {
            throw new InvalidArgumentException(
                'No valid fields provided for update.'
            );
        }

        $this->reviewDao->update($id, $updateData);

        return $this->getById($id);
    }

    // Delete review
    public function delete(int $id): bool
    {
        $this->getById($id);

        return $this->reviewDao->delete($id);
    }
}
