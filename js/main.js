(function ($, products, upsells, is_product_page, ajax_object) {
    
    /* Remove blue success banner */
    if (is_product_page) {
        var element = document.createElement('style'), sheet;
        document.head.appendChild(element);
        sheet = element.sheet;
        let cs = '#genesis-content > div.woocommerce-notices-wrapper { display: none; }'
        sheet.insertRule(cs, 0);
    }

    /* UI, open pop-up bootstrap */
    function openModal(products, upsells) {
            var body = '<div style="padding-left: 20px; padding-right: 20px;">'  +
            '<div class="row" style="clear:both; margin-bottom:20px; margin-top: 20px;">' + 
                '<div class="col-md-12" style="padding: 0 !important;">' +
                '<h4 class="winkelwagen-popup-h4">Toegevoegd aan je winkelmand</h4>'+
                '<button class="winkelwagen-popup-buttonverderwinkelen" data-bs-dismiss="modal" aria-label="Close"><i class="fa fa-times"> <span>Verder winkelen</span></i></button>' +
            
                '</div>' +
            '</div>';

            product = products.shift();

            body +=
                '<div class="row" style="display: flex; justify-content: center; clear:both; border-bottom: 1px solid #e6e6e6 !important; border-top: 1px solid #e6e6e6 !important; margin-bottom: 20px; padding-top: 10px; padding-bottom: 10px;">' + 
                '<div class="col-lg-2 col-md-6 col-sm-12" style="align-items: center;display: flex;justify-content: center;">' + 
                '<img src="'+product['image']+'" width="100px"></img>' +
                '</div>' +
                '<div class="col-lg-3 col-md-6 col-sm-12" style="align-items: center;display: flex;justify-content: flex-start;">' + 
                '<span class="winkelwagen-popup-name">'+product['name']+'</span>' +
                '</div>' +
                '<div class="col-lg-1 col-md-6 col-sm-12" style="align-items: center;display: flex;justify-content: center;">' + 
                '<span class="winkelwagen-popup-name">x'+product['quantity']+'</span>' +
                '</div>' +
                '<div class="col-lg-3 col-md-6 col-sm-12" style="align-items: center;display: flex;justify-content: center; flex-direction: column;">' + 
                
                '<div class="winkelwagen-popup-price ex-btw">€ '+Number(product['price'] * product['quantity']).toLocaleString("nl-BE", { minimumFractionDigits: 2, maximumFractionDigits: 2})+' <span>Excl. btw</span></div>' +
                '<div class="winkelwagen-popup-price inc-btw">€ '+Number(product['price'] * product['quantity'] * 1.21).toLocaleString("nl-BE", { minimumFractionDigits: 2, maximumFractionDigits: 2})+' <span>Incl. btw</span></div>' +
                '</div>' +
                '<div class="winkelwagen-popup-buttondiv col-lg-3 col-md-6 col-sm-12"> ' + 
                '<button class="winkelwagen-popup-button"><a id="winkelwagenpopup-button-link" href="/winkelmand">Winkelmand</a><i class="fa fa-arrow-right"></i></button>' +
                '</div>' +
                '</div>';
            
            products.forEach((product)=> {
            
                body +=
                '<div class="row" style="display: flex; justify-content: center; clear:both; border-bottom: 1px solid #e6e6e6 !important; border-top: 1px solid #e6e6e6 !important; margin-bottom: 20px; padding-top: 10px; padding-bottom: 10px;">' + 
                '<div class="col-lg-2 col-md-6 col-sm-12" style="align-items: center;display: flex;justify-content: center;">' + 
                '<img src="'+product['image']+'" width="100px"></img>' +
                '</div>' +
                '<div class="col-lg-3 col-md-6 col-sm-12" style="align-items: center;display: flex;justify-content: flex-start;">' + 
                '<span class="winkelwagen-popup-name">'+product['name']+'</span>' +
                '</div>' +
                '<div class="col-lg-1 col-md-6 col-sm-12" style="align-items: center;display: flex;justify-content: center;">' + 
                '<span class="winkelwagen-popup-name">x'+product['quantity']+'</span>' +
                '</div>' +
                '<div class="col-lg-3 col-md-6 col-sm-12" style="align-items: center;display: flex;justify-content: center; flex-direction: column;">' + 
                
                '<div class="winkelwagen-popup-price ex-btw">€ '+Number(product['price'] * product['quantity']).toLocaleString("nl-BE", { minimumFractionDigits: 2, maximumFractionDigits: 2})+' <span>Excl. btw</span></div>' +
                '<div class="winkelwagen-popup-price inc-btw">€ '+Number(product['price'] * product['quantity'] * 1.21).toLocaleString("nl-BE", { minimumFractionDigits: 2, maximumFractionDigits: 2})+' <span>Incl. btw</span></div>' +
                '</div>' +
                '<div class="winkelwagen-popup-buttondiv col-lg-3 col-md-6 col-sm-12"> ' + 
                '</div>' +
                '</div>';
            });
            if(upsells && upsells.length > 0) {
                body += '<div class="row" style="margin-bottom: 20px;"><div class="col-md-12" style="text-align:center;"><h5 class="winkelwagen-popup-h5">Ook handig om gelijk mee te bestellen!</h5></div></div>' + '<div class="products row" style="align-items: center; justify-content: center;">';
                upsells.forEach((upsell)=> {

                    body += '<div class="row" style="clear:both; border-bottom: 1px solid #e6e6e6 !important; border-top: 1px solid #e6e6e6 !important; margin-bottom: 20px; padding-top: 10px; padding-bottom: 10px;">' + 
                        '<div class="col-lg-3 col-md-6 col-sm-12" style="align-items: center;display: flex;justify-content: center;">' + 
                        '<img src="'+upsell['image']+'" width="100px"></img>' +
                        '</div>' +
                        '<div class="col-lg-3 col-md-6 col-sm-12" style="align-items: center;display: flex;justify-content: center;">' + 
                        '<span class="winkelwagen-popup-name"><a href="'+upsell['link']+'" class="woocommerce-LoopProduct-link woocommerce-loop-product__link">'+upsell['name']+'</a></span>' +
                        '</div>' +
                        '<div class="col-lg-3 col-md-6 col-sm-12" style="align-items: center;display: flex;justify-content: center; flex-direction: column;">' + 
                        
                        '<div class="winkelwagen-popup-price ex-btw">€ '+Number(upsell['price']).toLocaleString("nl-BE", { minimumFractionDigits: 2, maximumFractionDigits: 2})+' <span>Excl. btw</span></div>' +
                        '<div class="winkelwagen-popup-price inc-btw">€ '+Number(upsell['price'] * 1.21).toLocaleString("nl-BE", { minimumFractionDigits: 2, maximumFractionDigits: 2})+' <span>Incl. btw</span></div>' +
                        '</div>' +
                        '<div class="winkelwagen-popup-buttondiv col-lg-3 col-md-6 col-sm-12"> ' + 
                        '<button class="winkelwagen-popup-button"><a href="?add-to-cart='+upsell['id']+'" data-open="0" data-quantity="1" class="button wp-element-button product_type_simple ajax_add_to_cart add_to_cart_button" data-product_id="'+upsell['id']+'" data-product_sku="" aria-label="Toevoegen" rel="nofollow">Toevoegen</a></button>' +
                        '</div>' +
                        '</div>';
                });
                body += '</div>';
            }
            bootstrap.showModal({
                title: 'winkelmand',
                backdrop: true,
                body,
                footer: '<button type="submit" class="btn btn-primary save-btn" style="background-color: #1D97FF;">'+'save'+'</button>',
                onCreate: function (modal) {
                }
            });							
        
    }

    /* Triggers */
    /* After DOM is ready */
    $(document).ready(function () {
        /* Open pop-up if blue banner appears in dom */
        if($('.woocommerce-message > a').html() == 'Winkelwagen bekijken') {

            openModal(products, upsells);
            $('.modal-dialog').addClass('modal-lg');
            $('.modal-header').remove();
            $('.modal-footer').remove();
        }

        $("body").on("hidden.bs.modal", ".modal", function () {
            $.removeCookie('itemsincart', { path: '/' });
            $.removeCookie('newtocart', { path: '/' });
            $.removeCookie('itemsincart', { path: 'tankkopenbe.boltestnl.be' });
            $.removeCookie('newtocart', { path: 'tankkopenbe.boltestnl.be' });
            
        });

        $("body").on('click', '.modal', function(ev){
            if(ev.target != $(this)[0]) return;
            $('.modal').modal('hide');
        });

        /* Open pop-up on category page */
        $(".ajax_add_to_cart").click(function(ev){    
      
            if($(this).data('open') != undefined) return; 

            $.ajax({
                url: ajax_object.ajaxurl, 
                type: 'POST',
                data:{ 
                  action: 'get_productinfo', 
                  product_id: $(this).data('product_id')
                },
                success: function( data ){
                    data = JSON.parse(data);
                    var product = {
                        "id" : $(this).data('product_id'),
                        "image" : data['image'],
                        "price" : data['price'],
                        "name"  : data['name'],
                        "quantity" : 1
                    }
                    openModal([product], data['upsells']);
                    $('.modal-dialog').addClass('modal-lg');
                    $('.modal-header').remove();
                    $('.modal-footer').remove();
                }
              });            
        });
    });
    
})(jQuery, products, upsells, is_product_page, ajax_object);

