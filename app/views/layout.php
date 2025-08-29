<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_NAME ?? 'Mini ERP' ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?php echo BASE_URL;?>/public/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand fw-bold" href="/index.php">
                <i class="bi bi-shop"></i> Mini ERP
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL;?>">
                            <i class="bi bi-house"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL;?>/?p=home&a=catalog">
                            <i class="bi bi-grid"></i> Catálogo
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-box"></i> Produtos
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="<?php echo BASE_URL;?>/?p=product&a=index">Gerenciar Produtos</a></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_URL;?>/?p=product&a=create">Novo Produto</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL;?>/?p=order&a=index">
                            <i class="bi bi-receipt"></i> Pedidos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL;?>/?p=coupon&a=index">
                            <i class="bi bi-ticket-perforated"></i> Cupons
                        </a>
                    </li>
                </ul>
                
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo BASE_URL;?>/?p=cart&a=viewCart">
                            <i class="bi bi-cart"></i> Carrinho 
                            <span class="badge bg-warning text-dark" id="cart-count">0</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Flash Messages -->
    <?php 
    $flashMessages = $this->getFlashMessages();
    if (!empty($flashMessages)): 
    ?>
        <div class="container mt-3">
            <?php foreach ($flashMessages as $type => $message): ?>
                <div class="alert alert-<?= $type === 'error' ? 'danger' : $type ?> alert-dismissible fade show flash-messages" role="alert">
                    <?= $message ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Main Content -->
    <main class="container my-4">
        <?= $content ?? '' ?>
    </main>

    <!-- Footer -->
    <footer class="bg-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h6>Mini ERP</h6>
                    <p class="text-muted small">Sistema de controle de Pedidos, Produtos, Cupons e Estoque</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="text-muted small">
                        Desenvolvido com PHP + MySQL + Bootstrap<br>
                        © <?= date('Y') ?> - Todos os direitos reservados
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <script>
        const BASE_URL = '<?php echo BASE_URL;?>'
    </script>
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="<?php echo BASE_URL;?>/public/js/app.js"></script>
    
    <!-- Page specific scripts -->
    <?= $scripts ?? '' ?>
</body>
</html>

