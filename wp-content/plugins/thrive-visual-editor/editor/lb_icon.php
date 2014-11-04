<?php

/**
 * This is a good mix of icons from Icon Manager and icons from Landing Pages Templates :)
 */

require_once '../../../../wp-load.php';
$icon_data = get_option('thrive_icon_pack');
$selected = !empty($_POST['current_icon']) ? trim($_POST['current_icon']) : '';

$landing_page_template = '';
$landing_page_config = array();

if (isset($_POST['post_id']) && !empty($_POST['post_id'])) {
    $landing_page_template = tve_post_is_landing_page($_POST['post_id']);
}

$landing_page_config = tve_get_landing_page_config($landing_page_template);

?>
    <h2>Choose an icon</h2>
<?php if ((empty($icon_data) || empty($icon_data['icons'])) && empty($landing_page_config['icons'])) : ?>
    <p>It seems you don't have any icon pack loaded yet.
        <a target="_blank" href="<?php echo admin_url('admin.php?page=thrive_icon_manager') ?>">Click here</a> to add your first icon pack.
    </p>
<?php else : ?>
    <div class="icomoon-icon-list">

        <?php if (!empty($icon_data['icons'])) : ?>
            <?php foreach ($icon_data['icons'] as $class) : ?>
                <span class="icomoon-icon<?php echo ($class == $selected) ? ' tve_selected' : '' ?>" title="<?php echo $class ?>" data-cls="<?php echo $class ?>">
                <span class="<?php echo $class ?>"></span>
                <span class="tve_tick"></span>
            </span>
            <?php endforeach ?>
        <?php endif; ?>

        <?php if (!empty($landing_page_config['icons'])) : ?>
            <?php foreach ($landing_page_config['icons'] as $class) : ?>
                <span class="icomoon-icon<?php echo ($class == $selected) ? ' tve_selected' : '' ?>" title="<?php echo $class ?>" data-cls="<?php echo $class ?>">
                    <span class="<?php echo $class ?>"></span>
                    <span class="tve_tick"></span>
                </span>
            <?php endforeach ?>
        <?php endif; ?>
    </div>
<?php endif ?>
    <input type="hidden" name="tve_lb_type" value="sc_icon">
    <input type="hidden" name="cb_icon" value="<?php if (!empty($_POST['cb_icon'])) echo 1 ?>">

<?php if (!empty($icon_data['css'])) : $version = isset($icon_data['css_version']) ? $icon_data['css_version'] : TVE_VERSION; ?>
    <script type="text/javascript">
        var css = jQuery('#thrive_icon_pack');
        if (!css.length) {
            jQuery('<link id="thrive_icon_pack" rel="stylesheet" type="text/css" href="<?php echo $icon_data['css'] . '?ver=' . $version ?>">').appendTo('head');
        }
    </script>
<?php endif ?>

<?php if (!empty($landing_page_config['icons']) || !empty($icon_data)) : ?>
    <script type="text/javascript">
        jQuery('.icomoon-icon').click(function () {
            jQuery('.icomoon-icon').removeClass("tve_selected");
            jQuery(this).addClass('tve_selected');
        });
    </script>
<?php endif ?>