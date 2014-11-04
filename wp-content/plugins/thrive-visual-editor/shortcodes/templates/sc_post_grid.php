<?php
/**
 * Post Grid View
 */
?>
<?php if ( !empty($_POST['placeholder']) || !isset($this->_config['post_types'])) : ?>
    <div class="image_placeholder thrv_wrapper">
        <a id="lb_post_grid" class="tve_click tve_green_button tve_clearfix" href="javascript:void(0)"
           data-ctrl="controls.lb_open" data-btn-text="Update" data-load="1">
            <i></i>
            <span>Add Post Grid</span>
        </a>
    </div>
<?php else : ?>
    <?php
    $config = $this->_config;
    $post_types = $this->tve_get_post_types($config);
    $post_types = empty($post_types) ? 'any' : $post_types;
    ?>
    <?php if($this->wrap) : ?>
    <div class="thrv_wrapper thrv_post_grid">
        <div class="thrive-shortcode-config" style="display: none !important"><?php echo '__CONFIG_post_grid__' . json_encode($config) . '__CONFIG_post_grid__' ?></div>
    <?php endif; ?>
        <div class="tve_post_grid_wrapper tve_clearfix tve_post_grid_<?php echo $config['display']; ?>">
            <?php $index = 1; ?>
            <?php $posts = $this->tve_get_post_grid_posts($post_types, $config['filters'], $config['posts_per_page'], $config['order'], $config['orderby']); ?>
            <?php foreach ($posts as $post): ?>
                <div
                    class="tve_post tve_post_width_<?php echo $config['columns'] ?> <?php //echo $index % $config['columns'] === 0 ? 'tve_last' : '' ?>">

                    <?php foreach ($config['layout'] as $call) : ?>
                        <?php if (isset($config['teaser_layout'][$call]) && $config['teaser_layout'][$call] === 'true') : ?>
                            <?php $callable = 'tve_post_grid_display_post_' . $call; ?>
                            <?php $this->$callable($post, $config); ?>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <?php if (isset($config['teaser_layout']['read_more']) && $config['teaser_layout']['read_more'] === 'true') : ?>
                        <?php $this->tve_post_grid_display_post_read_more($post, $config); ?>
                    <?php endif; ?>
                </div>
                <?php if ($index % $config['columns'] === 0) : ?>
                    <div class="tve_clearfix"></div>
                <?php endif; ?>
                <?php $index++; ?>
            <?php endforeach; ?>
            <?php if(count($posts) === 0) : ?>
                <p>No posts to display for this grid. Please modify the criteria to display some posts</p>
            <?php endif; ?>
        </div>
    <?php if($this->wrap) : ?>
    </div>
    <?php endif; ?>
<?php endif; ?>
