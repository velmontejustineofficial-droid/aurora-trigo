<?php

class LoginService {

    public function __construct(
        private UserRepository $userRepository
    ) {}

    public function validate($username, $password, $role): array {
        $hashPassword = hash('sha256', $password);
        $user = $this->userRepository->getByUsername($username);

        if ($user === null) {
            throw new Exception("Invalid username");
        }
        if ($user['role'] !== $role) {
            throw new Exception("Invalid role");
        }
        if ($user['password'] !== $hashPassword) {
            throw new Exception("Invalid password");
        }

        if((int)$user['is_active'] === 0){
            $reason = $row['ban_reason'] ?? 'Violation of Terms of Service';
            $error = '🚫 Your account has been banned: ' . htmlspecialchars($reason);
            throw new Exception($error);
        }

        session_start();
        $_SESSION['auth_token'] = bin2hex(random_bytes(32));
        $_SESSION['user_id'] = $user['id'];
    }
}