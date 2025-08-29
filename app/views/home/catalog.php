<?php ob_start(); ?>

<div class="row mb-4">
    <div class="col-md-8">
        <h1 class="text-gradient">
            <i class="bi bi-grid"></i> Catálogo de Produtos
        </h1>
        <p class="text-muted">Navegue pelos nossos produtos disponíveis</p>
    </div>
    <div class="col-md-4 text-md-end">
        <a href="<?php echo BASE_URL;?>/?p=cart&a=viewCart" class="btn btn-outline-primary">
            <i class="bi bi-cart"></i> Ver Carrinho
        </a>
    </div>
</div>

<?php if (!empty($products)): ?>
    <div class="row">
        <?php foreach ($products as $product): ?>
            <div class="col-md-4 col-lg-3 mb-4">
                <div class="card product-card h-100">
                    <div class="product-image">
                        <i class="bi bi-box"></i>
                    </div>
                    
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title"><?= htmlspecialchars($product['nome']) ?></h5>
                        
                        <div class="mb-2">
                            <span class="price-tag">R$ <?= number_format($product['preco'], 2, ',', '.') ?></span>
                        </div>
                        
                        <div class="mb-3">
                            <small class="text-muted">
                                <i class="bi bi-box-seam"></i> 
                                Estoque: <?= $product['estoque_total'] ?? 0 ?>
                                <?php if ($product['variacoes_count'] > 1): ?>
                                    (<?= $product['variacoes_count'] ?> variações)
                                <?php endif; ?>
                            </small>
                        </div>
                        
                        <?php if ($product['variacoes']): ?>
                            <?php
                                $variacoes = json_decode($product['variacoes'], true);
                                if (!is_array($variacoes)) {
                                    $variacoes = []; // garante que seja array
                                }
                            ?>
                            <?php if (!empty($variacoes)): ?>
                                <div class="mb-3">
                                    <?php foreach ($variacoes as $tipo => $opcoes): ?>
                                        <small class="d-block text-muted">
                                            <strong><?= ucfirst($tipo) ?>:</strong> 
                                            <?= implode(', ', array_slice($opcoes, 0, 3)) ?>
                                            <?php if (count($opcoes) > 3): ?>
                                                <span class="text-primary">+<?= count($opcoes) - 3 ?> mais</span>
                                            <?php endif; ?>
                                        </small>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <div class="mt-auto">
                            <?php if ($product['estoque_total'] > 0): ?>
                                <div class="d-grid gap-2">
                                    <a href="<?php echo BASE_URL;?>/?p=product&a=viewProduct&id=<?= $product['id'] ?>" 
                                       class="btn btn-primary">
                                        <i class="bi bi-cart-plus"></i> Comprar
                                    </a>
                                    <a href="<?php echo BASE_URL;?>/?p=product&a=viewProduct&id=<?= $product['id'] ?>" 
                                       class="btn btn-outline-secondary btn-sm">
                                        <i class="bi bi-eye"></i> Ver Detalhes
                                    </a>
                                </div>
                            <?php else: ?>
                                <div class="d-grid">
                                    <button class="btn btn-secondary" disabled>
                                        <i class="bi bi-x-circle"></i> Sem Estoque
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Pagination placeholder -->
    <div class="row mt-4">
        <div class="col text-center">
            <p class="text-muted">
                Mostrando <?= count($products) ?> produto(s)
            </p>
        </div>
    </div>
    
<?php else: ?>
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="bi bi-box text-muted" style="font-size: 4rem;"></i>
                    <h3 class="mt-3 text-muted">Nenhum produto encontrado</h3>
                    <p class="text-muted">Não há produtos cadastrados no sistema ainda.</p>
                    <a href="<?php echo BASE_URL;?>/?p=product&a=create" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Cadastrar Primeiro Produto
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Filter/Search Section (for future enhancement) -->
<div class="row mt-4">
    <div class="col">
        <div class="card">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Buscar produtos..." id="search-products">
                            <button class="btn btn-outline-secondary" type="button">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end mt-2 mt-md-0">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-outline-secondary active">
                                <i class="bi bi-grid"></i> Grade
                            </button>
                            <button type="button" class="btn btn-outline-secondary">
                                <i class="bi bi-list"></i> Lista
                            </button>
                        </div>
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

