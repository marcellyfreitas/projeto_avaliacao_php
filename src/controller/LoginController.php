<?php

class LoginController
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    public function showForm(): void
    {
        $pageTitle = 'Login';
        $error = '';
        require __DIR__ . '/../view/login.php';
    }

    public function handleLogin(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        $result = $this->authService->authenticate($email, $password);

        if ($result['success']) {
            header('Location: /dashboard');
            exit;
        }

        $pageTitle = 'Login';
        $error = $result['message'];
        require __DIR__ . '/../view/login.php';
    }
}
