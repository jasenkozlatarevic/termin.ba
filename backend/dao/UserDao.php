
<?php

require_once __DIR__ . '/BaseDao.php';

class UserDao extends BaseDao
{
    public function __construct()
    {
        parent::__construct('users');
    }

    public function getByEmail(string $email): array|false
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM users WHERE email = :email'
        );

        $statement->execute(['email' => $email]);

        return $statement->fetch();
    }
}
