<?php

require_once __DIR__ . '/../dao/UserDao.php';

class UserService
{
    private UserDao $userDao;

    public function __construct()
    {
        $this->userDao = new UserDao();
    }

    // Remove password before returning user data
    private function sanitizeUser(array $user): array
    {
        unset($user['password']);

        return $user;
    }

    // Get all users
    public function getAll(): array
    {
        $users = $this->userDao->getAll();

        return array_map(
            fn($user) => $this->sanitizeUser($user),
            $users
        );
    }

    // Get user by ID
    public function getById(int $id): array
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('Invalid user ID.');
        }

        $user = $this->userDao->getById($id);

        if (!$user) {
            throw new RuntimeException('User not found.');
        }

        return $this->sanitizeUser($user);
    }

    // Create a new user
    public function create(array $data): array
    {
        $fullName = trim($data['full_name'] ?? '');
        $email = strtolower(trim($data['email'] ?? ''));
        $password = $data['password'] ?? '';

        if ($fullName === '') {
            throw new InvalidArgumentException('Full name is required.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email address.');
        }

        if (strlen($password) < 8) {
            throw new InvalidArgumentException(
                'Password must contain at least 8 characters.'
            );
        }

        if ($this->userDao->getByEmail($email)) {
            throw new InvalidArgumentException(
                'Email address is already registered.'
            );
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $userId = $this->userDao->create([
            'full_name' => $fullName,
            'email' => $email,
            'password' => $hashedPassword
        ]);

        return $this->getById((int) $userId);
    }

    // Update an existing user
    public function update(int $id, array $data): array
    {
        $existingUser = $this->userDao->getById($id);

        if (!$existingUser) {
            throw new RuntimeException('User not found.');
        }

        $updateData = [];

        if (isset($data['full_name'])) {
            $fullName = trim($data['full_name']);

            if ($fullName === '') {
                throw new InvalidArgumentException(
                    'Full name cannot be empty.'
                );
            }

            $updateData['full_name'] = $fullName;
        }

        if (isset($data['email'])) {
            $email = strtolower(trim($data['email']));

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new InvalidArgumentException(
                    'Invalid email address.'
                );
            }

            $userWithEmail = $this->userDao->getByEmail($email);

            if ($userWithEmail && (int) $userWithEmail['id'] !== $id) {
                throw new InvalidArgumentException(
                    'Email address is already registered.'
                );
            }

            $updateData['email'] = $email;
        }

        if (isset($data['password'])) {
            if (strlen($data['password']) < 8) {
                throw new InvalidArgumentException(
                    'Password must contain at least 8 characters.'
                );
            }

            $updateData['password'] = password_hash(
                $data['password'],
                PASSWORD_DEFAULT
            );
        }

        if (empty($updateData)) {
            throw new InvalidArgumentException(
                'No valid fields provided for update.'
            );
        }

        $this->userDao->update($id, $updateData);

        return $this->getById($id);
    }

    // Delete a user
    public function delete(int $id): bool
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('Invalid user ID.');
        }

        $existingUser = $this->userDao->getById($id);

        if (!$existingUser) {
            throw new RuntimeException('User not found.');
        }

        return $this->userDao->delete($id);
    }
}
