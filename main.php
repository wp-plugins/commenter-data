<?php
/**
 * Plugin Name: Commenter data
 * Description: Downloads commenter data in csv format.
 * Version: 2.1
 * Author: wpgeeko
 * Author URI: http://sharethingz.com
 * License: GPL2
 */
define( 'CD_SLASH' , '/' );

/* plugin url */
define( 'CD_URL', plugins_url('', __FILE__) );

/* Define all necessary variables first */
define( 'CD_CSS', CD_URL. "/assets/css/" );
define( 'CD_JS',  CD_URL. "/assets/js/" );
define( 'CD_IMG',  CD_URL. "/assets/img/" );

$postLoad = apply_filters( 'cd_postload', 200 );
define( 'CD_LOAD_POST', $postLoad );

global $commenter, $core, $cd_columns;
$cd_columns = array(
                    'Post id'=>'comment_post_ID',
                    'Comment date'=>'comment_date',
                    'Name'=>'comment_author',
                    'Email'=>'comment_author_email',
                    'Website'=>'comment_author_url',
                    'Comment content'=>'comment_content',
                    'Attchment URL'=>'comment_attachment'
               );

// Includes PHP files located in 'lib' folder
foreach( glob ( dirname(__FILE__). "/lib/*.php" ) as $lib_filename ) {

    require_once( $lib_filename );

}

/* Initialize commenter object */
$commenter = new commenter();
$core = new commenter_core();