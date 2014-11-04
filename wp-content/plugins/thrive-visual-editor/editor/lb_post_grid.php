<?php

require_once '../../../../wp-load.php';

/**
 * Wrapper for get_post_types. Just to apply some logic if needed
 * @return array
 */
function get_all_post_types()
{
    $types = array();

    $banned_types = array(
        'revision',
        'nav_menu_item',
        'project',
        'et_pb_layout',
        'tcb_lightbox',
        'focus_area',
        'thrive_optin',
        'thrive_ad_group',
        'thrive_ad',
        'thrive_slideshow',
        'thrive_slide_item'
    );
    foreach (get_post_types() as $type) {
        if (!in_array($type, $banned_types)) {
            $types[] = $type;
        }
    }

    return $types;
}

function display_layouts()
{
    $layouts = array(
        'featured_image' => 'Featured image',
        'title' => 'Title',
        'text' => 'Text',
    );

    if (isset($_POST['layout']) && !empty($_POST['layout'])) {
        foreach ($_POST['layout'] as $id) {
            ?>
            <li data-layout="<?php echo $id ?>" class="ui-state-default"><span
                class="ui-icon ui-icon-arrowthick-2-n-s"></span><?php echo $layouts[$id] ?></li><?php
        }
        return;
    }

    foreach ($layouts as $id => $label) {
        ?>
        <li data-layout="<?php echo $id ?>" class="ui-state-default"><span
            class="ui-icon ui-icon-arrowthick-2-n-s"></span><?php echo $label ?></li><?php
    }

}


?>
<div class="tve_post_grid_tabs_container">
    <input type="hidden" name="tve_lb_type" value="tve_post_grid">

    <div class="tve_scT tve_green">
        <ul class="tve_clearfix">
            <li class="tve_tS tve_click"><span class="tve_scTC1">Layout</span></li>
            <li class="tve_click"><span class="tve_scTC2">Edit Query</span></li>
            <li class="tve_click"><span class="tve_scTC3">Filter Settings</span></li>
            <li class="tve_click"><span class="tve_scTC4">Display Settings</span></li>
        </ul>

        <div class="tve_scTC tve_scTC1 tve_clearfix" style="display: block">

            <label>Teaser Layout</label>

            <div class="tve_fields_container">
                <label><input
                        type="checkbox" <?php echo isset($_POST['teaser_layout']) ? isset($_POST['teaser_layout']['featured_image']) && $_POST['teaser_layout']['featured_image'] === 'true' ? 'checked="checked"' : '' : 'checked="checked"' ?>
                        name="teaser_layout[featured_image]"/>Featured image</label>
                <label><input
                        type="checkbox" <?php echo isset($_POST['teaser_layout']) ? isset($_POST['teaser_layout']['title']) && $_POST['teaser_layout']['title'] === 'true' ? 'checked="checked"' : '' : 'checked="checked"' ?>
                        name="teaser_layout[title]"/>Title</label>
                <label><input
                        type="checkbox" <?php echo isset($_POST['teaser_layout']) ? isset($_POST['teaser_layout']['text']) && $_POST['teaser_layout']['text'] === 'true' ? 'checked="checked"' : '' : 'checked="checked"' ?>
                        name="teaser_layout[text]"/>Text</label>
                <label><input
                        type="checkbox" <?php echo isset($_POST['teaser_layout']) ? isset($_POST['teaser_layout']['read_more']) && $_POST['teaser_layout']['read_more'] === 'true' ? 'checked="checked"' : '' : 'checked="checked"' ?>
                        name="teaser_layout[read_more]"/>Read more link</label>
            </div>

            <label>Text type</label>

            <div class="tve_fields_container">
                <select name="text_type">
                    <option
                        value="summary" <?php echo isset($_POST['text_type']) && $_POST['text_type'] === 'summary' ? 'selected="selected"' : '' ?>>
                        Summary
                    </option>
                    <option
                        value="fulltext" <?php echo isset($_POST['text_type']) && $_POST['text_type'] === 'fulltext' ? 'selected="selected"' : '' ?>>
                        Full text
                    </option>
                </select>
            </div>

            <label>Layout</label>

            <div class="tve_fields_container">
                <p>Drag the items into the correct order for display:</p>

                <ul class="tve_sortable_layout">
                    <?php display_layouts() ?>
                </ul>
            </div>
        </div>

        <div class="tve_scTC tve_scTC2 tve_clearfix">

            <label>Content to include</label>

            <div class="tve_fields_container">
                <?php foreach (get_all_post_types() as $type) : ?>
                    <label><input
                            type="checkbox" <?php echo isset($_POST['post_types'][$type]) ? $_POST['post_types'][$type] === 'true' ? 'checked="checked"' : '' : $type === 'post' ? 'checked="checked"' : '' ?>
                            name="post_types[<?php echo $type ?>]"/><?php echo ucfirst($type) ?></label>
                <?php endforeach; ?>
            </div>

            <label>Number of posts</label>

            <div class="tve_fields_container">
                <select name="posts_per_page">
                    <?php for ($i = 1; $i <= 10; $i++) : ?>
                        <option <?php echo isset($_POST['posts_per_page']) ? $_POST['posts_per_page'] == $i ? 'selected="selected"' : '' :
                            $i === 6 ? 'selected="selected"' : '' ?>><?php echo $i; ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <label>Order by</label>

            <div class="tve_fields_container">
                <select name="orderby">
                    <option
                        value="date" <?php echo isset($_POST['orderby']) && $_POST['orderby'] === 'date' ? 'selected="selected"' : '' ?>>
                        Date
                    </option>
                    <option
                        value="title" <?php echo isset($_POST['orderby']) && $_POST['orderby'] === 'title' ? 'selected="selected"' : '' ?>>
                        Title
                    </option>
                    <option
                        value="author" <?php echo isset($_POST['orderby']) && $_POST['orderby'] === 'author' ? 'selected="selected"' : '' ?>>
                        Author
                    </option>
                    <option
                        value="comment_count" <?php echo isset($_POST['orderby']) && $_POST['orderby'] === 'comment_count' ? 'selected="selected"' : '' ?>>
                        Number of Comments
                    </option>
                    <option
                        value="rand" <?php echo isset($_POST['orderby']) && $_POST['orderby'] === 'rand' ? 'selected="selected"' : '' ?>>
                        Random
                    </option>
                </select>
            </div>

            <label>Order</label>

            <div class="tve_fields_container">
                <select name="order">
                    <option
                        value="DESC" <?php echo isset($_POST['order']) && $_POST['order'] === 'DESC' ? 'selected="selected"' : '' ?>>
                        Descending
                    </option>
                    <option
                        value="ASC" <?php echo isset($_POST['order']) && $_POST['order'] === 'ASC' ? 'selected="selected"' : '' ?>>
                        Ascending
                    </option>
                </select>
            </div>
        </div>

        <div class="tve_scTC tve_scTC3 tve_clearfix">
            <p>Choose which content to include</p>

            <div class="tve_fields_container ui-front">
                <label>Categories</label><input name="filters[category]"
                                                value="<?php echo @$_POST['filters']['category'] ?>" type="text"
                                                class="tve_post_grid_autocomplete" data-action="tve_categories_list"/>
            </div>

            <div class="tve_fields_container ui-front">
                <label>Tags</label><input name="filters[tag]" value="<?php echo @$_POST['filters']['tag'] ?>"
                                          type="text" class="tve_post_grid_autocomplete" data-action="tve_tags_list"/>
            </div>

            <div class="tve_fields_container ui-front">
                <label>Custom Taxonomies</label><input name="filters[tax]"
                                                       value="<?php echo @$_POST['filters']['tax'] ?>" type="text"
                                                       class="tve_post_grid_autocomplete"
                                                       data-action="tve_custom_taxonomies_list"/>
            </div>

            <div class="tve_fields_container ui-front">
                <label>Author</label><input name="filters[author]" value="<?php echo @$_POST['filters']['author'] ?>"
                                            type="text" class="tve_post_grid_autocomplete"
                                            data-action="tve_authors_list"/>
            </div>

            <div class="tve_fields_container ui-front">
                <label>Individual Posts/Pages</label><input name="filters[posts]"
                                                            value="<?php echo @$_POST['filters']['posts'] ?>"
                                                            type="text" class="tve_post_grid_autocomplete"
                                                            data-action="tve_posts_list"/>
            </div>
        </div>

        <div class="tve_scTC tve_scTC4 tve_clearfix">
            <label>Number of Columns</label>

            <div class="tve_fields_container">
                <select name="columns">
                    <?php for ($i = 1; $i <= 6; $i++) : ?>
                        <option <?php echo isset($_POST['columns']) ? $_POST['columns'] == $i ? 'selected="selected"' : '' :
                            $i === 3 ? 'selected="selected"' : '' ?> ><?php echo $i; ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <label>Thumbnail Size</label>

            <div class="tve_fields_container">
                <input name="thumb_size" type="text" value="<?php echo @$_POST['thumb_size'] ?>" placeholder="200x150"/>
            </div>

            <label>Order</label>

            <div class="tve_fields_container">
                <select name="display">
                    <option
                        value="grid" <?php echo isset($_POST['display']) && $_POST['display'] === 'grid' ? 'selected="selected"' : '' ?>>
                        Grid
                    </option>
                    <option
                        value="masonry" <?php echo isset($_POST['display']) && $_POST['display'] === 'masonry' ? 'selected="selected"' : '' ?>>
                        Masonry
                    </option>
                </select>
            </div>
        </div>
    </div>
</div>
