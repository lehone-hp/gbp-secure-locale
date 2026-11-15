$(function() {
    $('button.confirm-btn').click(function () {
        const r = confirm($(this).data('message'));
        if (r === true) {
            $(this).closest('form').submit();
        }
    });

    if ($('.sortable-table').length) {
        sortable('.sortable-table tbody')[0].addEventListener('sortupdate', function (e) {
            var el = $(e.detail.item);
            var tableContainer = el.closest('.sortable-table');
            var loader = tableContainer.find('.ajax-loader');
            var className = tableContainer.attr('data-class');
            var action = tableContainer.attr('data-action');

            if (loader.length) {
                loader.show();
            }

            $.ajax({
                method: 'PUT',
                data: {
                    'id': el.attr('data-id'),
                    'rank': e.detail.destination.index + 1,
                    'class': className,
                },
                url: action,
                success: function (data) {
                    //
                },
                error: function () {
                    loader.hide();
                    alert('An unexpected error occurred.');
                },
                complete: function () {
                    if (loader) {
                        loader.hide();
                    }
                }
            })
        });
    }

    $('.submit-onchange-input').change(function () {
        $(this).closest('form').submit();
    });

    // tiny mce
    tinymce.init({
        selector: '.tiny-textarea',
        height: "400px",
        plugins: "image lists link paste table",
        toolbar: "undo redo | styleselect | bold italic underline | table anchor | alignleft aligncenter alignright alignjustify | numlist bullist | indent outdent | link forecolor",
        a11y_advanced_options: true,
        paste_as_text: true,
        relative_urls : false,
        remove_script_host : true,
        color_picker_callback: function(callback, value) {
            callback('#4b83eb')
        }
        // language: $('html').attr('lang'),
    });

    $('.bootstrap-select').selectpicker();
});
