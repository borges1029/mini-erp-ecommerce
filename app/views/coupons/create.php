<?php ob_start(); ?>

<div class="row mb-4">
    <div class="col-md-8">
        <h1 class="text-gradient">
            <i class="bi bi-<?php echo isset($coupon) ? 'pencil-square' : 'plus-circle'; ?>"></i>
            <?php echo isset($coupon) ? 'Editar Cupom' : 'Criar Cupom'; ?>
        </h1>
        <p class="text-muted">
            <?php echo isset($coupon) ? 'Atualize os dados do cupom de desconto' : 'Cadastre um novo cupom de desconto'; ?>
        </p>
    </div>
    <div class="col-md-4 text-md-end">
        <a href="<?php echo BASE_URL;?>/?p=coupon&a=index" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
    </div>
</div>

<form method="POST" action="<?php echo BASE_URL;?>/?p=coupon&a=<?php echo isset($coupon) ? 'edit&id=' . $coupon['id'] : 'create'; ?>">
    <div class="row">
        <div class="col-md-8">
            <!-- Coupon Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Informações do Cupom</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="codigo" class="form-label">Código do Cupom *</label>
                            <input type="text"
                                   class="form-control"
                                   id="codigo"
                                   name="codigo"
                                   required
                                   placeholder="Ex: DESCONTO10"
                                   value="<?php echo isset($coupon) ? $coupon['codigo'] : ''; ?>"
                                   style="text-transform: uppercase;">
                            <div class="form-text">
                                Use apenas letras, números e underscore. Será convertido para maiúsculas.
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tipo_desconto" class="form-label">Tipo de Desconto *</label>
                            <select class="form-select" id="tipo_desconto" name="tipo_desconto" required>
                                <option value="">Selecione o tipo</option>
                                <option value="valor" <?php echo (isset($coupon) && $coupon['tipo_desconto'] === 'valor') ? 'selected' : ''; ?>>Valor Fixo (R$)</option>
                                <option value="percentual" <?php echo (isset($coupon) && $coupon['tipo_desconto'] === 'percentual') ? 'selected' : ''; ?>>Percentual (%)</option>
                            </select>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="desconto" class="form-label">Valor do Desconto *</label>
                            <div class="input-group">
                                <span class="input-group-text" id="desconto-prefix">R$</span>
                                <input type="number"
                                       class="form-control"
                                       id="desconto"
                                       name="desconto"
                                       step="0.01"
                                       min="0"
                                       required
                                       placeholder="0,00"
                                       value="<?php echo isset($coupon) ? $coupon['desconto'] : ''; ?>">
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="valor_minimo" class="form-label">Valor Mínimo do Pedido</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="number"
                                       class="form-control"
                                       id="valor_minimo"
                                       name="valor_minimo"
                                       step="0.01"
                                       min="0"
                                       value="<?php echo isset($coupon) ? $coupon['valor_minimo'] : '0'; ?>"
                                       placeholder="0,00">
                            </div>
                            <div class="form-text">
                                Valor mínimo do pedido para aplicar o cupom. Deixe 0 para sem mínimo.
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="validade" class="form-label">Data de Validade *</label>
                            <input type="date"
                                   class="form-control"
                                   id="validade"
                                   name="validade"
                                   required
                                   min="<?= date('Y-m-d') ?>"
                                   value="<?php echo isset($coupon) ? $coupon['validade'] : ''; ?>">
                        </div>
                        <div class="col-md-6 mb-3 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input"
                                       type="checkbox"
                                       id="ativo"
                                       name="ativo"
                                    <?php echo (isset($coupon) && $coupon['ativo']) || !isset($coupon) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="ativo">
                                    <strong>Cupom ativo</strong>
                                </label>
                                <div class="form-text">
                                    Desmarque para criar o cupom como inativo
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preview (mantém igual) -->
            ...
        </div>

        <div class="col-md-4">
            <!-- Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-gear"></i> Ações</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> <?php echo isset($coupon) ? 'Atualizar Cupom' : 'Criar Cupom'; ?>
                        </button>
                        <a href="<?php echo BASE_URL;?>/?p=coupon&a=index" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </a>
                    </div>
                </div>
            </div>

            <!-- Help -->
            ...
        </div>
    </div>
</form>

<?php 
$content = ob_get_clean();

// Add custom scripts
$scripts = '
<script>
// Update preview in real-time
document.addEventListener("input", updatePreview);
document.addEventListener("change", updatePreview);

function updatePreview() {
    const codigo = document.getElementById("codigo").value.toUpperCase() || "CÓDIGO";
    const desconto = parseFloat(document.getElementById("desconto").value) || 0;
    const tipoDesconto = document.getElementById("tipo_desconto").value;
    const valorMinimo = parseFloat(document.getElementById("valor_minimo").value) || 0;
    const validade = document.getElementById("validade").value;
    
    // Update code
    document.getElementById("preview-code").textContent = codigo;
    
    // Update discount
    let discountText = "Desconto";
    if (desconto > 0) {
        if (tipoDesconto === "percentual") {
            discountText = desconto + "% de desconto";
        } else if (tipoDesconto === "valor") {
            discountText = "R$ " + desconto.toFixed(2).replace(".", ",") + " de desconto";
        }
    }
    document.getElementById("preview-discount").textContent = discountText;
    
    // Update minimum
    let minimumText = "Sem valor mínimo";
    if (valorMinimo > 0) {
        minimumText = "Mínimo: R$ " + valorMinimo.toFixed(2).replace(".", ",");
    }
    document.getElementById("preview-minimum").textContent = minimumText;
    
    // Update validity
    let validityText = "Válido até";
    if (validade) {
        const date = new Date(validade + "T00:00:00");
        validityText = "Válido até " + date.toLocaleDateString("pt-BR");
    }
    document.getElementById("preview-validity").textContent = validityText;
}

// Update discount prefix based on type
document.getElementById("tipo_desconto").addEventListener("change", function() {
    const prefix = document.getElementById("desconto-prefix");
    const input = document.getElementById("desconto");
    
    if (this.value === "percentual") {
        prefix.textContent = "%";
        input.max = "100";
        input.placeholder = "0";
    } else {
        prefix.textContent = "R$";
        input.removeAttribute("max");
        input.placeholder = "0,00";
    }
    
    updatePreview();
});

// Auto-uppercase code input
document.getElementById("codigo").addEventListener("input", function() {
    this.value = this.value.toUpperCase().replace(/[^A-Z0-9_]/g, "");
});

// Initialize preview
updatePreview();
</script>
';

include __DIR__ . '/../layout.php';
?>

