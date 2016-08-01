<?php
/*
 * JR_SC - JackRabbit Short Code
 */

class JR_SC {
    
    private $attributes;

    function __construct() {
        $attributes = array();
    }

    function jackrabbit_shorcode_func( $atts ){
        $a = shortcode_atts( array(
        'sc' => '0'
        ), $atts );

        global $wpdb;
        $main_prefix = $wpdb->get_blog_prefix( BLOG_ID_CURRENT_SITE );
        $jr_shortcodes = $wpdb->get_row(
        "
        SELECT *
        FROM $main_prefix" . "wpjrsblogdata
        WHERE id = " . intval( $a['sc'] )
        );

        $jr_getextras = $wpdb->get_results(
            $wpdb->prepare( "SELECT sattr, sval FROM {$main_prefix}wpjrscustomattr WHERE scode=%d", $a['sc'] )
        );

        $s_hide = array();
        $s_hide_s = '';
        $s_show = array();
        $s_show_s = '';
        $default_hide = '';
        $default_show = '';
        if ( get_option( "jr_default_hide" ) !== false && get_option( "jr_default_hide" ) ) {
            $default_hide = get_option( "jr_default_hide" );
        }
        if ( get_option( "jr_default_show" ) !== false && get_option( "jr_default_show" ) ) {
            $default_show = get_option( "jr_default_show" );
        }

        foreach( $jr_getextras as $row) {
            $s_hide[ $row->sattr ][] = $row->sval;
        }
        $jrscript = '<script type="text/javascript" src="https://app.jackrabbitclass.com/jr3.0/Openings/OpeningsJS?OrgID=' . $jr_shortcodes->scode;

        /*
         * Get hidecols= default data + user added data and add to javascript string
         */
        if( sizeof( $s_hide ) > 0 ) {
            foreach ($s_hide as $key => $val) {
                $s_hide_s .= ',' . $val[0];
            }
        }
        $jrscript .= '&hidecols=' . $default_hide . $s_hide_s;

        /*
         * Get showcols= data and add to the javascript string
         */
        if( sizeof( $s_show ) > 0 ) {
            foreach ($s_show as $key => $val) {
                $s_show_s .= ',' . $val[0];
            }
        }
        $jrscript .= '&showcols=' . $default_show;
        $jrscript .= $s_show_s;

        /*
         * Default column sorting, should add feature to customize this later
         */
        $jrscript .= '&sort=Location,StartDate,Ages,Cat1,Class,Days,StartTime';

        if ( $jr_shortcodes->scat1 != '' ) {
            $jrscript .= '&Cat1=' . $jr_shortcodes->scat1;
        }

        // If Cat2 defined, show it as well
        if ( $jr_shortcodes->scat2 != '' ) {
            $jrscript .= '&' . $jr_shortcodes->scat2;
        }

        $jrscript .= '"></script>';

        return str_replace( "&", "&#038;", $jrscript );
    }

}