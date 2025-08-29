<?php ob_start(); ?>

<div class="row mb-4">
    <div class="col">
        <h1 class="text-gradient">
            <i class="bi bi-speedometer2"></i> Dashboard
        </h1>
        <p class="text-muted">Visão geral do sistema Mini ERP</p>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card stats-card h-100">
            <div class="card-body text-center">
                <i class="bi bi-box-seam text-primary mb-2" style="font-size: 2rem;"></i>
                <div class="stats-number"><?= $stats['total_produtos'] ?? 0 ?></div>
                <h6 class="card-title text-muted">Produtos</h6>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card stats-card success h-100">
            <div class="card-body text-center">
                <i class="bi bi-receipt text-success mb-2" style="font-size: 2rem;"></i>
                <div class="stats-number text-success"><?= $stats['pedidos']['total_pedidos'] ?? 0 ?></div>
                <h6 class="card-title text-muted">Pedidos</h6>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card stats-card warning h-100">
            <div class="card-body text-center">
                <i class="bi bi-ticket-perforated text-warning mb-2" style="font-size: 2rem;"></i>
                <div class="stats-number text-warning"><?= $stats['cupons']['total_cupons'] ?? 0 ?></div>
                <h6 class="card-title text-muted">Cupons</h6>
            </div>
        </div>
    </div>
    
    <div class="col-md-3 mb-3">
        <div class="card stats-card info h-100">
            <div class="card-body text-center">
                <i class="bi bi-currency-dollar text-info mb-2" style="font-size: 2rem;"></i>
                <div class="stats-number text-info">
                    R$ <?= number_format($stats['pedidos']['valor_total'] ?? 0, 2, ',', '.') ?>
                </div>
                <h6 class="card-title text-muted">Faturamento</h6>
            </div>
        </div>
    </div>
</div>

<!-- Order Status Overview -->
<?php if (!empty($stats['pedidos'])): ?>
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-pie-chart"></i> Status dos Pedidos</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 col-md-3 mb-2">
                        <div class="badge bg-warning text-dark fs-6"><?= $stats['pedidos']['pendentes'] ?? 0 ?></div>
                        <small class="d-block text-muted">Pendentes</small>
                    </div>
                    <div class="col-6 col-md-3 mb-2">
                        <div class="badge bg-info fs-6"><?= $stats['pedidos']['processando'] ?? 0 ?></div>
                        <small class="d-block text-muted">Processando</small>
                    </div>
                    <div class="col-6 col-md-3 mb-2">
                        <div class="badge bg-success fs-6"><?= $stats['pedidos']['concluidos'] ?? 0 ?></div>
                        <small class="d-block text-muted">Concluídos</small>
                    </div>
                    <div class="col-6 col-md-3 mb-2">
                        <div class="badge bg-danger fs-6"><?= $stats['pedidos']['cancelados'] ?? 0 ?></div>
                        <small class="d-block text-muted">Cancelados</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-exclamation-triangle"></i> Estoque Baixo</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($stats['estoque_baixo'])): ?>
                    <div class="list-group list-group-flush">
                        <?php foreach (array_slice($stats['estoque_baixo'], 0, 5) as $item): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <strong><?= htmlspecialchars($item['produto_nome']) ?></strong>
                                    <?php if ($item['variacao_id']): ?>
                                        <br><small class="text-muted"><?= htmlspecialchars($item['variacao_id']) ?></small>
                                    <?php endif; ?>
                                </div>
                                <span class="badge bg-danger"><?= $item['quantidade'] ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center">Nenhum item com estoque baixo</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Recent Products and Orders -->
<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-box"></i> Produtos Recentes</h5>
                <a href="<?php echo BASE_URL;?>/?p=product&a=index" class="btn btn-sm btn-outline-primary">
                    Ver todos
                </a>
            </div>
            <div class="card-body">
                <?php if (!empty($recentProducts)): ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recentProducts as $product): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <strong><?= htmlspecialchars($product['nome']) ?></strong>
                                    <br><small class="text-muted">Estoque: <?= $product['estoque_total'] ?? 0 ?></small>
                                </div>
                                <span class="price-tag">R$ <?= number_format($product['preco'], 2, ',', '.') ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center">Nenhum produto cadastrado</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-receipt"></i> Pedidos Recentes</h5>
                <a href="<?php echo BASE_URL;?>/?p=order&a=index" class="btn btn-sm btn-outline-primary">
                    Ver todos
                </a>
            </div>
            <div class="card-body">
                <?php if (!empty($recentOrders)): ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recentOrders as $order): ?>
                            <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <div>
                                    <strong>Pedido #<?= $order['id'] ?></strong>
                                    <br><small class="text-muted"><?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></small>
                                </div>
                                <div class="text-end">
                                    <div class="price-tag">R$ <?= number_format($order['total'], 2, ',', '.') ?></div>
                                    <span class="badge bg-<?= 
                                        $order['status'] === 'concluido' ? 'success' : 
                                        ($order['status'] === 'cancelado' ? 'danger' : 
                                        ($order['status'] === 'processando' ? 'info' : 'warning')) 
                                    ?>">
                                        <?= ucfirst($order['status']) ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center">Nenhum pedido encontrado</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mt-4">
    <div class="col">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-lightning"></i> Ações Rápidas</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3 mb-3">
                        <a href="<?php echo BASE_URL;?>/?p=product&a=create" class="btn btn-primary btn-lg w-100">
                            <i class="bi bi-plus-circle"></i><br>
                            <small>Novo Produto</small>
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="<?php echo BASE_URL;?>/?p=home&a=catalog" class="btn btn-success btn-lg w-100">
                            <i class="bi bi-grid"></i><br>
                            <small>Ver Catálogo</small>
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="<?php echo BASE_URL;?>/?p=order&a=index" class="btn btn-info btn-lg w-100">
                            <i class="bi bi-receipt"></i><br>
                            <small>Ver Pedidos</small>
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="<?php echo BASE_URL;?>/?p=coupon&a=create" class="btn btn-warning btn-lg w-100">
                            <i class="bi bi-ticket-perforated"></i><br>
                            <small>Novo Cupom</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();
include __DIR__ . '/../layout.php';
?>

