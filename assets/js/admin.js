
jQuery(document).ready(function () {

    if (!jQuery('#the-list').find('tr:first-child').hasClass('no-items')) {

        jQuery('#the-list').sortable({
            placeholder: "taxonomy-placeholder",
            forcePlaceholderSize: true,
            axis: "y",
            start: function (e, ui) {
                ui.placeholder.height(ui.item.height());
            },
            update: function (e, ui) {
                
                jQuery(ui.item[0]).find('input[type="checkbox"]').hide().after('<img src="images/spinner.gif" class="ms-cto-spinner" />');

                var data_array = [];

                jQuery('#the-list').find('tr.ui-sortable-handle').each(function () {
                    data_array.push([jQuery(this).attr('id').replace('tag-', ''), jQuery(this).index()]);
                });

                var data = {
                    'action': 'update_taxonomy_order',
                    'data_array': data_array
                };

                jQuery.ajax({
                    url: ms_cto_data.ajax_url
                    , data: data
                    , type: "POST"
                    , success: function (response) {
                        jQuery('.ms-cto-spinner').remove();
                        jQuery(ui.item[0]).find('input[type="checkbox"]').show();
                    }
                });

            }
        });
    }

}); 