-- Criacao do banco de dados mini_erp
CREATE DATABASE IF NOT EXISTS mini_erp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mini_erp;

-- Tabela de produtos
CREATE TABLE produtos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    preco DECIMAL(10,2) NOT NULL,
    variacoes JSON DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabela de estoque
CREATE TABLE estoque (
    id INT AUTO_INCREMENT PRIMARY KEY,
    produto_id INT NOT NULL,
    quantidade INT NOT NULL DEFAULT 0,
    variacao_id VARCHAR(100) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE,
    INDEX idx_produto_variacao (produto_id, variacao_id)
);

-- Tabela de pedidos
CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subtotal DECIMAL(10,2) NOT NULL,
    frete DECIMAL(10,2) NOT NULL DEFAULT 0,
    total DECIMAL(10,2) NOT NULL,
    status ENUM('pendente', 'processando', 'concluido', 'cancelado') DEFAULT 'pendente',
    cep VARCHAR(10) NOT NULL,
    endereco TEXT NOT NULL,
    email VARCHAR(255) NOT NULL,
    cupom_codigo VARCHAR(50) DEFAULT NULL,
    desconto DECIMAL(10,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabela de itens do pedido
CREATE TABLE pedido_itens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    produto_id INT NOT NULL,
    quantidade INT NOT NULL,
    preco DECIMAL(10,2) NOT NULL,
    variacao_id VARCHAR(100) DEFAULT NULL,
    FOREIGN KEY (pedido_id) REFERENCES pedidos(id) ON DELETE CASCADE,
    FOREIGN KEY (produto_id) REFERENCES produtos(id) ON DELETE CASCADE
);

-- Tabela de cupons
CREATE TABLE cupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) UNIQUE NOT NULL,
    desconto DECIMAL(10,2) NOT NULL,
    tipo_desconto ENUM('percentual', 'valor') DEFAULT 'valor',
    valor_minimo DECIMAL(10,2) DEFAULT 0,
    validade DATE NOT NULL,
    ativo BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Inserir dados de exemplo

-- Produtos de exemplo
INSERT INTO produtos (nome, preco, variacoes) VALUES 
('Camiseta Basica', 29.99, '{"cores": ["Branco", "Preto", "Azul"], "tamanhos": ["P", "M", "G", "GG"]}'),
('Calca Jeans', 89.99, '{"cores": ["Azul", "Preto"], "tamanhos": ["36", "38", "40", "42", "44"]}'),
('Tenis Esportivo', 159.99, '{"cores": ["Branco", "Preto", "Vermelho"], "tamanhos": ["37", "38", "39", "40", "41", "42", "43"]}'),
('Notebook Dell', 2499.99, '{"memoria": ["8GB", "16GB"], "armazenamento": ["256GB SSD", "512GB SSD", "1TB SSD"]}'),
('Smartphone Samsung', 899.99, '{"cores": ["Preto", "Branco", "Azul"], "memoria": ["128GB", "256GB"]}');

-- Estoque inicial
INSERT INTO estoque (produto_id, quantidade, variacao_id) VALUES 
-- Camiseta Basica
(1, 50, 'Branco-P'), (1, 45, 'Branco-M'), (1, 40, 'Branco-G'), (1, 30, 'Branco-GG'),
(1, 35, 'Preto-P'), (1, 40, 'Preto-M'), (1, 45, 'Preto-G'), (1, 25, 'Preto-GG'),
(1, 30, 'Azul-P'), (1, 35, 'Azul-M'), (1, 40, 'Azul-G'), (1, 20, 'Azul-GG'),

-- Calca Jeans
(2, 25, 'Azul-36'), (2, 30, 'Azul-38'), (2, 35, 'Azul-40'), (2, 30, 'Azul-42'), (2, 20, 'Azul-44'),
(2, 20, 'Preto-36'), (2, 25, 'Preto-38'), (2, 30, 'Preto-40'), (2, 25, 'Preto-42'), (2, 15, 'Preto-44'),

-- Tenis Esportivo
(3, 15, 'Branco-37'), (3, 20, 'Branco-38'), (3, 25, 'Branco-39'), (3, 30, 'Branco-40'), (3, 25, 'Branco-41'), (3, 20, 'Branco-42'), (3, 15, 'Branco-43'),
(3, 10, 'Preto-37'), (3, 15, 'Preto-38'), (3, 20, 'Preto-39'), (3, 25, 'Preto-40'), (3, 20, 'Preto-41'), (3, 15, 'Preto-42'), (3, 10, 'Preto-43'),
(3, 8, 'Vermelho-37'), (3, 12, 'Vermelho-38'), (3, 15, 'Vermelho-39'), (3, 18, 'Vermelho-40'), (3, 15, 'Vermelho-41'), (3, 12, 'Vermelho-42'), (3, 8, 'Vermelho-43'),

-- Notebook Dell
(4, 10, '8GB-256GB_SSD'), (4, 8, '8GB-512GB_SSD'), (4, 5, '8GB-1TB_SSD'),
(4, 7, '16GB-256GB_SSD'), (4, 10, '16GB-512GB_SSD'), (4, 8, '16GB-1TB_SSD'),

-- Smartphone Samsung
(5, 20, 'Preto-128GB'), (5, 15, 'Preto-256GB'),
(5, 18, 'Branco-128GB'), (5, 12, 'Branco-256GB'),
(5, 15, 'Azul-128GB'), (5, 10, 'Azul-256GB');

-- Cupons de exemplo
INSERT INTO cupons (codigo, desconto, tipo_desconto, valor_minimo, validade, ativo) VALUES 
('DESCONTO10', 10.00, 'valor', 50.00, '2025-12-31', TRUE),
('FRETE15', 15.00, 'valor', 100.00, '2025-12-31', TRUE),
('PROMO20', 20.00, 'percentual', 200.00, '2025-12-31', TRUE),
('BEMVINDO', 25.00, 'valor', 0.00, '2025-12-31', TRUE),
('NATAL2025', 30.00, 'percentual', 300.00, '2025-12-25', TRUE);

