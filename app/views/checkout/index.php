<?php ob_start(); ?>

<div class="row mb-4">
    <div class="col">
        <h1 class="text-gradient">
            <i class="bi bi-credit-card"></i> Finalizar Compra
        </h1>
        <p class="text-muted">Preencha seus dados para concluir o pedido</p>
    </div>
</div>

<form method="POST" action="<?php echo BASE_URL;?>/?p=checkout&a=index" id="checkout-form">
    <div class="row">
        <div class="col-md-8">
            <!-- Customer Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-person"></i> Dados do Cliente</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" 
                                   class="form-control" 
                                   id="email" 
                                   name="email" 
                                   required 
                                   placeholder="seu@email.com">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="cep" class="form-label">CEP *</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="cep" 
                                   name="cep" 
                                   required 
                                   placeholder="00000-000"
                                   maxlength="9">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="endereco" class="form-label">Endereço Completo *</label>
                        <textarea class="form-control" 
                                  id="endereco" 
                                  name="endereco" 
                                  rows="3" 
                                  required 
                                  placeholder="Rua, número, complemento, bairro, cidade - UF"></textarea>
                        <div class="form-text">
                            O endereço será preenchido automaticamente após informar o CEP.
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Order Items -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-list-check"></i> Itens do Pedido</h5>
                </div>
                <div class="card-body p-0">
                    <?php foreach ($cartItems as $index => $item): ?>
                        <div class="p-3 <?= $index < count($cartItems) - 1 ? 'border-bottom' : '' ?>">
                            <div class="row align-items-center">
                                <div class="col-md-2">
                                    <div class="product-image" style="height: 60px; width: 60px;">
                                        <i class="bi bi-box" style="font-size: 1.5rem;"></i>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="mb-1"><?= htmlspecialchars($item['product']['nome']) ?></h6>
                                    <?php if ($item['variation_id']): ?>
                                        <small class="text-muted">Variação: <?= htmlspecialchars($item['variation_id']) ?></small><br>
                                    <?php endif; ?>
                                    <small class="text-muted">Quantidade: <?= $item['quantity'] ?></small>
                                </div>
                                <div class="col-md-2 text-center">
                                    <div class="price-tag">R$ <?= number_format($item['product']['preco'], 2, ',', '.') ?></div>
                                </div>
                                <div class="col-md-2 text-end">
                                    <div class="price-tag">R$ <?= number_format($item['subtotal'], 2, ',', '.') ?></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <!-- Order Summary -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-receipt"></i> Resumo do Pedido</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span>R$ <?= number_format($subtotal, 2, ',', '.') ?></span>
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
                    
                    <!-- Applied Coupon -->
                    <?php if ($appliedCoupon): ?>
                        <div class="alert alert-success small alert-permanent">
                            <i class="bi bi-check-circle"></i>
                            <strong>Cupom aplicado:</strong> <?= htmlspecialchars($appliedCoupon['codigo']) ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg" id="submit-btn">
                            <i class="bi bi-check-circle"></i> Confirmar Pedido
                        </button>
                        <a href="<?php echo BASE_URL;?>/?p=cart&a=viewCart" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Voltar ao Carrinho
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Security Info -->
            <div class="card">
                <div class="card-body">
                    <h6><i class="bi bi-shield-check text-success"></i> Compra Segura</h6>
                    <ul class="list-unstyled small text-muted mb-0">
                        <li><i class="bi bi-check text-success"></i> Dados protegidos</li>
                        <li><i class="bi bi-check text-success"></i> Transação segura</li>
                        <li><i class="bi bi-check text-success"></i> Confirmação por email</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</form>

<?php 
$content = ob_get_clean();

// Add custom scripts
$scripts = '
<script>
// CEP formatting and validation
document.getElementById("cep").addEventListener("input", function() {
    let value = this.value.replace(/\D/g, "");
    
    if (value.length <= 8) {
        value = value.replace(/^(\d{5})(\d)/, "$1-$2");
        this.value = value;
        
        // Auto-fill address when CEP is complete
        if (value.replace(/\D/g, "").length === 8) {
            fetchAddressByCEP(value.replace(/\D/g, ""));
        }
    }
});

// Fetch address by CEP
function fetchAddressByCEP(cep) {
    const cepInput = document.getElementById("cep");
    const enderecoInput = document.getElementById("endereco");
    
    // Show loading
    cepInput.classList.add("loading");
    
    fetch(`https://viacep.com.br/ws/${cep}/json/`)
        .then(response => response.json())
        .then(data => {
            cepInput.classList.remove("loading");
            
            if (data.erro) {
                showAlert("CEP não encontrado", "warning");
                return;
            }
            
            // Fill address fields
            const endereco = `${data.logradouro}, ${data.bairro}, ${data.localidade} - ${data.uf}`;
            enderecoInput.value = endereco;
            
//            showAlert("Endereço preenchido automaticamente", "success");
        })
        .catch(error => {
            cepInput.classList.remove("loading");
            showAlert("Erro ao buscar CEP", "warning");
            console.error("Error fetching CEP:", error);
        });
}

// Form submission
document.getElementById("checkout-form").addEventListener("submit", function(e) {
    const submitBtn = document.getElementById("submit-btn");
    const originalText = submitBtn.innerHTML;
    
    submitBtn.innerHTML = \'<span class="loading"></span> Processando...\';
    submitBtn.disabled = true;
    
    // Re-enable button after 10 seconds to prevent permanent lock
    setTimeout(() => {
        if (submitBtn.disabled) {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    }, 10000);
});

// Email validation
document.getElementById("email").addEventListener("blur", function() {
    const email = this.value;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    if (email && !emailRegex.test(email)) {
        this.classList.add("is-invalid");
        showAlert("Email inválido", "warning");
    } else {
        this.classList.remove("is-invalid");
    }
});
</script>
';

include __DIR__ . '/../layout.php';
?>

