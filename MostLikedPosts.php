<?php
/*
Plugin Name: Most Liked Posts
Plugin URI: http://www.facebook.com/unholy69
Description: Widget displays posts ordered by facebook likes count.
Author: Wojciech Krzysztofik (unholy69@gmail.com)
Version: 1.0
*/

/**
 * Cron job stuff
 */
### CRON JOB TASK ACTIVATION
register_activation_hook( __FILE__, 'mostLikedPostsActivation' );

function mostLikedPostsActivation()
{
    wp_schedule_event( time(), 'hourly', 'hourlyUpdateLikesCount' );
}

add_action( 'hourlyUpdateLikesCount', 'updatePostsLikesCount' );

### CRON JOB TASK DEACTIVATION
register_deactivation_hook( __FILE__, 'mostLikedPostsDeactivation' );

function mostLikedPostsDeactivation() {
    wp_clear_scheduled_hook( 'hourlyUpdateLikesCount' );
}

/**
 * Register widget
 */
require_once 'MostLikedPosts_Widget.php';

/**
 * Register stylesheet
 */
add_action('wp_enqueue_scripts', 'registerPluginStyles');

function registerPluginStyles()
{
    wp_register_style('MostLikedPosts', plugins_url('wp-most-liked-posts/MostLikedPosts.css'));
    wp_enqueue_style('MostLikedPosts');
}

/**
 * Most important thing in that SHIIIIIIIEEEEET!
 */
function updatePostsLikesCount()
{
    $posts = getPosts();
    foreach ($posts as $post) {
        $postPath = get_permalink($post->ID);
        $result = file_get_contents('https://graph.facebook.com/fql?q=SELECT%20total_count%20FROM%20link_stat%20WHERE%20url=%27' . $postPath . '%27');
        $likesData = json_decode($result);
        $likesCount = $likesData->data[0]->total_count;

        updateLikesCount($post->ID, $likesCount);
    }
}

/**
 * @return array
 * Return list of all published posts
 */
function getPosts()
{
    $posts = get_posts('numberposts=-1&post_type=post&post_status=publish');

    return $posts;
}

/**
 * @param $postID
 * @param $likesCount
 * Update post likes count
 */
function updateLikesCount($postID, $likesCount)
{
    if ( ! update_post_meta ( $postID, 'likes_count', $likesCount ) ) {
        add_post_meta( $postID, 'likes_count', $likesCount, true );
    };
}