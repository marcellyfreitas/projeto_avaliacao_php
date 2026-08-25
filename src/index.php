<?php
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/security.php';

// Services (carregados uma vez)
require_once __DIR__ . '/service/EmailService.php';
require_once __DIR__ . '/service/AuthService.php';
require_once __DIR__ . '/service/ServiceService.php';
require_once __DIR__ . '/service/DashboardService.php';

// Models (carregados uma vez)
require_once __DIR__ . '/model/UserModel.php';
require_once __DIR__ . '/model/ServiceModel.php';

// Controllers (carregados uma vez)
require_once __DIR__ . '/controller/LoginController.php';
require_once __DIR__ . '/controller/DashboardController.php';
require_once __DIR__ . '/controller/ServiceController.php';

$route = $_GET['route'] ?? '';

if (empty($route)) {
    header('Location: /login');
    exit;
}

// Helper: verificar autenticação
$requireAuth = function (): void {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }
};

// Helper: obter ID da query string
$getId = function (): ?int {
    return isset($_GET['id']) ? (int) $_GET['id'] : null;
};

switch ($route) {
    // === Login ===
    case 'login':
        // Já logado? Redireciona para dashboard
        if (isset($_SESSION['user_id'])) {
            header('Location: /dashboard');
            exit;
        }
        $controller = new LoginController();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $controller->handleLogin();
        } else {
            $controller->showForm();
        }
        break;

    case 'logout':
        requireMethod('POST');
        if (!isset($_SESSION['user_id'])) {
            header('Location: /login');
            exit;
        }
        $authService = new AuthService();
        $authService->logout();
        header('Location: /login?msg=logout');
        exit;

        // === Dashboard ===
    case 'dashboard':
        $requireAuth();
        $controller = new DashboardController();
        $controller->index();
        break;

    // === Serviços ===
    case 'service_new':
        $requireAuth();
        $controller = new ServiceController();
        $controller->create();
        break;

    case 'service_edit':
        $requireAuth();
        $id = $getId();
        if (!$id) {
            header('Location: /dashboard');
            exit;
        }
        $controller = new ServiceController();
        $controller->edit($id);
        break;

    case 'service_delete':
        $requireAuth();
        requireMethod('POST');
        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            header('Location: /dashboard?msg=error');
            exit;
        }
        $id = $getId();
        if (!$id) {
            header('Location: /dashboard');
            exit;
        }
        $controller = new ServiceController();
        $controller->delete($id);
        break;

    case 'service_finish':
        $requireAuth();
        requireMethod('POST');
        if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
            header('Location: /dashboard?msg=error');
            exit;
        }
        $id = $getId();
        if (!$id) {
            header('Location: /dashboard');
            exit;
        }
        $controller = new ServiceController();
        $controller->finish($id);
        break;

    default:
        http_response_code(404);
        $pageTitle = '404';
        require __DIR__ . '/view/404.php';
        break;
}
