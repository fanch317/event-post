// @ref http://rachelcarden.com/2012/03/manage-wordpress-posts-using-bulk-edit-and-quick-edit/
(function ($) {

    // we create a copy of the WP inline edit post function
    var $eventpost_inline_edit = inlineEditPost.edit;
    // and then we overwrite the function with our own code
    inlineEditPost.edit = function (id) {
        // "call" the original WP edit function
        // we don't want to leave WordPress hanging
        $eventpost_inline_edit.apply(this, arguments);

        // now we take care of our business

        // get the post ID
        var $post_id = 0;
        if (typeof (id) === 'object')
            $post_id = parseInt(this.getId(id));

        console.log($post_id);

        if ($post_id > 0) {
            // define the edit row
            var key;
            var $edit_row = $('#edit-' + $post_id);
            var $post_row = $('#post-' + $post_id);
            var attrs = eventpost_inline_edit.quick;

            // get the data and populate
            for(field_group in attrs){
                for(key in attrs[field_group]){
                    $( '.eventpost-inline-'+key, $edit_row ).val( $( '.inline-edit-value.'+key, $post_row ).html() );
                    if(key==='event_color'){
                        eventpost_inline_colorpick($( '.eventpost-inline-'+key, $edit_row ));
                    }
                }
            }
        }
    };
    function eventpost_inline_colorpick(target){
        console.log(target);
        if(target.hasClass('is-bulk')){
            target.val('false').trigger('change');
        }
        if(target && target.next('.eventpost-inline-colorpicker-list').length){
            return;
        }
        var eventpost_inline_colorpicker_html=$('<span class="eventpost-inline-colorpicker-list"></span>');
        $('option', target).each(function(){
            if($(this).attr('value')!=='false'){
                color_item = $('<img src="'+$(this).data('path')+'" alt="'+$(this).attr('value')+'" class="ep-i-c-'+$(this).attr('value')+'"/>').click(function(){
                    $(this).parent().prev('.eventpost-inline-colorpicker').val($(this).attr('alt')).trigger('change');
                });
                eventpost_inline_colorpicker_html.append(color_item);
            }
        });
        target.after(eventpost_inline_colorpicker_html).hide().unbind('change').on('change', function(){
            value = $(this).val();
            console.log(value);
            $('img', $(this).next('.eventpost-inline-colorpicker-list')).attr('style', '').filter('.ep-i-c-'+value).css({border: '#000 1px solid', padding: '3px'});
        });
        target.trigger('change');
    }

    var $eventpost_bulk_sent = false;

    function eventpost_bulk_send(){
        if($eventpost_bulk_sent){
            return;
        }
        $eventpost_bulk_sent = true;
        
        // define the bulk edit row
        var key;
        var $bulk_row = $('#bulk-edit');
        var post_type = $('#posts-filter input[name=post_type]').val();
        var attrs = eventpost_inline_edit.bulk;

        // get the selected post ids that are being edited
        var $post_ids = new Array();
        $bulk_row.find('#bulk-titles').children().each(function () {
            $post_ids.push($(this).attr('id').replace(/^(ttle)/i, ''));
        });
        datas = {
            action: 'eventpost_save_bulk', // this is the name of our WP AJAX function that we'll set up next
            post_ids: $post_ids, // and these are the 2 parameters we're passing to our function
            eventpost_nonce: $('#eventpost_nonce').val()
        };

        // get the data
        for(field_group in attrs){
            for(key in attrs[field_group]){
                if($( '#posts-filter .eventpost-inline-'+key+'' ).val() !== 'false'){
                    datas[key] = $( '#posts-filter .eventpost-inline-'+key+'' ).val();
                }
            }
        }

        // save the data
        $.ajax({
            url: ajaxurl, // this is a variable that WordPress has already defined for us
            type: 'POST',
            async: false,
            cache: false,
            data: datas
        });
    }
    $('.eventpost-bulk-colorpicker-button').click(function(){
       eventpost_inline_colorpick($(this).next('.eventpost-inline-event_color'));
    }).next('.eventpost-inline-event_color').hide();
    $('#bulk_edit').live('click', function () {
        eventpost_bulk_send();
    });
    $('#posts-filter').live('submit', function(){
        eventpost_bulk_send();
    });
})(jQuery);