jQuery(document).ready(function($) {

    // add into table
    $(document).on( 'click', '.product a.compare:not(.added)', function(e){
        e.preventDefault();

        var button = $(this),
            data = {
                action: evdpl_woocompare.actionadd,
                id: button.data('product_id'),
                context: 'frontend'
            },
            widget_list = $('.evdpl-woocompare-widget ul.products-list');

        // add ajax loader
        if( typeof $.fn.block != 'undefined' ) {
            button.block({message: null, overlayCSS: { background: '#fff url(' + evdpl_woocompare.loader + ') no-repeat center', backgroundSize: '16px 16px', opacity: 0.6}});
            widget_list.block({message: null, overlayCSS: { background: '#fff url(' + evdpl_woocompare.loader + ') no-repeat center', backgroundSize: '16px 16px', opacity: 0.6}});
        }

        $.ajax({
            type: 'post',
            url: evdpl_woocompare.ajaxurl.toString().replace( '%%endpoint%%', evdpl_woocompare.actionadd ),
            data: data,
            dataType: 'json',
            success: function(response){

                if( typeof $.fn.block != 'undefined' ) {
                    button.unblock();
                    widget_list.unblock()
                }

                button.addClass('added')
                    .attr( 'href', response.table_url )
                    .text( evdpl_woocompare.added_label );

                // add the product in the widget
                widget_list.html( response.widget_table );

                $("<p id='"+button.data('product_id')+"' class='compare-message'>Product already added in the comparison list.</p>").insertBefore(button);

                if ( evdpl_woocompare.auto_open == 'yes')
                    $('body').trigger( 'evdpl_woocompare_open_popup', { response: response.table_url, button: button } );
            }
        });
    });

    $(document).on('click', '.product a.compare.added', function (ev) {
        ev.preventDefault();

        var table_url = this.href;

        if (typeof table_url == 'undefined')
            return;

        $('body').trigger('evdpl_woocompare_open_popup', {response: table_url, button: $(this)});
    });


    // open popup
    $('body').on( 'evdpl_woocompare_open_popup', function( e, data ) {
        $('body').addClass('evdpl-wc-compare-popup-added');
        var response = data.response;

        if ( $(window).width() >= 300 ) {
            $.colorbox({
                href: response,
                iframe: true,
                width: '90%',
                height: '90%',
                className: 'evdpl_woocompare_colorbox',
                close: evdpl_woocompare.close_label,
                transition: 'none',
                onClosed: function(){
                    var widget_list = $('.evdpl-woocompare-widget ul.products-list'),
                        data = {
                            action: evdpl_woocompare.actionreload,
                            context: 'frontend'
                        };

                    if( typeof $.fn.block != 'undefined' ) {
                        widget_list.block({message: null, overlayCSS: {background: '#fff url(' + evdpl_woocompare.loader + ') no-repeat center', backgroundSize: '16px 16px', opacity: 0.6}});
                    }

                    $.ajax({
                        type: 'post',
                        url: evdpl_woocompare.ajaxurl.toString().replace( '%%endpoint%%', evdpl_woocompare.actionreload ),
                        data: data,
                        success: function(response){
                            // add the product in the widget
                            if( typeof $.fn.block != 'undefined' ) {
                                widget_list.unblock().html(response);
                            }
                            widget_list.html(response);
                        }
                    });
                }
            });

            $(window).resize(function () {
                $.colorbox.resize({
                    width: '90%',
                    height: '90%'
                });
            });

        } else {
            var urlparts = response.split('?');
            var parameter = 'iframe';
            if (urlparts.length >= 2) {
                var prefix = encodeURIComponent(parameter) + '=';
                var pars = urlparts[1].split(/[&;]/g);
                for (var i = pars.length; i-- > 0;) {
                    if (pars[i].lastIndexOf(prefix, 0) !== -1) {
                        pars.splice(i, 1);
                    }
                }
                response = urlparts[0] + '?' + pars.join('&');
            }

            window.open( response, evdpl_woocompare.table_title);
        }
    });

    // remove from table
    $(document).on( 'click', '.remove a', function(e){
        e.preventDefault();

        var button = $(this),
            data = {
                action: evdpl_woocompare.actionremove,
                id: button.data('product_id'),
                context: 'frontend'
            },
            product_cell = $( 'td.product_' + data.id + ', th.product_' + data.id );

        // add ajax loader
        if( typeof $.fn.block != 'undefined' ) {
            button.block({
                message: null,
                overlayCSS: {
                    background: '#fff url(' + evdpl_woocompare.loader + ') no-repeat center',
                    backgroundSize: '16px 16px',
                    opacity: 0.6
                }
            });
        }

        $.ajax({
            type: 'post',
            url: evdpl_woocompare.ajaxurl.toString().replace( '%%endpoint%%', evdpl_woocompare.actionremove ),
            data: data,
            dataType:'html',
            success: function(response){
                $('.compare[data-product_id="' + button.data('product_id') + '"]').addClass('test');
                // in compare table
                var content = $(response).filter('#evdpl-woocompare');
                // replace content
                $( '#evdpl-woocompare' ).replaceWith( content );

                $('.compare[data-product_id="' + button.data('product_id') + '"]', window.parent.document).removeClass('added').html( evdpl_woocompare.button_text );
                
                $('p.compare-message#'+button.data('product_id'), window.parent.document).remove();

                // removed trigger
                $( document ).trigger('evdpl_woocompare_product_removed');
            }
        });
    });


    // ##### LINK OPEN COMPARE POPUP #####

    $('.evdpl-woocompare-open a, a.evdpl-woocompare-open').on('click', function(e){
        e.preventDefault();
        $('body').trigger('evdpl_woocompare_open_popup', { response: evdpl_add_query_arg('action', evdpl_woocompare.actionview) + '&iframe=true' });
    });

    // ##### WIDGET ######

    $('.evdpl-woocompare-widget')

    // view table (click on compare
        .on('click', 'a.compare', function (e) {
            e.preventDefault();
            $('body').trigger('evdpl_woocompare_open_popup', { response: $(this).attr('href') });
        })

        // remove product & clear all
        .on('click', 'li a.remove, a.clear-all', function (e) {
            e.preventDefault();

            var lang = $( '.evdpl-woocompare-widget .products-list').data('lang');

            var button = $(this),
                prod_id = button.data('product_id'),
                data = {
                    action: evdpl_woocompare.actionremove,
                    id: prod_id,
                    context: 'frontend',
                    responseType: 'product_list',
                    lang: lang
                },
                product_list = button.parents('.evdpl-woocompare-widget').find('ul.products-list');

            // add ajax loader
            if( typeof $.fn.block != 'undefined' ) {
                product_list.block({message: null,
                    overlayCSS             : {
                        background    : '#fff url(' + evdpl_woocompare.loader + ') no-repeat center',
                        backgroundSize: '16px 16px',
                        opacity       : 0.6
                    }
                });
            }

            $.ajax({
                type: 'post',
                url: evdpl_woocompare.ajaxurl.toString().replace( '%%endpoint%%', evdpl_woocompare.actionremove ),
                data: data,
                dataType: 'html',
                success: function (response) {

                    if( prod_id == 'all' ) {
                        $( '.compare.added' ).removeClass('added').html( evdpl_woocompare.button_text );
                    }
                    else {
                        $('.compare[data-product_id="' + prod_id + '"]' ).removeClass('added').html( evdpl_woocompare.button_text );
                    }

                    product_list.html(response);
                    if( typeof $.fn.block != 'undefined' ) {
                        product_list.unblock();
                    }


                }
            });
        });

    $('body').on('added_to_cart', function( ev, fragments, cart_hash, $thisbutton ){
        if( $( $thisbutton).closest( 'table.compare-list' ).length )
            $thisbutton.hide();
    });

    function evdpl_add_query_arg(key, value)
    {
        key = escape(key); value = escape(value);

        var s = document.location.search;
        var kvp = key+"="+value;

        var r = new RegExp("(&|\\?)"+key+"=[^\&]*");

        s = s.replace(r,"$1"+kvp);

        if(!RegExp.$1) {s += (s.length>0 ? '&' : '?') + kvp;};

        //again, do what you will here
        return s;
    }

    $(document).on('click','#cboxClose',function(){
        $('body').removeClass('evdpl-wc-compare-popup-added');
    });

});