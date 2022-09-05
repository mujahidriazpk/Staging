/*
 * sorttable.js
 *
 * Requires: jQuery (tested with v 1.11)
 *
 * jQuery plug-in that allows you to sort table by any column
*/

jQuery.fn.addSortWidget = function(options){
	var defaults = {
		img_asc: "/wp-content/plugins/WP_Sale_Graph/img/desc_sort.gif",	
        img_desc: "/wp-content/plugins/WP_Sale_Graph/img/asc_sort.gif",	
		img_nosort: "/wp-content/plugins/WP_Sale_Graph/img/no_sort.gif",		
	};
	
	var options = jQuery.extend({}, defaults, options),
		$destElement = jQuery(this),
        is_asc = true;
		
	jQuery("th", $destElement).each(function(index){ // to each header cell (index is useful while sorting)
        jQuery("<img>")                              // create image that allows you to sort by specific column 
            .attr('src', options.img_nosort)
            .addClass('sorttable_img')
            .css({
                cursor: 'pointer',
                'margin-left': '10px',
            })
            .on('click', function(){
                jQuery(".sorttable_img", $destElement).attr('src', options.img_nosort); 
                jQuery(this).attr('src', (is_asc) ? options.img_desc : options.img_asc);
                is_asc = !is_asc;
                
                var rows = jQuery("tr", $destElement).not(":has(th)").get(); // save all rows (tr) into array (.get())
                rows.sort(function(a, b){               
                    // sort array with table rows
                    var m = jQuery("td:eq(" + index + ")", a).text().replace(/,/g,''); // get column you needed by using index of th element (closure)
                    var n =jQuery("td:eq(" + index + ")", b).text().replace(/,/g,'');
					
					var m = m.substring(1,m.length);
					var n = n.substring(1,n.length);
                    console.log(m+"=="+n)
                    // if elements are numbers
                    if (!isNaN(m) && !isNaN(n))     
                        return (is_asc) ? (m - n) : (n - m);
                    
                    // if elements are strings
                    if (is_asc)
                        return m.localeCompare(n); // asc
                    else
                        return n.localeCompare(m); // desc
                });
                
                var tbody = ($destElement.has("tbody")) ? "tbody" : ""; // check if table has tbody
                for (var i=0; i<rows.length; i++){
                    jQuery(tbody, $destElement).append(rows[i]); // add each row to table (elements do not duplicate, just place to new position)
                }
            })
            .appendTo(this); // add created sort image with click event handler to current th element
    });
	
	return $destElement;

}