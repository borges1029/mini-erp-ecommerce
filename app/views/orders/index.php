<?php ob_start(); ?>

<div class="row mb-4">
    <div class="col-md-8">
        <h1 class="text-gradient">
            <i class="bi bi-receipt"></i> Gerenciar Pedidos
        </h1>
        <p class="text-muted">Visualize e gerencie todos os pedidos do sistema</p>
    </div>
    <div class="col-md-4 text-md-end">
        <div class="btn-group" role="group">
            <a href="<?php echo BASE_URL;?>/?p=order&a=export&format=csv" class="btn btn-outline-success">
                <i class="bi bi-download"></i> Exportar CSV
            </a>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card stats-card">
            <div class="card-body text-center">
                <i class="bi bi-receipt text-primary mb-2" style="font-size: 2rem;"></i>
                <div class="stats-number"><?= $stats['total_pedidos'] ?? 0 ?></div>
                <h6 class="card-title text-muted">Total de Pedidos</h6>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card success">
            <div class="card-body text-center">
                <i class="bi bi-check-circle text-success mb-2" style="font-size: 2rem;"></i>
                <div class="stats-number text-success"><?= $stats['confirmados'] ?? 0 ?></div>
                <h6 class="card-title text-muted">Confirmados</h6>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card warning">
            <div class="card-body text-center">
                <i class="bi bi-clock text-warning mb-2" style="font-size: 2rem;"></i>
                <div class="stats-number text-warning"><?= $stats['pendentes'] ?? 0 ?></div>
                <h6 class="card-title text-muted">Pendentes</h6>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card info">
            <div class="card-body text-center">
                <i class="bi bi-currency-dollar text-info mb-2" style="font-size: 2rem;"></i>
                <div class="stats-number text-info">R$ <?= number_format($stats['total_vendas'] ?? 0, 0, ',', '.') ?></div>
                <h6 class="card-title text-muted">Total em Vendas</h6>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($orders)): ?>
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0">Lista de Pedidos</h5>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Buscar pedidos..." id="search-orders">
                        <button class="btn btn-outline-secondary" type="button">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="orders-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Data</th>
                        <th>Cliente</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Itens</th>
                        <th width="200">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>
                                <strong>#<?= $order['id'] ?></strong>
                            </td>
                            <td>
                                <div>
                                    <?= date('d/m/Y', strtotime($order['created_at'])) ?>
                                    <br><small class="text-muted"><?= date('H:i', strtotime($order['created_at'])) ?></small>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <?= htmlspecialchars($order['email']) ?>
                                    <br><small class="text-muted">CEP: <?= htmlspecialchars($order['cep']) ?></small>
                                </div>
                            </td>
                            <td>
                                <select class="form-select form-select-sm status-select" 
                                        data-order-id="<?= $order['id'] ?>"
                                        data-current-status="<?= $order['status'] ?>">
                                    <option value="pendente" <?= $order['status'] === 'pendente' ? 'selected' : '' ?>>Pendente</option>
                                    <option value="confirmado" <?= $order['status'] === 'confirmado' ? 'selected' : '' ?>>Confirmado</option>
                                    <option value="enviado" <?= $order['status'] === 'enviado' ? 'selected' : '' ?>>Enviado</option>
                                    <option value="entregue" <?= $order['status'] === 'entregue' ? 'selected' : '' ?>>Entregue</option>
                                    <option value="cancelado" <?= $order['status'] === 'cancelado' ? 'selected' : '' ?>>Cancelado</option>
                                </select>
                            </td>
                            <td>
                                <div>
                                    <strong class="price-tag">R$ <?= number_format($order['total'], 2, ',', '.') ?></strong>
                                    <?php if ($order['desconto'] > 0): ?>
                                        <br><small class="text-success">
                                            Desc: R$ <?= number_format($order['desconto'], 2, ',', '.') ?>
                                        </small>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-secondary"><?= $order['total_itens'] ?? 0 ?> item(s)</span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
<!--                                    <a href="--><?php //echo BASE_URL;?><!--/?p=order&a=viewOrder&id=--><?php //= $order['id'] ?><!--" -->
<!--                                       class="btn btn-outline-info" -->
<!--                                       data-bs-toggle="tooltip" -->
<!--                                       title="Ver Pedido">-->
<!--                                        <i class="bi bi-eye"></i>-->
<!--                                    </a>-->
                                    <button type="button" 
                                            class="btn btn-outline-primary" 
                                            data-bs-toggle="tooltip" 
                                            title="Enviar Email"
                                            onclick="sendOrderEmail(<?= $order['id'] ?>)">
                                        <i class="bi bi-envelope"></i>
                                    </button>
                                    <button type="button" 
                                            class="btn btn-outline-danger btn-delete" 
                                            data-bs-toggle="tooltip" 
                                            title="Excluir"
                                            onclick="deleteOrder(<?= $order['id'] ?>)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="card-footer">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <small class="text-muted">
                        Mostrando <?= count($orders) ?> pedido(s)
                    </small>
                </div>
                <div class="col-md-6 text-md-end">
                    <!-- Pagination would go here -->
                </div>
            </div>
        </div>
    </div>
    
<?php else: ?>
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-receipt text-muted" style="font-size: 4rem;"></i>
            <h3 class="mt-3 text-muted">Nenhum pedido encontrado</h3>
            <p class="text-muted">Os pedidos aparecerão aqui quando forem realizados.</p>
            <a href="<?php echo BASE_URL;?>/?p=home&a=catalog" class="btn btn-primary">
                <i class="bi bi-grid"></i> Ver Catálogo
            </a>
        </div>
    </div>
<?php endif; ?>

<!-- Webhook Test Modal -->
<div class="modal fade" id="webhookModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Testar Webhook</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Use este endpoint para testar atualizações de status via webhook:</p>
                <div class="alert alert-info alert-permanent">
                    <strong>URL:</strong> <code id="webhook-url"><?= $_SERVER['HTTP_HOST'] ?? 'localhost' ?><?php echo BASE_URL;?>/?p=webhook&a=orderStatus</code>
                </div>
                <p><strong>Método:</strong> POST</p>
                <p><strong>Payload JSON:</strong></p>
                <pre class="bg-light p-3 rounded"><code>{
  "order_id": 1,
  "status": "confirmado"
}</code></pre>
                <p><strong>Status aceitos:</strong> pendente, confirmado, enviado, entregue, cancelado</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" onclick="testWebhook()">Testar Webhook</button>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col text-center">
        <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#webhookModal">
            <i class="bi bi-webhook"></i> Informações do Webhook
        </button>
    </div>
</div>

<?php 
$content = ob_get_clean();

// Add custom scripts
$scripts = '
<script>
// Status change handler
document.addEventListener("change", function(e) {
    if (e.target.classList.contains("status-select")) {
        const orderId = e.target.dataset.orderId;
        const newStatus = e.target.value;
        const currentStatus = e.target.dataset.currentStatus;
        
        if (newStatus !== currentStatus) {
            updateOrderStatus(orderId, newStatus, e.target);
        }
    }
});

function updateOrderStatus(orderId, status, selectElement) {
    const formData = new FormData();
    formData.append("order_id", orderId);
    formData.append("status", status);
    
    fetch(BASE_URL + "/?p=order&a=updateStatus", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, "success");
            selectElement.dataset.currentStatus = status;
        } else {
            showAlert(data.message, "danger");
            // Revert selection
            selectElement.value = selectElement.dataset.currentStatus;
        }
    })
    .catch(error => {
        showAlert("Erro ao atualizar status", "danger");
        console.error("Error:", error);
        // Revert selection
        selectElement.value = selectElement.dataset.currentStatus;
    });
}

function deleteOrder(id) {
    if (confirm("Tem certeza que deseja excluir este pedido?")) {
        fetch(BASE_URL + "/?p=order&a=delete&id=" + id, {
            method: "GET"
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert(data.message, "success");
                setTimeout(() => location.reload(), 1000);
            } else {
                showAlert(data.message, "danger");
            }
        })
        .catch(error => {
            showAlert("Erro ao excluir pedido", "danger");
            console.error("Error:", error);
        });
    }
}

function sendOrderEmail(orderId) {
    fetch(BASE_URL + "/?p=email&a=resendEmail", {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            order_id: orderId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert("E-mail enviado com sucesso!", "success");
        } else {
            showAlert("Erro ao tentar enviar e-mail: " + data.message, "danger");
        }
    })
    .catch(error => {
        showAlert("Erro ao tentar enviar e-mail", "danger");
        console.error("Error:", error);
    });
}

function testWebhook() {
    const webhookUrl = "http://" + window.location.host + "<?php echo BASE_URL;?>/?p=webhook&a=test";
    
    fetch(webhookUrl, {
        method: "POST",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            test: true
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert("Webhook funcionando corretamente!", "success");
        } else {
            showAlert("Erro no webhook: " + data.message, "danger");
        }
    })
    .catch(error => {
        showAlert("Erro ao testar webhook", "danger");
        console.error("Error:", error);
    });
}

// Simple table search
document.getElementById("search-orders").addEventListener("input", function() {
    const searchTerm = this.value.toLowerCase();
    const tableRows = document.querySelectorAll("#orders-table tbody tr");
    
    tableRows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? "" : "none";
    });
});

// Update webhook URL
document.addEventListener("DOMContentLoaded", function() {
    const webhookUrl = document.getElementById("webhook-url");
    if (webhookUrl) {
        webhookUrl.textContent = "http://" + window.location.host + "<?php echo BASE_URL;?>/?p=webhook&a=orderStatus";
    }
});
</script>
';

include __DIR__ . '/../layout.php';
?>

