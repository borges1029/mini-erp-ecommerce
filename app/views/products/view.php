<?php ob_start(); ?>

<div class="row mb-4">
    <div class="col-md-8">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/mini-erp/public/index.php">Home</a></li>
                <li class="breadcrumb-item"><a href="<?php echo BASE_URL;?>/?p=home&a=catalog">Catálogo</a></li>
                <li class="breadcrumb-item active"><?= htmlspecialchars($product['nome']) ?></li>
            </ol>
        </nav>
    </div>
    <div class="col-md-4 text-md-end">
        <a href="<?php echo BASE_URL;?>/?p=home&a=catalog" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Voltar ao Catálogo
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <!-- Product Image -->
        <div class="card mb-4">
            <div class="product-image" style="height: 400px;">
                <i class="bi bi-box" style="font-size: 6rem;"></i>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <!-- Product Information -->
        <div class="card">
            <div class="card-body">
                <h1 class="card-title"><?= htmlspecialchars($product['nome']) ?></h1>
                
                <div class="mb-3">
                    <span class="price-tag" style="font-size: 2rem;">
                        R$ <?= number_format($product['preco'], 2, ',', '.') ?>
                    </span>
                </div>
                
                <!-- Stock Information -->
                <div class="mb-4">
                    <?php 
                    $totalStock = 0;
                    if (!empty($product['variacoes_estoque'])) {
                        foreach ($product['variacoes_estoque'] as $stock) {
                            $totalStock += $stock['quantidade'];
                        }
                    }
                    ?>
                    
                    <?php if ($totalStock > 0): ?>
                        <div class="alert alert-success alert-permanent">
                            <i class="bi bi-check-circle"></i>
                            <strong>Em estoque:</strong> <?= $totalStock ?> unidade(s) disponível(is)
                        </div>
                    <?php else: ?>
                        <div class="alert alert-danger alert-permanent">
                            <i class="bi bi-x-circle"></i>
                            <strong>Produto indisponível</strong>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Variations -->
                <?php if (!empty($product['variacoes']) && !empty($product['variacoes_estoque'])): ?>
                    <div class="mb-4">
                        <h5>Variações Disponíveis:</h5>
                        <form id="purchase-form">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                            
                            <div class="mb-3">
                                <label class="form-label">Selecione a variação:</label>
                                <select class="form-select" name="variation_id" id="variation-select" required>
                                    <option value="">Escolha uma opção...</option>
                                    <?php foreach ($product['variacoes_estoque'] as $stock): ?>
                                        <?php if ($stock['quantidade'] > 0): ?>
                                            <option value="<?= htmlspecialchars($stock['variacao_id']) ?>" 
                                                    data-stock="<?= $stock['quantidade'] ?>">
                                                <?= htmlspecialchars($stock['variacao_id']) ?> 
                                                (<?= $stock['quantidade'] ?> disponível)
                                            </option>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">Quantidade:</label>
                                <div class="input-group" style="max-width: 150px;">
                                    <button type="button" class="btn btn-outline-secondary" onclick="changeQuantity(-1)">-</button>
                                    <input type="number" 
                                           class="form-control text-center" 
                                           name="quantity" 
                                           id="quantity" 
                                           value="1" 
                                           min="1" 
                                           max="1">
                                    <button type="button" class="btn btn-outline-secondary" onclick="changeQuantity(1)">+</button>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="button" 
                                        class="btn btn-primary btn-lg" 
                                        onclick="addToCart()" 
                                        <?= $totalStock <= 0 ? 'disabled' : '' ?>>
                                    <i class="bi bi-cart-plus"></i> Adicionar ao Carrinho
                                </button>
                                <a href="<?php echo BASE_URL;?>/?p=cart&a=viewCart" class="btn btn-outline-primary">
                                    <i class="bi bi-cart"></i> Ver Carrinho
                                </a>
                            </div>
                        </form>
                    </div>
                    
                <?php elseif ($totalStock > 0): ?>
                    <!-- Simple product without variations -->
                    <div class="mb-4">
                        <form id="purchase-form">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                            <input type="hidden" name="variation_id" value="">
                            
                            <div class="mb-3">
                                <label class="form-label">Quantidade:</label>
                                <div class="input-group" style="max-width: 150px;">
                                    <button type="button" class="btn btn-outline-secondary" onclick="changeQuantity(-1)">-</button>
                                    <input type="number" 
                                           class="form-control text-center" 
                                           name="quantity" 
                                           id="quantity" 
                                           value="1" 
                                           min="1" 
                                           max="<?= $totalStock ?>">
                                    <button type="button" class="btn btn-outline-secondary" onclick="changeQuantity(1)">+</button>
                                </div>
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="button" class="btn btn-primary btn-lg" onclick="addToCart()">
                                    <i class="bi bi-cart-plus"></i> Adicionar ao Carrinho
                                </button>
                                <a href="<?php echo BASE_URL;?>/?p=cart&a=viewCart" class="btn btn-outline-primary">
                                    <i class="bi bi-cart"></i> Ver Carrinho
                                </a>
                            </div>
                        </form>
                    </div>
                <?php endif; ?>
                
                <!-- Product Details -->
                <div class="mt-4">
                    <h5>Detalhes do Produto:</h5>
                    <ul class="list-unstyled">
                        <li><strong>ID:</strong> #<?= $product['id'] ?></li>
                        <li><strong>Cadastrado em:</strong> <?= date('d/m/Y H:i', strtotime($product['created_at'])) ?></li>
                        <?php if ($product['updated_at'] !== $product['created_at']): ?>
                            <li><strong>Atualizado em:</strong> <?= date('d/m/Y H:i', strtotime($product['updated_at'])) ?></li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stock Details -->
<?php if (!empty($product['variacoes_estoque'])): ?>
    <div class="row mt-4">
        <div class="col">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-box-seam"></i> Detalhes do Estoque</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Variação</th>
                                <th>Quantidade</th>
                                <th>Status</th>
                                <th>Última Atualização</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($product['variacoes_estoque'] as $stock): ?>
                                <tr>
                                    <td>
                                        <?= $stock['variacao_id'] ? htmlspecialchars($stock['variacao_id']) : '<em>Produto simples</em>' ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $stock['quantidade'] > 10 ? 'success' : ($stock['quantidade'] > 0 ? 'warning' : 'danger') ?>">
                                            <?= $stock['quantidade'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($stock['quantidade'] > 10): ?>
                                            <span class="text-success"><i class="bi bi-check-circle"></i> Disponível</span>
                                        <?php elseif ($stock['quantidade'] > 0): ?>
                                            <span class="text-warning"><i class="bi bi-exclamation-triangle"></i> Estoque baixo</span>
                                        <?php else: ?>
                                            <span class="text-danger"><i class="bi bi-x-circle"></i> Indisponível</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <?= date('d/m/Y H:i', strtotime($stock['updated_at'])) ?>
                                        </small>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php 
$content = ob_get_clean();

// Add custom scripts
$scripts = '
<script>
// Update quantity limits when variation changes
document.getElementById("variation-select")?.addEventListener("change", function() {
    const selectedOption = this.options[this.selectedIndex];
    const maxStock = selectedOption.dataset.stock || 1;
    const quantityInput = document.getElementById("quantity");
    
    quantityInput.max = maxStock;
    if (parseInt(quantityInput.value) > parseInt(maxStock)) {
        quantityInput.value = maxStock;
    }
});

function changeQuantity(delta) {
    const quantityInput = document.getElementById("quantity");
    const currentValue = parseInt(quantityInput.value);
    const minValue = parseInt(quantityInput.min);
    const maxValue = parseInt(quantityInput.max);
    
    const newValue = currentValue + delta;
    
    if (newValue >= minValue && newValue <= maxValue) {
        quantityInput.value = newValue;
    }
}

function addToCart() {
    const form = document.getElementById("purchase-form");
    const formData = new FormData(form);
    
    // Validate variation selection if required
    const variationSelect = document.getElementById("variation-select");
    if (variationSelect && !variationSelect.value) {
        showAlert("Por favor, selecione uma variação", "warning");
        return;
    }
    
    // Show loading state
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = \'<span class="loading"></span> Adicionando...\';
    button.disabled = true;
    
    fetch(BASE_URL + "/?p=cart&a=add", {
        method: "POST",
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        button.innerHTML = originalText;
        button.disabled = false;
        
        if (data.success) {
            showAlert(data.message, "success");
            updateCartCount();
        } else {
            showAlert(data.message, "danger");
        }
    })
    .catch(error => {
        button.innerHTML = originalText;
        button.disabled = false;
        showAlert("Erro ao adicionar ao carrinho", "danger");
        console.error("Error:", error);
    });
}
</script>
';

include __DIR__ . '/../layout.php';
?>

