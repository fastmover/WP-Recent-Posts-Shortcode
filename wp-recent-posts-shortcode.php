<?php
/*
Plugin Name: WP Recent Posts Shortcode
Plugin URI: http://StevenKohlmeyer.com/wp-recent-posts-shortcode
Description: Shortcode to show recent posts - filterable by current category
Author: fastmover
Author URI: http://StevenKohlmeyer.com
Version: 0.0.1
License: GPL version 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

class SK_WPRecentPostsShortcode
{

    public $open = false;
    public $close = false;
    public $column = 1;
    public $postsPerPage = 4;
    public $classes = "small-6 large-3 columns";

    public function __construct()
    {

        add_shortcode( 'wp_recent_posts', array( $this, 'shortcode' ) );

    }

    public function shortcode( $atts )
    {

        $currentCategory = get_the_category( get_the_ID() );

        $atts = shortcode_atts(
            array(
                'current_post_category' => 'false',
                'all_post_categories'   => 'false',
                'posts_per_page'  => 4
            ),
            $atts,
            'wp_recent_posts'
        );
        $args = array(
            'posts_per_page' => $atts[ 'posts_per_page' ]
        );

        if( $atts[ 'current_post_category' ] === 'true' ) {
            $currentCategory = get_the_category( get_the_ID() );
            if( ! is_array( $currentCategory ) )
                break 1;

            $args[ 'category__in' ] = array( $currentCategory[ 0 ]->term_id );
        }

        if( $atts[ 'all_post_categories' ] === 'true' ) {
            $currentCategory = get_the_category( get_the_ID() );
            if( ! is_array( $currentCategory ) )
                break 1;

            $cats = array();
            foreach( $currentCategory as $cat ) {
                $cats[] = $cat->term_id;
            }

            $args[ 'category__in' ] = $cats;
        }

        $recentPosts = new WP_Query( $args );
        $this->postsPerPage = $args[ 'posts_per_page' ];

        // var_dump( $recentPosts );

        ob_start();
        while( $recentPosts->have_posts() ) {

            $recentPosts->the_post();

            $this->render();

        }

        $output = ob_end_flush();

        wp_reset_postdata();

        return $output;

    }

    public function render()
    {

        if( $this->open === false ) {
            $this->open = true;
            ?><div class="row recent-posts-shortcode"><?php
        }

        $id = get_the_ID();
        $thumbID = get_post_thumbnail_id( $id );
        $thumbSrc = wp_get_attachment_image_src( $thumbID );
        $permalink = get_permalink( $id );

        ?>
        <div class="<?= $this->classes; ?>">
            <a href="<?= $permalink; ?>">
            <div class="bg-image" style="background-image: url( '<?= $thumbSrc[ 0 ]; ?>');">
                <div class="recent-post-title">
                    <?= get_the_title(); ?>
                </div>
            </div>
            </a>
        </div>
        <?php

        if( $this->column === $this->postsPerPage ) {

            ?></div><?php

        }

    }

}

$SKWPRecentPostsShortcode = new SK_WPRecentPostsShortcode();
