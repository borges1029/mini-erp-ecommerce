# Mini ERP - Sistema de Controle de Pedidos, Produtos, Cupons e Estoque

## Descrição

Sistema completo de ERP desenvolvido em PHP puro com MySQL e Bootstrap, seguindo o padrão MVC.

## Tecnologias Utilizadas

- **Backend**: PHP 8.3 (PHP Puro)
- **Banco de Dados**: MySQL 8.0
- **Frontend**: Bootstrap 5.3, HTML5, CSS3, JavaScript
- **Arquitetura**: MVC (Model-View-Controller)

## Funcionalidades Implementadas

### ✅ Requisitos Obrigatórios

1. **Banco de Dados com 5 tabelas**:
   - `produtos` - Armazena informações dos produtos
   - `estoque` - Controla estoque por variação
   - `pedidos` - Registra pedidos realizados
   - `pedido_itens` - Inter de pedidos com produtos
   - `cupons` - Gerencia cupons de desconto

2. **Gestão de Produtos**:
   - Cadastro de produtos com nome, preço e variações
   - Controle de estoque por variação
   - Atualização de dados do produto e estoque
   - Interface para visualização e edição

3. **Sistema de Carrinho**:
   - Carrinho em sessão
   - Controle automático de estoque
   - Cálculo de frete baseado no subtotal:
     - R$52,00 - R$166,59: Frete R$15,00
     - Acima de R$200,00: Frete grátis
     - Outros valores: Frete R$20,00

4. **Integração ViaCEP**:
   - Validação automática de CEP
   - Preenchimento automático de endereço

### ✅ Funcionalidades Extras

1. **Sistema de Cupons**:
   - Cupons com validade
   - Regras de valor mínimo
   - Aplicação automática no carrinho

2. **Sistema de Email**:
   - Envio de email ao finalizar pedido
   - Notificações de status do pedido

3. **Webhook para Status**:
   - Endpoint para receber atualizações de status
   - Cancelamento automático com restauração de estoque
   - Log de todas as operações

## Estrutura do Projeto

```
mini-erp/
├── app/
│   ├── controllers/    # Controllers MVC
│   ├── models/         # Models para banco de dados
│   └── views/          # Views (templates)
|
├── config/             # Configurações
|	├── config.php      # Configurações do projeto
│   ├── Database.php    # Configurações da base de dados
|
├── database/           # Scripts SQL
|
├── public/             # Arquivos públicos
│   ├── css/           	# Estilos customizados
│   ├── js/            	# JavaScript
|
└── index.php      		# Ponto de entrada
	
```

## Instalação e Configuração

### Pré-requisitos
- PHP 8.1+
- MySQL 8.0+
- Servidor web (Apache/Nginx) ou PHP built-in server

### Passos de Instalação

1. **Clone/Baixe o projeto**
2. **Configure o banco de dados**

3. **Configure as credenciais** em `config/Database.php`:**
   ```php
   private $host = 'localhost';
   private $database = 'mini_erp';
   private $username = 'root';
   private $password = 'sua_senha';
   ```
4. **Configure o BASE_URL em `config/config.php`**

5. **Configure as credenciais de E-mail em `config/config.php`**: Para teste sugiro o uso do serviço Mailtrap acessando https://mailtrap.io

6. **Inicie o servidor ou abra a URL caso esteja usando um ambiente preparado**: Geralmente http://localhost/mini-erp

7. **Acesse o sistema**: Geralmente http://localhost

### Dados de Teste Pré-carregados

O sistema já vem com dados de exemplo:

- **5 produtos** cadastrados com variações
- **Estoque** configurado para cada variação
- **5 cupons** de desconto ativos
- **Dashboard** com estatísticas em tempo real

## Principais Funcionalidades Testadas

### 1. Dashboard
- Visão geral do sistema
- Estatísticas de produtos, pedidos e estoque
- Alertas de estoque baixo

### 2. Catálogo de Produtos
- Listagem de produtos disponíveis
- Filtros e busca
- Adição ao carrinho

### 3. Gestão de Produtos
- CRUD completo de produtos
- Controle de variações e estoque
- Interface responsiva

### 4. Sistema de Carrinho
- Adição/remoção de itens
- Cálculo automático de frete
- Aplicação de cupons

### 5. Checkout
- Validação de CEP via ViaCEP
- Finalização de pedidos
- Envio de email de confirmação

### 6. Webhook de Status
- Endpoint: `BASE_URL/?p=webhook&a=orderStatus`
- Aceita JSON com `order_id` e `status`
- Status aceitos: pendente, confirmado, enviado, entregue, cancelado

## Webhook - Exemplo de Uso

```bash
curl -X POST BASE_URL/?p=webhook&a=orderStatus \
  -H "Content-Type: application/json" \
  -d '{
    "order_id": 1,
    "status": "confirmado"
  }'
```

## Boas Práticas Implementadas

- **Código Limpo**: Seguindo princípios SOLID
- **MVC**: Separação clara de responsabilidades
- **Segurança**: Prepared statements, validação de dados
- **Responsividade**: Interface adaptável para mobile
- **Logs**: Sistema de logging para debugging
- **Tratamento de Erros**: Exceções tratadas adequadamente

## Considerações Técnicas

### Arquitetura MVC
- **Models**: Interação com banco de dados
- **Views**: Apresentação (HTML/CSS/JS)
- **Controllers**: Lógica de negócio

### Segurança
- Proteção contra SQL Injection
- Validação de entrada de dados
- Sanitização de outputs

### Performance
- Consultas otimizadas
- Cache de sessão para carrinho
- Carregamento assíncrono via AJAX

## Melhorias Futuras

- Sistema de autenticação
- Relatórios avançados
- API REST completa
- Integração com gateways de pagamento
- Sistema de notificações push

