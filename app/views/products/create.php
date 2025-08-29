<?php ob_start(); ?>

<?php
// Detecta se está editando
$isEdit = isset($product) && !empty($product);
$formAction = BASE_URL . '/?p=product&a=' . ($isEdit ? 'edit&id=' . $product['id'] : 'create');
$title = $isEdit ? 'Editar Produto' : 'Criar Produto';
$subtitle = $isEdit ? 'Edite as informações do produto' : 'Cadastre um novo produto com variações e estoque';
$buttonText = $isEdit ? 'Salvar Alterações' : 'Criar Produto';
$icon = $isEdit ? 'bi-pencil-square' : 'bi-plus-circle';

$variacoesValue = '';
if ($isEdit && !empty($product['variacoes'])) {
    // Decodifica se for string JSON
    $decoded = is_array($product['variacoes'])
        ? $product['variacoes']
        : json_decode($product['variacoes'], true);

    if ($decoded !== null && is_array($decoded)) {
        $variacoesValue = "{\n";
        foreach ($decoded as $key => $values) {
            $variacoesValue .= '  "' . $key . '": ' . json_encode($values, JSON_UNESCAPED_UNICODE) . ",\n";
        }
        $variacoesValue = rtrim($variacoesValue, ",\n") . "\n}";
    } else {
        // Mantém como string bruta se não for JSON válido
        $variacoesValue = $product['variacoes'];
    }
}

?>

<div class="row mb-4">
    <div class="col-md-8">
        <h1 class="text-gradient">
            <i class="bi bi-plus-circle"></i> <?= $title ?>
        </h1>
        <p class="text-muted"><?= $subtitle ?></p>
    </div>
    <div class="col-md-4 text-md-end">
        <a href="<?php echo BASE_URL;?>/?p=product&a=index" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
    </div>
</div>

<form method="POST" action="<?php echo $formAction;?>">
    <div class="row">
        <div class="col-md-8">
            <!-- Product Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Informações do Produto</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8 mb-3">
                            <label for="nome" class="form-label">Nome do Produto *</label>
                            <input type="text" 
                                   class="form-control" 
                                   id="nome" 
                                   name="nome"
                                   value="<?= $isEdit ? htmlspecialchars($product['nome']) : '' ?>"
                                   required 
                                   placeholder="Ex: Camiseta Básica">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="preco" class="form-label">Preço *</label>
                            <div class="input-group">
                                <span class="input-group-text">R$</span>
                                <input type="number" 
                                       class="form-control" 
                                       id="preco" 
                                       name="preco" 
                                       step="0.01" 
                                       min="0"
                                       value="<?= $isEdit ? number_format($product['preco'], 2, '.', '') : '' ?>"
                                       required 
                                       placeholder="0,00">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="variacoes" class="form-label">Variações (JSON)</label>
                        <textarea class="form-control" 
                                  id="variacoes" 
                                  name="variacoes"
                                  placeholder='{"cores": ["Branco", "Preto", "Azul"], "tamanhos": ["P", "M", "G", "GG"]}'
                                  rows="4"><?= htmlspecialchars($variacoesValue) ?></textarea>
                        <div class="form-text">
                            Formato JSON com as variações do produto. Deixe em branco se não houver variações.
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Stock Management -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-box-seam"></i> Controle de Estoque</h5>
                    <button type="button" class="btn btn-sm btn-primary add-variation">
                        <i class="bi bi-plus"></i> Adicionar Variação
                    </button>
                </div>
                <div class="card-body">
                    <div id="variations-container">
                        <?php if ($isEdit && !empty($product['variacoes_estoque'])): ?>
                            <?php
                            foreach ($product['variacoes_estoque'] as $i => $estoque): ?>
                                <div class="variation-item mb-3 p-3 border rounded">
                                    <div class="row">
                                        <div class="col-md-5">
                                            <label class="form-label">Variação</label>
                                            <input type="text"
                                                   class="form-control"
                                                   name="variacoes_estoque[<?= $i ?>][variacao]"
                                                   value="<?= htmlspecialchars($estoque['variacao_id']) ?>"
                                                   placeholder="Ex: Branco-P ou deixe vazio para produto simples">
                                        </div>
                                        <div class="col-md-5">
                                            <label class="form-label">Quantidade</label>
                                            <input type="number"
                                                   class="form-control"
                                                   name="variacoes_estoque[<?= $i ?>][quantidade]"
                                                   value="<?= (int)$estoque['quantidade'] ?>"
                                                   min="0">
                                        </div>
                                        <?php if ($i > 0): ?>
                                            <div class="col-md-2 d-flex align-items-end">
                                                <button type="button" class="btn btn-danger remove-variation">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="variation-item mb-3 p-3 border rounded">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label class="form-label">Variação</label>
                                        <input type="text"
                                               class="form-control"
                                               name="variacoes_estoque[0][variacao]"
                                               placeholder="Ex: Branco-P ou deixe vazio para produto simples">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">Quantidade</label>
                                        <input type="number"
                                               class="form-control"
                                               name="variacoes_estoque[0][quantidade]"
                                               min="0"
                                               value="0">
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="alert alert-info alert-permanent">
                        <i class="bi bi-info-circle"></i>
                        <strong>Dica:</strong> Para produtos sem variações, deixe o campo "Variação" em branco e informe apenas a quantidade total.
                        Para produtos com variações, crie uma entrada para cada combinação (ex: Azul-P, Azul-M, etc.).
                    </div>
                </div>
            </div>
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
                            <i class="bi bi-check-circle"></i> <?= $buttonText ?>
                        </button>
                        <a href="<?php echo BASE_URL;?>/?p=product&a=index" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Help -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-question-circle"></i> Ajuda</h5>
                </div>
                <div class="card-body">
                    <h6>Variações JSON</h6>
                    <p class="small text-muted">
                        Use o formato JSON para definir as variações do produto. Exemplo:
                    </p>
                    <pre class="small bg-light p-2 rounded"><code>{
  "cores": ["Branco", "Preto"],
  "tamanhos": ["P", "M", "G"]
}</code></pre>
                    
                    <h6 class="mt-3">Controle de Estoque</h6>
                    <p class="small text-muted">
                        Para cada variação, informe a quantidade disponível. 
                        Use o formato: <code>Cor-Tamanho</code> (ex: Branco-P).
                    </p>
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
let variationIndex = 1;

document.addEventListener("click", function(e) {
    if (e.target.classList.contains("add-variation") || e.target.closest(".add-variation")) {
        e.preventDefault();
        addVariationField();
    }
    
    if (e.target.classList.contains("remove-variation") || e.target.closest(".remove-variation")) {
        e.preventDefault();
        e.target.closest(".variation-item").remove();
    }
});

function addVariationField() {
    const container = document.getElementById("variations-container");
    const variationHtml = `
        <div class="variation-item mb-3 p-3 border rounded">
            <div class="row">
                <div class="col-md-5">
                    <label class="form-label">Variação</label>
                    <input type="text" 
                           class="form-control" 
                           name="variacoes_estoque[${variationIndex}][variacao]" 
                           placeholder="Ex: Preto-M">
                </div>
                <div class="col-md-5">
                    <label class="form-label">Quantidade</label>
                    <input type="number" 
                           class="form-control" 
                           name="variacoes_estoque[${variationIndex}][quantidade]" 
                           min="0" 
                           value="0">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="button" class="btn btn-danger remove-variation">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML("beforeend", variationHtml);
    variationIndex++;
}

// JSON validation for variations
document.getElementById("variacoes").addEventListener("blur", function() {
    const value = this.value.trim();
    if (value && value !== "") {
        try {
            JSON.parse(value);
            this.classList.remove("is-invalid");
            this.classList.add("is-valid");
        } catch (e) {
            this.classList.remove("is-valid");
            this.classList.add("is-invalid");
            showAlert("JSON inválido para variações", "warning");
        }
    } else {
        this.classList.remove("is-invalid", "is-valid");
    }
});
</script>
';

include __DIR__ . '/../layout.php';
?>

