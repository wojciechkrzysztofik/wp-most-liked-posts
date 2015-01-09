<?php

/**
 * Class MostLikedPosts_Widget
 * @author: Wojciech Krzysztofik (unholy69@gmail.com)
 */
class MostLikedPosts_Widget extends WP_Widget
{
    function __construct()
    {
        parent::__construct(
            'most_liked_posts',
            __('Most liked posts', 'MostLikedPosts'),
            array('description' => __('Displays posts list ordered by count of facebook likes.', 'MostLikedPosts'),)
        );
    }

    /**
     * @param array $args
     * @param array $instance
     */
    public function widget($args, $instance)
    {
        if (!empty($instance['number'])) {
            $limit = $instance['number'];
        } else {
            $limit = 8;
        }

        $args = array(
            'numberposts' => $limit,
            'post_type' => 'post',
            'post_status' => 'publish',
            'meta_key' => 'likes_count',
            'meta_value' => 0,
            'meta_compare' => '>',
            'orderby' => 'meta_value_num',
            'order' => 'DESC'
        );
        $posts = get_posts($args);
        ?>
        <aside id="most_popular_posts" class="widget widget_recent_entries">
            <?php if (!empty($instance['title'])): ?>
                <h3 class="widget-title"><?php echo $instance['title'] ?></h3>
            <?php else: ?>
                <h3 class="widget-title"><?php _e('Most popular posts', 'MostLikedPosts') ?></h3>
            <?php endif ?>
            <ul>
                <?php foreach ($posts as $post): ?>
                    <?php $likesCount = get_post_meta($post->ID, 'likes_count'); ?>
                    <li>
                        <a href="<?php echo get_permalink($post->ID) ?>" rel="external nofollow" class="url">
                            <?php echo $post->post_title; ?>
                            <span class="fb-likes"><?php echo $likesCount[0] ?></span>
                        </a>
                    </li>
                <?php endforeach ?>
            </ul>
        </aside>
    <?php
    }

    /**
     * @param array $instance
     * @return string|void
     */
    public function form($instance)
    {
        $title = !empty($instance['title']) ? $instance['title'] : '';
        $number = !empty($instance['number']) ? $instance['number'] : '';
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
                   name="<?php echo $this->get_field_name('title'); ?>" type="text"
                   value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Posts number:'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('number'); ?>"
                   type="number"
                   name="<?php echo $this->get_field_name('number'); ?>" type="text"
                   value="<?php echo esc_attr($number); ?>">
        </p>
    <?php
    }

    /**
     * @param array $new_instance
     * @param array $old_instance
     * @return array
     */
    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? strip_tags($new_instance['title']) : '';
        $instance['number'] = (!empty($new_instance['number'])) ? strip_tags($new_instance['number']) : '';

        return $instance;
    }
}

function register_most_liked_posts_widget()
{
    register_widget('MostLikedPosts_Widget');
}

add_action('widgets_init', 'register_most_liked_posts_widget');

?>