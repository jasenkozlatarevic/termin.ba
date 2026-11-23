<?php

require_once __DIR__ . '/services/UserService.php';

$userService = new UserService();

try {
    // CREATE
    echo "Testing CREATE:\n";

    $newUser = $userService->create([
        'full_name' => 'Test User',
        'email' => 'test.user.m3@example.com',
        'password' => 'TestPassword123'
    ]);

    print_r($newUser);

    $userId = (int) $newUser['id'];

    echo "PASS: User created with ID $userId\n";

    // UPDATE
    echo "\nTesting UPDATE:\n";

    $updatedUser = $userService->update($userId, [
        'full_name' => 'Updated Test User'
    ]);

    print_r($updatedUser);

    if ($updatedUser['full_name'] === 'Updated Test User') {
        echo "PASS: User updated successfully.\n";
    }

    // DELETE
    echo "\nTesting DELETE:\n";

    $deleted = $userService->delete($userId);

    if ($deleted) {
        echo "PASS: User deleted successfully.\n";
    }

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
