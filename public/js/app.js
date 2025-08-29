// Mini ERP - Custom JavaScript

document.addEventListener('DOMContentLoaded', function() {
    
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Initialize popovers
    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
    });
    
    // // Auto-hide alerts after 5 seconds
    // setTimeout(function() {
    //     var alerts = document.querySelectorAll('.alert');
    //     alerts.forEach(function(alert) {
    //         var bsAlert = new bootstrap.Alert(alert);
    //         bsAlert.close();
    //     });
    // }, 5000);

    // Auto-hide alerts após 5 segundos
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    alerts.forEach(alert => {
        setTimeout(() => {
            if (alert.parentNode) {
                alert.style.opacity = '0';
                setTimeout(() => {
                    if (alert.parentNode) {
                        alert.parentNode.removeChild(alert);
                    }
                }, 300);
            }
        }, 5000);
    });
    
    // Confirm delete actions
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-delete') || e.target.closest('.btn-delete')) {
            e.preventDefault();
            
            if (confirm('Tem certeza que deseja excluir este item?')) {
                var form = e.target.closest('form');
                if (form) {
                    form.submit();
                } else {
                    var href = e.target.href || e.target.closest('a').href;
                    if (href) {
                        window.location.href = href;
                    }
                }
            }
        }
    });
    
    // Format currency inputs
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('currency-input')) {
            formatCurrency(e.target);
        }
    });
    
    // Update cart count
    updateCartCount();
    
    // CEP validation and auto-fill
    document.addEventListener('input', function(e) {
        if (e.target.id === 'cep') {
            var cep = e.target.value.replace(/\D/g, '');
            if (cep.length === 8) {
                fetchAddressByCEP(cep);
            }
        }
    });

    const elVariacoes = document.getElementById("variacoes");

    if (elVariacoes) {
        elVariacoes.addEventListener("blur", function() {
            const value = this.value.trim();
            if (value && value !== "") {
                try {
                    JSON.parse(value);
                    this.classList.remove("is-invalid");
                    this.classList.add("is-valid");
                    showAlert("JSON válido", "success");
                } catch (e) {
                    this.classList.remove("is-valid");
                    this.classList.add("is-invalid");
                    showAlert("JSON inválido para variações", "warning");
                }
            } else {
                this.classList.remove("is-invalid", "is-valid");
            }
        });
    }

    // Dynamic variation management
    initVariationManager();
});

// Format currency input
function formatCurrency(input) {
    var value = input.value.replace(/\D/g, '');
    value = (value / 100).toFixed(2);
    value = value.replace('.', ',');
    value = value.replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.');
    input.value = 'R$ ' + value;
}

// Update cart count
function updateCartCount() {
    fetch(BASE_URL + '/?p=cart&a=count')
        .then(response => response.json())
        .then(data => {
            var cartCount = document.getElementById('cart-count');
            if (cartCount) {
                cartCount.textContent = data.count || 0;
            }
        })
        .catch(error => console.error('Error updating cart count:', error));
}

// Fetch address by CEP
function fetchAddressByCEP(cep) {
    var cepInput = document.getElementById('cep');
    var enderecoInput = document.getElementById('endereco');
    
    if (!cepInput || !enderecoInput) return;
    
    // Show loading
    cepInput.classList.add('loading');
    
    fetch(`https://viacep.com.br/ws/${cep}/json/`)
        .then(response => response.json())
        .then(data => {
            cepInput.classList.remove('loading');
            
            if (data.erro) {
                showAlert('CEP não encontrado', 'warning');
                return;
            }
            
            // Fill address fields
            var endereco = `${data.logradouro}, ${data.bairro}, ${data.localidade} - ${data.uf}`;
            enderecoInput.value = endereco;
            
            // Store additional data
            enderecoInput.dataset.cidade = data.localidade;
            enderecoInput.dataset.uf = data.uf;
            enderecoInput.dataset.bairro = data.bairro;
            
            // showAlert('Endereço preenchido automaticamente', 'success');
        })
        .catch(error => {
            cepInput.classList.remove('loading');
            showAlert('Erro ao buscar CEP', 'danger');
            console.error('Error fetching CEP:', error);
        });
}

// Show alert message
function showAlert(message, type = 'info') {
    var alertContainer = document.querySelector('.container');
    if (!alertContainer) return;
    
    var alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show flash-messages`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    alertContainer.insertBefore(alertDiv, alertContainer.firstChild);
    
    // Auto-hide after 3 seconds
    setTimeout(function() {
        var bsAlert = new bootstrap.Alert(alertDiv);
        bsAlert.close();
    }, 3000);
}

// Initialize variation manager
function initVariationManager() {
    var variationContainer = document.getElementById('variations-container');
    if (!variationContainer) return;
    
    // Add variation button
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('add-variation')) {
            e.preventDefault();
            addVariationField();
        }
    });
    
    // Remove variation button
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-variation')) {
            e.preventDefault();
            e.target.closest('.variation-item').remove();
        }
    });
}

// Add variation field
function addVariationField() {
    var container = document.getElementById('variations-container');
    if (!container) return;
    
    var index = container.children.length;
    var variationHtml = `
        <div class="variation-item mb-3 p-3 border rounded">
            <div class="row">
                <div class="col-md-4">
                    <label class="form-label">Variação</label>
                    <input type="text" class="form-control" name="variacoes_estoque[${index}][variacao]" placeholder="Ex: Azul-M">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Quantidade</label>
                    <input type="number" class="form-control" name="variacoes_estoque[${index}][quantidade]" min="0" value="0">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="button" class="btn btn-danger remove-variation">
                        <i class="bi bi-trash"></i> Remover
                    </button>
                </div>
            </div>
        </div>
    `;
    
    container.insertAdjacentHTML('beforeend', variationHtml);
}

// Cart functions
function addToCart(productId, variationId = null, quantity = 1) {
    var formData = new FormData();
    formData.append('product_id', productId);
    formData.append('variation_id', variationId);
    formData.append('quantity', quantity);
    
    fetch(BASE_URL + '/?p=cart&a=add', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCartCount();
            showAlert(data.message, 'success');
        } else {
            showAlert(data.message, 'danger');
        }
    })
    .catch(error => {
        showAlert('Erro ao adicionar ao carrinho', 'danger');
        console.error('Error adding to cart:', error);
    });
}

// Apply coupon
function applyCoupon() {
    var couponCode = document.getElementById('coupon-code');
    if (!couponCode) return;
    
    var code = couponCode.value.trim();
    if (!code) {
        showAlert('Digite o código do cupom', 'warning');
        return;
    }
    
    var formData = new FormData();
    formData.append('coupon_code', code);
    
    fetch(BASE_URL + '/?p=cart&a=apply_coupon', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            location.reload(); // Reload to update cart totals
        } else {
            showAlert(data.message, 'danger');
        }
    })
    .catch(error => {
        showAlert('Erro ao aplicar cupom', 'danger');
        console.error('Error applying coupon:', error);
    });
}

// Calculate shipping
function calculateShipping() {
    var subtotal = parseFloat(document.getElementById('subtotal').dataset.value || 0);
    var shipping = 0;
    
    if (subtotal >= 200.00) {
        shipping = 0.00; // Free shipping
    } else if (subtotal >= 52.00 && subtotal <= 166.59) {
        shipping = 15.00;
    } else {
        shipping = 20.00;
    }
    
    return shipping;
}

