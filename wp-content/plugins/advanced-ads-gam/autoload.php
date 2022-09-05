<?php
/**
 * Auto-load all class files.
 *
 * Find and require all files whose name are ending with ".class.php"
 */

/**
 * Find all files in a directory
 *
 * @param string $dir the directory.
 * @return The file list
 */
function advanced_ads_find_all_files( $dir ) {
	$root   = scandir( $dir );
	$result = [];
	foreach ( $root as $value ) {

		if ( '.' === $value || '..' === $value ) {
			continue;
		}
		$result[] = "$dir/$value";
		if ( is_file( "$dir/$value" ) ) {
			continue;
		}
		foreach ( advanced_ads_find_all_files( "$dir/$value" ) as $value ) {
			$result[] = $value;
		}
	}
	return $result;
}

/**
 * Require all class files (starting with "class-" and ending with ".php")
 */
function advanced_ads_class_autoload() {
	$files = advanced_ads_find_all_files( dirname( __FILE__ ) );
	foreach ( $files as $file ) {
		if ( 0 === strpos( basename( $file ), 'class-' ) && ( strlen( $file ) - 4 ) === strpos( $file, '.php' ) ) {
			require_once $file;
		}
	}
}

advanced_ads_class_autoload();
