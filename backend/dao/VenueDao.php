
<?php

require_once __DIR__ . '/BaseDao.php';

class VenueDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct('venues');
    }

    public function getByLocation(string $location): array
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM venues WHERE location LIKE :location'
        );

        $statement->execute([
            'location' => '%' . $location . '%',
        ]);

        return $statement->fetchAll();
    }
}
