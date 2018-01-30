jQuery(document).ready(function( $ ) {

  // Copy Shortcode / Template Tag
  function copyToClipboard(element) {  var $temp = $("<input>");
    $("body").append($temp);
    $temp.val($(element).text()).select();
    document.execCommand("copy");
    $temp.remove();
  }

  $('#bq_copy_sc').on('click', function() {
    copyToClipboard("#wop-shortcode")
  })

  $('#bq_copy_tt').on('click', function() {
    copyToClipboard("#wop-template-tag")
  })

});