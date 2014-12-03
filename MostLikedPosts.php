<?php
/*
Plugin Name: Most Liked Posts
Plugin URI: http://www.facebook.com/unholy69
Description: Widget displays posts ordered by facebook likes count.
Author: Wojciech Krzysztofik (unholy69@gmail.com)
Version: 1.0
*/

/**
 * Register plugin widget
 */
require_once 'MostLikedPosts_Widget.php';

function register_most_liked_posts_widget()
{
    register_widget('MostLikedPosts_Widget');
}

add_action('widgets_init', 'register_most_liked_posts_widget');


/**
 * Init MostLikedPosts plugin
 */
add_action('init', function () {
    new MostLikedPosts();
});

/**
 * Class MostLikedPosts
 */
class MostLikedPosts
{
    public function __construct()
    {
        add_action('admin_init', array($this, 'registerPluginOptions'));
        add_action('wp_enqueue_scripts', array($this, 'register_plugin_styles'));
    }

    /**
     * Register plugin stylesheet
     */
    public function register_plugin_styles()
    {
        wp_register_style('MostLikedPosts', plugins_url('most-liked-posts/MostLikedPosts.css'));
        wp_enqueue_style('MostLikedPosts');
    }

    /**
     * Register plugin options
     */
    public function registerPluginOptions()
    {
        $posts = $this->getPosts();
        foreach ($posts as $post) {
            $rand = rand(0, 5); //TODO: cronjob instead of random
            if ($rand == 1) {
                $postPath = get_permalink($post->ID);
                $result = file_get_contents('https://graph.facebook.com/fql?q=SELECT%20total_count%20FROM%20link_stat%20WHERE%20url=%27' . $postPath . '%27');
                $likesData = json_decode($result);
                $likesCount = $likesData->data[0]->total_count;

                $this->updateLikesCount($post->ID, $likesCount);
            }
        }
    }

    /**
     * @return array
     * Return list of all published posts
     */
    public function getPosts()
    {
        $posts = get_posts('numberposts=-1&post_type=post&post_status=publish');

        return $posts;
    }

    /**
     * @param $postID
     * @param $likesCount
     * Update post likes count
     */
    public function updateLikesCount($postID, $likesCount)
    {
        if ( ! update_post_meta ( $postID, 'likes_count', $likesCount ) ) {
            add_post_meta( $postID, 'likes_count', $likesCount, true );
        };
    }
}