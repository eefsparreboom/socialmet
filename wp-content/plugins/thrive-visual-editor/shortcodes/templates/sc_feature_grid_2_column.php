<?php $use_icons = !empty($_POST['use_icons']); ?>
<div class="thrv_wrapper thrv_feature_grid tve_gr tve_gr3" data-tve-style="1">
    <div class="tve_colm tve_twc">
        <div class="tve_left tve_gri">
            <?php if ($use_icons) : ?>
                <?php include dirname(__FILE__) . '/sc_icon.php' ?>
            <?php else : ?>
                <div class="image_placeholder thrv_wrapper">
                    <a class="upload_image tve_green_button clearfix" href="#" target="_self">
                        <i></i>
                        <span>Add Media</span>
                    </a>
                </div>
            <?php endif ?>
        </div>
        <div class="tve_left tve_grt">
            <h3>Heading 1</h3>

            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Libero, vero?</p>
        </div>
        <div class="tve_clear"></div>
    </div>
    <div class="tve_colm tve_twc tve_lst">
        <div class="tve_left tve_gri">
            <?php if ($use_icons) : ?>
                <?php include dirname(__FILE__) . '/sc_icon.php' ?>
            <?php else : ?>
                <div class="image_placeholder thrv_wrapper">
                    <a class="upload_image tve_green_button clearfix" href="#" target="_self">
                        <i></i>
                        <span>Add Media</span>
                    </a>
                </div>
            <?php endif ?>
        </div>
        <div class="tve_left tve_grt">
            <h3>Heading 2</h3>

            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Libero, vero?</p>
        </div>
        <div class="tve_clear"></div>
    </div>
    <div class="tve_clear"></div>
</div>