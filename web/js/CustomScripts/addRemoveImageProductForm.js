// $(document).ready(function () {
//     $('.js-datepicker').datepicker();
// });

var $addImageLink = $('<a href="#" class="add-image-link">Add image</a>');
var $newImageLink = $('<div></div>').append($addImageLink);

$(document).ready(function() {
    // Get the div that holds the collection of images
    var $collectionHolder = $('div.images-holder');

    // add a delete link to all of the existing image form div elements
    $collectionHolder.find('div.image-single').each(function() {
        addImageFormDeleteLink($(this));
    });

    // add the "add image" anchor and div to the images-holder div
    $collectionHolder.append($newImageLink);

    // count the current form inputs we have (e.g. 2), use that as the new
    // index when inserting a new item (e.g. 2)
    $collectionHolder.data('index', $collectionHolder.find(':input').length);

    $addImageLink.on('click', function(e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        // add a new image form (see code block below)
        addImageForm($collectionHolder, $newImageLink);
    });

});


function addImageForm($collectionHolder, $newImageLink) {
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

    // Display the form in the page in div, before the "Add image" link div
    var $newFormDiv = $('<div></div>').append(newForm);

    // also add a remove button, just for this example
    // $newFormDiv.append('<a href="#" class="remove-image">x</a>');

    $newImageLink.before($newFormDiv);

    // handle the removal, just for this example
    // $('.remove-image').click(function(e) {
    //     e.preventDefault();
    //
    //     $(this).parent().remove();
    //
    //     return false;
    // });

// add a delete link to the new form
    addImageFormDeleteLink($newFormDiv);

}

function addImageFormDeleteLink($imageFormDiv) {
    var $removeImageFormA = $('<a href="#">delete this Image</a>');
    $imageFormDiv.append($removeImageFormA);

    $removeImageFormA.on('click', function(e) {
        // prevent the link from creating a "#" on the URL
        e.preventDefault();

        // remove the li for the tag form
        $imageFormDiv.remove();
    });
}
