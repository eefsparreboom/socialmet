/* ==========================================================
 * new.js
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
        // Add class to every 3rd theme.
        $('.optin-monster-theme:nth-child(3n)').addClass('om-last-theme');

        // Change the theme selection pane when choosing a new optin type.
        $(document).on('click', '.om-optin-type', function(e){
            e.preventDefault();
            var $this      = $(this),
                active     = $this.parent().parent().find('.om-optin-type-active'),
                optin_type = $this.data('om-optin-type'),
                data       = {
                    action: 'optin_monster_load_themes',
                    type:   optin_type,
                    nonce:  optin_monster_new.type_nonce
                };

            $('.fa-spinner').fadeTo(0, 1);
            active.removeClass('om-optin-type-active');
            $this.addClass('om-optin-type-active');
            $.post(optin_monster_new.ajax, data, function(res){
                $('.optin-monster-themes').html(res);
                $('.optin-monster-theme:nth-child(3n)').addClass('om-last-theme');
                $('.fa-spinner').fadeTo(300, 0);
            }, 'json');
        });

        // Save the theme selection and take the next step to the customizer.
        $(document).on('click', '.optin-monster-theme, .om-theme-select', function(e){
            e.preventDefault();
            e.stopPropagation();
            var $this = $(this),
                title = $('#optin-campaign-title').val(),
                theme = $this.data('om-optin-theme'),
                type  = $this.data('om-optin-type'),
                data  = {
                    action: 'optin_monster_create_optin',
                    theme:  theme,
                    type:   type,
                    title:  title,
                    nonce:  optin_monster_new.create_nonce
                };

            // If no campaign title has been entered, show an error and make them enter a title.
            if ( 0 === title.length ) {
				$('span.om-error').remove();
                $('#optin-campaign-title').addClass('om-input-error').focus().after('<span class="om-error">' + optin_monster_new.campaign + '</span>');
                return;
            }

            $('.om-error').remove();
            $('#optin-campaign-title').removeClass('om-input-error');
            $('.fa-spinner').fadeTo(0, 1);
            $.post(optin_monster_new.ajax, data, function(res){
                window.location.href = res;
            }, 'json');
        });
    });
}(jQuery));