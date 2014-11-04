<?php

/**
 * Created by PhpStorm.
 * User: Danut
 * Date: 10/16/2014
 * Time: 9:54 AM
 */
class PostGridHelper
{
    protected $_template = 'sc_post_grid.php';
    protected $_config;
    public $wrap = true;

    public function __construct($config)
    {
        $this->_config = $config;
    }

    public function render()
    {
        return '';
        ob_start();
        $this->_getView($this->_template);
        $html = ob_get_contents();
        ob_end_clean();
        return $html;
    }

    protected function _getView($template)
    {
        $path = dirname(__FILE__) . '/../../../shortcodes/templates/' . $template;
        include $path;
    }


    /**
     * @param string|array $types
     * @return array
     */
    function tve_get_post_grid_posts($types = 'any', $filters = array(), $post_per_page = 3, $order = "ASC", $orderby = 'title')
    {
        $args = array(
            'post_type' => $types,
            'posts_per_page' => $post_per_page,
            'order' => $order,
            'orderby' => $orderby,
            'post__not_in' => array($this->_config['exclude']),
        );

        if (!empty($filters['category'])) {
            $categories = trim($filters['category'], ",");
            $args['category_name'] = $categories;
        }

        if (!empty($filters['tag'])) {
            $tag_names = explode(",", trim($filters['tag'], ','));
            $tag_names = array_unique($tag_names);
            $tags = array();
            foreach ($tag_names as $tag_name) {
                $tag = get_term_by('name', $tag_name, 'post_tag');
                if(!$tag) {
                    continue;
                }
                $tags[] = $tag->slug;
            }
            $args['tag_slug__in'] = $tags;
        }

        if (!empty($filters['tax'])) {
            $tax_names = explode(",", trim($filters['tax'], ","));
            $tax_names = array_unique($tax_names);
            $tax_query = array();
            //foreach taxonomy name get all its terms and build tax_query for it
            foreach ($tax_names as $tax_name) {
                $termsObj = get_terms($tax_name);
                if (empty($termsObj) || $termsObj instanceof WP_Error) {
                    continue;
                }
                $tax_terms = array();
                foreach ($termsObj as $term) {
                    $tax_terms[] = $term->slug;
                }
                $tax_query[] = array(
                    'taxonomy' => $tax_name,
                    'field' => 'slug',
                    'terms' => $tax_terms
                );
            }
            if (!empty($tax_query)) {
                $tax_query['relation'] = 'OR';
                $args['tax_query'] = $tax_query;
            }
        }

        if (!empty($filters['author'])) {
            $author_names = explode(",", trim($filters['author'], ","));
            $author_names = array_unique($author_names);
            $author_ids = array();
            foreach ($author_names as $name) {
                $author = get_user_by('slug', $name);
                if ($author) {
                    $author_ids[] = $author->ID;
                }
            }
            if (!empty($author_ids)) {
                $args['author'] = implode(",", $author_ids);
            }
        }

        if (!empty($filters['posts'])) {
            $post_ids = explode(",", trim($filters['posts'], ","));
            $post_ids = array_unique($post_ids);
            $args['post__in'] = $post_ids;
        }

        $results = new WP_Query($args);

        return $results->get_posts();
    }

    function tve_get_post_types($config)
    {
        $types = array();
        if (isset($config['post_types']) && is_array($config['post_types']) && !empty($config['post_types'])) {
            foreach ($config['post_types'] as $type => $checked) {
                if ($checked === 'true') {
                    $types[] = $type;
                }
            }
        }
        return $types;
    }

    function tve_post_grid_get_config()
    {
        $config = $_POST;

        unset($config['tve_lb_type']);
        unset($config['security']);

        return $config;
    }

    function tve_post_grid_display_post_featured_image($post, array $config)
    {
        $size = $config['thumb_size'];
        $size = explode("x", $size);
        $size = array_filter($size);

        if (empty($size)) {
            $size = array('200px', '150px');
        } else {
            $size[0] .= 'px';
            $size[1] .= 'px';
        }

        if (has_post_thumbnail($post->ID)) {
            $src = wp_get_attachment_url(get_post_thumbnail_id($post->ID));
        } else {
            $src = TVE_DIR . "/editor/css/images/tve_no_image.png";
        }

        $attr = array(
            'src' => $src,
            'class' => ''
        );
        ?>
        <div class="tve_post_grid_image_wrapper"
             style="width: <?php echo $size[0] ?>; height: <?php echo $size[1] ?>; background-position: center center; background-size: cover; background-repeat: no-repeat; background-image: url('<?php echo $src; ?>')"></div>
    <?php
    }

    function tve_post_grid_display_post_text($post_item, array $config)
    {
        global $post;
        $old_post = $post;
        $post = $post_item;

        $content = $post->post_content;
        $content = str_replace(']]>', ']]&gt;', apply_filters('the_content', $post->post_content));

        $post = $old_post;

        $content = wp_strip_all_tags($content, true);

        if ($config['text_type'] === 'summary') {
            $words = explode(" ", $content);
            $content = implode(" ", array_splice($words, 0, 20)) . "...";
        }

        if (empty($content)) {
            return;
        }

        ?><p><?php echo $content; ?></p><?php
    }

    function tve_post_grid_display_post_title($post, $config)
    {
        if (empty($post->post_title)) {
            return;
        }
        ?><h1><?php echo $post->post_title ?></h1><?php
    }

    function tve_post_grid_display_post_read_more($post, $config)
    {
        ?><a href="<?php echo $post->guid ?>"><?php echo __('Read More') ?></a><?php
    }
}
