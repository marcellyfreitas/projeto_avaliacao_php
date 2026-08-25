<?php

class DashboardController
{
    private DashboardService $dashboardService;
    private AuthService $authService;

    public function __construct()
    {
        $this->dashboardService = new DashboardService();
        $this->authService = new AuthService();
    }

    public function index(): void
    {
        $userId = $this->authService->getLoggedUserId();

        $filters = [];
        if (!empty($_GET['date_start'])) $filters['date_start'] = $_GET['date_start'];
        if (!empty($_GET['date_end']))   $filters['date_end']   = $_GET['date_end'];
        if (!empty($_GET['description'])) $filters['description'] = $_GET['description'];
        if (!empty($_GET['status']))     $filters['status']     = $_GET['status'];
        if (!empty($_GET['user_name']))  $filters['user_name']  = $_GET['user_name'];

        $page = max(1, (int) ($_GET['page'] ?? 1));

        $data = $this->dashboardService->getData($userId, $filters, $page);

        $services       = $data['services'];
        $totalServices  = $data['totalServices'];
        $pendingCount   = $data['pendingServices'];
        $pendingList    = $data['pendingList'];
        $currentPage    = $data['currentPage'];
        $totalPages     = $data['totalPages'];
        $totalItems     = $data['totalItems'];

        require __DIR__ . '/../view/dashboard.php';
    }
}
