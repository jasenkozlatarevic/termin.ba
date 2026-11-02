
<?php

require_once __DIR__ . '/BaseDao.php';

class GameDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct('games');
    }

    public function getByVenueId(int $venueId): array
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM games WHERE venue_id = :venue_id'
        );

        $statement->execute(['venue_id' => $venueId]);

        return $statement->fetchAll();
    }

    public function getByCreatorId(int $creatorId): array
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM games WHERE creator_id = :creator_id'
        );

        $statement->execute(['creator_id' => $creatorId]);

        return $statement->fetchAll();
    }
}
