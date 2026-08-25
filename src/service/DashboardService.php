<?php

class DashboardService
{
    private ServiceModel $serviceModel;

    public function __construct()
    {
        $this->serviceModel = new ServiceModel();
    }

    public function getData(int $userId, array $filters = [], int $page = 1, int $perPage = 5): array
    {
        $filters['user_id'] = $userId;
        $paginated = $this->serviceModel->findPaginated($filters, $page, $perPage);
        $total = $this->serviceModel->getTotalByUser($userId);
        $pending = $this->serviceModel->getPendingByUser($userId, 5);
        $pendingCount = $this->serviceModel->countPendingByUser($userId);

        return [
            'services' => $paginated['data'],
            'totalServices' => $total,
            'pendingServices' => $pendingCount,
            'pendingList' => $pending,
            'currentPage' => $paginated['page'],
            'totalPages' => $paginated['pages'],
            'totalItems' => $paginated['total'],
        ];
    }
}
