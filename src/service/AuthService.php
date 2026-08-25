<?php

class AuthService
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function authenticate(string $email, string $password): array
    {
        $mensagemErro = 'Ops, Email ou Senha inválido';

        if (empty($email) || empty($password)) {
            return ['success' => false, 'message' => $mensagemErro, 'user' => null];
        }

        $user = $this->userModel->findByEmail($email);

        if (!$user) {
            return ['success' => false, 'message' => $mensagemErro, 'user' => null];
        }

        if (!$this->userModel->verifyPassword($password, $user['password'])) {
            return ['success' => false, 'message' => $mensagemErro, 'user' => null];
        }

        session_regenerate_id(true);

        $_SESSION['user_id'] = $user['id_user'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];

        return ['success' => true, 'message' => '', 'user' => $user];
    }

    public function logout(): void
    {
        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy();
    }

    public function isLoggedIn(): bool
    {
        return isset($_SESSION['user_id']);
    }

    public function getLoggedUser(): ?array
    {
        if (!$this->isLoggedIn()) {
            return null;
        }
        return [
            'id' => $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'email' => $_SESSION['user_email']
        ];
    }

    public function getLoggedUserId(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }
}
