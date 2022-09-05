<?php



if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly



/**

 * Auction Product Class

 *

 * @class WC_Product_Auction

 *

 */

if (  !class_exists( 'WC_Product_Auction' ) ) :

class WC_Product_Auction extends WC_Product {



	public $post_type = 'product';

    public $product_type = 'auction';



    /**

     * Stores product data.

     * auction_start_price

     * @var array

     */

    protected $extra_data = array(

				

    );



	/**

	 * __construct function.

	 *

	 * @access public

	 * @param mixed $product

     *

	 */

	public function __construct( $product ) {

		global $sitepress;



		date_default_timezone_set("UTC");



		if(is_array($this->data))

            $this->data = array_merge( $this->data, $this->extra_data );





		$this->auction_item_condition_array = apply_filters( 'simple_auction_item_condition',array( 'new' => __('New', 'wc_simple_auctions'), 'used'=> __('Used', 'wc_simple_auctions') ));



		parent::__construct( $product );

		$this->is_closed();

		$this->is_started();

		$this->check_bid_count();





	}

	/**

     * Returns the unique ID for this object.

     * @return int

     */

    public function get_id() {

        return $this->id; 

    }



    /**

     * Get internal type.

     *

     * @return string

     */

    public function get_type() {

        return 'auction';

    }



	/**

	 * Checks if a product is auction

	 *

	 * @access public

	 * @return bool

     *

	 */

	function is_auction() {



		return $this->get_type() == 'auction' ? true : false;

	}



	/**

	 * Get current bid

	 *

	 * @access public

	 * @return int

     *

	 */

	function get_curent_bid() {

		

			if ($this->get_auction_current_bid()){

				return apply_filters('woocommerce_simple_auctions_get_current_bid' ,(float)$this->get_auction_current_bid(),$this);

			}

			return apply_filters('woocommerce_simple_auctions_get_current_bid' ,(float)$this->get_auction_start_price(), $this);

		

	}



    /**

     * Get bid increment

     *

     * @access public

     * @return mixed

     *

     */

	function get_increase_bid_value() {

		

			if ($this->get_auction_bid_increment()){

				return apply_filters('woocommerce_simple_auctions_get_increase_bid_value' , $this->get_auction_bid_increment() , $this );

			} else {

				return FALSE;

			}

		

	}



    /**

     * Get auction condition

     *

     * @access public

     * @return mixed

     *

     */

	function get_condition() {

		

			if ($this->get_auction_item_condition()){

				return apply_filters('woocommerce_simple_auctions_get_condition' , $this->auction_item_condition_array[$this->get_auction_item_condition()] , $this );

			} else {

				return FALSE;

			}

		

	}



    /**

     * Get auction end time

     *

     * @access public

     * @return mixed

     *

     */

	function get_auction_end_time() {

		

			if ($this->get_auction_dates_to()){

				return apply_filters('woocommerce_simple_auctions_get_auction_end_time' ,$this->get_auction_dates_to(), $this );

			} else {

				return FALSE;

			}

		

	}



    /**

     * Get auction start time

     *

     * @access public

     * @return mixed

     *

     */

	function get_auction_start_time() {

		

			if ($this->get_auction_dates_from()){

				return apply_filters('woocommerce_simple_auctions_get_auction_start_time', $this->get_auction_dates_from(), $this);

			} else {

				return FALSE;

			}

		

	}



    /**

     * Get remaining seconds till auction end

     *

     * @access public

     * @return mixed

     *

     */

	function get_seconds_remaining() {

		

			if ($this->get_auction_dates_to()){

				if ( is_user_logged_in() ){
					return apply_filters('woocommerce_simple_auctions_get_seconds_remaining', strtotime($this->get_auction_dates_to())  -  (get_option( 'gmt_offset' )*3600) - time() ,  $this );
				} else { 
					return apply_filters('woocommerce_simple_auctions_get_seconds_remaining', strtotime($this->get_auction_dates_to())  -  (get_option( 'gmt_offset' )*3600) ,  $this );
				}

				

			} else {

				return FALSE;

			}

		

	}



    /**

     * Get seconds till auction starts

     *

     * @access public

     * @return mixed

     *

     */

	function get_seconds_to_auction() {

			if ($this->get_auction_dates_from()){
				if ( is_user_logged_in() ){
					return apply_filters('woocommerce_simple_auctions_get_seconds_to_auction', strtotime($this->get_auction_dates_from())  -  (get_option( 'gmt_offset' )*3600) - time() ,  $this );
				} else { 
					return apply_filters('woocommerce_simple_auctions_get_seconds_to_auction', strtotime($this->get_auction_dates_from())  -  (get_option( 'gmt_offset' )*3600) ,  $this );
				}
				//return apply_filters('woocommerce_simple_auctions_get_seconds_to_auction', strtotime($this->get_auction_dates_from()) - (get_option( 'gmt_offset' )*3600), $this );

			} else {

				return FALSE;

			}

		

	}



    /**

     * Has auction started

     *

     * @access public

     * @return mixed

     *

     */

	function is_started() {

		global $sitepress;



        $id = $this->get_main_wpml_product_id();



        if($this->get_auction_has_started() === '1' ){

         	return TRUE;

        }



		if ($this->get_auction_dates_from() != false ){

			

			$date1 = new DateTime($this->get_auction_dates_from());

			$date2 = new DateTime(current_time('mysql'));

			if ($date1 < $date2){

				update_post_meta( $id, '_auction_has_started', '1');

				delete_post_meta( $id, '_auction_started');

				do_action('woocommerce_simple_auction_started',$id);

			} else{

				update_post_meta( $id, '_auction_started', '0');

			}



			return ($date1 < $date2) ;

		} else {

			update_post_meta( $id, '_auction_started', '0');

			return FALSE;

		}

	}



    /**

     * Does auction have reserve price

     *

     * @access public

     * @return bool

     *

     */

	function is_reserved() {



		if ($this->get_auction_reserved_price()){

			return TRUE;

		} else {

			return FALSE;

		}

	}



    /**

     * Has auction met reserve price

     *

     * @access public

     * @return mixed

     *

     */

	function is_reserve_met() {





		if (!empty($this->get_auction_reserved_price())){

			if($this->get_auction_type() == 'reverse' ){

				return ( (float)$this->get_auction_reserved_price() >= (float)$this->get_auction_current_bid());

			} else {

				return ( (float)$this->get_auction_reserved_price() <= (float)$this->get_auction_current_bid());

			}

		}

		return TRUE;

	}



    /**

     * Has auction finished

     *

     * @access public

     * @return mixed

     *

     */

	function is_finished() {

		if (!empty($this->get_auction_dates_to()) ){

			$date1 = new DateTime($this->get_auction_dates_to());

			$date2 = new DateTime(current_time('mysql'));



			if( $date1 < $date2){

				do_action('woocommerce_simple_auction_finished',$this->get_id());

				return TRUE;

			} else{

				return FALSE;

			}





		} else {

				return FALSE;

		}

	}



    /**

     * Is auction closed

     *

     * @access public

     * @return bool

     *

     */

	function is_closed() {



		$id = $this->get_main_wpml_product_id();

				

		if (!empty($this->get_auction_closed())){



				return TRUE;



		} else {



			if ($this->is_finished() && $this->is_started() ){



				if ( !$this->get_auction_current_bider() && !$this->get_auction_current_bid()){

					update_post_meta( $id, '_auction_closed', '1');

					update_post_meta( $id, '_auction_fail_reason', '1');

					$order_id = FALSE;

					do_action('woocommerce_simple_auction_close',  $id);

					do_action('woocommerce_simple_auction_fail', array('auction_id' => $id , 'reason' => __('There was no bid','wc_simple_auctions') ));

					return FALSE;

				}

				if ( $this->is_reserve_met() == FALSE){

					update_post_meta( $id, '_auction_closed', '1');

					update_post_meta( $id, '_auction_fail_reason', '2');

					$order_id = FALSE;

					do_action('woocommerce_simple_auction_close',  $id);

					do_action('woocommerce_simple_auction_reserve_fail', array('user_id' => $this->get_auction_current_bider(),'product_id' => $id ));

					do_action('woocommerce_simple_auction_fail', array('auction_id' => $id , 'reason' => __('The item didn\'t make it to reserve price','wc_simple_auctions') ));

					return FALSE;

				}

				update_post_meta( $id, '_auction_closed', '2');

				add_user_meta( $this->get_auction_current_bider(), '_auction_win', $id);

				do_action('woocommerce_simple_auction_close', $id);

				do_action('woocommerce_simple_auction_won', $id);



				return TRUE;



			} else {



				return FALSE;



			}

		}

	}



    /**

     * Get auction history

     *

     * @access public

     * @return object

     *

     */

	function auction_history($datefrom = FALSE, $user_id = FALSE) {

		global $wpdb;

		global $sitepress;

        $wheredatefrom ='';



        $id = $this->get_main_wpml_product_id();



        $relisteddate = get_post_meta( $id, '_auction_relisted', true );

        if(!is_admin() && !empty($relisteddate)){

            $datefrom = $relisteddate;

        }



        if($datefrom){

            $wheredatefrom =" AND CAST(date AS DATETIME) > '$datefrom' ";

        }



        if($user_id){

        	$wheredatefrom =" AND userid = $user_id";

        }



		if($this->get_auction_type() == 'reverse' ){

			$history = $wpdb->get_results( 'SELECT * 	FROM '.$wpdb->prefix.'simple_auction_log  WHERE auction_id =' . $id . $wheredatefrom.' ORDER BY  `date` desc , `bid`  asc, `id`  desc   ');

		} else {

			$history = $wpdb->get_results( 'SELECT * 	FROM '.$wpdb->prefix.'simple_auction_log  WHERE auction_id =' . $id . $wheredatefrom.' ORDER BY  `date` desc , `bid`  desc ,`id`  desc  ');

		}

		return $history;

	}





	/**

     * Get auction history line

     *

     * @access public

     * @return object

     *

     */

	function auction_history_last($id) {

		global $wpdb;

		global $sitepress;

		$data = '';

		$history_value = $wpdb->get_row( 'SELECT * 	FROM '.$wpdb->prefix.'simple_auction_log  WHERE auction_id =' . $id .' ORDER BY  `date` desc ');

		if($history_value){

			$data = "<tr>";

	            $data .= "<td class='date'>$history_value->date</td>";

	            $data .= "<td class='bid'>".wc_price($history_value->bid)."</td>";

	            $data .= "<td class='username'>".esc_html( apply_filters( 'woocommerce_simple_auctions_displayname', get_userdata( $history_value->userid )->display_name, $this ) ) ."</td>";

	            if ($history_value->proxy == 1)

	                $data .= " <td class='proxy'>".__('Auto', 'wc_simple_auctions')."</td>";

	            else

	                $data .= " <td class='proxy'></td>";

	         $data .= "</tr>";

	    }     

		return $data;

	}



	/**

	 * Returns price in html format.

	 *

	 * @access public

	 * @param string $price (default: '')

	 * @return string

     *

	 */

	public function get_price_html( $price = '' ) {

		$id = $this->get_main_wpml_product_id();



		if ($this->is_closed() && $this->is_started() ){

			if ($this->get_auction_closed() == '3'){

				$price = __('<span class="sold-for auction">Sold for</span>: ','wc_simple_auctions').wc_price($this->get_price());

			}

			else{

				if ($this->get_auction_current_bid()){

					if ( $this->is_reserve_met() == FALSE){

						$price = __('<span class="winned-for auction">Auction item did not make it to reserve price</span> ','wc_simple_auctions');

					} else{

						$price = __('<span class="winned-for auction">Current Bid:</span> ','wc_simple_auctions').wc_price($this->get_auction_current_bid());
						//$price = '<span class="auction-price starting-bid" data-auction-id="'.$id.'" data-bid="" data-status="running"><span class="starting auction red">Next Bid:&nbsp;</span><span class="woocommerce-Price-amount amount">$0.00</span></span>';

					}

				}

				else{

					//$price = __('<span class="winned-for auction">No Bids</span> ','wc_simple_auctions');
					$price ='<span class="starting auction current_bid_txt">Current Bid:&nbsp;</span><span class="woocommerce-Price-amount_new amount underline">None</span>';
					//Mujahid Code for flash cycle logic
					global $today_date_time;
					$_flash_cycle_start = get_post_meta( $id , '_flash_cycle_start' , TRUE);
					$_flash_cycle_end = get_post_meta( $id , '_flash_cycle_end' , TRUE);
					$post   = get_post( $id );
					//echo $seller = $post->post_author;
					$user_id = get_current_user_id();
				     $user = get_userdata( $user_id );
				  if ($user_id == $post->post_author && 1==2){
					if(strtotime($today_date_time) > strtotime($_flash_cycle_start) && strtotime($_flash_cycle_end) > strtotime($today_date_time)){
						$price .='<br /><form action="'.add_query_arg( array(), dokan_get_navigation_url( 'auction' ) ).'" name="flash_cycle" method="post" id="flash_cycle"><input type="hidden" name="action" value="update_price" /><input type="hidden" name="product_id" value="'.$id.'" /><input type="text" style="width:70px;" name="_new_price" id="_new_price" value="'.$this->get_curent_bid().'" max="'.$this->get_curent_bid().'"/><button type="submit" class="edit_button button alt">Update Price</button></form>';
					}
				  }

				}





			}



		} elseif(!$this->is_started()){

			$price = '<span class="auction-price starting-bid" data-auction-id="'.$id.'" data-bid="'.$this->get_auction_current_bid().'" data-status="future">'.__('<span class="starting auction">Starting bid:</span> ','wc_simple_auctions').wc_price($this->get_curent_bid()).'</span>';
			$post   = get_post( $id );
			$user_id = get_current_user_id();
			$user = get_userdata( $user_id );
			if ($user_id == $post->post_author && 1==2){
				$price .='<form class="price_form" action="'.get_permalink().'" method="post" onsubmit="playAudio(\''.home_url('wp-content/uploads/sounds/Money%20(MP3).mp3').'\');"><input type="hidden" value="'.$this->get_curent_bid().'" id="_new_price" name="_new_price" /><span><img src="'.home_url('wp-content/themes/dokan-child/Up.png').'" onclick="updatePrice(\'up\');" alt="up" class="up_img" border="0" style="width:40px;"/><img id="down_price_btn" src="'.home_url('wp-content/themes/dokan-child/Down.png').'" onclick="updatePrice(\'down\');" alt="down" class="down_img" border="0" style="width:40px;"/></span><span><input type="image" src="'.home_url('wp-content/themes/dokan-child/Update.png').'" name="update_distance" id="update_price" style="width:100px;padding:0;border:0;background:transparent;vertical-align:middle;"/></span>
						<input type="hidden" name="mode" value="update_price" />
				</form>';
			}

		} else {

			if($this->get_auction_sealed() == 'yes'){

				$price = '<span class="auction-price" data-auction-id="'.$id.'"  data-status="running">'.__('<span class="current auction">This is sealed bid auction.</span> ','wc_simple_auctions').'</span>';

			} else{

				if (!$this->get_auction_current_bid()){
					$post   = get_post( $id );
					$user_id = get_current_user_id();
					$user = get_userdata( $user_id );
					$tooltip = ' ';
					if ($user_id == $post->post_author){
						$tooltip = '<span class="tooltip_New"><span class="Increase ask fee to attract interest until a bid is made.">&nbsp;</span></span>';
					}
					$price = '<span class="auction-price starting-bid" data-auction-id="'.$id.'" data-bid="'.$this->get_auction_current_bid().'" data-status="running">'.__('<span class="current auction">Starting bid:</span> ','wc_simple_auctions').wc_price($this->get_curent_bid()).'</span>';
					if ($user_id == $post->post_author && 1==2){
						$price .='<form class="price_form" action="'.get_permalink().'" method="post" onsubmit="playAudio(\''.home_url('wp-content/uploads/sounds/Money%20(MP3).mp3').'\');"><input type="hidden" value="'.$this->get_curent_bid().'" id="_new_price" name="_new_price" /><span><img src="'.home_url('wp-content/themes/dokan-child/Up.png').'" onclick="updatePrice(\'up\');" alt="up" class="up_img" border="0" style="width:40px;"/><img id="down_price_btn" src="'.home_url('wp-content/themes/dokan-child/Down.png').'" onclick="updatePrice(\'down\');" alt="down" class="down_img" border="0" style="width:40px;"/></span><span><input type="image" src="'.home_url('wp-content/themes/dokan-child/Update.png').'" name="update_distance" id="update_price" style="width:100px;padding:0;border:0;background:transparent;vertical-align:middle;"/></span>
						<input type="hidden" name="mode" value="update_price" />
				</form>';
					}

				} else {

					$price = '<span class="auction-price current-bid" data-auction-id="'.$id.'" data-bid="'.$this->get_auction_current_bid().'" data-status="running">'.__('<span class="current auction">Current Bid:</span> ','wc_simple_auctions').wc_price($this->get_curent_bid()).'</span>';

				}

			}



		}

		return apply_filters( 'woocommerce_get_price_html', $price, $this );

	}



	/**

	 * Returns product's price.

	 *

	 * @access public

	 * @return string

     *

	 */

	function get_price($context = 'view') {

		if ( version_compare( WC_VERSION, '2.7', '<' ) ) {



			if ($this->is_closed()){



				if ($this->get_auction_closed() == '3'){

					return apply_filters( 'woocommerce_get_price', $this->regular_price, $this );

				}

				if ($this->is_reserve_met()) {



					return apply_filters( 'woocommerce_get_price', $this->auction_current_bid, $this );

				}

			}

			return apply_filters( 'woocommerce_get_price', $this->price, $this );

		} else {

			if ($this->is_closed()){

				

				if(empty($this->get_prop( 'price', $context )) OR $this->get_auction_closed() !== '3') {

					

					$price = null;

					if ($this->is_reserve_met()) {

						$price= get_post_meta( $this->get_main_wpml_product_id(), '_auction_current_bid', true );			

					}



					$this->set_price($price);

				}				

				return $this->get_prop( 'price', $context );

			}



			return apply_filters( 'woocommerce_product_get_price',get_post_meta( $this->get_main_wpml_product_id(), '_price', true ),$this);



		}



		

	}





	/**

	 * Get the add to url used mainly in loops.

	 *

	 * @access public

	 * @return string

	 */

	public function add_to_cart_url() {

		$id = $this->get_main_wpml_product_id();

		return apply_filters( 'woocommerce_product_add_to_cart_url', get_permalink( $id ), $this );

	}



	/**

	 * Wrapper for get_permalink

	 * @return string

	 */

	public function get_permalink() {

		$id = $this->get_main_wpml_product_id();

		return get_permalink( $id );

	}



	/**

	 * Get the add to cart button text

	 *

	 * @access public

	 * @return string

	 */

	public function add_to_cart_text() {

		if (!$this->is_finished() && $this->is_started() ){

			$text = __( 'Bid now', 'wc_simple_auctions' ) ;

		} elseif($this->is_finished()  ){

			$text = __( 'Auction finished', 'wc_simple_auctions' ) ;

		} elseif(!$this->is_finished() && !$this->is_started()  ){

			$text =  __( 'Auction not started', 'wc_simple_auctions' ) ;

		}



		return apply_filters( 'woocommerce_product_add_to_cart_text', $text, $this );

	}



	/**

	 * Get the bid value

	 *

	 * @access public

	 * @return string

	 */

	public function bid_value() {

		$auction_bid_increment = ($this->get_increase_bid_value()) ? $this->get_increase_bid_value() : 1;

		

		if ( ! $this->get_auction_current_bid() ) {

			return $this->get_curent_bid();

		} else  {

			if($this->get_auction_type() == 'reverse' ){



				return apply_filters('woocommerce_simple_auctions_bid_value' ,round( wc_format_decimal($this->get_curent_bid()) - wc_format_decimal($auction_bid_increment),wc_get_price_decimals()), $this);

			}else{

				

				return apply_filters('woocommerce_simple_auctions_bid_value' ,round( wc_format_decimal($this->get_curent_bid()) + wc_format_decimal($auction_bid_increment),wc_get_price_decimals()), $this);

			}

		}



		return FALSE;

	}



	

	/**

	 * Get the title of the post.

	 *

	 * @access public

	 * @return string

	 */

	public function get_title() {

		$id = $this->get_main_wpml_product_id();



		return apply_filters( 'woocommerce_product_title', get_the_title( $id ), $this );

	}



	/**

	 * Check if auctions is on user watchlist

	 *

	 * @access public

	 * @return string

	 */

	public function is_user_watching( $user_ID = false){



		$post_id = $this->get_main_wpml_product_id();



		if(!$user_ID){

			$user_ID = get_current_user_id();

		}



		$users_watching_auction = get_post_meta( $post_id, '_auction_watch', FALSE );



		if(is_array($users_watching_auction) && in_array($user_ID, $users_watching_auction)){

			$return =  true;

		} else{

			$return =  false;

		}



		return apply_filters( 'woocommerce_simple_auctions_bis_user_watching', $return, $user_ID, $post_id );



		



	}







    /**

     * Get main product id for multilanguage purpose

     *

     * @access public

     * @return int

     *

     */



    function get_main_wpml_product_id(){



        global $sitepress;



        if (function_exists('icl_object_id') && function_exists('pll_default_language')) { // Polylang with use of WPML compatibility mode

            $id = icl_object_id($this->id,'product',false, pll_default_language());

        }

        elseif (function_exists('icl_object_id') && method_exists($sitepress, 'get_default_language')) { // WPML

            $id = icl_object_id($this->id,'product',false, $sitepress->get_default_language());

        }

        else {

            $id = $this->id;

        }



        return $id;



    }



    /**

	 * Get if user is biding on auction

	 *

	 * @access public

	 * @return int

	 */

    public function is_user_biding( $auction_id , $user_ID = false){



    	global $wpdb;



		$id = $this->get_main_wpml_product_id();



		if(!$user_ID){

			$user_ID = get_current_user_id();

		}



		$bid_count = $wpdb->get_var( 'SELECT COUNT(*) 	FROM '.$wpdb->prefix.'simple_auction_log  WHERE auction_id =' . $auction_id .' and userid = '.$user_ID);



		return  apply_filters('woocommerce_simple_auctions_is_user_biding' ,intval($bid_count) , $this );



	}



	/**

	 * Get user max bid

	 *

	 * @access public

	 * @return float

	 */

	public function get_user_max_bid( $auction_id , $user_ID = false){



    	global $wpdb;



    	$wheredatefrom ='';

    	$datefrom = false;





		$id = $this->get_main_wpml_product_id();



		$relisteddate = get_post_meta( $id, '_auction_relisted', true );

		if(!is_admin() && !empty($relisteddate)){

		    $datefrom = $relisteddate;

		}



		if($datefrom){

		    $wheredatefrom =" AND CAST(date AS DATETIME) > '$datefrom' ";

		}



		if(!$user_ID){

			$user_ID = get_current_user_id();

		}



		$maxbid = $wpdb->get_var( 'SELECT bid FROM '.$wpdb->prefix.'simple_auction_log  WHERE auction_id =' . $auction_id .' and userid = ' . $user_ID. $wheredatefrom . '  ORDER BY  `bid` desc');







		return apply_filters('woocommerce_simple_auctions_get_user_max_bid' ,$maxbid , $this);



	}



	/**

	 * Get is auction is sealed

	 *

	 * @access public

	 * @return bolean

	 */

	function is_sealed(){

		if ($this->is_closed()){

			return false;

		}

		return apply_filters('woocommerce_simple_auctions_is_sealed' ,$this->get_auction_sealed() == 'yes',$this );

	}



	function check_bid_count(){

		$id = $this->get_main_wpml_product_id();

		

		if ($this->get_auction_bid_count() == '') {

			

			update_post_meta( $id, '_auction_bid_count', '0');

		} 

			

		return;

		

	}





	/**

     * Get get_auction_current_bid

     *

     * @since 1.2.8

     * @param  string $context

     * @return string

     */

    public function get_auction_current_bid( $context = 'view' ) {



    	return get_post_meta( $this->get_main_wpml_product_id(), '_auction_current_bid', true );

        

    }



    /**

     * Get get_auction_current_bider

     *

     * @since 1.2.8

     * @param  string $context

     * @return string

     */

    public function get_auction_current_bider( $context = 'view' ) {

         

         return get_post_meta( $this->get_main_wpml_product_id(), '_auction_current_bider', true );

        

    }



     /**

     * Get get_auction_bid_increment

     *

     * @since 1.2.8

     * @param  string $context

     * @return string

     */

    public function get_auction_bid_increment( $context = 'view' ) {
		//Mujahid code to update increment logic
		global $wpdb;
		$auction_id = $this->get_main_wpml_product_id();
		$bid_amount = $wpdb->get_var($wpdb->prepare("SELECT MIN(bid) FROM wp_simple_auction_log WHERE auction_id = %d LIMIT 1",$auction_id));
		if ($bid_amount > 0){
			//$_auction_current_bid = get_post_meta( $this->get_main_wpml_product_id(), '_auction_current_bid', true );
			$_auction_bid_increment =  ($bid_amount * 3)/100;
		}else{
			$_auction_bid_increment = 0;
		}
        return $_auction_bid_increment;
       // return get_post_meta( $this->get_main_wpml_product_id(), '_auction_bid_increment', true );
    }

  

    /**

     * Get get_auction_item_condition

     *

     * @since 1.2.8

     * @param  string $context

     * @return string

     */

    public function get_auction_item_condition( $context = 'view' ) {

       return get_post_meta( $this->get_main_wpml_product_id(), '_auction_item_condition', true );

    }

    /**

     * Get get_auction_dates_from

     *

     * @since 1.2.8

     * @param  string $context

     * @return string

     */

    public function get_auction_dates_from( $context = 'view' ) {



    	return get_post_meta( $this->get_main_wpml_product_id(), '_auction_dates_from', true );

              

    }

    /**

     * Get get_auction_dates_to

     *

     * @since 1.2.8

     * @param  string $context

     * @return string

     */

    public function get_auction_dates_to( $context = 'view' ) {



    	return get_post_meta( $this->get_main_wpml_product_id(), '_auction_dates_to', true );

        

    }

    /**

     * Get get_auction_reserved_price

     *

     * @since 1.2.8

     * @param  string $context

     * @return string

     */

    public function get_auction_reserved_price( $context = 'view' ) {

         

        return get_post_meta( $this->get_main_wpml_product_id(), '_auction_reserved_price', true );

        

    }



    /**

     * Get get_auction_type

     *

     * @since 1.2.8

     * @param  string $context

     * @return string

     */

    public function get_auction_type( $context = 'view' ) {

         

        return get_post_meta( $this->get_main_wpml_product_id(), '_auction_type', true );

        

    }

    /**

     * Get get_auction_closed

     *

     * @since 1.2.8

     * @param  string $context

     * @return string

     */

    public function get_auction_closed( $context = 'view' ) {

         

        return get_post_meta( $this->get_main_wpml_product_id(), '_auction_closed', true );

    }



     /**

     * Get get_auction_started

     *

     * @since 1.2.8

     * @param  string $context

     * @return string

     */

    public function get_auction_started( $context = 'view' ) {

         

        return get_post_meta( $this->get_main_wpml_product_id(), '_auction_started', true );

        

    }



    /**

     * Get get_has_auction_started

     *

     * @since 1.2.8

     * @param  string $context

     * @return string

     */

    public function get_auction_has_started( $context = 'view' ) {

         

        return get_post_meta( $this->get_main_wpml_product_id(), '_auction_has_started', true );

        

    }



     /**

     * Get get_auction_sealed

     *

     * @since 1.2.8

     * @param  string $context

     * @return string

     */

    public function get_auction_sealed( $context = 'view' ) {

         

        return get_post_meta( $this->get_main_wpml_product_id(), '_auction_sealed', true );

        

    }



    /**

     * Get get_auction_bid_count

     *

     * @since 1.2.8

     * @param  string $context

     * @return string

     */

    public function get_auction_bid_count( $context = 'view' ) {

         

        return get_post_meta( $this->get_main_wpml_product_id(), '_auction_bid_count', true );

        

    }



    /**

     * Get get_auction_max_bid

     *

     * @since 1.2.8

     * @param  string $context

     * @return string

     */

    public function get_auction_max_bid( $context = 'view' ) {

        

        return get_post_meta( $this->get_main_wpml_product_id(), '_auction_max_bid', true );

    }



    /**

     * Get get_auction_max_current_bider

     *

     * @since 1.2.8

     * @param  string $context

     * @return string

     */

    public function get_auction_max_current_bider( $context = 'view' ) {

         

        return get_post_meta( $this->get_main_wpml_product_id(), '_auction_max_current_bider', true );

        

    }



    /**

     * Get get_auction_fail_reason

     *

     * @since 1.2.8

     * @param  string $context

     * @return string

     */

    public function get_auction_fail_reason( $context = 'view' ) {

         

         return get_post_meta( $this->get_main_wpml_product_id(), '_auction_fail_reason', true );

    }



    /**

     * Get get_order_id

     *

     * @since 1.2.8

     * @param  string $context

     * @return string

     */

    public function get_order_id( $context = 'view' ) {



        return get_post_meta( $this->get_main_wpml_product_id(), '_order_id', true );

        

    }



    /**

     * Get get_stop_mails

     *

     * @since 1.2.8

     * @param  string $context

     * @return string

     */

    public function get_stop_mails( $context = 'view' ) {



        return get_post_meta( $this->get_main_wpml_product_id(), '_stop_mails', true );

        

    }



    /**

     * Get get_auction_proxy

     *

     * @since 1.2.8

     * @param  string $context

     * @return string

     */

    public function get_auction_proxy( $context = 'view' ) {



        return get_post_meta( $this->get_main_wpml_product_id(), '_auction_proxy', true );

        

    }



    /**

     * Get get_auction_start_price

     *

     * @since 1.2.8

     * @param  string $context

     * @return string

     */

    public function get_auction_start_price( $context = 'view' ) {



        return get_post_meta( $this->get_main_wpml_product_id(), '_auction_start_price', true );

        

    }



    /**

     * Get get_auction_wpml_language

     *

     * @since 1.2.8

     * @param  string $context

     * @return string

     */

    public function get_auction_wpml_language( $context = 'view' ) {



        return get_post_meta( $this->get_main_wpml_product_id(), '_auction_wpml_language', true );

        

    }



    /**

     * Get get_auction_relist_fail_time

     *

     * @since 1.2.8

     * @param  string $context

     * @return string

     */

    public function get_auction_relist_fail_time( $context = 'view' ) {



        return get_post_meta( $this->get_main_wpml_product_id(), '_auction_relist_fail_time', true );

        

    }



    /**

     * Get get_auction_relist_not_paid_time

     *

     * @since 1.2.8

     * @param  string $context

     * @return string

     */

    public function get_auction_relist_not_paid_time( $context = 'view' ) {

         

        return get_post_meta( $this->get_main_wpml_product_id(), '_auction_relist_not_paid_time', true );

        

    }



    /**

     * Get get_auction_automatic_relist

     *

     * @since 1.2.8

     * @param  string $context

     * @return string

     */

    public function get_auction_automatic_relist( $context = 'view' ) {



        return get_post_meta( $this->get_main_wpml_product_id(), '_auction_automatic_relist', true );

        

    }



    /**

     * Get get_auction_relist_duration

     *

     * @since 1.2.8

     * @param  string $context

     * @return string

     */

    public function get_auction_relist_duration( $context = 'view' ) {

         

        return get_post_meta( $this->get_main_wpml_product_id(), '_auction_relist_duration', true );

        

    }

    

     /**

     * Get get_auction_payed

     *

     * @since 1.2.8

     * @param  string $context

     * @return string

     */

    public function get_auction_payed( $context = 'view' ) {

         

        return get_post_meta( $this->get_main_wpml_product_id(), '_auction_payed', true );

    }



    /**

     * Get get_number_of_sent_mails

     *

     * @since 1.2.8

     * @param  string $context

     * @return string

     */

    public function get_number_of_sent_mails( $context = 'view' ) {



        return get_post_meta( $this->get_main_wpml_product_id(), '_number_of_sent_mails', true );

        

    }

    

    /**

     * Get get_auction_relisted

     *

     * @since 1.2.8

     * @param  string $context

     * @return string

     */

    public function get_auction_relisted( $context = 'view' ) {



        return get_post_meta( $this->get_main_wpml_product_id(), '_auction_relisted', true );

        

    }



    /**

	 * Returns the product's regular price.

	 *

	 * @param  string $context

	 * @return string price

	 */

	public function get_regular_price( $context = 'view' ) {

		

		return get_post_meta( $this->get_main_wpml_product_id(), '_regular_price', true );

	}

    







}

endif;