<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'JM Informática') ?> - JM Informática</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="/public/assets/css/boot.css">
    <link rel="stylesheet" href="/public/assets/css/style.css">
    <link rel="stylesheet" href="/public/assets/css/dashboard.css">
</head>
<body<?= isset($pageFixedFooter) ? ' class="page-fixed-footer"' : '' ?>>
    <button class="sidebar-toggle" id="sidebar-toggle" aria-label="Abrir menu" aria-expanded="false" aria-controls="sidebar">&#9776;</button>
    <div class="sidebar-overlay" id="sidebar-overlay" aria-hidden="true"></div>

    <div class="dashboard-layout">
        <aside class="sidebar" id="sidebar" aria-label="Menu principal">
            <div class="sidebar-user">
                <div class="sidebar-avatar" aria-hidden="true"><?= strtoupper(substr($_SESSION['user_name'] ?? '', 0, 1)) ?></div>
                <span class="sidebar-user-name"><?= htmlspecialchars($_SESSION['user_name'] ?? '') ?></span>
                <span class="sidebar-user-email"><?= htmlspecialchars($_SESSION['user_email'] ?? '') ?></span>
            </div>
            <nav class="sidebar-nav" aria-label=" navegação principal">
                <a href="/dashboard" class="sidebar-link">
                    <span class="sidebar-link-icon" aria-hidden="true">&#9632;</span>
                    <span class="sidebar-link-label">Dashboard</span>
                </a>
                <a href="/servico/novo" class="sidebar-link">
                    <span class="sidebar-link-icon" aria-hidden="true">+</span>
                    <span class="sidebar-link-label">Novo serviço</span>
                </a>
            </nav>
            <div class="sidebar-footer">
                <form method="POST" action="/logout" style="width: 100%;">
                    <?= csrfField() ?>
                    <button type="submit" class="sidebar-logout-btn" title="Sair">
                        <span class="sidebar-logout-label">Sair</span>
                    </button>
                </form>
            </div>
        </aside>

        <main class="main-content">
