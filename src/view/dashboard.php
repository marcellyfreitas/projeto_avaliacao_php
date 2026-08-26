<?php $pageTitle = 'Dashboard'; ?>
<?php require __DIR__ . '/layouts/header.php'; ?>

        <header class="main-header">
            <div class="header-content">
                <div class="logo">
                    <h1>JM Informática</h1>
                </div>
                <div class="user-info">
                    <span class="current-date"><?= date('d/m/Y') ?></span>
                </div>
            </div>
        </header>

        <?php if (isset($_GET['msg'])): ?>
            <?php if ($_GET['msg'] === 'finished'): ?>
                <div class="alert alert-success toast-fade" role="alert">
                    <strong>Serviço finalizado com sucesso.</strong>
                </div>
            <?php else: ?>
                <div class="alert alert-<?= $_GET['msg'] === 'error' ? 'error' : 'success' ?> toast-fade" role="alert">
                    <?php
                    $messages = [
                        'success' => 'Serviço cadastrado com sucesso!',
                        'updated' => 'Serviço atualizado com sucesso!',
                        'deleted' => 'Serviço excluído com sucesso!',
                        'error' => 'Não foi possível cadastrar o serviço. Verifique os dados e tente novamente.'
                    ];
                    echo $messages[$_GET['msg']] ?? 'Operação realizada.';
                    ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div class="summary-cards">
            <div class="card card-total">
                <div class="card-icon">R$</div>
                <div class="card-content">
                    <h3>Valor Total dos Serviços</h3>
                    <p class="card-value">R$ <?= number_format($totalServices, 2, ',', '.') ?></p>
                </div>
            </div>

            <div class="card card-pending">
                <div class="card-icon">!</div>
                <div class="card-content">
                    <h3>Serviços Pendentes</h3>
                    <p class="card-value"><?= $pendingCount ?> serviço(s)</p>
                </div>
            </div>
        </div>

        <?php if (!empty($pendingList)): ?>
            <div class="pending-list">
                <h3>Últimos Serviços Pendentes</h3>
                <ul>
                    <?php foreach ($pendingList as $pending): ?>
                        <li>
                            <span class="pending-desc"><?= htmlspecialchars($pending['description']) ?></span>
                            <span class="pending-price">R$ <?= number_format($pending['price'], 2, ',', '.') ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="filters-section">
            <h3>Filtros</h3>
            <form method="GET" action="/dashboard" class="filters-form">
                <div class="filter-row">
                    <div class="filter-group">
                        <label for="date_start">Data Início</label>
                        <input type="date" id="date_start" name="date_start" value="<?= htmlspecialchars($_GET['date_start'] ?? '') ?>">
                    </div>
                    <div class="filter-group">
                        <label for="date_end">Data Fim</label>
                        <input type="date" id="date_end" name="date_end" value="<?= htmlspecialchars($_GET['date_end'] ?? '') ?>">
                    </div>
                    <div class="filter-group">
                        <label for="description">Descrição</label>
                        <input type="text" id="description" name="description" placeholder="Nome do serviço" 
                               value="<?= htmlspecialchars($_GET['description'] ?? '') ?>">
                    </div>
                    <div class="filter-group">
                        <label for="status">Status</label>
                        <select id="status" name="status">
                            <option value="">Todos</option>
                            <option value="Pendente" <?= ($_GET['status'] ?? '') === 'Pendente' ? 'selected' : '' ?>>Pendente</option>
                            <option value="Finalizado" <?= ($_GET['status'] ?? '') === 'Finalizado' ? 'selected' : '' ?>>Finalizado</option>
                        </select>
                    </div>
                    <div class="filter-group">
                        <label for="user_name">Prestador</label>
                        <select id="user_name" name="user_name">
                            <option value="<?= htmlspecialchars($_SESSION['user_name'] ?? '') ?>" selected>
                                <?= htmlspecialchars($_SESSION['user_name'] ?? '') ?>
                            </option>
                        </select>
                    </div>
                    <div class="filter-group filter-buttons">
                        <button type="submit" class="btn btn-primary">Filtrar</button>
                        <a href="/dashboard" class="btn btn-secondary">Limpar</a>
                    </div>
                </div>
            </form>
        </div>

        <div class="table-section">
            <h3>Serviços Prestados</h3>
            <?php if (empty($services)): ?>
                <p class="no-data">Nenhum serviço encontrado.</p>
            <?php else: ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Descrição</th>
                            <th scope="col">Status</th>
                            <th scope="col">Valor</th>
                            <th scope="col">Prestador</th>
                            <th scope="col">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($services as $service): ?>
                            <tr>
                                <td><?= (int) $service['id_service'] ?></td>
                                <td><?= htmlspecialchars($service['description']) ?></td>
                                <td>
                                    <span class="status <?= empty($service['finished_at']) ? 'status-pending' : 'status-finished' ?>">
                                        <?= empty($service['finished_at']) ? 'Pendente' : 'Finalizado' ?>
                                    </span>
                                </td>
                                <td>R$ <?= number_format($service['price'], 2, ',', '.') ?></td>
                                <td><?= htmlspecialchars($service['user_name']) ?></td>
                                <td class="actions-cell">
                                    <a href="/servico/<?= (int) $service['id_service'] ?>/editar" 
                                       class="btn btn-small btn-primary" title="Editar">Editar</a>
                                     
                                    <form method="POST" action="/servico/<?= (int) $service['id_service'] ?>/excluir" 
                                          class="form-inline">
                                        <?= csrfField() ?>
                                        <button type="submit" class="btn btn-small btn-danger" title="Excluir"
                                                data-confirm="Deseja realmente excluir este serviço?">Excluir</button>
                                    </form>
                                    
                                    <?php if (empty($service['finished_at'])): ?>
                                        <form method="POST" action="/servico/<?= (int) $service['id_service'] ?>/finalizar" 
                                              class="form-inline">
                                            <?= csrfField() ?>
                                            <button type="submit" class="btn btn-small btn-success" title="Finalizar"
                                                    data-confirm="Deseja finalizar este serviço?">Finalizar</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="commission-value <?= $service['commission_user'] > 0 ? '' : 'commission-undefined' ?>">
                                            Comissão: <?= $service['commission_user'] > 0 ? 'R$ ' . number_format($service['commission_user'], 2, ',', '.') : 'Não definido' ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php
                $queryParams = $_GET;
                unset($queryParams['page']);
                unset($queryParams['msg']);

                $buildUrl = function(int $p) use ($queryParams) {
                    $params = $queryParams;
                    $params['page'] = $p;
                    return '/dashboard?' . http_build_query($params);
                };
                ?>

                <?php if ($currentPage > 1): ?>
                    <a href="<?= $buildUrl(1) ?>" class="btn btn-small btn-secondary" title="Primeira">&laquo;</a>
                    <a href="<?= $buildUrl($currentPage - 1) ?>" class="btn btn-small btn-secondary" title="Anterior">&lsaquo;</a>
                <?php endif; ?>

                <?php
                $start = max(1, $currentPage - 2);
                $end = min($totalPages, $currentPage + 2);
                for ($i = $start; $i <= $end; $i++):
                ?>
                    <?php if ($i === $currentPage): ?>
                        <span class="btn btn-small btn-primary pagination-current"><?= $i ?></span>
                    <?php else: ?>
                        <a href="<?= $buildUrl($i) ?>" class="btn btn-small btn-secondary"><?= $i ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($currentPage < $totalPages): ?>
                    <a href="<?= $buildUrl($currentPage + 1) ?>" class="btn btn-small btn-secondary" title="Próxima">&rsaquo;</a>
                    <a href="<?= $buildUrl($totalPages) ?>" class="btn btn-small btn-secondary" title="Última">&raquo;</a>
                <?php endif; ?>

                <span class="pagination-info">Página <?= $currentPage ?> de <?= $totalPages ?> (<?= $totalItems ?> serviço(s))</span>
            </div>
        <?php endif; ?>

<?php require __DIR__ . '/layouts/footer.php'; ?>
