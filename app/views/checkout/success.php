<?php ob_start(); ?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <!-- Success Message -->
        <div class="card border-success mb-4">
            <div class="card-body text-center py-5">
                <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                <h2 class="text-success mt-3">Pedido Realizado com Sucesso!</h2>
                <p class="text-muted lead">Seu pedido foi recebido e está sendo processado.</p>
            </div>
        </div>
        
        <!-- Order Details -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-receipt"></i> Detalhes do Pedido</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Informações do Pedido</h6>
                        <ul class="list-unstyled">
                            <li><strong>Número:</strong> #<?= $order['id'] ?></li>
                            <li><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></li>
                            <li><strong>Status:</strong> 
                                <span class="badge bg-warning"><?= ucfirst($order['status']) ?></span>
                            </li>
                            <li><strong>Email:</strong> <?= htmlspecialchars($order['email']) ?></li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>Endereço de Entrega</h6>
                        <address class="text-muted">
                            CEP: <?= htmlspecialchars($order['cep']) ?><br>
                            <?= nl2br(htmlspecialchars($order['endereco'])) ?>
                        </address>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Order Items -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-list-check"></i> Itens do Pedido</h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>Produto</th>
                            <th>Variação</th>
                            <th>Quantidade</th>
                            <th>Preço Unit.</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($order['itens'] as $item): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($item['produto_nome']) ?></strong>
                                </td>
                                <td>
                                    <?= $item['variacao_id'] ? htmlspecialchars($item['variacao_id']) : '-' ?>
                                </td>
                                <td>
                                    <span class="badge bg-secondary"><?= $item['quantidade'] ?></span>
                                </td>
                                <td>
                                    R$ <?= number_format($item['preco'], 2, ',', '.') ?>
                                </td>
                                <td>
                                    <strong>R$ <?= number_format($item['preco'] * $item['quantidade'], 2, ',', '.') ?></strong>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Order Summary -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-calculator"></i> Resumo Financeiro</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 offset-md-6">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <span>R$ <?= number_format($order['subtotal'], 2, ',', '.') ?></span>
                        </div>
                        
                        <?php if ($order['desconto'] > 0): ?>
                            <div class="d-flex justify-content-between mb-2 text-success">
                                <span>Desconto:</span>
                                <span>- R$ <?= number_format($order['desconto'], 2, ',', '.') ?></span>
                            </div>
                            <?php if ($order['cupom_codigo']): ?>
                                <div class="d-flex justify-content-between mb-2">
                                    <small class="text-muted">Cupom:</small>
                                    <small class="text-muted"><?= htmlspecialchars($order['cupom_codigo']) ?></small>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Frete:</span>
                            <span class="<?= $order['frete'] == 0 ? 'text-success' : '' ?>">
                                <?= $order['frete'] == 0 ? 'GRÁTIS' : 'R$ ' . number_format($order['frete'], 2, ',', '.') ?>
                            </span>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between">
                            <strong>Total:</strong>
                            <strong class="price-tag" style="font-size: 1.25rem;">
                                R$ <?= number_format($order['total'], 2, ',', '.') ?>
                            </strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Next Steps -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-info-circle"></i> Próximos Passos</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-center mb-3">
                        <i class="bi bi-envelope text-primary" style="font-size: 2rem;"></i>
                        <h6 class="mt-2">Confirmação</h6>
                        <p class="small text-muted">Você receberá um email de confirmação em breve.</p>
                    </div>
                    <div class="col-md-4 text-center mb-3">
                        <i class="bi bi-box-seam text-info" style="font-size: 2rem;"></i>
                        <h6 class="mt-2">Processamento</h6>
                        <p class="small text-muted">Seu pedido será processado em até 1 dia útil.</p>
                    </div>
                    <div class="col-md-4 text-center mb-3">
                        <i class="bi bi-truck text-success" style="font-size: 2rem;"></i>
                        <h6 class="mt-2">Entrega</h6>
                        <p class="small text-muted">Você receberá o código de rastreamento por email.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Actions -->
        <div class="text-center mb-4">
            <div class="d-grid gap-2 d-md-block">
                <a href="<?php echo BASE_URL;?>/?p=order&a=viewOrder&id=<?= $order['id'] ?>" 
                   class="btn btn-primary">
                    <i class="bi bi-eye"></i> Acompanhar Pedido
                </a>
                <a href="<?php echo BASE_URL;?>/?p=home&a=catalog" 
                   class="btn btn-outline-primary">
                    <i class="bi bi-grid"></i> Continuar Comprando
                </a>
                <a href="/mini-erp/public/index.php" 
                   class="btn btn-outline-secondary">
                    <i class="bi bi-house"></i> Voltar ao Início
                </a>
            </div>
        </div>
        
        <!-- Support -->
        <div class="alert alert-info alert-permanent">
            <h6><i class="bi bi-headset"></i> Precisa de Ajuda?</h6>
            <p class="mb-0">
                Se você tiver alguma dúvida sobre seu pedido, entre em contato conosco através do email 
                <strong>suporte@mini-erp.com</strong> informando o número do pedido <strong>#<?= $order['id'] ?></strong>.
            </p>
        </div>
    </div>
</div>

<?php 
$content = ob_get_clean();

// Add custom scripts for confetti effect
$scripts = '
<script>
// Simple confetti effect
function createConfetti() {
    const colors = ["#ff6b6b", "#4ecdc4", "#45b7d1", "#96ceb4", "#feca57"];
    
    for (let i = 0; i < 50; i++) {
        setTimeout(() => {
            const confetti = document.createElement("div");
            confetti.style.position = "fixed";
            confetti.style.left = Math.random() * 100 + "%";
            confetti.style.top = "-10px";
            confetti.style.width = "10px";
            confetti.style.height = "10px";
            confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
            confetti.style.borderRadius = "50%";
            confetti.style.pointerEvents = "none";
            confetti.style.zIndex = "9999";
            confetti.style.animation = "fall 3s linear forwards";
            
            document.body.appendChild(confetti);
            
            setTimeout(() => {
                confetti.remove();
            }, 3000);
        }, i * 100);
    }
}

// CSS for falling animation
const style = document.createElement("style");
style.textContent = `
    @keyframes fall {
        0% {
            transform: translateY(-10px) rotate(0deg);
            opacity: 1;
        }
        100% {
            transform: translateY(100vh) rotate(360deg);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);

// Trigger confetti on page load
document.addEventListener("DOMContentLoaded", function() {
    setTimeout(createConfetti, 500);
});
</script>
';

include __DIR__ . '/../layout.php';
?>

