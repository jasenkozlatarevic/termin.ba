<?php

require_once __DIR__ . '/../dao/GameDao.php';
require_once __DIR__ . '/../dao/UserDao.php';
require_once __DIR__ . '/../dao/VenueDao.php';

class GameService
{
    private GameDao $gameDao;
    private UserDao $userDao;
    private VenueDao $venueDao;

    public function __construct()
    {
        $this->gameDao = new GameDao();
        $this->userDao = new UserDao();
        $this->venueDao = new VenueDao();
    }

    // Get all games
    public function getAll(): array
    {
        return $this->gameDao->getAll();
    }

    // Get game by ID
    public function getById(int $id): array
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('Invalid game ID.');
        }

        $game = $this->gameDao->getById($id);

        if (!$game) {
            throw new RuntimeException('Game not found.');
        }

        return $game;
    }

    // Get games by venue
    public function getByVenueId(int $venueId): array
    {
        if ($venueId <= 0) {
            throw new InvalidArgumentException('Invalid venue ID.');
        }

        if (!$this->venueDao->getById($venueId)) {
            throw new RuntimeException('Venue not found.');
        }

        return $this->gameDao->getByVenueId($venueId);
    }

    // Get games by creator
    public function getByCreatorId(int $creatorId): array
    {
        if ($creatorId <= 0) {
            throw new InvalidArgumentException('Invalid creator ID.');
        }

        if (!$this->userDao->getById($creatorId)) {
            throw new RuntimeException('User not found.');
        }

        return $this->gameDao->getByCreatorId($creatorId);
    }

    // Create game
    public function create(array $data): array
    {
        $creatorId = (int) ($data['creator_id'] ?? 0);
        $venueId = (int) ($data['venue_id'] ?? 0);
        $gameName = trim($data['game_name'] ?? '');
        $gameDate = $data['game_date'] ?? '';
        $gameTime = $data['game_time'] ?? '';
        $maxPlayers = $data['max_players'] ?? 0;
        $skillLevel = trim($data['skill_level'] ?? 'All levels');

        if (!$this->userDao->getById($creatorId)) {
            throw new InvalidArgumentException('Creator does not exist.');
        }

        if (!$this->venueDao->getById($venueId)) {
            throw new InvalidArgumentException('Venue does not exist.');
        }

        if ($gameName === '') {
            throw new InvalidArgumentException('Game name is required.');
        }

        $date = DateTime::createFromFormat('!Y-m-d', $gameDate);

        if (!$date || $date->format('Y-m-d') !== $gameDate) {
            throw new InvalidArgumentException('Invalid game date.');
        }

        if ($gameDate < date('Y-m-d')) {
            throw new InvalidArgumentException('Game date cannot be in the past.');
        }

        if (
            !preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d(?::[0-5]\d)?$/', $gameTime)
        ) {
            throw new InvalidArgumentException('Invalid game time.');
        }

        if (!filter_var($maxPlayers, FILTER_VALIDATE_INT) || $maxPlayers <= 0) {
            throw new InvalidArgumentException(
                'Maximum players must be a positive integer.'
            );
        }

        if ($skillLevel === '') {
            throw new InvalidArgumentException('Skill level is required.');
        }

        $gameId = $this->gameDao->create([
            'creator_id' => $creatorId,
            'venue_id' => $venueId,
            'game_name' => $gameName,
            'game_date' => $gameDate,
            'game_time' => $gameTime,
            'max_players' => $maxPlayers,
            'skill_level' => $skillLevel,
            'description' => $data['description'] ?? null
        ]);

        return $this->getById((int) $gameId);
    }

    // Update game
    public function update(int $id, array $data): array
    {
        $this->getById($id);

        $updateData = [];

        if (isset($data['creator_id'])) {
            $creatorId = (int) $data['creator_id'];

            if (!$this->userDao->getById($creatorId)) {
                throw new InvalidArgumentException('Creator does not exist.');
            }

            $updateData['creator_id'] = $creatorId;
        }

        if (isset($data['venue_id'])) {
            $venueId = (int) $data['venue_id'];

            if (!$this->venueDao->getById($venueId)) {
                throw new InvalidArgumentException('Venue does not exist.');
            }

            $updateData['venue_id'] = $venueId;
        }

        if (isset($data['game_name'])) {
            $gameName = trim($data['game_name']);

            if ($gameName === '') {
                throw new InvalidArgumentException('Game name cannot be empty.');
            }

            $updateData['game_name'] = $gameName;
        }

        if (isset($data['game_date'])) {
            $gameDate = $data['game_date'];
            $date = DateTime::createFromFormat('!Y-m-d', $gameDate);

            if (!$date || $date->format('Y-m-d') !== $gameDate) {
                throw new InvalidArgumentException('Invalid game date.');
            }

            if ($gameDate < date('Y-m-d')) {
                throw new InvalidArgumentException('Game date cannot be in the past.');
            }

            $updateData['game_date'] = $gameDate;
        }

        if (isset($data['game_time'])) {
            if (
                !preg_match(
                    '/^(?:[01]\d|2[0-3]):[0-5]\d(?::[0-5]\d)?$/',
                    $data['game_time']
                )
            ) {
                throw new InvalidArgumentException('Invalid game time.');
            }

            $updateData['game_time'] = $data['game_time'];
        }

        if (isset($data['max_players'])) {
            if (
                !filter_var($data['max_players'], FILTER_VALIDATE_INT) ||
                $data['max_players'] <= 0
            ) {
                throw new InvalidArgumentException(
                    'Maximum players must be a positive integer.'
                );
            }

            $updateData['max_players'] = $data['max_players'];
        }

        if (isset($data['skill_level'])) {
            $skillLevel = trim($data['skill_level']);

            if ($skillLevel === '') {
                throw new InvalidArgumentException('Skill level cannot be empty.');
            }

            $updateData['skill_level'] = $skillLevel;
        }

        if (array_key_exists('description', $data)) {
            $updateData['description'] = $data['description'];
        }

        if (empty($updateData)) {
            throw new InvalidArgumentException(
                'No valid fields provided for update.'
            );
        }

        $this->gameDao->update($id, $updateData);

        return $this->getById($id);
    }

    // Delete game
    public function delete(int $id): bool
    {
        $this->getById($id);

        return $this->gameDao->delete($id);
    }
}
