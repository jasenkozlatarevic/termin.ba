<?php

require_once __DIR__ . '/../dao/ReservationDao.php';
require_once __DIR__ . '/../dao/UserDao.php';
require_once __DIR__ . '/../dao/VenueDao.php';

class ReservationService
{
    private ReservationDao $reservationDao;
    private UserDao $userDao;
    private VenueDao $venueDao;

    public function __construct()
    {
        $this->reservationDao = new ReservationDao();
        $this->userDao = new UserDao();
        $this->venueDao = new VenueDao();
    }

    // Get all reservations
    public function getAll(): array
    {
        return $this->reservationDao->getAll();
    }

    // Get reservation by ID
    public function getById(int $id): array
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('Invalid reservation ID.');
        }

        $reservation = $this->reservationDao->getById($id);

        if (!$reservation) {
            throw new RuntimeException('Reservation not found.');
        }

        return $reservation;
    }

    // Get reservations by user
    public function getByUserId(int $userId): array
    {
        if ($userId <= 0 || !$this->userDao->getById($userId)) {
            throw new InvalidArgumentException('Invalid or nonexistent user.');
        }

        return $this->reservationDao->getByUserId($userId);
    }

    // Get reservations by venue
    public function getByVenueId(int $venueId): array
    {
        if ($venueId <= 0 || !$this->venueDao->getById($venueId)) {
            throw new InvalidArgumentException('Invalid or nonexistent venue.');
        }

        return $this->reservationDao->getByVenueId($venueId);
    }

    // Validate date format
    private function validateDate(string $date): void
    {
        $parsedDate = DateTime::createFromFormat('!Y-m-d', $date);

        if (!$parsedDate || $parsedDate->format('Y-m-d') !== $date) {
            throw new InvalidArgumentException('Invalid reservation date.');
        }

        if ($date < date('Y-m-d')) {
            throw new InvalidArgumentException(
                'Reservation date cannot be in the past.'
            );
        }
    }

    // Validate time format
    private function validateTime(string $time): void
    {
        if (!preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d(?::[0-5]\d)?$/', $time)) {
            throw new InvalidArgumentException('Invalid reservation time.');
        }
    }

    // Check overlapping reservations for the same venue
    private function checkAvailability(
        int $venueId,
        string $date,
        string $startTime,
        string $endTime,
        ?int $excludeId = null
    ): void {
        $reservations = $this->reservationDao->getByVenueId($venueId);

        foreach ($reservations as $reservation) {
            if ($reservation['reservation_date'] !== $date) {
                continue;
            }

            if ($excludeId !== null && (int) $reservation['id'] === $excludeId) {
                continue;
            }

            $existingStart = $reservation['start_time'];
            $existingEnd = $reservation['end_time'];

            if ($startTime < $existingEnd && $endTime > $existingStart) {
                throw new InvalidArgumentException(
                    'Venue is already reserved during this time.'
                );
            }
        }
    }

    // Create reservation
    public function create(array $data): array
    {
        $userId = (int) ($data['user_id'] ?? 0);
        $venueId = (int) ($data['venue_id'] ?? 0);
        $date = $data['reservation_date'] ?? '';
        $startTime = $data['start_time'] ?? '';
        $endTime = $data['end_time'] ?? '';
        $numberOfPlayers = $data['number_of_players'] ?? 0;

        if (!$this->userDao->getById($userId)) {
            throw new InvalidArgumentException('User does not exist.');
        }

        if (!$this->venueDao->getById($venueId)) {
            throw new InvalidArgumentException('Venue does not exist.');
        }

        $this->validateDate($date);
        $this->validateTime($startTime);
        $this->validateTime($endTime);

        if ($endTime <= $startTime) {
            throw new InvalidArgumentException(
                'End time must be later than start time.'
            );
        }

        if (
            !filter_var($numberOfPlayers, FILTER_VALIDATE_INT) ||
            $numberOfPlayers <= 0
        ) {
            throw new InvalidArgumentException(
                'Number of players must be a positive integer.'
            );
        }

        $this->checkAvailability($venueId, $date, $startTime, $endTime);

        $reservationId = $this->reservationDao->create([
            'user_id' => $userId,
            'venue_id' => $venueId,
            'reservation_date' => $date,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'number_of_players' => $numberOfPlayers
        ]);

        return $this->getById((int) $reservationId);
    }

    // Update reservation
    public function update(int $id, array $data): array
    {
        $existing = $this->getById($id);

        $updateData = [];

        $userId = isset($data['user_id'])
            ? (int) $data['user_id']
            : (int) $existing['user_id'];

        $venueId = isset($data['venue_id'])
            ? (int) $data['venue_id']
            : (int) $existing['venue_id'];

        $date = $data['reservation_date'] ?? $existing['reservation_date'];
        $startTime = $data['start_time'] ?? $existing['start_time'];
        $endTime = $data['end_time'] ?? $existing['end_time'];
        $numberOfPlayers = $data['number_of_players']
            ?? $existing['number_of_players'];

        if (!$this->userDao->getById($userId)) {
            throw new InvalidArgumentException('User does not exist.');
        }

        if (!$this->venueDao->getById($venueId)) {
            throw new InvalidArgumentException('Venue does not exist.');
        }

        $this->validateDate($date);
        $this->validateTime($startTime);
        $this->validateTime($endTime);

        if ($endTime <= $startTime) {
            throw new InvalidArgumentException(
                'End time must be later than start time.'
            );
        }

        if (
            !filter_var($numberOfPlayers, FILTER_VALIDATE_INT) ||
            $numberOfPlayers <= 0
        ) {
            throw new InvalidArgumentException(
                'Number of players must be a positive integer.'
            );
        }

        $this->checkAvailability(
            $venueId,
            $date,
            $startTime,
            $endTime,
            $id
        );

        foreach ([
            'user_id' => $userId,
            'venue_id' => $venueId,
            'reservation_date' => $date,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'number_of_players' => $numberOfPlayers
        ] as $field => $value) {
            if ((string) $existing[$field] !== (string) $value) {
                $updateData[$field] = $value;
            }
        }

        if (empty($updateData)) {
            throw new InvalidArgumentException(
                'No changes provided for update.'
            );
        }

        $this->reservationDao->update($id, $updateData);

        return $this->getById($id);
    }

    // Delete reservation
    public function delete(int $id): bool
    {
        $this->getById($id);

        return $this->reservationDao->delete($id);
    }
}
