<?php ob_start(); ?>

<div class="row mb-4">
    <div class="col-md-8">
        <h1 class="text-gradient">
            <i class="bi bi-ticket-perforated"></i> Gerenciar Cupons
        </h1>
        <p class="text-muted">Gerencie cupons de desconto e promoções</p>
    </div>
    <div class="col-md-4 text-md-end">
        <a href="<?php echo BASE_URL;?>/?p=coupon&a=create" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Novo Cupom
        </a>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card stats-card">
            <div class="card-body text-center">
                <i class="bi bi-ticket-perforated text-primary mb-2" style="font-size: 2rem;"></i>
                <div class="stats-number"><?= $stats['total_cupons'] ?? 0 ?></div>
                <h6 class="card-title text-muted">Total de Cupons</h6>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card success">
            <div class="card-body text-center">
                <i class="bi bi-check-circle text-success mb-2" style="font-size: 2rem;"></i>
                <div class="stats-number text-success"><?= $stats['ativos'] ?? 0 ?></div>
                <h6 class="card-title text-muted">Ativos</h6>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card warning">
            <div class="card-body text-center">
                <i class="bi bi-pause-circle text-warning mb-2" style="font-size: 2rem;"></i>
                <div class="stats-number text-warning"><?= $stats['inativos'] ?? 0 ?></div>
                <h6 class="card-title text-muted">Inativos</h6>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card danger">
            <div class="card-body text-center">
                <i class="bi bi-x-circle text-danger mb-2" style="font-size: 2rem;"></i>
                <div class="stats-number text-danger"><?= $stats['expirados'] ?? 0 ?></div>
                <h6 class="card-title text-muted">Expirados</h6>
            </div>
        </div>
    </div>
</div>

<!-- Active Coupons -->
<?php if (!empty($activeCoupons)): ?>
    <div class="card mb-4">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0 text-white">
                        <i class="bi bi-check-circle"></i> Cupons Ativos
                    </h5>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Buscar cupons..." id="search-active">
                        <button class="btn btn-outline-light" type="button">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="active-coupons-table">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Desconto</th>
                        <th>Tipo</th>
                        <th>Valor Mínimo</th>
                        <th>Validade</th>
                        <th>Status</th>
                        <th width="200">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($allCoupons as $coupon): ?>
                        <tr>
                            <td>
                                <strong class="text-primary"><?= htmlspecialchars($coupon['codigo']) ?></strong>
                            </td>
                            <td>
                                <?php if ($coupon['tipo_desconto'] === 'percentual'): ?>
                                    <span class="badge bg-info"><?= $coupon['desconto'] ?>%</span>
                                <?php else: ?>
                                    <span class="badge bg-success">R$ <?= number_format($coupon['desconto'], 2, ',', '.') ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <?= $coupon['tipo_desconto'] === 'percentual' ? 'Percentual' : 'Valor Fixo' ?>
                                </small>
                            </td>
                            <td>
                                <?php if ($coupon['valor_minimo'] > 0): ?>
                                    R$ <?= number_format($coupon['valor_minimo'], 2, ',', '.') ?>
                                <?php else: ?>
                                    <span class="text-muted">Sem mínimo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php 
                                $validade = new DateTime($coupon['validade']);
                                $hoje = new DateTime();
                                $diff = $hoje->diff($validade);
                                ?>
                                <div>
                                    <?= $validade->format('d/m/Y') ?>
                                    <?php if ($validade > $hoje): ?>
                                        <br><small class="text-success">
                                            <?= $diff->days ?> dia(s) restante(s)
                                        </small>
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-<?= $coupon['ativo'] ? 'success' : 'secondary' ?>">
                                    <?= $coupon['ativo'] ? 'Ativo' : 'Inativo' ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="<?php echo BASE_URL;?>/?p=coupon&a=edit&id=<?= $coupon['id'] ?>" 
                                       class="btn btn-outline-primary" 
                                       data-bs-toggle="tooltip" 
                                       title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-outline-warning" 
                                            data-bs-toggle="tooltip" 
                                            title="Ativar/Desativar"
                                            onclick="toggleCoupon(<?= $coupon['id'] ?>)">
                                        <i class="bi bi-power"></i>
                                    </button>
                                    <button type="button" 
                                            class="btn btn-outline-danger btn-delete" 
                                            data-bs-toggle="tooltip" 
                                            title="Excluir"
                                            onclick="deleteCoupon(<?= $coupon['id'] ?>)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<!-- Expired Coupons -->
<?php if (!empty($expiredCoupons)): ?>
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0 text-muted">
                <i class="bi bi-clock-history"></i> Cupons Expirados
            </h5>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Desconto</th>
                        <th>Tipo</th>
                        <th>Expirou em</th>
                        <th width="100">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (array_slice($expiredCoupons, 0, 10) as $coupon): ?>
                        <tr class="text-muted">
                            <td>
                                <del><?= htmlspecialchars($coupon['codigo']) ?></del>
                            </td>
                            <td>
                                <?php if ($coupon['tipo_desconto'] === 'percentual'): ?>
                                    <?= $coupon['desconto'] ?>%
                                <?php else: ?>
                                    R$ <?= number_format($coupon['desconto'], 2, ',', '.') ?>
                                <?php endif; ?>
                            </td>
                            <td>
                                <small><?= $coupon['tipo_desconto'] === 'percentual' ? 'Percentual' : 'Valor Fixo' ?></small>
                            </td>
                            <td>
                                <?= date('d/m/Y', strtotime($coupon['validade'])) ?>
                            </td>
                            <td>
                                <button type="button" 
                                        class="btn btn-outline-danger btn-sm" 
                                        onclick="deleteCoupon(<?= $coupon['id'] ?>)"
                                        data-bs-toggle="tooltip" 
                                        title="Excluir">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <?php if (count($expiredCoupons) > 10): ?>
            <div class="card-footer text-center">
                <small class="text-muted">
                    Mostrando 10 de <?= count($expiredCoupons) ?> cupons expirados
                </small>
            </div>
        <?php endif; ?>
    </div>
<?php endif; ?>

<!-- Empty State -->
<?php if (empty($activeCoupons) && empty($expiredCoupons)): ?>
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-ticket-perforated text-muted" style="font-size: 4rem;"></i>
            <h3 class="mt-3 text-muted">Nenhum cupom cadastrado</h3>
            <p class="text-muted">Comece criando seu primeiro cupom de desconto.</p>
            <a href="<?php echo BASE_URL;?>/?p=coupon&a=create" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Criar Primeiro Cupom
            </a>
        </div>
    </div>
<?php endif; ?>

<?php 
$content = ob_get_clean();

// Add custom scripts
$scripts = '
<script>
function deleteCoupon(id) {
    if (confirm("Tem certeza que deseja excluir este cupom?")) {
        fetch(BASE_URL + "/?p=coupon&a=delete&id=" + id, {
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
            showAlert("Erro ao excluir cupom", "danger");
            console.error("Error:", error);
        });
    }
}

function toggleCoupon(id) {
    if (confirm("Tem certeza que deseja alterar o status deste cupom?")) {
        fetch(BASE_URL + "/?p=coupon&a=toggle&id=" + id, {
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
            showAlert("Erro ao alterar status", "danger");
            console.error("Error:", error);
        });
    }
}

// Simple table search
document.getElementById("search-active")?.addEventListener("input", function() {
    const searchTerm = this.value.toLowerCase();
    const tableRows = document.querySelectorAll("#active-coupons-table tbody tr");
    
    tableRows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? "" : "none";
    });
});
</script>
';

include __DIR__ . '/../layout.php';
?>

