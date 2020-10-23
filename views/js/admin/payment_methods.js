$(document).ready(function() {
  // jquery sortable plugin must be included. @see https://jqueryui.com/sortable/
  var $sortableElement = $('#js-payment-methods-sortable')

  $sortableElement.sortable({
    appendTo: document.body
  });

  $sortableElement.bind( "sortupdate", function(event, ui) {
    var $item = ui.item
    var position = ui.end_index

    $('.js-payment-option-position').each(function (index) {
      $(this).val(index)
    })
  });
})
