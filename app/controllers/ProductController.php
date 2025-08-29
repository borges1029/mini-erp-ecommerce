<?php

require_once 'BaseController.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Stock.php';

class ProductController extends BaseController {
    private $productModel;
    private $stockModel;
    
    public function __construct() {
        $this->productModel = new Product();
        $this->stockModel = new Stock();
    }
    
    public function index() {
        $products = $this->productModel->getProductsWithStock();
        $this->view('products/index', [
            'products' => $products,
            'title' => 'Gerenciar Produtos'
        ]);
    }
    
    public function create() {
        if ($this->isPost()) {
            $data = $this->sanitizeInput($this->getPostData());
            
            // Validar dados obrigatórios
            $errors = $this->validateRequired($data, ['nome', 'preco']);
            
            if (empty($errors)) {
                try {
                    // Preparar dados do produto
                    $productData = [
                        'nome' => $data['nome'],
                        'preco' => floatval($data['preco']),
                    ];

                    // Validação e conversão do JSON de variações
                    if (!empty($data['variacoes'])) {
                        // Substituir &quot; por " e remover espaços extras
                        $variacoesStr = html_entity_decode(trim($data['variacoes']));

                        // Tentar decodificar o JSON
                        $decoded = json_decode($variacoesStr, true);

                        if ($decoded === null) {
                            // Se ainda não for válido, podemos tentar corrigir problemas simples
                            // Por exemplo, se vier como array de strings fragmentadas
                            if (is_array($data['variacoes'])) {
                                // Se veio como array de strings, juntar tudo
                                $joined = implode('', $data['variacoes']);
                                $decoded = json_decode(html_entity_decode($joined), true);
                            }
                        }

                        // Se conseguiu decodificar, salva como JSON bonito
                        if ($decoded !== null) {
                            $productData['variacoes'] = json_encode($decoded, JSON_UNESCAPED_UNICODE);
                        } else {
                            $productData['variacoes'] = null;
                        }
                    } else {
                        $productData['variacoes'] = null;
                    }

                    // Preparar dados de estoque das variações
                    $variationsStock = [];
                    if (!empty($data['variacoes_estoque'])) {
                        foreach ($data['variacoes_estoque'] as $item) {
                            // só adiciona se tiver variação e quantidade > 0
                            if (!empty($item['variacao']) && intval($item['quantidade']) > 0) {
                                $variationsStock[] = [
                                    'variacao_id' => $item['variacao'],
                                    'quantidade' => intval($item['quantidade'])
                                ];
                            }
                        }
                    }

                    $productId = $this->productModel->createWithVariations($productData, $variationsStock);
                    
                    $this->setFlashMessage('success', 'Produto criado com sucesso!');
                    $this->redirect(BASE_URL . '/?p=product&a=index');
                    
                } catch (Exception $e) {
                    $this->setFlashMessage('error', 'Erro ao criar produto: ' . $e->getMessage());
                }
            } else {
                $this->setFlashMessage('error', implode('<br>', $errors));
            }
        }
        
        $this->view('products/create', [
            'title' => 'Criar Produto'
        ]);
    }
    
    public function edit() {
        $id = $this->getParam('id');
        
        if (!$id) {
            $this->setFlashMessage('error', 'ID do produto não informado');
            $this->redirect(BASE_URL . '/?p=product&a=index');
        }
        
        $product = $this->productModel->getProductWithVariations($id);
        
        if (!$product) {
            $this->setFlashMessage('error', 'Produto não encontrado');
            $this->redirect(BASE_URL . '/?p=product&a=index');
        }
        
        if ($this->isPost()) {
            $data = $this->sanitizeInput($this->getPostData());
            
            // Validar dados obrigatórios
            $errors = $this->validateRequired($data, ['nome', 'preco']);
            
            if (empty($errors)) {
                try {
                    // Preparar dados do produto
                    $productData = [
                        'nome' => $data['nome'],
                        'preco' => floatval($data['preco']),
                    ];

                    // Validação e conversão do JSON de variações
                    if (!empty($data['variacoes'])) {
                        // Substituir &quot; por " e remover espaços extras
                        $variacoesStr = html_entity_decode(trim($data['variacoes']));

                        // Tentar decodificar o JSON
                        $decoded = json_decode($variacoesStr, true);

                        if ($decoded === null) {
                            // Se ainda não for válido, podemos tentar corrigir problemas simples
                            // Por exemplo, se vier como array de strings fragmentadas
                            if (is_array($data['variacoes'])) {
                                // Se veio como array de strings, juntar tudo
                                $joined = implode('', $data['variacoes']);
                                $decoded = json_decode(html_entity_decode($joined), true);
                            }
                        }

                        // Se conseguiu decodificar, salva como JSON bonito
                        if ($decoded !== null) {
                            $productData['variacoes'] = json_encode($decoded, JSON_UNESCAPED_UNICODE);
                        } else {
                            $productData['variacoes'] = null;
                        }
                    } else {
                        $productData['variacoes'] = null;
                    }

                    // Preparar dados de estoque das variações
                    $variationsStock = [];
                    if (!empty($data['variacoes_estoque'])) {
                        foreach ($data['variacoes_estoque'] as $item) {
                            // só adiciona se tiver variação e quantidade > 0
                            if (!empty($item['variacao']) && intval($item['quantidade']) > 0) {
                                $variationsStock[] = [
                                    'variacao_id' => $item['variacao'],
                                    'quantidade' => intval($item['quantidade'])
                                ];
                            }
                        }
                    }

                    $this->productModel->updateWithVariations($id, $productData, $variationsStock);
                    
                    $this->setFlashMessage('success', 'Produto atualizado com sucesso!');
                    $this->redirect(BASE_URL . '/?p=product&a=index');
                    
                } catch (Exception $e) {
                    $this->setFlashMessage('error', 'Erro ao atualizar produto: ' . $e->getMessage());
                }
            } else {
                $this->setFlashMessage('error', implode('<br>', $errors));
            }
        }
        
        $this->view('products/create', [
            'product' => $product,
            'title' => 'Editar Produto'
        ]);
    }
    
    public function delete() {
        $id = $this->getParam('id');
        
        if (!$id) {
            $this->json(['success' => false, 'message' => 'ID não informado']);
        }
        
        try {
            $this->productModel->delete($id);
            $this->json(['success' => true, 'message' => 'Produto excluído com sucesso']);
        } catch (Exception $e) {
            $this->json(['success' => false, 'message' => 'Erro ao excluir produto: ' . $e->getMessage()]);
        }
    }
    
    public function viewProduct() {
        $id = $this->getParam('id');
        
        if (!$id) {
            $this->setFlashMessage('error', 'ID do produto não informado');
            $this->redirect(BASE_URL . '/?p=product&a=index');
        }
        
        $product = $this->productModel->getProductWithVariations($id);
        
        if (!$product) {
            $this->setFlashMessage('error', 'Produto não encontrado');
            $this->redirect(BASE_URL . '/?p=product&a=index');
        }
        
        $this->view('products/view', [
            'product' => $product,
            'title' => $product['nome']
        ]);
    }
}

