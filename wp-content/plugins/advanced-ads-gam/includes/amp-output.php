<?php
/**
 * Ad output on AMP pages.
 */

/**
 * Check for fluid size.
 */
if ( false === strpos( $size, 'fluid' ) ) {
	// no fluid size.

	$size_array = array();
	/**
	 * Check if there are any secondary size.
	 */
	if ( 0 === strpos( $size, '[[' ) ) {

		$size = explode( '],[', $size );
		foreach ( $size as $s ) {
			$size_array[] = str_replace( array( ']', '[', ',' ), array( '', '', 'x' ), $s );
		}
		sort( $size_array );
		$size_array = array_reverse( $size_array );
	} else {
		$size       = trim( $size, '][' );
		$size_array = array( str_replace( ',', 'x', $size ) );
	}
	if ( 1 < count( $size_array ) ) {
		$primary_size = explode( 'x', array_shift( $size_array ) );
		echo '<amp-ad type="doubleclick" layout="fixed" width="' . $primary_size[0] . '" height="' . $primary_size[1] . '" ';
		echo 'data-slot="' . $path . '" data-multi-size="' . implode( ',', $size_array ) . '" data-multi-size-validation="false"';
		echo '></amp-ad>';
	} else {
		$size = explode( 'x', $size_array[0] );
		if ( count( $size ) > 1 ) {
			printf( '<amp-ad type="doubleclick" layout="fixed" width="%d" height="%d" data-slot="%s" ></amp-ad>', $size[0], $size[1], $path );
		}
	}
} else {
	// fluid ad.

	if ( "['fluid']" == $size ) {

		// fluid size only, no fixed size.
		echo '<amp-ad type="doubleclick" layout="fluid" height="fluid" ';
		echo 'data-slot="' . $path . '" ';
		echo 'data-multi-size-validation="false"></amp-ad>';

	} else {

		$size         = str_replace( "'fluid'", '[fluid]', $size );
		$static_sizes = array();
		$sizes        = explode( '],[', $size );
		foreach ( $sizes as $str ) {
			$str = trim( $str, '][' );
			if ( 'fluid' == $str ) {
				continue;
			}
			$static_sizes[] = str_replace( ',', 'x', $str );
		}

		if ( 1 == count( $static_sizes ) ) {
			$multisize = $static_sizes[0];
		} else {
			$multisize = implode( ',', $static_sizes );
		}

		echo '<amp-ad type="doubleclick" layout="fluid" height="fluid" ';
		echo 'data-slot="' . $path . '" ';
		echo 'data-multi-size="' . $multisize . '" ';
		echo 'data-multi-size-validation="false"></amp-ad>';

	}
}
