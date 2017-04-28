// $(document).ready(function () {
//     $('.js-datepicker').datepicker();
// });

var $addTagLink = $('<a href="#" class="add_price_link">Add price</a>');
var $newLinkLi = $('<div></div>').append($addTagLink);

$(document).ready(function() {
    // Get the div that holds the collection of prices
    var $collectionHolder = $('div.prices-holder');

    // add a delete link to all of the existing price form div elements
    $collectionHolder.find('div.price-single').each(function() {
        addPriceFormDeleteLink($(this));
    });

    // add the "add price" anchor and div to the prices-holder div
    $collectionHolder.append($newLinkLi);

    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    $collectionHolder.data('index', $collectionHolder.find(':input').length);

    $addTagLink.on('click', function(e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        // add a new tag form (see code block below)
        addPriceForm($collectionHolder, $newLinkLi);
    });

    // $('.js-datepicker').datepicker({
    //     format: 'yyyy-mm-dd'
    // });

});


function addPriceForm($collectionHolder, $newLinkLi) {
    // Get the data-prototype
    var prototype = $collectionHolder.data('prototype');


    console.log($collectionHolder);

    // get the new index
    var index = $collectionHolder.data('index');

    // Replace '$$name$$' in the prototype's HTML to
    // instead be a number based on how many items we have
    var newForm = prototype.replace(/__name__/g, index);

    // increase the index with one for the next item
    $collectionHolder.data('index', index + 1);

    // Display the form in the page in div, before the "Add price" link div
    var $newFormDiv = $('<div></div>').append(newForm);

    // also add a remove button, just for this example
    // $newFormDiv.append('<a href="#" class="remove-price">x</a>');

    $newLinkLi.before($newFormDiv);

    // handle the removal, just for this example
    // $('.remove-price').click(function(e) {
    //     e.preventDefault();
    //
    //     $(this).parent().remove();
    //
    //     return false;
    // });

    // $('.js-datepicker').datepicker({
    //     format: 'yyyy-mm-dd'
    // });

// add a delete link to the new form
    addPriceFormDeleteLink($newFormDiv);

}

function addPriceFormDeleteLink($priceFormDiv) {
    var $removePriceFormA = $('<a href="#">delete this price</a>');
    $priceFormDiv.append($removePriceFormA);

    $removePriceFormA.on('click', function(e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        // remove the li for the tag form
        $priceFormDiv.remove();
    });
}
