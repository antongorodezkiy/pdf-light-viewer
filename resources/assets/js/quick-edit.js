// (c) https://rudrastyh.com/wordpress/quick-edit-tutorial.html
jQuery(function($) {

  if (typeof inlineEditPost == 'undefined') return;

  // it is a copy of the inline edit function
  var wp_inline_edit_function = inlineEditPost.edit;

  // we overwrite the it with our own
  inlineEditPost.edit = function(post_id) {

    // let's merge arguments of the original function
    wp_inline_edit_function.apply(this, arguments);

    // get the post ID from the argument
    var id = 0;
    if (typeof(post_id) == 'object') { // if it is object, get the ID number
      id = parseInt(this.getId(post_id));
    }

    //if post id exists
    if (id > 0) {

      // add rows to variables
      var
        specific_post_edit_row = $('#edit-' + id),
        specific_post_row = $('#post-' + id),
        product_price = $('.column-price', specific_post_row).text().substring(1);

      // populate the inputs with column data
      $('td', specific_post_row).each(function() {
        var column = $(this);
        var classList = column.prop('classList');

        for (var metaKey in classList) {

          if ($('input[name="'+classList[metaKey]+'"]:text', specific_post_edit_row).length) {
            $('input[name="'+classList[metaKey]+'"]:text', specific_post_edit_row).val(column.text());
          }

          else if ($('input[name="'+classList[metaKey]+'"]:checkbox', specific_post_edit_row).length) {
            $('input[name="'+classList[metaKey]+'"]:checkbox', specific_post_edit_row).prop('checked', column.text() == 'on');
          }

          else if ($('select[name="'+classList[metaKey]+'"]', specific_post_edit_row).length) {
            $('select[name="'+classList[metaKey]+'"] option[value="'+column.text()+'"]', specific_post_edit_row).prop('selected', true);
          }
        }
      });
    }
  }
});
