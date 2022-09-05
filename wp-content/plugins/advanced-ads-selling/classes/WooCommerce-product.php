<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


class WC_Product_Advanced_Ad extends WC_Product {

	public function __construct( $product ) {

	    $this->product_type = 'advanced_ad';
	    
	    parent::__construct( $product );
	}
}