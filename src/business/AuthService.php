<?php
namespace Business;

use Data\UserRepository;

class AuthService {
    private $userRepository;

    public function __construct(UserRepository $userRepository) {
        $this->userRepository = $userRepository;
    }

    public function login(string $username, string $password): bool {
        $user = $this->userRepository->findByUsername($username);
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            return true;
        }
        
        return false;
    }

    public function logout(): void {
        session_destroy();
    }

    public function isLoggedIn(): bool {
        return isset($_SESSION['user_id']);
    }
} 