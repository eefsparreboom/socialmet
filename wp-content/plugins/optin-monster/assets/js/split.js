/* ==========================================================
 * split.js
 * http://optinmonster.com/
 * ==========================================================
 * Copyright 2014 Thomas Griffin.
 *
 * Licensed under the GPL License, Version 2.0 or later (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 * ========================================================== */
;(function($){
    $(function(){
        // Make sure that we update to the proper view when making bulk actions.
        $('#optin-monster-optins-table').attr('action', $('#optin-monster-optins-table').attr('action') + '&om_view=split');
        $('<input type="hidden" name="om_view" value="split" />').prependTo($('#optin-monster-optins-table'));
        $('<input type="hidden" name="om_optin_id" value="' + optin_monster_split.id + '" />').prependTo($('#optin-monster-optins-table'));

        // Make sure users want to delete optins before proceeding.
        $(document).on('click', '.submitdelete', function(e){
            return confirm(optin_monster_split.confirm);
        });

        // Make sure users want to perform the bulk action before proceeding.
        $('#optin-monster-optins-table').submit( function () {
            return confirm(optin_monster_split.confirm);
        });

        // Open the optin settings icon on click.
        $(document).on('click', '.om-settings-button', function(e){
            e.preventDefault();
            var $this = $(this),
                popup = $this.next();

            // Hide any popovers active.
            $('.om-settings-popover').hide();

            if ( $this.hasClass('om-active') ) {
                $this.removeClass('om-active');
                popup.hide();
            } else {
                $('.om-settings-button').removeClass('om-active');
                $this.addClass('om-active');
                popup.css('left', - (popup.width() + 22)).show();
            }
        });

        // Hide the settings popover when the user clicks anywhere on the page.
        $(document).on('click', function(e){
            var target = e.target,
                parent = $(e.target).closest('.om-settings-popover');

            if ( $(e.target).hasClass('om-settings-button') || $(e.target).hasClass('fa-cog') ) {
                return;
            }

            if ( 0 === parent.length ) {
                $('.om-settings-popover').hide();
                $('.om-settings-button').removeClass('om-active');
            }
        });

        // Load modal for adding a new split test.
        $(document).on('click', '.om-add-split', function(e){
            e.preventDefault();
            $('#optin-monster-add-split').css({
                top: ($(window).height() - $('#optin-monster-add-split').height()) / 2,
                left: ($(window).width() - $('#optin-monster-add-split').width()) / 2
            });
            $(window).resize(function(){
                $('#optin-monster-add-split').css({
                    top: ($(window).height() - $('#optin-monster-add-split').height()) / 2,
                    left: ($(window).width() - $('#optin-monster-add-split').width()) / 2
                });
            });
            $('.om-split-overlay').appendTo('body').show();
            $('#optin-monster-add-split').show();
            $('#om-split-title').focus();
        });

        // Close the split test modal.
        $(document).on('click', '.om-close-split', function(e){
            e.preventDefault();
            $('.om-split-overlay').appendTo('#optin-monster-add-split').hide();
            $('#optin-monster-add-split').hide();
        });
    });
}(jQuery));