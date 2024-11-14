<script src="../../js/jquery.js"></script>
<script>
window.onload = function() {
    if (window.jQuery) {  
        $(document).ready(function () {
            var client = $('#eu_client_id').data('id');
            $('#product_autocomplete').on('input', function () {
                var inputString = $(this).val();
                
                if (inputString.length > 3) {
                    $.ajax({
                        url: '/products/' + client + '/' + inputString,
                        type: 'GET',
                        success: function (response) {
                            $('#autocomplete_results').empty();
                            response.forEach(function (product) {
                                $('#autocomplete_results').append('<a class="list-group-item list-group-item-action autocomplete-result" data-product-guid="' + product.guid + '">' + product.name + '</a>');
                            });
                            selectProduct();
                        },
                        error: function (xhr, status, error) {
                            console.error(xhr.responseText);
                        }
                    });
                } else {
                    $('#autocomplete_results').empty();
                    $('input[id="product"]').val(null);
                }
            });
            $('#forbidden_product_autocomplete').on('input', function () {
                var inputString = $(this).val();
                
                if (inputString.length > 3) {
                    $.ajax({
                        url: '/products/' + client + '/' + inputString,
                        type: 'GET',
                        success: function (response) {
                            $('#autocomplete_forbidden_results').empty();
                            response.forEach(function (product) {
                                $('#autocomplete_forbidden_results').append('<a class="list-group-item list-group-item-action autocomplete-forbidden-result" data-product-guid="' + product.guid + '">' + product.name + '</a>');
                            });
                            selectForbiddenProduct();
                        },
                        error: function (xhr, status, error) {
                            console.error(xhr.responseText);
                        }
                    });
                } else {
                    $('#autocomplete_forbidden_results').empty();
                    $('input[id="product"]').val(null);
                }
            });
        });
        function selectProduct() {
            $('#autocomplete_results').on('click', '.autocomplete-result', function () {
                var productGuid = $(this).data('product-guid');
                var productName = $(this).text();
                $('#product_autocomplete').val(productName);
                $('input[id="product"]').val(productGuid);
                $('#autocomplete_results').empty();
            });
        }

        function selectForbiddenProduct() {
            $('#autocomplete_forbidden_results').on('click', '.autocomplete-forbidden-result', function () {
                var productGuid = $(this).data('product-guid');
                var productName = $(this).text();
                $('#forbidden_product_autocomplete').val(productName);
                $('input[id="forbidden_product"]').val(productGuid);
                $('#autocomplete_forbidden_results').empty();
            });
        }
    }
}
</script>