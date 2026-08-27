<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Cadastro' ?> - JM Informática</title>
    <link rel="stylesheet" href="/public/assets/css/boot.css">
    <link rel="stylesheet" href="/public/assets/css/style.css">
    <link rel="stylesheet" href="/public/assets/css/login.css">
</head>
<body class="login-page">
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1>JM Informática</h1>
                <p>Criar nova conta</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="/cadastro">
                <div class="form-group">
                    <label for="name">Nome completo</label>
                    <input type="text" id="name" name="name" required
                           placeholder="Digite seu nome"
                           value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" required
                           placeholder="Digite seu e-mail"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>

                <div class="form-group">
                    <label for="password">Senha</label>
                    <input type="password" id="password" name="password" required
                           placeholder="Mínimo 6 caracteres">
                </div>

                <div class="form-group">
                    <label for="password_confirm">Confirmar senha</label>
                    <input type="password" id="password_confirm" name="password_confirm" required
                           placeholder="Repita a senha">
                </div>

                <button type="submit" class="btn btn-primary btn-block">
                    Cadastrar
                </button>
            </form>

            <p style="text-align: center; margin-top: 20px; font-size: 14px; color: var(--text-light);">
                Já tem uma conta? <a href="/login">Entrar</a>
            </p>
        </div>
    </div>
</body>
</html>
