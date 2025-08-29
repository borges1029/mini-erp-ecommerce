<?php ob_start(); ?>

<div class="row mb-4">
    <div class="col-md-8">
        <h1 class="text-gradient">
            <i class="bi bi-box-seam"></i> Gerenciar Produtos
        </h1>
        <p class="text-muted">Gerencie produtos, variações e estoque</p>
    </div>
    <div class="col-md-4 text-md-end">
        <a href="<?php echo BASE_URL;?>/?p=product&a=create" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Novo Produto
        </a>
    </div>
</div>

<?php if (!empty($products)): ?>
    <div class="card">
        <div class="card-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h5 class="mb-0">Lista de Produtos</h5>
                </div>
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Buscar produtos..." id="search-table">
                        <button class="btn btn-outline-secondary" type="button">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Preço</th>
                        <th>Estoque Total</th>
                        <th>Variações</th>
                        <th>Criado em</th>
                        <th width="200">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td>
                                <strong>#<?= $product['id'] ?></strong>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($product['nome']) ?></strong>
                            </td>
                            <td>
                                <span class="price-tag">R$ <?= number_format($product['preco'], 2, ',', '.') ?></span>
                            </td>
                            <td>
                                <span class="badge bg-<?= $product['estoque_total'] > 10 ? 'success' : ($product['estoque_total'] > 0 ? 'warning' : 'danger') ?>">
                                    <?= $product['estoque_total'] ?? 0 ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($product['variacoes_count'] > 0): ?>
                                    <span class="badge bg-info"><?= $product['variacoes_count'] ?> variação(ões)</span>
                                <?php else: ?>
                                    <span class="text-muted">Sem variações</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <small class="text-muted">
                                    <?= date('d/m/Y', strtotime($product['created_at'])) ?>
                                </small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="<?php echo BASE_URL;?>/?p=product&a=viewProduct&id=<?= $product['id'] ?>" 
                                       class="btn btn-outline-info" 
                                       data-bs-toggle="tooltip" 
                                       title="Ver Produto">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="<?php echo BASE_URL;?>/?p=product&a=edit&id=<?= $product['id'] ?>" 
                                       class="btn btn-outline-primary" 
                                       data-bs-toggle="tooltip" 
                                       title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-outline-danger btn-delete" 
                                            data-bs-toggle="tooltip" 
                                            title="Excluir"
                                            onclick="deleteProduct(<?= $product['id'] ?>)">
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
                        Mostrando <?= count($products) ?> produto(s)
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
            <i class="bi bi-box text-muted" style="font-size: 4rem;"></i>
            <h3 class="mt-3 text-muted">Nenhum produto cadastrado</h3>
            <p class="text-muted">Comece criando seu primeiro produto.</p>
            <a href="<?php echo BASE_URL;?>/?p=product&a=create" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Cadastrar Primeiro Produto
            </a>
        </div>
    </div>
<?php endif; ?>

<!-- Statistics Cards -->
<div class="row mt-4">
    <div class="col-md-3">
        <div class="card stats-card">
            <div class="card-body text-center">
                <i class="bi bi-box-seam text-primary mb-2" style="font-size: 2rem;"></i>
                <div class="stats-number"><?= count($products) ?></div>
                <h6 class="card-title text-muted">Total de Produtos</h6>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card success">
            <div class="card-body text-center">
                <i class="bi bi-check-circle text-success mb-2" style="font-size: 2rem;"></i>
                <div class="stats-number text-success">
                    <?= count(array_filter($products, function($p) { return $p['estoque_total'] > 0; })) ?>
                </div>
                <h6 class="card-title text-muted">Com Estoque</h6>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card warning">
            <div class="card-body text-center">
                <i class="bi bi-exclamation-triangle text-warning mb-2" style="font-size: 2rem;"></i>
                <div class="stats-number text-warning">
                    <?= count(array_filter($products, function($p) { return $p['estoque_total'] <= 10 && $p['estoque_total'] > 0; })) ?>
                </div>
                <h6 class="card-title text-muted">Estoque Baixo</h6>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stats-card danger">
            <div class="card-body text-center">
                <i class="bi bi-x-circle text-danger mb-2" style="font-size: 2rem;"></i>
                <div class="stats-number text-danger">
                    <?= count(array_filter($products, function($p) { return $p['estoque_total'] == 0; })) ?>
                </div>
                <h6 class="card-title text-muted">Sem Estoque</h6>
            </div>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();

// Add custom scripts
$scripts = '
<script>
function deleteProduct(id) {
    if (confirm("Tem certeza que deseja excluir este produto?")) {
        fetch(BASE_URL + "/?p=product&a=delete&id=" + id, {
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
            showAlert("Erro ao excluir produto", "danger");
            console.error("Error:", error);
        });
    }
}

// Simple table search
document.getElementById("search-table").addEventListener("input", function() {
    const searchTerm = this.value.toLowerCase();
    const tableRows = document.querySelectorAll("tbody tr");
    
    tableRows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? "" : "none";
    });
});
</script>
';

include __DIR__ . '/../layout.php';
?>

