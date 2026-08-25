<?php

class ServiceService
{
    private ServiceModel $serviceModel;

    public function __construct()
    {
        $this->serviceModel = new ServiceModel();
    }

    public function findById(int $id): ?array
    {
        return $this->serviceModel->findById($id);
    }

    public function create(string $description, float $price, int $userId): array
    {
        $errors = $this->validate($description, $price);
        if (!empty($errors)) {
            return ['success' => false, 'message' => implode(' ', $errors), 'id' => null];
        }

        $id = $this->serviceModel->insert($description, $price, $userId);

        if ($id) {
            return ['success' => true, 'message' => 'Serviço criado com sucesso!', 'id' => $id];
        }

        return ['success' => false, 'message' => 'Erro ao criar serviço.', 'id' => null];
    }

    public function update(int $id, string $description, float $price): array
    {
        $errors = $this->validate($description, $price);
        if (!empty($errors)) {
            return ['success' => false, 'message' => implode(' ', $errors)];
        }

        $existing = $this->serviceModel->findById($id);
        if (!$existing) {
            return ['success' => false, 'message' => 'Serviço não encontrado.'];
        }

        $result = $this->serviceModel->update($id, $description, $price);

        if ($result) {
            return ['success' => true, 'message' => 'Serviço atualizado com sucesso!'];
        }

        return ['success' => false, 'message' => 'Erro ao atualizar serviço.'];
    }

    public function delete(int $id): array
    {
        $existing = $this->serviceModel->findById($id);
        if (!$existing) {
            return ['success' => false, 'message' => 'Serviço não encontrado.'];
        }

        $result = $this->serviceModel->delete($id);

        if ($result) {
            return ['success' => true, 'message' => 'Serviço excluído com sucesso!'];
        }

        return ['success' => false, 'message' => 'Erro ao excluir serviço.'];
    }

    public function finish(int $id): array
    {
        $service = $this->serviceModel->findById($id);
        if (!$service) {
            return ['success' => false, 'message' => 'Serviço não encontrado.', 'commission' => null];
        }

        if ($service['finished_at'] !== null) {
            return ['success' => false, 'message' => 'Este serviço já foi finalizado.', 'commission' => null];
        }

        $price = (float) $service['price'];
        $commission = $this->calculateCommission($price);

        $result = $this->serviceModel->setFinished($id, $commission);

        if (!$result) {
            return ['success' => false, 'message' => 'Erro ao finalizar serviço.', 'commission' => null];
        }

        $this->sendFinishEmail($service, $price, $commission);

        return ['success' => true, 'message' => 'Serviço finalizado com sucesso!', 'commission' => $commission];
    }

    public function getCommissionData(float $price): array
    {
        if ($price > 10000) {
            $percent = 0.20;
            $label = '20%';
        } elseif ($price > 1000) {
            $percent = 0.10;
            $label = '10%';
        } else {
            $percent = 0.05;
            $label = '5%';
        }
        return [
            'percent' => $percent,
            'label'   => $label,
            'value'   => round($price * $percent, 2),
        ];
    }

    public function calculateCommission(float $price): float
    {
        return $this->getCommissionData($price)['value'];
    }

    public function getCommissionPercentLabel(float $price): string
    {
        return $this->getCommissionData($price)['label'];
    }

    private function validate(string $description, float $price): array
    {
        $errors = [];

        if (empty(trim($description))) {
            $errors[] = 'A descrição é obrigatória.';
        } elseif (strlen($description) > 255) {
            $errors[] = 'A descrição deve ter no máximo 255 caracteres.';
        }

        if (!is_numeric($price) || $price <= 0) {
            $errors[] = 'O valor do serviço deve ser um número positivo.';
        }

        return $errors;
    }

    private function sendFinishEmail(array $service, float $price, float $commission): void
    {
        try {
            $emailService = new EmailService();
            $percentLabel = $this->getCommissionPercentLabel($price);

            $subject = "Serviço Finalizado - JM Informática";
            $body = "Olá {$service['user_name']},\n\n";
            $body .= "Seu serviço foi finalizado com sucesso!\n\n";
            $body .= "Descrição: {$service['description']}\n";
            $body .= "Valor do serviço: R$ " . number_format($price, 2, ',', '.') . "\n";
            $body .= "Regra de comissão aplicada: {$percentLabel}\n";
            $body .= "Valor da comissão: R$ " . number_format($commission, 2, ',', '.') . "\n\n";
            $body .= "Atenciosamente,\n";
            $body .= "Equipe JM Informática";

            $emailService->send($service['user_email'], $subject, $body);
        } catch (Exception $e) {
            // Erro de email não interrompe finalização
        }
    }
}
