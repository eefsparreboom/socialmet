/* ==========================================================
 * ckeditor.js
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
CKEDITOR.editorConfig = function( config ) {
    // Modify the toolbar groups.
    config.toolbarGroups = [
        { name: 'basicstyles', groups: [ 'basicstyles', 'list' ] },
        { name: 'paragraph',   groups: [ 'list', 'align' ] },
        { name: 'colors' },
        { name: 'styles' },
        { name: 'links' }
    ];

    // Override core styles for bold, italiv and underlined to allow styling defaults.
    config.coreStyles_bold      = { element : 'span', attributes : { 'style' : 'font-weight:bold' } };
    config.coreStyles_italic    = { element : 'span', attributes : { 'style' : 'font-style:italic' } };
    config.coreStyles_underline = { element : 'span', attributes : { 'style' : 'text-decoration:underline' } };

    // Handle other config properties.
    config.removeButtons         = 'Strike,Subscript,Superscript,Styles';
    config.format_tags           = 'p;h1;h2;h3;pre';
    config.removeDialogTabs      = 'image:advanced;link:advanced';
    config.baseFloatZIndex       = 6351541435;
    config.enterMode             = CKEDITOR.ENTER_BR;
    config.shiftEnterMode        = CKEDITOR.ENTER_BR;
    config.allowedContent        = true;
    config.extraAllowedContent   = 'div(*)';
    config.extraPlugins          = 'pastetext';
    config.forcePasteAsPlainText = true;
    config.basePath              = optin_monster_preview.ckpath;

    // Add custom fonts.
    var fonts          = optin_monster_preview.ckfonts;
    config.font_names  = fonts.split(';').sort().join(';');
    config.contentsCss = optin_monster_preview.google + optin_monster_preview.fonts;
};