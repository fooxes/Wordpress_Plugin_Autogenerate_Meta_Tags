<?php

/*
 
Plugin Name: Fooxes - Auto generate meta, open graph and twitter tags
 
Plugin URI: https://fooxes.de
 
Description: Adds meta, open graph and twitter tags to the header from post/page titles, excerpt, text and images
 
Version: 1.0
 
Author: Hannes Kleist
 
Author URI: https://fooxes.de
 
License: MIT
 
*/

function wordpress_open_graph() {
    
    $post_id = get_the_ID();
    
    /****************
    * OG: LOCALE
    ****************/
    
    ?>


    <!-- Fooxes Meta Tags. -->
    <meta property="og:locale" content="<?php echo get_locale(); ?>" />	
    <meta name="twitter:card" content="summary_large_image" />
<?php 
    
    /****************
    * OG: TYPE
    ****************/
    
    if( is_singular() ) { 

        /****************
        * OG: TYPE -> ARTICLE
        ****************/

        ?>
    <meta property="og:type" content="article" />
<?php 
        
        /****************
        * OG: TYPE -> ARTICLE: PUBLISHED TIME
        ****************/
        
        $date = new DateTime( get_the_date('Y-m-d', $post_id) );
        $date_published = $date->format( DateTime::ISO8601 ); 

        if( $date_published ) { ?>
    <meta property="article:published_time" content="<?php echo esc_html( $date_published ); ?>" />    
<?php }
                         
        /****************
        * OG: TYPE -> ARTICLE: MODIFIED TIME
        ****************/
        
        $date = new DateTime( get_the_modified_date('Y-m-d', $post_id) );
        $date_modified = $date->format( DateTime::ISO8601 ); 

        if( $date_modified ) { ?>
    <meta property="article:modified_time" content="<?php echo esc_html( $date_modified ); ?>" />    
<?php }
                         
        /****************
        * OG: TYPE -> ARTICLE: SECTION
        ****************/
        
        $categories = get_the_terms( $post_id, 'category' );
        
        $get_the_categories = '';
        if( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
            $the_cats = array();		
            foreach( $categories as $category ) {
                $the_cats[] = $category->name;
            }
            $get_the_categories = implode(',', $the_cats);				
        }
        
        if( $get_the_categories ) { ?>
    <meta property="article:section" content="<?php echo esc_html( $get_the_categories ); ?>" />
<?php }
                         
        /****************
        * OG: TYPE -> ARTICLE: TAG
        ****************/
                         
        $tags = get_the_terms( $post_id, 'post_tag' );
        
        $get_the_tags = '';
        if( ! empty( $tags ) && ! is_wp_error( $tags ) ) {
            $the_tags = array();		
            foreach( $tags as $tag ) {
                $the_tags[] = $tag->name;
            }
            $get_the_tags = implode(',', $the_tags);				
        }
        
        if( $get_the_tags ) { ?>
    <meta property="article:tag" content="<?php echo esc_html( $get_the_tags ); ?>" />
<?php }
    
    } else { ?>
    <meta property="og:type" content="website" />
<?php } 

    /****************
    * OG: TITLE
    ****************/

    $title = get_the_title( $post_id );
    
    // Taxonomy page title
    if( is_archive() ) {            
        $title = get_the_archive_title();
    } ?>
    <meta property="og:title" content="<?php echo esc_html( $title ); ?>" />
	<meta name="twitter:title" content="<?php echo esc_html( $title ); ?>" />
<?php 
    
    /****************
    * OG: DESCRIPTION
    ****************/
    
    $excerpt = get_the_excerpt( $post_id );
    $content = get_post( $post_id );
    $content = isset( $content->post_content ) ? $content->post_content : '';
    
    // Taxonomy page content
    if( is_archive() || is_category() || is_tag() || is_tax() ) {			
        $content = get_the_archive_description();
    }
    
    if( $excerpt ) { ?>
    <meta property="og:description" content="<?php echo strip_tags( $excerpt ); ?>" />
    <meta name="description" content="<?php echo strip_tags( $excerpt ); ?>" />
    <meta name="twitter:description" content="<?php echo strip_tags( $excerpt ); ?>" />
<?php } elseif( $content ) { ?>
    <meta property="og:description" content="<?php echo strip_tags( wp_trim_words( $content, 100, '...' ) ); ?>" />
    <meta name="description" content="<?php echo strip_tags( wp_trim_words( $content, 100, '...' ) ); ?>" />
    <meta name="twitter:description" content="<?php echo strip_tags( wp_trim_words( $content, 100, '...' ) ); ?>" />
<?php } else { ?>
    <meta property="og:description" content="<?php echo get_bloginfo( 'description' ); ?>" />
    <meta name="description" content="<?php echo get_bloginfo( 'description' ); ?>" />
    <meta name="twitter:description" content="<?php echo get_bloginfo( 'description' ); ?>" />
<?php }		
    
    /****************
    * OG: SITE NAME
    ****************/
    
    if( get_bloginfo('name') ) { ?>
    <meta property="og:site_name" content="<?php echo get_bloginfo('name'); ?>" />
<?php } 
    
    /****************
    * OG: URL
    ****************/
    
    global $wp;
    $request = isset( $wp->request ) ? $wp->request : '';		
    $url = home_url( $request ) . '/';
    
    if( empty( $url ) ) {
        $url = get_the_permalink( $post_id );
    } ?>
    <meta property="og:url" content="<?php echo esc_url( $url ); ?>" />
<?php 
    
    /****************
    * OG: UPDATED TIME
    ****************/
    
    $date = new DateTime( get_the_modified_date('Y-m-d', $post_id) );
    $date_modified = $date->format( DateTime::ISO8601 ); 
    
    if( is_singular() && $date_modified ) { ?>
    <meta property="og:updated_time" content="<?php echo esc_html( $date_modified ); ?>" />
<?php }
    
    /****************
    * OG: IMAGE
    ****************/
    
    $image = has_post_thumbnail( $post_id );
    $thumbnail_id = get_post_thumbnail_id( $post_id );
        
    if( $image ) {
        
        // Get image meta
        $meta = wp_get_attachment_metadata( $thumbnail_id );
        
        $image_width = isset( $meta['width'] ) ? $meta['width'] : '';
        $image_height = isset( $meta['height'] ) ? $meta['height'] : '';
        $image_type = isset( $meta['sizes']['thumbnail']['mime-type'] ) ? $meta['sizes']['thumbnail']['mime-type'] : '';
        
        $image_alt = get_post_meta( $thumbnail_id, '_wp_attachment_image_alt', true );
        if( empty( $image_alt ) ) {
            $image_meta = isset( $meta['image_meta'] ) ? $meta['image_meta'] : '';			
            if( $image_meta ) {
                $image_caption = isset( $image_meta['caption'] ) ? $image_meta['caption'] : '';
                $image_title = isset( $image_meta['title'] ) ? $image_meta['title'] : '';				
                if( $image_caption ) {
                    $image_alt = $image_caption;
                } elseif( $image_title ) {
                    $image_alt = $image_title;
                }
            }
        } ?>
    <meta property="og:image" content="<?php echo wp_get_attachment_image_src( $thumbnail_id, 'full' )[0]; ?>" />
    <meta name="twitter:image" content="<?php echo wp_get_attachment_image_src( $thumbnail_id, 'full' )[0]; ?>" />
<?php 
        
        /****************
        * OG: IMAGE SECURE URL
        ****************/ 
        
        if( is_ssl() ) { ?>
    <meta property="og:image:secure_url" content="<?php echo wp_get_attachment_image_src( $thumbnail_id, 'full' )[0]; ?>" />
<?php }

        /****************
        * OG: IMAGE WIDTH
        ****************/
                                         
        if( $image_width ) { ?>
    <meta property="og:image:width" content="<?php echo esc_html( $image_width ); ?>" />
<?php }
                                          
        /****************
        * OG: IMAGE HEIGHT
        ****************/
                                         
        if( $image_height ) { ?>
    <meta property="og:image:height" content="<?php echo esc_html( $image_height ); ?>" />
<?php }

        /****************
        * OG: IMAGE ALT
        ****************/
                                         
        if( $image_alt ) { ?>
    <meta property="og:image:alt" content="<?php echo esc_html( $image_alt ); ?>" />
<?php }

        /****************
        * OG: IMAGE TYPE
        ****************/
                                         
        if( $image_type ) { ?>
    <meta property="og:image:type" content="<?php echo esc_html( $image_type ); ?>" />
<?php }
        
    } 

    ?>
    <!-- / Fooxes Meta Tags. -->

    <?php

}

add_action( 'wp_head', 'wordpress_open_graph' );