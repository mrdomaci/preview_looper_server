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
                                $('#autocomplete_results').append('<a class="list-group-item list-group-item-action autocomplete-result" data-product-id="' + product.id + '">' + product.name + '</a>');
                            });
                            selectProduct();
                        },
                        error: function (xhr, status, error) {
                            console.error(xhr.responseText);
                        }
                    });
                } else {
                    $('#autocomplete_results').empty();
                    $('input[name="product"]').val(null);
                }
            });
        });
        function selectProduct() {
            $('#autocomplete_results').on('click', '.autocomplete-result', function () {
                var productId = $(this).data('product-id');
                var productName = $(this).text();
                $('#product_autocomplete').val(productName);
                $('input[name="product"]').val(productId);
                $('#autocomplete_results').empty();
            });
        }
    }
}
</script>