
<?php

require_once __DIR__ . '/BaseDao.php';

class GameParticipantDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct('game_participants');
    }

    public function getByGameId(int $gameId): array
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM game_participants WHERE game_id = :game_id'
        );

        $statement->execute(['game_id' => $gameId]);

        return $statement->fetchAll();
    }

    public function getByUserId(int $userId): array
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM game_participants WHERE user_id = :user_id'
        );

        $statement->execute(['user_id' => $userId]);

        return $statement->fetchAll();
    }
}
