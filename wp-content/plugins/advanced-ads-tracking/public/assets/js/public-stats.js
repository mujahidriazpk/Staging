;(function($){
    "use strict";
    
    var spinner = $( '<img alt="" style="margin-top:150px;" class="ajax-spinner" src="/wp-admin/images/spinner-2x.gif" />' );
    
    $( document ).on( 'submit', '#period-form', function ( ev ) {
        var overlay = $( '<div />' ).css({
            position: 'fixed',
            width: '100%',
            height: '100%',
            top: 0,
            left: 0,
            textAlign: 'center',
            zindex: 900,
            backgroundColor: 'rgba( 255, 255, 255, 0.8)',
        }).append( spinner );
        $( '#stats-content' ).append( overlay );
    } );
    
    $( document ).on( 'change', '#period-form select', function(){
        $( this ).parents( 'form' ).submit();
    } );
    
    $(function(){
        var lang = $( 'html' ).attr( 'lang' );
        
        var supportedLocale = ['en', 'fr', 'de', 'ar', 'ru', 'pt'];
        
        if ( -1 != supportedLocale.indexOf( lang.split( '-' )[0] ) ) {
            $.jsDate.config.defaultLocale = lang.split( '-' )[0];
        }
        if ( 'pt-BR' == lang ) {
            // português do Brasil
            $.jsDate.config.defaultLocale = 'pt-BR';
        }
        statsGraphOptions['axes']['xaxis']['renderer'] = $.jqplot.DateAxisRenderer;
        window.myGraph = $.jqplot( 'public-stat-graph', lines, statsGraphOptions );
    });
    
})(jQuery);
