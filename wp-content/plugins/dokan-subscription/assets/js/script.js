;(function($) {

$('.pack_content_wrapper').on('click','.buy_product_pack',function(evt){
    
    url = $(this).attr('href');
    
    console.log(dps);
    console.log(url);
    
});

var wrapper = $( '.dps-pack-wrappper' );
var Dokan_Subscription_details = {

    init : function() {
        //$('.dps-pack-details').hide();
        wrapper.on( 'change', 'select#dokan-subscription-pack', this.show_details );
        this.show_details();
    },   
    show_details : function(){
        id = $( 'select#dokan-subscription-pack' ).val();        
        $('.dps-pack').hide();
        $('.dps-pack-'+id).show();
    }
    
    
};

$(function() {
    Dokan_Subscription_details.init();
});

})(jQuery);