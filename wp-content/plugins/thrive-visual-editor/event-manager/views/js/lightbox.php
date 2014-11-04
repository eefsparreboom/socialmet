<?php
$animationClasses = array();
foreach (array_keys($this->_animations) as $animation) {
    $animationClasses [] = 'tve_lb_anim_' . $animation;
}
$animationClasses = implode(' ', $animationClasses);
/* adding <script type="text/javascript"> just for editor autocompletion */
?>
<script type="text/javascript">
    function (trigger, action, config) {

        var $target = jQuery("#tve_thrive_lightbox_" + config.l_id).css('display', ''),//.clone(true).css('display', '').removeAttr('id'),
            animation = config.l_anim ? config.l_anim : "instant",
            $body = jQuery('body');

        function close_it() {
            $target.removeClass('tve_lb_open').addClass('tve_lb_closing');
            setTimeout(function () {
                $target.attr('class', '').css('display', 'none');
            }, 300);
        }

        $target.off().on("click", ".tve_p_lb_close", function () {
            close_it();
        });

        $body.off('keyup.tve_lb_close').on('keyup.tve_lb_close', function (e) {
            if (e.which == 27) {
                close_it();
            }
        });

        $target.children('.tve_p_lb_overlay').off('click.tve_lb_close').on('click.tve_lb_close', close_it);

        $target.addClass('tve_p_lb_background tve_lb_anim_' + animation);

        setTimeout(function () {
            $target.addClass('tve_lb_opening');

            /* reload any iframe that might be in there, this was causing issues with google maps embeds in hidden tabs */
            $target.find('iframe').each(function () {
                var $this = jQuery(this);
                if ($this.data('tve_ifr_loaded')) {
                    return;
                }
                $this.data('tve_ifr_loaded', 1).attr('src', $this.attr('src'));
            });
        }, 20);

        setTimeout(function () {
            $target.removeClass('tve_lb_opening').addClass('tve_lb_open').find('.tve_p_lb_content').trigger('tve.lightbox-open');
        }, 300);

        return false;
    }
    ;
</script>