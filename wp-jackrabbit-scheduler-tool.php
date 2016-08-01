<?php
/**
 * Data Reports on Copies
 *
 * This page shows when a user selects Copy Logger from the admin menu
 * Initial features: Should be able to search/sort based on times and keywords
 *                   Should be able to turn highlights on/off for users and/or admins
 */

global $wpdb;
$main_prefix = $wpdb->get_blog_prefix( BLOG_ID_CURRENT_SITE );

if( isset( $_REQUEST['default_hide'] ) ) {
    update_option( "jr_default_hide", $_REQUEST['default_hide'] );
    update_option( "jr_default_show", $_REQUEST['default_show'] );
    update_option(  $main_prefix . 'jr_def_code', $_REQUEST['default_code'] );
}

if( isset( $_REQUEST['delIt'] ) && $_REQUEST['delIt'] != '0' ) {
    echo '<p style="background: #FFF; padding:30px;font-weight:bold;font-size:14pt;margin-top: 10px;">Deleted Short Code</p>';
    $wpdb->delete(
        $main_prefix . 'wpjrscustomattr',
        array( 'scode' => intval( $_REQUEST['delIt'] ) ),
        array( '%d' )
    );
    $wpdb->delete(
        $main_prefix . 'wpjrsblogdata',
        array( 'id' => intval( $_REQUEST['delIt'] ) ),
        array( '%d' )
    );
}

if( isset( $_REQUEST['s_name'] ) && $_REQUEST['delIt'] == '0') {
    $i_id = 0;

    if( $_REQUEST['editing'] != '0' ) {
        $i_id = intval( $_REQUEST['editing'] );
        $wpdb->update(
            $main_prefix . 'wpjrsblogdata',
            array(
                'sname' => str_replace("'", "&squot;", $_REQUEST['s_name']),
                'scode' => intval($_REQUEST['s_code']),
                'scat1' => $_REQUEST['s_cat1'],
                'scat2' => $_REQUEST['s_cat2']
            ),
            array( 'id' => intval( $_REQUEST['editing'] ) ),
            array(
                '%s',
                '%d',
                '%s',
                '%s'
            ),
            array( '%d' )
        );
        $wpdb->delete(
            $main_prefix . 'wpjrscustomattr',
            array( 'scode' => intval( $_REQUEST['editing'] ) ),
            array( '%d' )
        );
    } else {
        $wpdb->insert(
            $main_prefix . 'wpjrsblogdata',
            array(
                'blogid' => get_current_blog_id(),
                'sname' => str_replace("'", "&squot;", $_REQUEST['s_name']),
                'scode' => intval($_REQUEST['s_code']),
                'scat1' => $_REQUEST['s_cat1'],
                'scat2' => $_REQUEST['s_cat2']
            ),
            array(
                '%d',
                '%s',
                '%d',
                '%s',
                '%s'
            )
        );

        $i_id = $wpdb->insert_id;
    }

    $s_all = array();
    $s_all['show'] = explode( ',', $_REQUEST['s_show'] );
    $s_all['hide'] = explode( ',', $_REQUEST['s_hide'] );

    foreach( $s_all as $extra => $val ) {
        foreach( $val as $attr ) {
            if( ! empty( $attr ) ) {
                $wpdb->insert(
                    $main_prefix . 'wpjrscustomattr',
                    array(
                        'blogid' => get_current_blog_id(),
                        'scode' => $i_id,
                        'sattr' => $extra,
                        'sval' => $attr
                    ),
                    array(
                        '%d',
                        '%d',
                        '%s',
                        '%s'
                    )
                );
            }
        }
    }
}
?>
<div class="wrap">
    <h2>JackRabbit Shortcodes</h2>
    <form action="" method="post" id="mainFormJR">
        <input type="hidden" name="delIt" value="0" id="delIt" />
        <h3>Set Default Code & Hide/Show Columns</h3>
        <p>
            Code: <input type="text" size="8" name="default_code" placeholder="XXXXXX" <?php
            if ( get_option( $main_prefix . 'jr_def_code' ) !== false && get_option( $main_prefix . 'jr_def_code' ) ) {
                echo 'value="' . get_option( $main_prefix . 'jr_def_code' ) . '" ';
            }
            ?> />
        </p>
        <p>
            Hide: <input type="text" size="125" name="default_hide" placeholder="Separate By Comma" <?php
            if ( get_option( "jr_default_hide" ) !== false && get_option( "jr_default_hide" ) ) {
                echo 'value="' . get_option( "jr_default_hide" ) . '" ';
            }
            ?> />
        </p>
        <p>
            Show: <input type="text" size="125" name="default_show" placeholder="Separate By Comma" <?php
            if ( get_option( "jr_default_show" ) !== false && get_option( "jr_default_show" ) ) {
                echo 'value="' . get_option( "jr_default_show" ) . '" ';
            }
            ?> />
        </p>
        <p><input type="submit" value="Save Defaults" /></p>
    </form>
    <form action="" method="post">
        <h3>Manage Shortcodes</h3>
        <input type="hidden" name="editing" id="editing" value="0" />
        <table border="0">
            <tr>
                <th>Schedule Name</th>
                <th>Schedule Code (XXXXXX)</th>
                <th>Cat 1</th>
                <th>Extra</th>
                <!-- to be premium //-->
                <td>Hide Columns</td>
                <td>Show Columns</td>
            </tr>
            <tr>
                <td><input type="text" id="s_name" name="s_name" /></td>
                <td><input type="text" id="s_code" name="s_code" size="6" <?php
                    if ( get_option( $main_prefix . 'jr_def_code' ) !== false && get_option( $main_prefix . 'jr_def_code' ) ) {
                        echo 'value="' . get_option( $main_prefix . 'jr_def_code' ) . '" ';
                    }
                    ?> /></td>
                <td><input type="text" id="s_cat1" name="s_cat1" /></td>
                <td><input type="text" id="s_cat2" name="s_cat2" /></td>
                <td><input type="text" id="s_hide" name="s_hide" placeholder="Separate By Comma" /></td>
                <td><input type="text" id="s_show" name="s_show" placeholder="Separate By Comma" /></td>
            </tr>
            <tr>
                <td colspan="5">
                    <input type="submit" id="save_btn" value="Create Shortcode" /> <input type="button" id="cancel_edit" value="Cancel Edit" />
                </td>
            </tr>
        </table>
    </form>
    <ul id="jr_shortcodes">
        <?php
        $jr_shortcodes = $wpdb->get_results(
            "
            SELECT * 
            FROM $main_prefix" . "wpjrsblogdata
            WHERE blogid = " . get_current_blog_id() . "
                ORDER BY id DESC
            "
        );

        $jr_attributes = $wpdb->get_results(
            "
            SELECT * 
            FROM $main_prefix" . "wpjrscustomattr
            WHERE blogid = " . get_current_blog_id() . "
                ORDER BY id DESC
            "
        );

        $hide_attr = array();
        $show_attr = array();
        foreach ( $jr_attributes as $jra )
        {
            if( ! array_key_exists( $jra->scode, $hide_attr ) ) {
                $hide_attr[ $jra->scode ] = '';
                $show_attr[ $jra->scode ] = '';
            }
            if( $jra->sattr == 'hide' ) {
                $hide_attr[ $jra->scode ] .= esc_attr( $jra->sval ) . ',';
            }
            if( $jra->sattr == 'show' ) {
                $show_attr[ $jra->scode ] .= esc_attr( $jra->sval ) . ',';
            }
        }

        foreach ( $jr_shortcodes as $jrs )
        {
            echo '<li>[<a 
            jname="' . esc_attr( $jrs->sname ) . '" 
            jcode="' . esc_attr( $jrs->scode ) . '" 
            jid="' . esc_attr( $jrs->id ) . '" 
            jcat1="' . esc_attr( $jrs->scat1 ) . '" 
            jcat2="' . esc_attr( $jrs->scat2 ) . '" 
            jshow="' . esc_attr( rtrim( $show_attr[ $jrs->id ], ",") ) . '" 
            jhide="' . esc_attr( rtrim( $hide_attr[ $jrs->id ], ",") ) . '" 
            href="#editIt" class="edit_js">EDIT</a>] [<a href="#delIt" jid="' . esc_attr( $jrs->id ) . '" class="del_js" style="color:Red;">DELETE</a>]' . $jrs->sname . ' - Shortcode: <span class="jr_sc_copy">[wpjackrabbit sc=' . $jrs->id . ']</span></li>';
        }
        ?>
    </ul>
</div>