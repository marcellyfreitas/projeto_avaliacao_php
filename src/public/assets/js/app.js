document.addEventListener('DOMContentLoaded', function () {
    // =============================================
    // Sidebar mobile
    // =============================================
    var sidebarToggle = document.getElementById('sidebar-toggle');
    var sidebarOverlay = document.getElementById('sidebar-overlay');
    var sidebar = document.getElementById('sidebar');

    function openSidebar() {
        sidebar.classList.add('sidebar-open');
        sidebarOverlay.classList.add('active');
        sidebarToggle.setAttribute('aria-expanded', 'true');
        sidebarOverlay.setAttribute('aria-hidden', 'false');
    }

    function closeSidebar() {
        sidebar.classList.remove('sidebar-open');
        sidebarOverlay.classList.remove('active');
        sidebarToggle.setAttribute('aria-expanded', 'false');
        sidebarOverlay.setAttribute('aria-hidden', 'true');
    }

    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function () {
            if (sidebar.classList.contains('sidebar-open')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', closeSidebar);
    }

    // =============================================
    // Modal de confirmação customizado
    // =============================================
    var dataConfirmElements = document.querySelectorAll('[data-confirm]');

    if (dataConfirmElements.length > 0) {
        var overlay = document.createElement('div');
        overlay.className = 'modal-overlay';
        overlay.setAttribute('role', 'dialog');
        overlay.setAttribute('aria-modal', 'true');
        overlay.setAttribute('aria-labelledby', 'modal-message');
        overlay.innerHTML = '<div class="modal-box">' +
            '<p id="modal-message"></p>' +
            '<div class="modal-buttons">' +
            '<button class="btn btn-secondary" id="modal-cancel">Cancelar</button>' +
            '<button class="btn btn-danger" id="modal-confirm">Confirmar</button>' +
            '</div></div>';
        document.body.appendChild(overlay);

        var modalMessage = document.getElementById('modal-message');
        var modalCancel = document.getElementById('modal-cancel');
        var modalConfirm = document.getElementById('modal-confirm');
        var modalCallback = null;
        var lastFocusedElement = null;

        function showModal(mensagem, callback) {
            lastFocusedElement = document.activeElement;
            modalMessage.textContent = mensagem;
            modalCallback = callback;
            overlay.classList.add('active');
            modalCancel.focus();
        }

        function hideModal() {
            overlay.classList.remove('active');
            modalCallback = null;
            if (lastFocusedElement) {
                lastFocusedElement.focus();
                lastFocusedElement = null;
            }
        }

        modalCancel.addEventListener('click', hideModal);

        modalConfirm.addEventListener('click', function () {
            var cb = modalCallback;
            hideModal();
            if (cb) cb();
        });

        overlay.addEventListener('click', function (e) {
            if (e.target === overlay) {
                hideModal();
            }
        });

        // Tecla Escape fecha o modal
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && overlay.classList.contains('active')) {
                hideModal();
            }
        });

        // Focus trap dentro do modal
        overlay.addEventListener('keydown', function (e) {
            if (e.key !== 'Tab') return;
            var focusable = overlay.querySelectorAll('button');
            var first = focusable[0];
            var last = focusable[focusable.length - 1];

            if (e.shiftKey) {
                if (document.activeElement === first) {
                    e.preventDefault();
                    last.focus();
                }
            } else {
                if (document.activeElement === last) {
                    e.preventDefault();
                    first.focus();
                }
            }
        });

        // Botões com data-confirm
        dataConfirmElements.forEach(function (el) {
            el.addEventListener('click', function (e) {
                e.preventDefault();
                var mensagem = el.getAttribute('data-confirm');
                var target = el.closest('form');
                showModal(mensagem, function () {
                    if (target) {
                        target.submit();
                    } else if (el.href) {
                        window.location.href = el.href;
                    }
                });
            });
        });
    }

    // =============================================
    // Máscara de valor monetário
    // =============================================
    var priceInput = document.getElementById('price');
    if (priceInput) {
        if (priceInput.value) {
            var valorLimpo = priceInput.value.replace(/[^\d]/g, '');
            if (valorLimpo) {
                var valorNumerico = parseInt(valorLimpo, 10) / 100;
                priceInput.value = formatarMoeda(valorNumerico.toFixed(2));
            }
        }

        priceInput.addEventListener('input', function () {
            var posicaoCursor = this.selectionStart;
            var valorAntes = this.value.length;
            this.value = formatarMoeda(this.value);
            var diferenca = this.value.length - valorAntes;
            this.setSelectionRange(posicaoCursor + diferenca, posicaoCursor + diferenca);
        });

        priceInput.addEventListener('blur', function () {
            if (this.value && this.value !== 'R$ 0,00') {
                this.value = formatarMoeda(this.value);
            }
        });

        priceInput.addEventListener('focus', function () {
            var apenasDigitos = this.value.replace(/\D/g, '');
            if (apenasDigitos && parseInt(apenasDigitos) > 0) {
                this.value = apenasDigitos;
            }
        });
    }

    function formatarMoeda(valor) {
        var apenasDigitos = valor.replace(/\D/g, '');
        if (!apenasDigitos) return '';
        var numero = parseInt(apenasDigitos, 10);
        if (isNaN(numero)) return '';
        var valorFormatado = (numero / 100).toFixed(2);
        var partes = valorFormatado.split('.');
        partes[0] = partes[0].replace(/\B(?=(\d{3})+(?!\d))/g, '.');
        return 'R$ ' + partes.join(',');
    }

    // =============================================
    // Auto-hide apenas alertas toast (toast-fade)
    // =============================================
    var toasts = document.querySelectorAll('.toast-fade');
    toasts.forEach(function (alert) {
        setTimeout(function () {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(function () {
                alert.remove();
            }, 500);
        }, 5000);
    });

    // =============================================
    // Paginação via AJAX (sem recarregar a página)
    // =============================================
    var pagination = document.querySelector('.pagination');
    if (pagination) {
        pagination.addEventListener('click', function (e) {
            var link = e.target.closest('a');
            if (!link) return;

            e.preventDefault();
            var url = link.getAttribute('href');

            fetch(url)
                .then(function (resp) { return resp.text(); })
                .then(function (html) {
                    var parser = new DOMParser();
                    var doc = parser.parseFromString(html, 'text/html');

                    var newTable = doc.querySelector('.table-section');
                    var newPagination = doc.querySelector('.pagination');
                    var newCards = doc.querySelector('.summary-cards');
                    var newAlerts = doc.querySelector('.main-header');

                    if (newTable) {
                        document.querySelector('.table-section').innerHTML = newTable.innerHTML;
                    }
                    if (newPagination) {
                        document.querySelector('.pagination').innerHTML = newPagination.innerHTML;
                    }
                    if (newCards) {
                        document.querySelector('.summary-cards').innerHTML = newCards.innerHTML;
                    }

                    history.pushState(null, '', url);
                });
        });

        window.addEventListener('popstate', function () {
            location.reload();
        });
    }

    // =============================================
    // Validação do formulário de serviço
    // =============================================
    var serviceForm = document.querySelector('.service-form');
    if (serviceForm) {
        serviceForm.addEventListener('submit', function (e) {
            var description = document.getElementById('description').value.trim();
            var priceEl = document.getElementById('price');

            if (priceEl) {
                var priceValue = priceEl.value.replace(/\D/g, '');
                var price = parseFloat(priceValue) / 100;

                if (description.length < 3) {
                    e.preventDefault();
                    alert('A descrição deve ter pelo menos 3 caracteres.');
                    return;
                }

                if (isNaN(price) || price <= 0) {
                    e.preventDefault();
                    alert('O valor deve ser um número maior que zero.');
                    return;
                }

                priceEl.value = price.toFixed(2).replace('.', ',');
            }
        });
    }
});
