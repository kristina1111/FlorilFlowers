// $(document).ready(function () {
//     $('.js-datepicker').datepicker();
// });

var $addTagLink = $('<a href="#" class="add_tag_link">Add price</a>');
var $newLinkLi = $('<li></li>').append($addTagLink);

$(document).ready(function() {
    // Get the ul that holds the collection of prices
    var $collectionHolder = $('ul.prices-holder');

    // add the "add price" anchor and li to the prices-holder ul
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

    // $('.js-datepicker').datepicker();

});


function addPriceForm($collectionHolder, $newLinkLi) {
    // Get the data-prototype explained earlier
    var prototype = $collectionHolder.data('prototype');


    console.log($collectionHolder);

    // get the new index
    var index = $collectionHolder.data('index');

    // Replace '$$name$$' in the prototype's HTML to
    // instead be a number based on how many items we have
    var newForm = prototype.replace(/__name__/g, index);

    // increase the index with one for the next item
    $collectionHolder.data('index', index + 1);

    // Display the form in the page in an li, before the "Add a tag" link li
    var $newFormLi = $('<li></li>').append(newForm);

    // also add a remove button, just for this example
    $newFormLi.append('<a href="#" class="remove-tag">x</a>');

    $newLinkLi.before($newFormLi);

    // handle the removal, just for this example
    $('.remove-tag').click(function(e) {
        e.preventDefault();

        $(this).parent().remove();

        return false;
    });

    $('.js-datepicker').datepicker({
        format: 'yyyy-mm-dd'
    });
}
