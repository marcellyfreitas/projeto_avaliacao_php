<?php


class ServiceController
{
    private ServiceService $serviceService;
    private AuthService $authService;

    public function __construct()
    {
        $this->serviceService = new ServiceService();
        $this->authService = new AuthService();
    }

    public function create(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
                header('Location: /dashboard?msg=error');
                exit;
            }

            $description = trim($_POST['description'] ?? '');
            $price = $this->parsePrice($_POST['price'] ?? '');
            $userId = $_SESSION['user_id'];

            $result = $this->serviceService->create($description, $price, $userId);

            if ($result['success']) {
                header('Location: /dashboard?msg=success');
                exit;
            }

            header('Location: /dashboard?msg=error');
            exit;
        }

        $pageTitle   = 'Novo Serviço';
        $message     = '';
        $messageType = '';
        $route       = 'service_new';
        $id          = null;
        $service     = null;
        require __DIR__ . '/../view/service_form.php';
    }

    public function edit(int $id): void
    {
        $userId = $this->authService->getLoggedUserId();
        $service = $this->serviceService->findById($id);

        if (!$service || !isServiceOwner($service, $userId)) {
            header('Location: /dashboard');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!validateCsrfToken($_POST['csrf_token'] ?? '')) {
                header('Location: /dashboard?msg=error');
                exit;
            }

            $description = trim($_POST['description'] ?? '');
            $price = $this->parsePrice($_POST['price'] ?? '');

            $result = $this->serviceService->update($id, $description, $price);

            if ($result['success']) {
                header('Location: /dashboard?msg=updated');
                exit;
            }

            $pageTitle   = 'Editar Serviço';
            $message     = $result['message'];
            $messageType = 'error';
            $route       = 'service_edit';
            $service['description'] = $description;
            $service['price']       = $_POST['price'] ?? '';
            require __DIR__ . '/../view/service_form.php';
            return;
        }

        $pageTitle   = 'Editar Serviço';
        $message     = '';
        $messageType = '';
        $route       = 'service_edit';
        require __DIR__ . '/../view/service_form.php';
    }

    public function delete(int $id): void
    {
        $userId = $this->authService->getLoggedUserId();
        $service = $this->serviceService->findById($id);

        if (!$service || !isServiceOwner($service, $userId)) {
            header('Location: /dashboard');
            exit;
        }

        $result = $this->serviceService->delete($id);

        if ($result['success']) {
            header('Location: /dashboard?msg=deleted');
        } else {
            header('Location: /dashboard?msg=error');
        }
        exit;
    }

    public function finish(int $id): void
    {
        $userId = $this->authService->getLoggedUserId();
        $service = $this->serviceService->findById($id);

        if (!$service || !isServiceOwner($service, $userId)) {
            header('Location: /dashboard');
            exit;
        }

        $result = $this->serviceService->finish($id);

        if ($result['success']) {
            header("Location: /dashboard?msg=finished");
        } else {
            header('Location: /dashboard?msg=error');
        }
        exit;
    }

    private function parsePrice(string $input): float
    {
        $clean = preg_replace('/[^\d,\.]/', '', $input);
        if ($clean === '' || $clean === '.' || $clean === ',') {
            return 0.0;
        }
        if (strpos($clean, ',') !== false && strpos($clean, '.') !== false) {
            $clean = str_replace('.', '', $clean);
            $clean = str_replace(',', '.', $clean);
        } elseif (strpos($clean, ',') !== false) {
            $clean = str_replace(',', '.', $clean);
        }
        return (float) $clean;
    }
}
