<?php $pageTitle = $pageTitle ?? 'Serviço'; ?>
<?php $pageFixedFooter = true; ?>
<?php require __DIR__ . '/layouts/header.php'; ?>

            <header class="main-header">
                <div class="header-content">
                    <div class="logo">
                        <h1><?= htmlspecialchars($pageTitle) ?></h1>
                    </div>
                </div>
            </header>

            <div class="form-container">
                <?php if (!empty($message)): ?>
                    <div class="alert alert-<?= $messageType === 'error' ? 'error' : 'success' ?>" role="alert">
                        <?= htmlspecialchars($message) ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="<?= $route === 'service_edit' ? '/servico/' . (int) $id . '/editar' : '/servico/novo' ?>" 
                      class="service-form">
                    <?= csrfField() ?>
                    
                    <div class="form-group">
                        <label for="description">Descrição do Serviço *</label>
                        <input type="text" id="description" name="description" required 
                               maxlength="100" placeholder="Descreva o serviço"
                               value="<?= htmlspecialchars($service['description'] ?? $_POST['description'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label for="price">Valor (R$) *</label>
                        <input type="text" id="price" name="price" required 
                               inputmode="decimal" placeholder="0,00"
                               value="<?= htmlspecialchars($service['price'] ?? $_POST['price'] ?? '') ?>">
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <?= $route === 'service_edit' ? 'Atualizar' : 'Cadastrar' ?>
                        </button>
                        <a href="/dashboard" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>

<?php require __DIR__ . '/layouts/footer.php'; ?>
