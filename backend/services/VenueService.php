<?php

require_once __DIR__ . '/../dao/VenueDao.php';

class VenueService
{
    private VenueDao $venueDao;

    public function __construct()
    {
        $this->venueDao = new VenueDao();
    }

    // Get all venues
    public function getAll(): array
    {
        return $this->venueDao->getAll();
    }

    // Get venue by ID
    public function getById(int $id): array
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('Invalid venue ID.');
        }

        $venue = $this->venueDao->getById($id);

        if (!$venue) {
            throw new RuntimeException('Venue not found.');
        }

        return $venue;
    }

    // Search venues by location
    public function getByLocation(string $location): array
    {
        $location = trim($location);

        if ($location === '') {
            throw new InvalidArgumentException('Location is required.');
        }

        return $this->venueDao->getByLocation($location);
    }

    // Create venue
    public function create(array $data): array
    {
        $name = trim($data['name'] ?? '');
        $location = trim($data['location'] ?? '');
        $sport = trim($data['sport'] ?? 'Football');
        $price = $data['price_per_hour'] ?? null;
        $description = $data['description'] ?? null;

        if ($name === '') {
            throw new InvalidArgumentException('Venue name is required.');
        }

        if ($location === '') {
            throw new InvalidArgumentException('Venue location is required.');
        }

        if ($sport === '') {
            throw new InvalidArgumentException('Sport is required.');
        }

        if (!is_numeric($price) || $price < 0) {
            throw new InvalidArgumentException(
                'Price per hour must be a non-negative number.'
            );
        }

        $venueId = $this->venueDao->create([
            'name' => $name,
            'location' => $location,
            'sport' => $sport,
            'price_per_hour' => $price,
            'description' => $description
        ]);

        return $this->getById((int) $venueId);
    }

    // Update venue
    public function update(int $id, array $data): array
    {
        $this->getById($id);

        $updateData = [];

        if (isset($data['name'])) {
            $name = trim($data['name']);

            if ($name === '') {
                throw new InvalidArgumentException(
                    'Venue name cannot be empty.'
                );
            }

            $updateData['name'] = $name;
        }

        if (isset($data['location'])) {
            $location = trim($data['location']);

            if ($location === '') {
                throw new InvalidArgumentException(
                    'Venue location cannot be empty.'
                );
            }

            $updateData['location'] = $location;
        }

        if (isset($data['sport'])) {
            $sport = trim($data['sport']);

            if ($sport === '') {
                throw new InvalidArgumentException(
                    'Sport cannot be empty.'
                );
            }

            $updateData['sport'] = $sport;
        }

        if (isset($data['price_per_hour'])) {
            if (
                !is_numeric($data['price_per_hour']) ||
                $data['price_per_hour'] < 0
            ) {
                throw new InvalidArgumentException(
                    'Price per hour must be a non-negative number.'
                );
            }

            $updateData['price_per_hour'] = $data['price_per_hour'];
        }

        if (array_key_exists('description', $data)) {
            $updateData['description'] = $data['description'];
        }

        if (empty($updateData)) {
            throw new InvalidArgumentException(
                'No valid fields provided for update.'
            );
        }

        $this->venueDao->update($id, $updateData);

        return $this->getById($id);
    }

    // Delete venue
    public function delete(int $id): bool
    {
        $this->getById($id);

        return $this->venueDao->delete($id);
    }
}
