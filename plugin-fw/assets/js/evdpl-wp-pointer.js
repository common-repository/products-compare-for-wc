jQuery( function($) {
    var pointers    = custom_pointer.pointers[0],
        options     = pointers.options,
        target      = $(pointers.target),
        pointer_id  = pointers.pointer_id;

    $(target).find('.wp-submenu li a').each(function () {

            var t = $(this),
                href = t.attr('href');

            href = href.replace('admin.php?page=', '');

            if( href == pointer_id ){

                var selected_plugin_row = t.add( target ),
                    top_level_menu      = target.find( pointers.target.replace( '#', '.' ) );

                target.toggleClass('wp-no-current-submenu wp-menu-open wp-has-current-submenu');

                t.pointer({
                    pointerClass: 'evdpl-wp-pointer',
                    content : options.content,
                    position: options.position,
                    open    : function () {
                        selected_plugin_row.toggleClass( 'evdpl-pointer-selected-row' );
                        top_level_menu.addClass( 'evdpl-pointer' );
                    },


                    close   : function () {
                        target.toggleClass('wp-no-current-submenu wp-menu-open wp-has-current-submenu');
                        selected_plugin_row.toggleClass( 'evdpl-pointer-selected-row' );
                        top_level_menu.removeClass( 'evdpl-pointer' );

                        $.ajax({
                            type   : 'POST',
                            url    : ajaxurl,
                            data   : {
                                "action" : "dismiss-wp-pointer",
                                "pointer": pointer_id
                            },
                            success: function (response) {
                            }
                        });

                    }
                }).pointer('open');
            } else if( 'evdpl_default_pointer' == pointer_id ) {

                 var selected_plugin_row = t.add( target ),
                     top_level_menu      = target.find( pointers.target.replace( '#', '.' )),
                     evdpl_plugins         = $( pointers.target );

                evdpl_plugins.addClass('wp-has-current-submenu');

                top_level_menu.pointer({
                    pointerClass: 'evdpl-wp-pointer',
                    content : options.content,
                    position: options.position,

                    open    : function () {
                        evdpl_plugins.addClass( 'evdpl-pointer-selected-row' );
                    },

                    close   : function () {
                        evdpl_plugins.removeClass( 'evdpl-pointer-selected-row wp-has-current-submenu' );

                        $.ajax({
                            type   : 'POST',
                            url    : ajaxurl,
                            data   : {
                                "action" : "dismiss-wp-pointer",
                                "pointer": pointer_id
                            },
                            success: function (response) {
                            }
                        });
                    }
                }).pointer('open');
            }
        });
});