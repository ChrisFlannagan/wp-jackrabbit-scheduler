<?php

/*
 * JR_SC - JackRabbit Short Code
 */

class JR_SC {

	private $attributes;

	function __construct() {
		$attributes = array();
	}

	function jackrabbit_shorcode_func( $atts ) {
		$a = shortcode_atts( array(
			'sc' => '0'
		), $atts );

		global $wpdb;
		$main_prefix   = $wpdb->get_blog_prefix( BLOG_ID_CURRENT_SITE );
		$jr_shortcodes = $wpdb->get_row(
			"
        SELECT *
        FROM $main_prefix" . "wpjrsblogdata
        WHERE id = " . intval( $a['sc'] )
		);

		$jr_getextras = $wpdb->get_results(
			"
            SELECT * 
            FROM $main_prefix" . "wpjrscustomattr
            WHERE scode = " . $a['sc'] . "
                ORDER BY id DESC
            "
		);

		$s_hide       = array();
		$s_hide_s     = '';
		$s_show       = array();
		$s_show_s     = '';
		$default_hide = '';
		$default_show = '';
		if ( get_option( "jr_default_hide" ) !== false && get_option( "jr_default_hide" ) ) {
			$default_hide = get_option( "jr_default_hide" );
		}
		if ( get_option( "jr_default_show" ) !== false && get_option( "jr_default_show" ) ) {
			$default_show = get_option( "jr_default_show" );
		}

		foreach ( $jr_getextras as $row ) {
			if( $row->sattr == 'hide' ) {
				$s_hide_s .= $row->sval;
			}
			if( $row->sattr == 'show' ) {
				$default_hide = str_replace( $row->sval, '', $default_hide );
				$s_show_s .= $row->sval;
			}
		}
		$jrscript = '<script type="text/javascript" src="https://app.jackrabbitclass.com/jr3.0/Openings/OpeningsJS?OrgID=' . $jr_shortcodes->scode;

		$jrscript .= '&hidecols=' . $default_hide;
		$jrscript .= $s_hide_s;

		$jrscript .= '&showcols=' . $default_show;
		$jrscript .= $s_show_s;

		if ( $jr_shortcodes->scat1 != '' ) {
			$jrscript .= '&Cat1=' . $jr_shortcodes->scat1;
		}

		// If Cat2 defined, show it as well
		if ( $jr_shortcodes->scat2 != '' ) {
			$jrscript .= '&' . $jr_shortcodes->scat2;
		}

		/*
		 * Default column sorting, should add feature to customize this later
		 */
		$jrscript .= '&sort=Location,StartDate,Ages,Cat1,Class,Days,StartTime';

		$jrscript .= '"></script>';

		return str_replace( "&", "&#038;", $jrscript );
	}

}