
<?php

require_once __DIR__ . '/config/Database.php';
require_once __DIR__ . '/dao/UserDao.php';

$userDao = new UserDao();

$testEmail = 'crud.test.' . uniqid() . '@example.com';
$testUserId = null;

try {
    echo "=== Termin.ba CRUD tests ===" . PHP_EOL;

    // 1. CREATE
    $testUserId = $userDao->create([
        'full_name' => 'CRUD Test User',
        'email' => $testEmail,
        'password' => password_hash('TestPassword123!', PASSWORD_DEFAULT)
    ]);

    echo "CREATE: User created with ID {$testUserId}" . PHP_EOL;

    // 2. READ BY ID
    $user = $userDao->getById((int) $testUserId);

    if (!$user || $user['email'] !== $testEmail) {
        throw new RuntimeException('READ BY ID failed.');
    }

    echo "READ BY ID: Passed" . PHP_EOL;

    // 3. READ BY EMAIL
    $user = $userDao->getByEmail($testEmail);

    if (!$user || $user['email'] !== $testEmail) {
        throw new RuntimeException('READ BY EMAIL failed.');
    }

    echo "READ BY EMAIL: Passed" . PHP_EOL;

    // 4. UPDATE
    $updated = $userDao->update((int) $testUserId, [
        'full_name' => 'CRUD Test User Updated'
    ]);

    $user = $userDao->getById((int) $testUserId);

    if (!$user || $user['full_name'] !== 'CRUD Test User Updated') {
        throw new RuntimeException('UPDATE failed.');
    }

    echo "UPDATE: Passed" . PHP_EOL;

    // 5. DELETE
    $deleted = $userDao->delete((int) $testUserId);

    if (!$deleted || $userDao->getById((int) $testUserId) !== false) {
        throw new RuntimeException('DELETE failed.');
    }

    $testUserId = null;

    echo "DELETE: Passed" . PHP_EOL;
    echo "All CRUD tests passed successfully." . PHP_EOL;

} catch (Throwable $exception) {
    echo "CRUD test failed: " . $exception->getMessage() . PHP_EOL;

} finally {
    // Clean up the test user if a previous step failed.
    if ($testUserId !== null) {
        try {
            $userDao->delete((int) $testUserId);
            echo "Cleanup: Test user removed." . PHP_EOL;
        } catch (Throwable $cleanupException) {
            echo "Cleanup failed: " . $cleanupException->getMessage() . PHP_EOL;
        }
    }
}
