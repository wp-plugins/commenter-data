<?php
if( !class_exists('commenter_core') ){

    class commenter_core{

        public $fields;
        public $attachment;
        public $cd_fields;
        public $cd_karma; // For developer's use, storing extra data for use in filters and actions

        function __construct() {

            global $commenter;
            $this->cd_fields    =   $commenter->cd_setting_val;
            $this->cd_karma     =   array();
            if( in_array( 'comment_attachment', $this->cd_fields ) ){

                /* Check if comment attachment plugin is installed */
                if( class_exists('wpCommentAttachment') )
                    $this->attachment = true;
                else
                    $this->attachment = false;

                unset($this->cd_fields['attachment']);
            }

            /* Unset any data using this filter which is not present in wp_comments table i.e. columns */
            $this->cd_karma     =   apply_filters( 'commenter_karma', $this->cd_karma, $this->cd_fields );
            $this->cd_fields    =   apply_filters( 'commenter_filter_setting_data', $this->cd_fields );

            $this->fields = implode( ',', $this->cd_fields );

            add_action( 'wp_ajax_commenter', array( $this, 'cd_create_csv' ) );
            add_action( 'wp_ajax_commenter_loadpost', array( $this, 'commenter_loadpost' ) );
            add_action( 'wp_ajax_commenter_setting', array( $this, 'cd_setting' ) );

        }

        /* Creates csv file on ajax request */
        function cd_create_csv(){

            global $wpdb, $cd_columns, $commenter;
            $response = array('error'=>false, 'msg'=>'');

            $query   =   $wpdb->prepare( "SELECT comment_ID, $this->fields FROM ".$wpdb->prefix."comments WHERE comment_approved='1' AND comment_post_ID='%d'", $_POST['pid'] );
            $comments = $wpdb->get_results($query);
            $upload_dir = wp_upload_dir();

            $csv_file = fopen( $upload_dir['basedir']. '/commentdata.csv', 'w+');

            if( !$csv_file ){

                $response = array('error'=>true, 'msg'=> __( '<p class="error">Uploads folder is not writable</p>', 'cd' ) );

            }else{

                $csv_heading = array();

                foreach( $cd_columns as $key => $val ){

                    if( in_array( $val, $commenter->cd_setting_val ) )
                        array_push( $csv_heading, $key );
                }

                fputcsv( $csv_file, $csv_heading );

                if( !empty( $comments ) ){

                    foreach ( $comments as $comment ){

                        $comment  = (array)$comment;
                        $attachment = '';

                        /* Check if we also need to put attachment url */
                        if( $this->attachment ){

                            $attachment_id = get_comment_meta( $comment['comment_ID'], 'attachmentId', true );
                            if( !empty( $attachment_id ) ){

                                $attachment = wp_get_attachment_image_src($attachment_id);
                                $attachment = !empty($attachment[0]) ? $attachment[0] : '';
                            }
                        }

                        $comment['attachment'] = $attachment;

                        $comment    =   apply_filters( 'commenter_add_data_to_csv', $comment, $this->cd_karma );

                        unset($comment['comment_ID']);

                        array_map( array($this, 'commenter_strip_tags'), $comment );

                        fputcsv( $csv_file, $comment );
                    }

                    $response['data'] = $upload_dir['basedir']. '/commentdata.csv';
                }
            }

            echo json_encode( $response );
            die;
        }

        /* Save settings of plugin */
        function cd_setting(){

            $response = array('error'=>false, 'msg'=>'','data'=>'');

            $success = '<p class="success">'.__( 'Options updated successfully', 'cd' ).'</p>';
            $error   = '<p class="error">'.__( 'Failed to update, please try again!', 'cd' ).'</p>';

            parse_str( $_POST['data'], $data );
            $prevVal    = get_option('cd_setting');
            if( $prevVal != $data['cd_fields'] ){
                
                if( update_option( 'cd_setting', $data['cd_fields'] ) )
                    $response['data'] = $success;
                else
                    $response['data'] = $error;

            }else
                $response['data'] = $success;
            
            echo json_encode($response);
            die;

        }
        
        /* Load posts */
        function commenter_loadpost(){

            $response = array('ispost'=>true,'data'=>'');
            
            $offset = empty( $_POST['offset'] ) ? 1 : $_POST['offset'];
            $offset = $offset * CD_LOAD_POST;

            $postTypes = get_post_types();
            unset( $postTypes['nav_menu_item'] );
            unset( $postTypes['attachment'] );
            unset( $postTypes['revision'] );
            
            $posts = new WP_Query( array( 'post_type'=>$postTypes, 'post_status'=>'publish', 'offset'=>$offset, 'posts_per_page'=> CD_LOAD_POST ) );

            if( $posts->have_posts() ){
                
                ob_start();

                while( $posts->have_posts() ){

                    $posts->the_post();
                    $title = get_the_title(); ?>

                    <div class="cd-each-post">
                        <span class="cd-title-post"><?php echo empty( $title ) ? 'No Title Post' : $title; ?></span>
                        <input type="button" data-pid="<?php the_ID() ?>" class="button cd-csv" value="Download" />
                    </div><?php

                }
                
                $response['data'] = ob_get_contents();
                ob_end_clean();
            }else
                $response['ispost'] = false;
            
            echo json_encode($response);
            die;

        }
        
        /**
         * Strip tags and return array value
         */
        function commenter_strip_tags($val){

            return strip_tags($val);
        }

    };
}