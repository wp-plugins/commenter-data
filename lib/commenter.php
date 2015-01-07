<?php
if( !class_exists('commenter') ){

    class commenter {

        public $cd_setting;
        public $cd_setting_val;
        public $cd_setting_default = array( 'name'=>0, 'email'=>0, 'website'=>0 );

        /* Constructor */
        function __construct() {

            add_action( 'admin_enqueue_scripts', array( $this, 'cd_scripts' ) );
            add_action( 'admin_menu', array( $this, 'cd_setting_page' ) );
            add_action( 'add_meta_boxes', array($this, 'cd_metaboxes') );
            add_action( 'admin_init' , array( $this, 'cd_download' ) );

            $this->cd_setting = $this->cd_setting_val = get_option( 'cd_setting', array() );
            $this->cd_setting = wp_parse_args( $this->cd_setting, $this->cd_setting_default );
        }

        /* Add scripts to admin */
        function cd_scripts(){

            wp_enqueue_script('cd-admin', CD_JS.'cd-admin.js', array('jquery'));
            wp_enqueue_style( 'cd-style', CD_CSS.'cd-admin.css', array(), false, 'all' );

        }

        /* Add option page */
        function cd_setting_page(){

            add_menu_page( __( 'Commenter data Settings', 'cd' ) , __('Commenter Data', 'cd'), apply_filters( 'cd_cap', 'manage_options' ), 'commenterdata-settings', array( $this, 'cd_renderer' ), CD_IMG.'commenter.png' );
        }

        /* Function to download csv file */
        function cd_download(){

            $upload_dir =   wp_upload_dir();
            $filename   =   $upload_dir['basedir']. '/commentdata.csv';

            if( !empty( $_GET['cddcsv'] ) && $_GET['cddcsv'] == 1 && !empty( $_GET['pid'] ) && file_exists( $filename ) ){

                $post = get_post( $_GET['pid'] );
                $post = !empty($post->post_title) ? sanitize_title($post->post_title) : 'commenterdata';

                $file = fopen( $filename, 'r' );
                $contents = fread($file, filesize($filename));
                fclose($file);

                unlink($filename);

                header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
                header('Content-Description: File Transfer');
                header("Content-type: text/csv");
                header("Content-Disposition: attachment; filename=".$post.'.csv');
                header("Expires: 0");
                header("Pragma: public");

                $fh = @fopen( 'php://output', 'w' );
                fwrite( $fh, $contents );
                fclose($fh);
                exit();

            }
        }

        /* Render backend setting page */
        public function cd_renderer(){ ?>

            <div class="cd-setting-wrap">

                <h2 class="cd-title"><?php _e('Commenter data setting', 'cd') ?></h2>

                <div class="cd-msg"></div>
            
                <form id="cd-setting-form" method="post" action="./">
                    
                    <div class="cds-field">
                        <input id="name" type="checkbox" name="cd_fields[cpost]" value="comment_post_ID" <?php echo !empty( $this->cd_setting['cpost'] ) ? 'checked="checked"' : '' ?> />
                        <label for="cpost"><?php _e('Comment post id','cd') ?></label>
                    </div>

                    <div class="cds-field">
                        <input id="name" type="checkbox" name="cd_fields[cdate]" value="comment_date" <?php echo !empty( $this->cd_setting['cdate'] ) ? 'checked="checked"' : '' ?> />
                        <label for="cdate"><?php _e('Comment date','cd') ?></label>
                    </div>
                    
                    <div class="cds-field">
                        <input id="name" type="checkbox" name="cd_fields[name]" value="comment_author" <?php echo !empty( $this->cd_setting['name'] ) ? 'checked="checked"' : '' ?> />
                        <label for="name"><?php _e('Name','cd') ?></label>
                    </div>

                    <div class="cds-field">
                        <input id="email" type="checkbox" name="cd_fields[email]" value="comment_author_email" <?php echo !empty( $this->cd_setting['email'] ) ? 'checked="checked"' : '' ?> />
                        <label for="email"><?php _e('Email','cd') ?></label>
                    </div>

                    <div class="cds-field">
                        <input id="website" type="checkbox" name="cd_fields[website]" value="comment_author_url" <?php echo !empty( $this->cd_setting['website'] ) ? 'checked="checked"' : '' ?> />
                        <label for="website"><?php _e('Website','cd') ?></label>
                    </div>

                    <div class="cds-field">
                        <input id="content" type="checkbox" name="cd_fields[content]" value="comment_content" <?php echo !empty( $this->cd_setting['content'] ) ? 'checked="checked"' : '' ?> />
                        <label for="content"><?php _e('Comment content','cd') ?></label>
                    </div>

                    <div class="cds-field">
                        <input id="attachment" type="checkbox" name="cd_fields[attachment]" value="comment_attachment" <?php echo !empty( $this->cd_setting['attachment'] ) ? 'checked="checked"' : '' ?> />
                        <label for="attachment"><?php _e('Attachment URL','cd') ?></label>
                    </div>

                    <?php
                        /* Add fields in backend using this hook */
                        do_action('commenter_add_field');
                    ?>

                    <div class="cds-field">
                        <input class="cd-setting button" type="button" name="cds_submit" value="Save" />
                    </div>
                    
                </form>
            </div>

            <div class="cd-setting-wrap cd-download-section">
                
                <h2 class="cd-title"><?php _e('Download commenter data', 'cd') ?></h2><?php
                
                    $postTypes = get_post_types();
                    unset( $postTypes['nav_menu_item'] );
                    unset( $postTypes['attachment'] );
                    unset( $postTypes['revision'] );

                    $posts = new WP_Query( array( 'post_type'=>$postTypes, 'post_status'=>'publish', 'posts_per_page'=> CD_LOAD_POST ) ); ?>

                <div class="cd-post-listing"><?php
                    
                    if( $posts->have_posts() ){
                        
                        while( $posts->have_posts() ){
                            
                            $posts->the_post();
                            $title = get_the_title(); ?>
                            
                            <div class="cd-each-post">
                                <span class="cd-title-post"><?php echo empty( $title ) ? 'No Title Post' : $title; ?></span>
                                <input type="button" data-pid="<?php the_ID() ?>" class="button cd-csv" value="Download" />
                            </div><?php

                        }

                        echo '<div class="cd-load" data-offset="1">'.__( 'Load more posts', 'cd' ).'</div>';

                    } ?>

                </div>

            </div>
            <?php
        }

        /* Add metaboxes */
        function cd_metaboxes(){
            
            $noMetabox = array( 'attachment', 'revision', 'nav_menu_item' ); // no metabox for these post types
            $postTypes = get_post_types();

            foreach( $postTypes as $posttype ){
                
                if( in_array( $posttype, $noMetabox ) )
                    continue;

                add_meta_box( 'commenter_data', __( 'Commenter data', 'cd' ), array( $this, 'cd_form_metabox' ) , $posttype , 'side', 'high' );

            }
        }
        
        /* Metabox formation */
        function cd_form_metabox(){ ?>

            <div class="cd-wrapper">
                <form method="post" action="./">
                    <div class="cd-field">
                        <input type="button" data-pid="<?php the_ID() ?>" class="cd-csv button" value="Downalod commenter data" />
                        <img src="<?php echo CD_IMG.'loader.gif' ?>" alt="Loader" />
                    </div>
                </form>
            </div><?php

        }

    };

}