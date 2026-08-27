<?php

class RegisterController
{
    private UserModel $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    public function showForm(): void
    {
        $pageTitle = 'Cadastro';
        $error     = '';
        $success   = '';
        require __DIR__ . '/../view/register.php';
    }

    public function handleRegister(): void
    {
        $name            = trim($_POST['name']             ?? '');
        $email           = trim($_POST['email']            ?? '');
        $password        = trim($_POST['password']         ?? '');
        $passwordConfirm = trim($_POST['password_confirm'] ?? '');

        $pageTitle = 'Cadastro';
        $error     = '';
        $success   = '';

        // Validacoes
        if (empty($name) || empty($email) || empty($password) || empty($passwordConfirm)) {
            $error = 'Todos os campos são obrigatórios.';
            require __DIR__ . '/../view/register.php';
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Informe um e-mail válido.';
            require __DIR__ . '/../view/register.php';
            return;
        }

        if (strlen($password) < 6) {
            $error = 'A senha deve ter pelo menos 6 caracteres.';
            require __DIR__ . '/../view/register.php';
            return;
        }

        if ($password !== $passwordConfirm) {
            $error = 'As senhas não coincidem.';
            require __DIR__ . '/../view/register.php';
            return;
        }

        if ($this->userModel->findByEmail($email)) {
            $error = 'Este e-mail já está cadastrado.';
            require __DIR__ . '/../view/register.php';
            return;
        }

        $hash   = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $result = $this->userModel->create($name, $email, $hash);

        if (!$result) {
            $error = 'Erro ao realizar o cadastro. Tente novamente.';
            require __DIR__ . '/../view/register.php';
            return;
        }

        header('Location: /login?msg=registered');
        exit;
    }
}
