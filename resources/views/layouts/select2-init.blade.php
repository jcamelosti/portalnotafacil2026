<script type="text/javascript">
    (function ($) {
        // Inicializa o select2 nos selects com a classe .select2 que ainda não foram inicializados
        window.initSelect2 = function (context) {
            $(context || document)
                .find('select.select2')
                .addBack('select.select2')
                .each(function () {
                    if (!$(this).hasClass('select2-hidden-accessible')) {
                        $(this).select2();
                    }
                });
        };

        // Select2 dentro de <label>: o clique no label repassa o foco/clique para o <select> oculto
        // e o dropdown abre e fecha em seguida. Cancela a ação padrão do label nesses casos.
        $(document).on('click', 'label', function (e) {
            var $select = $(this).find('select.select2-hidden-accessible');
            if (!$select.length) {
                return;
            }

            e.preventDefault();

            // Clique no texto do label (fora do componente): abre o select2
            if (!$(e.target).closest('.select2-container').length) {
                $select.first().select2('open');
            }
        });

        $(function () {
            window.initSelect2();

            // Selects inseridos dinamicamente (ex.: cidades carregadas via AJAX ao trocar a UF)
            if (window.MutationObserver) {
                new MutationObserver(function (mutations) {
                    mutations.forEach(function (mutation) {
                        $(mutation.addedNodes).each(function () {
                            if (this.nodeType === 1) {
                                window.initSelect2(this);
                            }
                        });
                    });
                }).observe(document.body, { childList: true, subtree: true });
            }
        });
    })(jQuery);
</script>
