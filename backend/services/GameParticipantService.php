<?php

require_once __DIR__ . '/../dao/GameParticipantDao.php';
require_once __DIR__ . '/../dao/GameDao.php';
require_once __DIR__ . '/../dao/UserDao.php';

class GameParticipantService
{
    private GameParticipantDao $participantDao;
    private GameDao $gameDao;
    private UserDao $userDao;

    public function __construct()
    {
        $this->participantDao = new GameParticipantDao();
        $this->gameDao = new GameDao();
        $this->userDao = new UserDao();
    }

    // Get all participants
    public function getAll(): array
    {
        return $this->participantDao->getAll();
    }

    // Get participant by ID
    public function getById(int $id): array
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('Invalid participant ID.');
        }

        $participant = $this->participantDao->getById($id);

        if (!$participant) {
            throw new RuntimeException('Participant not found.');
        }

        return $participant;
    }

    // Get participants by game
    public function getByGameId(int $gameId): array
    {
        if ($gameId <= 0 || !$this->gameDao->getById($gameId)) {
            throw new InvalidArgumentException('Invalid or nonexistent game.');
        }

        return $this->participantDao->getByGameId($gameId);
    }

    // Get participations by user
    public function getByUserId(int $userId): array
    {
        if ($userId <= 0 || !$this->userDao->getById($userId)) {
            throw new InvalidArgumentException('Invalid or nonexistent user.');
        }

        return $this->participantDao->getByUserId($userId);
    }

    // Create participation
    public function create(array $data): array
    {
        $gameId = (int) ($data['game_id'] ?? 0);
        $userId = (int) ($data['user_id'] ?? 0);

        $game = $this->gameDao->getById($gameId);

        if (!$game) {
            throw new InvalidArgumentException('Game does not exist.');
        }

        if (!$this->userDao->getById($userId)) {
            throw new InvalidArgumentException('User does not exist.');
        }

        $participants = $this->participantDao->getByGameId($gameId);

        foreach ($participants as $participant) {
            if ((int) $participant['user_id'] === $userId) {
                throw new InvalidArgumentException(
                    'User has already joined this game.'
                );
            }
        }

        if (count($participants) >= (int) $game['max_players']) {
            throw new InvalidArgumentException(
                'Game has reached its maximum number of players.'
            );
        }

        $participantId = $this->participantDao->create([
            'game_id' => $gameId,
            'user_id' => $userId
        ]);

        return $this->getById((int) $participantId);
    }

    // Update participation
    public function update(int $id, array $data): array
    {
        $existing = $this->getById($id);

        $gameId = isset($data['game_id'])
            ? (int) $data['game_id']
            : (int) $existing['game_id'];

        $userId = isset($data['user_id'])
            ? (int) $data['user_id']
            : (int) $existing['user_id'];

        $game = $this->gameDao->getById($gameId);

        if (!$game) {
            throw new InvalidArgumentException('Game does not exist.');
        }

        if (!$this->userDao->getById($userId)) {
            throw new InvalidArgumentException('User does not exist.');
        }

        $participants = $this->participantDao->getByGameId($gameId);

        $count = 0;

        foreach ($participants as $participant) {
            if ((int) $participant['id'] === $id) {
                continue;
            }

            if ((int) $participant['user_id'] === $userId) {
                throw new InvalidArgumentException(
                    'User has already joined this game.'
                );
            }

            $count++;
        }

        if ($count >= (int) $game['max_players']) {
            throw new InvalidArgumentException(
                'Game has reached its maximum number of players.'
            );
        }

        $updateData = [];

        if ($gameId !== (int) $existing['game_id']) {
            $updateData['game_id'] = $gameId;
        }

        if ($userId !== (int) $existing['user_id']) {
            $updateData['user_id'] = $userId;
        }

        if (empty($updateData)) {
            throw new InvalidArgumentException(
                'No changes provided for update.'
            );
        }

        $this->participantDao->update($id, $updateData);

        return $this->getById($id);
    }

    // Delete participation
    public function delete(int $id): bool
    {
        $this->getById($id);

        return $this->participantDao->delete($id);
    }
}
