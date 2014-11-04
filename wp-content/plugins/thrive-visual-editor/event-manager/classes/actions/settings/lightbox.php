<?php /* render specific settings for Thrive Lightbox actions */ ?>
<h5>Thrive Lightbox Settings</h5>

<table class="tve_no_brdr">
    <tr>
        <td>Which Thrive Lightbox should be displayed ?</td>
        <td>
            <select name="l_id" class="tve_ctrl_validate" data-validators="required">
                <option value="">Select Lighbox</option>
                <?php foreach ($this->lightboxes as $lightbox) : ?>
                    <option value="<?php echo $lightbox->ID ?>"<?php
                        echo !empty($this->config['l_id']) && $this->config['l_id'] == $lightbox->ID ? ' selected="selected"' : '' ?>><?php echo $lightbox->post_title ?></option>
                <?php endforeach ?>
            </select>
        </td>
    </tr>
    <tr>
        <td>Lightbox animation</td>
        <td>
            <select name="l_anim">
                <?php foreach ($this->_animations as $value => $label) : ?>
                    <option value="<?php echo $value ?>"<?php
                    echo !empty($this->config['l_anim']) && $this->config['l_anim'] == $value ? ' selected="selected"' : '' ?>><?php echo $label ?></option>
                <?php endforeach ?>
            </select>
        </td>
    </tr>
</table>