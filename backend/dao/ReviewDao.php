
<?php

require_once __DIR__ . '/BaseDao.php';

class ReviewDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct('reviews');
    }

    public function getByVenueId(int $venueId): array
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM reviews WHERE venue_id = :venue_id'
        );

        $statement->execute(['venue_id' => $venueId]);

        return $statement->fetchAll();
    }

    public function getByUserId(int $userId): array
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM reviews WHERE user_id = :user_id'
        );

        $statement->execute(['user_id' => $userId]);

        return $statement->fetchAll();
    }
}
