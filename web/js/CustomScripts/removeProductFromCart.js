// $(document).ready(function () {
//     $('.js-datepicker').datepicker();
// });

$(document).ready(function() {
    // Get the div that holds the collection of products
    var $collectionHolder = $('table.cart-table');

    // add a delete link to all of the existing rows with products
    $collectionHolder.find('tr.cart-row').each(function() {
        addImageFormDeleteLink($(this));
    });

});

function addImageFormDeleteLink($productFormCart) {
    var $removeImageFormA = $('<td><a href="#">Remove this product</a></td>');
    $productFormCart.append($removeImageFormA);

    $removeImageFormA.on('click', function(e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        // remove the li for the tag form
        $productFormCart.remove();
        $('#edit-cart').click();
    });
}
