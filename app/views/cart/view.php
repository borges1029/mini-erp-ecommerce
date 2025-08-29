<?php ob_start(); ?>

<div class="row mb-4">
    <div class="col-md-8">
        <h1 class="text-gradient">
            <i class="bi bi-cart"></i> Carrinho de Compras
        </h1>
        <p class="text-muted">Revise seus itens antes de finalizar a compra</p>
    </div>
    <div class="col-md-4 text-md-end">
        <a href="<?php echo BASE_URL;?>/?p=home&a=catalog" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Continuar Comprando
        </a>
    </div>
</div>

<?php if (!empty($cartItems)): ?>
    <div class="row">
        <div class="col-md-8">
            <!-- Cart Items -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Itens do Carrinho</h5>
                    <button type="button" class="btn btn-sm btn-danger" onclick="clearCart()">
                        <i class="bi bi-trash"></i> Limpar Carrinho
                    </button>
                </div>
                <div class="card-body p-0">
                    <?php foreach ($cartItems as $index => $item): ?>
                        <div class="cart-item p-3 <?= $index < count($cartItems) - 1 ? 'border-bottom' : '' ?>">
                            <div class="row align-items-center">
                                <div class="col-md-2">
                                    <div class="product-image" style="height: 80px; width: 80px;">
                                        <i class="bi bi-box" style="font-size: 2rem;"></i>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <h6 class="mb-1"><?= htmlspecialchars($item['product']['nome']) ?></h6>
                                    <?php if ($item['variation_id']): ?>
                                        <small class="text-muted">Variação: <?= htmlspecialchars($item['variation_id']) ?></small>
                                    <?php endif; ?>
                                    <div class="price-tag">R$ <?= number_format($item['product']['preco'], 2, ',', '.') ?></div>
                                </div>
                                <div class="col-md-3">
                                    <div class="input-group">
                                        <button type="button" 
                                                class="btn btn-outline-secondary btn-sm" 
                                                onclick="updateQuantity(<?= $item['product']['id'] ?>, '<?= $item['variation_id'] ?>', <?= $item['quantity'] - 1 ?>)">
                                            -
                                        </button>
                                        <input type="number" 
                                               class="form-control form-control-sm text-center" 
                                               value="<?= $item['quantity'] ?>" 
                                               min="1" 
                                               onchange="updateQuantity(<?= $item['product']['id'] ?>, '<?= $item['variation_id'] ?>', this.value)">
                                        <button type="button" 
                                                class="btn btn-outline-secondary btn-sm" 
                                                onclick="updateQuantity(<?= $item['product']['id'] ?>, '<?= $item['variation_id'] ?>', <?= $item['quantity'] + 1 ?>)">
                                            +
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-2 text-end">
                                    <div class="price-tag">R$ <?= number_format($item['subtotal'], 2, ',', '.') ?></div>
                                </div>
                                <div class="col-md-1 text-end">
                                    <button type="button" 
                                            class="btn btn-outline-danger btn-sm" 
                                            onclick="removeItem(<?= $item['product']['id'] ?>, '<?= $item['variation_id'] ?>')"
                                            data-bs-toggle="tooltip" 
                                            title="Remover item">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Coupon Section -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-ticket-perforated"></i> Cupom de Desconto</h5>
                </div>
                <div class="card-body">
                    <?php if ($appliedCoupon): ?>
                        <div class="alert alert-success alert-permanent d-flex justify-content-between align-items-center">
                            <div>
                                <i class="bi bi-check-circle"></i>
                                <strong>Cupom aplicado:</strong> <?= htmlspecialchars($appliedCoupon['codigo']) ?>
                                <br><small>Desconto: R$ <?= number_format($discount, 2, ',', '.') ?></small>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeCoupon()">
                                <i class="bi bi-x"></i> Remover
                            </button>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <div class="col-md-8">
                                <input type="text" 
                                       class="form-control" 
                                       id="coupon-code" 
                                       placeholder="Digite o código do cupom">
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn-primary w-100" onclick="applyCoupon()">
                                    <i class="bi bi-check"></i> Aplicar Cupom
                                </button>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- Order Summary -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-receipt"></i> Resumo do Pedido</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span id="subtotal" data-value="<?= $subtotal ?>">R$ <?= number_format($subtotal, 2, ',', '.') ?></span>
                    </div>
                    
                    <?php if ($discount > 0): ?>
                        <div class="d-flex justify-content-between mb-2 text-success">
                            <span>Desconto:</span>
                            <span>- R$ <?= number_format($discount, 2, ',', '.') ?></span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span>Frete:</span>
                        <span class="<?= $shipping == 0 ? 'text-success' : '' ?>">
                            <?= $shipping == 0 ? 'GRÁTIS' : 'R$ ' . number_format($shipping, 2, ',', '.') ?>
                        </span>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <strong>Total:</strong>
                        <strong class="price-tag" style="font-size: 1.25rem;">
                            R$ <?= number_format($total, 2, ',', '.') ?>
                        </strong>
                    </div>
                    
                    <!-- Shipping Info -->
                    <div class="alert alert-info small alert-permanent">
                        <i class="bi bi-info-circle"></i>
                        <strong>Frete:</strong><br>
                        • Grátis para compras acima de R$ 200,00<br>
                        • R$ 15,00 para compras entre R$ 52,00 e R$ 166,59<br>
                        • R$ 20,00 para outros valores
                    </div>
                    
                    <div class="d-grid gap-2">
                        <a href="<?php echo BASE_URL;?>/?p=checkout&a=index" class="btn btn-primary btn-lg">
                            <i class="bi bi-credit-card"></i> Finalizar Compra
                        </a>
                        <a href="<?php echo BASE_URL;?>/?p=home&a=catalog" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Continuar Comprando
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
<?php else: ?>
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-cart-x text-muted" style="font-size: 4rem;"></i>
                    <h3 class="mt-3 text-muted">Seu carrinho está vazio</h3>
                    <p class="text-muted">Adicione produtos ao seu carrinho para continuar.</p>
                    <a href="<?php echo BASE_URL;?>/?p=home&a=catalog" class="btn btn-primary">
                        <i class="bi bi-grid"></i> Ver Catálogo
                    </a>
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
function updateQuantity(productId, variationId, quantity) {
    if (quantity < 1) {
        removeItem(productId, variationId);
        return;
    }
    
    const formData = new FormData();
    formData.append("product_id", productId);
    formData.append("variation_id", variationId);
    formData.append("quantity", quantity);
    
    fetch(BASE_URL + "/?p=cart&a=update", {
        method: "POST",
        body: formData
    })
    .catch(error => {
        showAlert("Erro ao atualizar carrinho", "danger");
        console.error("Error:", error);
    });
}

function removeItem(productId, variationId) {
    if (confirm("Tem certeza que deseja remover este item?")) {
        const formData = new FormData();
        formData.append("product_id", productId);
        formData.append("variation_id", variationId);
        
        fetch(BASE_URL + "/?p=cart&a=remove", {
            method: "POST",
            body: formData
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
            showAlert("Erro ao remover item", "danger");
            console.error("Error:", error);
        });
    }
}

function clearCart() {
    if (confirm("Tem certeza que deseja limpar todo o carrinho?")) {
        fetch(BASE_URL + "/?p=cart&a=clear", {
            method: "POST"
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
            showAlert("Erro ao limpar carrinho", "danger");
            console.error("Error:", error);
        });
    }
}

function applyCoupon() {
    const couponCode = document.getElementById("coupon-code").value.trim();
    
    if (!couponCode) {
        showAlert("Digite o código do cupom", "warning");
        return;
    }
    
    const formData = new FormData();
    formData.append("coupon_code", couponCode);
    
    fetch(BASE_URL + "/?p=cart&a=applyCoupon", {
        method: "POST",
        body: formData
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
        showAlert("Erro ao aplicar cupom", "danger");
        console.error("Error:", error);
    });
}

function removeCoupon() {
    fetch(BASE_URL + "/?p=cart&a=removeCoupon", {
        method: "POST"
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
        showAlert("Erro ao remover cupom", "danger");
        console.error("Error:", error);
    });
}
</script>
';

include __DIR__ . '/../layout.php';
?>

