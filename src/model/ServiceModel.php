<?php

class ServiceModel
{
    private PDO $db;

    public function __construct()
    {
        $this->db = getConnection();
    }

    public function findAll(array $filters = []): array
    {
        $sql = "SELECT s.*, u.name as user_name 
                FROM service s 
                INNER JOIN user u ON s.user_id_user = u.id_user 
                WHERE 1=1";
        $params = [];

        if (!empty($filters['date_start'])) {
            $sql .= " AND s.created_at >= :date_start";
            $params[':date_start'] = $filters['date_start'] . ' 00:00:00';
        }
        if (!empty($filters['date_end'])) {
            $sql .= " AND s.created_at <= :date_end";
            $params[':date_end'] = $filters['date_end'] . ' 23:59:59';
        }
        if (!empty($filters['description'])) {
            $sql .= " AND s.description LIKE :description";
            $params[':description'] = '%' . $filters['description'] . '%';
        }
        if (!empty($filters['status'])) {
            if ($filters['status'] === 'Pendente') {
                $sql .= " AND s.finished_at IS NULL";
            } elseif ($filters['status'] === 'Finalizado') {
                $sql .= " AND s.finished_at IS NOT NULL";
            }
        }
        if (!empty($filters['user_name'])) {
            $sql .= " AND u.name LIKE :user_name";
            $params[':user_name'] = '%' . $filters['user_name'] . '%';
        }

        $sql .= " ORDER BY s.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT s.*, u.name as user_name, u.email as user_email 
                                    FROM service s 
                                    INNER JOIN user u ON s.user_id_user = u.id_user 
                                    WHERE s.id_service = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch() ?: null;
    }

    public function insert(string $description, float $price, int $userId)
    {
        $stmt = $this->db->prepare("INSERT INTO service (description, price, user_id_user) 
                                    VALUES (:description, :price, :user_id)");
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        return $stmt->execute() ? $this->db->lastInsertId() : false;
    }

    public function update(int $id, string $description, float $price): bool
    {
        $stmt = $this->db->prepare("UPDATE service 
                                    SET description = :description, price = :price 
                                    WHERE id_service = :id");
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':price', $price);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("DELETE FROM service WHERE id_service = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function setFinished(int $id, float $commission): bool
    {
        $stmt = $this->db->prepare("UPDATE service 
                                    SET finished_at = NOW(), commission_user = :commission 
                                    WHERE id_service = :id");
        $stmt->bindParam(':commission', $commission);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getTotalByUser(int $userId): float
    {
        $stmt = $this->db->prepare("SELECT COALESCE(SUM(price), 0) as total 
                                    FROM service WHERE user_id_user = :user_id");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        return (float) $result['total'];
    }

    public function getPendingByUser(int $userId, int $limit = 5): array
    {
        $stmt = $this->db->prepare("SELECT * FROM service 
                                    WHERE user_id_user = :user_id AND finished_at IS NULL 
                                    ORDER BY created_at DESC 
                                    LIMIT :limit");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countPendingByUser(int $userId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) as total FROM service 
                                    WHERE user_id_user = :user_id AND finished_at IS NULL");
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch();
        return (int) $result['total'];
    }

    public function findPaginated(array $filters = [], int $page = 1, int $perPage = 10): array
    {
        $countSql = "SELECT COUNT(*) as total 
                     FROM service s 
                     INNER JOIN user u ON s.user_id_user = u.id_user 
                     WHERE s.user_id_user = :user_id";
        $sql = "SELECT s.*, u.name as user_name 
                FROM service s 
                INNER JOIN user u ON s.user_id_user = u.id_user 
                WHERE s.user_id_user = :user_id";
        $params = [':user_id' => $filters['user_id']];
        $countParams = [':user_id' => $filters['user_id']];

        if (!empty($filters['date_start'])) {
            $sql .= " AND s.created_at >= :date_start";
            $countSql .= " AND s.created_at >= :date_start";
            $params[':date_start'] = $filters['date_start'] . ' 00:00:00';
            $countParams[':date_start'] = $filters['date_start'] . ' 00:00:00';
        }
        if (!empty($filters['date_end'])) {
            $sql .= " AND s.created_at <= :date_end";
            $countSql .= " AND s.created_at <= :date_end";
            $params[':date_end'] = $filters['date_end'] . ' 23:59:59';
            $countParams[':date_end'] = $filters['date_end'] . ' 23:59:59';
        }
        if (!empty($filters['description'])) {
            $sql .= " AND s.description LIKE :description";
            $countSql .= " AND s.description LIKE :description";
            $params[':description'] = '%' . $filters['description'] . '%';
            $countParams[':description'] = '%' . $filters['description'] . '%';
        }
        if (!empty($filters['status'])) {
            $statusCondition = $filters['status'] === 'Pendente'
                ? " AND s.finished_at IS NULL"
                : " AND s.finished_at IS NOT NULL";
            $sql .= $statusCondition;
            $countSql .= $statusCondition;
        }
        if (!empty($filters['user_name'])) {
            $sql .= " AND u.name LIKE :user_name";
            $countSql .= " AND u.name LIKE :user_name";
            $params[':user_name'] = '%' . $filters['user_name'] . '%';
            $countParams[':user_name'] = '%' . $filters['user_name'] . '%';
        }

        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($countParams);
        $total = (int) $countStmt->fetch()['total'];
        $pages = max(1, (int) ceil($total / $perPage));
        $page = max(1, min($page, $pages));
        $offset = ($page - 1) * $perPage;

        $sql .= " ORDER BY s.created_at DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return [
            'data'  => $stmt->fetchAll(),
            'total' => $total,
            'pages' => $pages,
            'page'  => $page,
        ];
    }
}
