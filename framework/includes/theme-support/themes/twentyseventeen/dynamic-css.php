<?php
/*
$background_color
$secondary_background_color
$page_background_color
$main_text_color
$secondary_text_color
$first_main_color
$first_main_active_color
$second_main_color
$second_main_active_color
$main_bg_text_color
$second_main_bg_text_color
$border_color
$secondary_border_color

$border_radius

$body_font_family          
$headings_font_family

$site_title_font_size
$site_desc_font_size
$menu_items_font_size
$h1_font_size
$h2_font_size
$h3_font_size
$h4_font_size
$h5_font_size
$h6_font_size
$body_font_size
$body_line_height
$headings_line_height  

*/

if( $tpl_mode === false ) {

    $link_hover_underline = (bool)$link_hover_underline;


    if ($link_hover_underline === true) {

        $link_underline_value = "inset 0 -1px 0 {$first_main_color}";
        $second_link_underline_value = "inset 0 -1px 0 {$background_color}";
        $link_hover_underline_value = "inset 0 0 0 rgba(0, 0, 0, 0), 0 3px 0 {$first_main_color}";
        $img_hover_underline_value = "0 0 0 8px {$background_color}";

    } else {

        $link_underline_value = "none";
        $second_link_underline_value = "none";
        $link_hover_underline_value = "none";
        $img_hover_underline_value = "none";

    }


    $reset_default_spacing = (bool)$reset_default_spacing;


    if ($reset_default_spacing === true) {

        $page_content_padding_bottom = "0px";
        $rps_page_content_padding_bottom = "0px";
        $home_content_padding_bottom = "0px";
        $rps_home_content_padding_bottom = "0px";
        $home_content_padding_top = "0px";
        $rps_home_content_padding_top = "0px";
        $site_content_padding_top = "0px";
        $rps_site_content_padding_top = "0px";
        $page404_content_padding_bottom = "0px";
        $rps_page404_content_padding_bottom = "0px";
        $site_footer_margin_top = "0px";

    }

}



$css .= <<<CSS
        

            /*--------------------------------------------------------------
            1.0 Normalize
            --------------------------------------------------------------*/

            abbr[title] {
                border-bottom-color: {$first_main_active_color};
            }

            mark {
                background-color: {$secondary_background_color};
                color: {$main_text_color}; 
            }

            fieldset {
                border-color: {$secondary_border_color};
            }

            /*--------------------------------------------------------------
            5.0 Typography
            --------------------------------------------------------------*/
                       
            html[lang] body,
            html[lang] button,
            html[lang] input,
            html[lang] select, 
            html[lang] textarea,
            body,
            button,
            input,
            select,
            textarea {
                font-family: {$body_font_family}, "Helvetica Neue", helvetica, arial, sans-serif;
            }

            html[lang] h1,
            html[lang] h2,
            html[lang] h3,
            html[lang] h4,
            html[lang] h5,
            html[lang] h6,
            h1,
            h2,
            h3,
            h4,
            h5,
            h6 {
                font-family: {$headings_font_family}, "Helvetica Neue", helvetica, arial, sans-serif; 
            }
                       
            body,
            button,
            input,
            select,
            textarea {
                color: {$body_color};
                font-size: {$responsive_body_font_size};
                line-height: {$body_line_height};
            }

            h1,
            h2,
            h3,
            h4,
            h5,
            h6 {
                color: {$headings_color};
                line-height: {$headings_line_height};
            }


            h1 {
                font-size: {$responsive_h1_font_size};
            }

            .prev.page-numbers,
            .next.page-numbers {
                font-size: {$responsive_h1_font_size}; 
            }

            h2 {
                font-size: {$responsive_h2_font_size};
            } 

            .widget_rss li .rsswidget ,
            .page .panel-content .recent-posts .entry-title,
            .format-quote blockquote ,
            .comments-title /*,
            .no-svg .dropdown-toggle .svg-fallback.icon-angle-down*/ {
                font-size: {$responsive_h2_font_size};
            }

            h3 {
                font-size: {$responsive_h3_font_size};
            }

            blockquote {
                font-size: {$responsive_h3_font_size};
            }

            h4 {
                font-size: {$responsive_h4_font_size};
            }

            /*.dropdown-toggle ,*/
            .social-navigation ,
            .comment-author ,
            .widget-grofile h4 ,
            .no-svg .next.page-numbers .screen-reader-text,
            .no-svg .prev.page-numbers .screen-reader-text,
            .no-svg .social-navigation li a .screen-reader-text,
            .no-svg .search-submit .screen-reader-text {
                font-size: {$responsive_h4_font_size};
            }

            h5 {
                font-size: {$responsive_h5_font_size};
            }

            h6 {
                font-size: {$responsive_h6_font_size};
            }

            .entry-content blockquote.alignleft,
            .entry-content blockquote.alignrigh ,
            .taxonomy-description ,
            h2.widget-title ,
            .wp-caption,
            .gallery-caption,
            pre ,
            code,
            kbd,
            tt, 
            var,
            .site-footer,
            .nav-title {
                font-size: {$responsive_body_font_size};
            }

            button,
            input[type="button"],
            input[type="submit"],
            .menu-toggle,
            .twentyseventeen-panel .entry-header .edit-link,
            .page .panel-content .entry-title,
            .page-title,
            body.page:not(.twentyseventeen-front-page) .entry-title ,
            .pagination,
            .comments-pagination,
            .page-links ,
            .entry-footer .edit-link a.post-edit-link ,
            .page .entry-header .edit-link,
            .site-info,
            .comment-body,
            .no-comments,
            .comment-awaiting-moderation {
                font-size: {$responsive_md_font_size};
            }

            .widget .tagcloud a,
            .widget.widget_tag_cloud a,
            .wp_widget_tag_cloud a {
                font-size: {$responsive_md_font_size} !important;
            }

            .twentyseventeen-panel .recent-posts .entry-header .edit-link,
            .entry-meta,
            .entry-footer .cat-links,
            .entry-footer .tags-links,
            .nav-subtitle,
            .search .page .entry-header .edit-link,
            .comment-metadata,
            .widget_rss .rss-date,
            .widget_rss li cite , 
            .site-content .wp-playlist-current-item .wp-playlist-item-artist {
                font-size: {$responsive_sm_font_size}; 
            } 

            blockquote {
                color: {$secondary_text_color};
            }

            pre {
                background: {$secondary_background_color};
            }

            abbr,
            acronym {
                border-bottom-color: {$first_main_active_color};
            }

            mark,
            ins {
                background: {$secondary_background_color};
            }

            /*--------------------------------------------------------------
            19.0 Media Queries
            --------------------------------------------------------------*/

            @media screen and (min-width: 30em) {

                /* Typography */

                body,
                button,
                input,
                select,
                textarea {
                    font-size: {$body_font_size};
                }

                h1 {
                    font-size: {$h1_font_size};
                }

                h2 {
                    font-size: {$h2_font_size};
                } 

                .single-post .entry-title,
                .page .entry-title,
                .widget_rss li .rsswidget ,
                .page .panel-content .recent-posts .entry-title,
                .format-quote blockquote ,
                .comments-title /*,
                .no-svg .dropdown-toggle .svg-fallback.icon-angle-down*/ {
                    font-size: {$h2_font_size};
                }

                h3 {
                    font-size: {$h3_font_size};
                } 

                blockquote {
                    font-size: {$h3_font_size};
                }

                h4 {
                    font-size: {$h4_font_size};
                }

                /*.dropdown-toggle ,*/
                .social-navigation ,
                .comment-author ,
                .widget-grofile h4 ,
                .no-svg .next.page-numbers .screen-reader-text,
                .no-svg .prev.page-numbers .screen-reader-text,
                .no-svg .social-navigation li a .screen-reader-text,
                .no-svg .search-submit .screen-reader-text {
                    font-size: {$h4_font_size};
                }

                h5 {
                    font-size: {$h5_font_size};
                }

                h6 {
                    font-size: {$h6_font_size};
                }

                .entry-content blockquote.alignleft,
                .entry-content blockquote.alignrigh ,
                .taxonomy-description ,
                h2.widget-title ,
                .wp-caption,
                .gallery-caption,
                pre ,
                code,
                kbd,
                tt, 
                var,
                .site-footer,
                .nav-title {
                    font-size: {$body_font_size};
                }

                .page-numbers.current,
                #secondary,
                button, 
                input[type="button"],
                input[type="submit"],
                .menu-toggle,
                .twentyseventeen-panel .entry-header .edit-link,
                .page .panel-content .entry-title,
                .page-title,
                body.page:not(.twentyseventeen-front-page) .entry-title ,
                .pagination,
                .comments-pagination,
                .page-links ,
                .entry-footer .edit-link a.post-edit-link ,
                .page .entry-header .edit-link,
                .site-info,
                .comment-body,
                .no-comments,
                .comment-awaiting-moderation {
                    font-size: {$md_font_size};
                }

                .widget .tagcloud a,
                .widget.widget_tag_cloud a,
                .wp_widget_tag_cloud a { 
                    font-size: {$md_font_size} !important;
                }

                h2.widget-title,
                .twentyseventeen-panel .recent-posts .entry-header .edit-link,
                .entry-meta,
                .entry-footer .cat-links,
                .entry-footer .tags-links,
                .nav-subtitle,
                .search .page .entry-header .edit-link,
                .comment-metadata,
                .widget_rss .rss-date,
                .widget_rss li cite , 
                .site-content .wp-playlist-current-item .wp-playlist-item-artist {
                    font-size: {$sm_font_size}; 
                } 

            }          


            /*--------------------------------------------------------------
            6.0 Forms
            --------------------------------------------------------------*/

            label {
                color: {$main_text_color};
            }

            select,
            input[type="text"],
            input[type="email"],
            input[type="url"],
            input[type="password"],
            input[type="search"],
            input[type="number"],
            input[type="tel"],
            input[type="range"],
            input[type="date"],
            input[type="month"],
            input[type="week"],
            input[type="time"],
            input[type="datetime"],
            input[type="datetime-local"],
            input[type="color"],
            textarea {
                color: {$form_control_color};
                background: {$form_control_bg}; 
                border-color: {$form_control_border};
                -moz-box-shadow:    {$form_control_box_shadow};
                -webkit-box-shadow: {$form_control_box_shadow};
                box-shadow:         {$form_control_box_shadow};
                -webkit-border-radius: {$form_control_border_radius};
                border-radius:         {$form_control_border_radius};  
                padding: {$form_control_padding};
                border-width: {$form_control_border_width};
            }

            select:focus,
            input[type="text"]:focus,
            input[type="email"]:focus,
            input[type="url"]:focus,
            input[type="password"]:focus,
            input[type="search"]:focus,
            input[type="number"]:focus,
            input[type="tel"]:focus,
            input[type="range"]:focus,
            input[type="date"]:focus,
            input[type="month"]:focus,
            input[type="week"]:focus,
            input[type="time"]:focus,
            input[type="datetime"]:focus,
            input[type="datetime-local"]:focus,
            input[type="color"]:focus,
            textarea:focus {
                color: {$form_control_active_color};
                background: {$form_control_active_bg}; 
                border-color: {$form_control_active_border}; 
                -moz-box-shadow:    {$form_control_active_box_shadow};
                -webkit-box-shadow: {$form_control_active_box_shadow};
                box-shadow:         {$form_control_active_box_shadow};
            }

            /*select {
                border-color: {$form_control_border};
                -webkit-border-radius: {$form_control_border_radius};
                border-radius: {$form_control_border_radius};
            }*/

            button,
            input[type="button"],
            input[type="submit"] {
                color: {$button_color};
                background-color: {$button_bg};
                border-color: {$button_border};
                -webkit-border-radius: {$button_border_radius};
                border-radius: {$button_border_radius};
                padding: {$button_padding};
                border-width: {$button_border_width};
                font-weight: {$button_font_weight};
                text-transform: {$button_text_transform};
                border-style: solid;
                -webkit-box-shadow: none;
                box-shadow: none;
            }


            body .module.module-button .btn {
                -webkit-border-radius: {$button_border_radius};
                border-radius: {$button_border_radius};
                padding: {$button_padding};
                border-width: {$button_border_width};
                font-weight: {$button_font_weight};
                text-transform: {$button_text_transform};
            }

            button.secondary,
            input[type="reset"],
            input[type="button"].secondary,
            input[type="reset"].secondary,
            input[type="submit"].secondary {
                color: {$secondary_button_color};
                background-color: {$secondary_button_bg};
                border-color: {$secondary_button_border};
            }

            button:hover,
            button:focus,
            input[type="button"]:hover,
            input[type="button"]:focus,
            input[type="submit"]:hover,
            input[type="submit"]:focus {
                color: {$button_active_color};
                background: {$button_active_bg};
                border-color: {$button_active_border};
            }

            button.secondary:hover,
            button.secondary:focus,
            input[type="reset"]:hover,
            input[type="reset"]:focus,
            input[type="button"].secondary:hover,
            input[type="button"].secondary:focus,
            input[type="reset"].secondary:hover,
            input[type="reset"].secondary:focus,
            input[type="submit"].secondary:hover,
            input[type="submit"].secondary:focus {
                color: {$secondary_button_active_color};
                background: {$secondary_button_active_bg};
                border-color: {$secondary_button_active_border};
            }

            /* Placeholder text color -- selectors need to be separate to work. */
            ::-webkit-input-placeholder {
                color: {$placeholder_color};
                font-family: {$body_font_family} , "Helvetica Neue", helvetica, arial, sans-serif;
            }

            :-moz-placeholder {
                color: {$placeholder_color};
                font-family: {$body_font_family} , "Helvetica Neue", helvetica, arial, sans-serif;
            }

            ::-moz-placeholder {
                color: {$placeholder_color};
                font-family: {$body_font_family} , "Helvetica Neue", helvetica, arial, sans-serif;
                /* Since FF19 lowers the opacity of the placeholder by default */
            }

            :-ms-input-placeholder {
                color: {$placeholder_color};
                font-family: {$body_font_family} , "Helvetica Neue", helvetica, arial, sans-serif;
            }


            /* Placeholder text color -- selectors need to be separate to work. */
            ::-webkit-input-placeholder:focus {
                color: {$active_placeholder_color};
            }

            :-moz-placeholder:focus {
                color: {$active_placeholder_color};
            }

            ::-moz-placeholder:focus {
                color: {$active_placeholder_color};
                /* Since FF19 lowers the opacity of the placeholder by default */
            }

            :-ms-input-placeholder:focus {
                color: {$active_placeholder_color};
            }

            /*--------------------------------------------------------------
            7.0 Formatting
            --------------------------------------------------------------*/

            hr {
                background-color: {$secondary_border_color};
            }

            /*--------------------------------------------------------------
            9.0 Tables
            --------------------------------------------------------------*/

            thead th {
                border-bottom-color: {$secondary_border_color};
            }

            tr {
                border-bottom-color: {$border_color};
            }

            /*--------------------------------------------------------------
            10.0 Links
            --------------------------------------------------------------*/

            a {
                color: {$main_text_color}; 
            }

            a:hover,
            a:active {
                color: {$first_main_color};
            }

            /* Hover effects */

            .entry-content a,
            .entry-summary a,
            .widget a,
            .site-footer .widget-area a,
            .posts-navigation a,
            .widget_authors a strong {
                -webkit-box-shadow: {$link_underline_value};
                box-shadow: {$link_underline_value};
            }

            .entry-title a,
            .entry-meta a,
            .page-links a,
            .page-links a .page-number,
            .entry-footer a,
            .entry-footer .cat-links a,
            .entry-footer .tags-links a,
            .edit-link a,
            .post-navigation a,
            .logged-in-as a,
            .comment-navigation a,
            .comment-metadata a,
            .comment-metadata a.comment-edit-link,
            .comment-reply-link,
            a .nav-title,
            .pagination a,
            .comments-pagination a,
            .site-info a,
            .widget .widget-title a,
            .widget ul li a,
            .site-footer .widget-area ul li a,
            .site-footer .widget-area ul li a {
                -webkit-box-shadow: {$second_link_underline_value};
                box-shadow: {$second_link_underline_value}; 
            }

            .entry-content a:focus,
            .entry-content a:hover,
            .entry-summary a:focus,
            .entry-summary a:hover,
            .widget a:focus,
            .widget a:hover,
            .site-footer .widget-area a:focus,
            .site-footer .widget-area a:hover,
            .posts-navigation a:focus,
            .posts-navigation a:hover,
            .comment-metadata a:focus,
            .comment-metadata a:hover,
            .comment-metadata a.comment-edit-link:focus,
            .comment-metadata a.comment-edit-link:hover,
            .comment-reply-link:focus,
            .comment-reply-link:hover,
            .widget_authors a:focus strong,
            .widget_authors a:hover strong,
            .entry-title a:focus,
            .entry-title a:hover,
            .entry-meta a:focus,
            .entry-meta a:hover,
            .page-links a:focus .page-number,
            .page-links a:hover .page-number,
            .entry-footer a:focus,
            .entry-footer a:hover,
            .entry-footer .cat-links a:focus,
            .entry-footer .cat-links a:hover,
            .entry-footer .tags-links a:focus,
            .entry-footer .tags-links a:hover,
            .post-navigation a:focus,
            .post-navigation a:hover,
            .pagination a:not(.prev):not(.next):focus,
            .pagination a:not(.prev):not(.next):hover,
            .comments-pagination a:not(.prev):not(.next):focus,
            .comments-pagination a:not(.prev):not(.next):hover,
            .logged-in-as a:focus,
            .logged-in-as a:hover,
            a:focus .nav-title,
            a:hover .nav-title,
            .edit-link a:focus,
            .edit-link a:hover,
            .site-info a:focus,
            .site-info a:hover,
            .widget .widget-title a:focus,
            .widget .widget-title a:hover,
            .widget ul li a:focus,
            .widget ul li a:hover {
                color: {$first_main_color};
                -webkit-box-shadow: {$link_hover_underline_value};
                box-shadow: {$link_hover_underline_value};
            }

            /* Fixes linked images */
            .entry-content a img,
            .widget a img {
                -webkit-box-shadow: {$img_hover_underline_value};
                box-shadow: {$img_hover_underline_value};
            }

            .post-navigation a:focus .icon,
            .post-navigation a:hover .icon {
                color: {$first_main_color};
            } 


            /*--------------------------------------------------------------
            12.0 Navigation
            --------------------------------------------------------------*/

            .navigation-top {
                background: {$navigation_bar_bg};
                border-bottom-color: {$navigation_bar_border};
                border-top-color: {$navigation_bar_border};
                font-size: initial; 
            }

            .main-navigation {
                /*margin-left: -0.75em; 
                margin-right: -0.75em; */ 
                font-size: {$menu_items_font_size}; 
            }

            .navigation-top a {
                color: {$navigation_bar_color}; 
                font-size: {$menu_items_font_size}; 
            }

            .navigation-top .current-menu-item > a,
            .navigation-top .current_page_item > a {
                color: {$navigation_submenu_item_bg};
            }

            .main-navigation ul {
                background: {$navigation_bar_bg};
            }

            /* Hide the menu on small screens when JavaScript is available.
             * It only works with JavaScript.
             */

            .main-navigation > div > ul {
                border-top-color: {$navigation_bar_border};
            }

            .main-navigation li {
                border-bottom-color: {$navigation_bar_border};
            }

            .main-navigation a:hover {
                color: {$navigation_submenu_item_bg};
            }

            /* Menu toggle */

            .menu-toggle {
                color: {$navigation_bar_color};
                -webkit-box-shadow: none;
                box-shadow: none;
            }

            /* Display the menu toggle when JavaScript is available. */

            .menu-toggle:hover,
            .menu-toggle:focus {
                background-color: transparent;
                -webkit-box-shadow: none;
                box-shadow: none;
            }

            /* Dropdown Toggle */

            .dropdown-toggle {
                color: {$navigation_bar_color};
                -webkit-box-shadow: none;
                box-shadow: none;
            }


            @media screen and ( min-width: 67em ) {

                .navigation-top nav {
                    margin-left: -1.25em;   
                }
            }    

            @media screen and (min-width: 48em) {

                /* Main Navigation */

                .main-navigation ul ul {
                    background: {$navigation_submenu_bg};
                    border-color: {$navigation_submenu_border};
                }

                .navigation-top ul ul a { 
                    color: {$navigation_submenu_color};
                }

                .main-navigation ul li.menu-item-has-children:before,
                .main-navigation ul li.page_item_has_children:before {
                    border-color: transparent transparent {$navigation_submenu_border};
                }

                .main-navigation ul li.menu-item-has-children:after,
                .main-navigation ul li.page_item_has_children:after {
                    border-color: transparent transparent {$navigation_submenu_bg};
                }

                .main-navigation li li:hover,
                .main-navigation li li.focus {
                    background: {$navigation_submenu_item_bg};
                }

                .main-navigation li li.focus > a,
                .main-navigation li li:focus > a,
                .main-navigation li li:hover > a,
                .main-navigation li li a:hover,
                .main-navigation li li a:focus,
                .main-navigation li li.current_page_item a:hover,
                .main-navigation li li.current-menu-item a:hover,
                .main-navigation li li.current_page_item a:focus,
                .main-navigation li li.current-menu-item a:focus {
                    color: {$navigation_submenu_item_color};
                }

                /* Scroll down arrow */

                .site-header .menu-scroll-down {
                    color: {$header_title_color};
                }

                .site-header .navigation-top .menu-scroll-down {
                    color: {$navigation_submenu_item_bg};
                }

            }


            /*--------------------------------------------------------------
            13.0 Layout
            --------------------------------------------------------------*/


            body {
                background: {$background_color};
                /* Fallback for when there is no custom background color defined. */
            }


            /*--------------------------------------------------------------
            13.1 Header
            --------------------------------------------------------------*/

            .site-header {
                background-color: {$header_bg};
            }

            /* Site branding */

            .site-title,
            .site-title a {
                color: {$header_title_color}; 
                font-size: {$responsive_site_title_font_size};
            }

            body.has-header-image .site-title,
            body.has-header-video .site-title,
            body.has-header-image .site-title a,
            body.has-header-video .site-title a {
                color: {$header_title_color};
            }

            .site-description {
                color: {$header_description_color};
                font-size: {$responsive_site_desc_font_size};
            }

            body.has-header-image .site-description,
            body.has-header-video .site-description {
                color: {$header_description_color}; 
            }
            /*==================================*/
            .custom-header-media:before { 
                background-color: {$overlay_background}; 
            }
            
            @media screen and (min-width: 48em){
                .twentyseventeen-front-page.has-header-image .custom-header-media:before, 
                .twentyseventeen-front-page.has-header-video .custom-header-media:before, 
                .home.blog.has-header-image .custom-header-media:before, 
                .home.blog.has-header-video .custom-header-media:before {
                    height: {$overlay_height};
                } 
            }


            /*
            .wp-custom-header .wp-custom-header-video-button { /* Specificity prevents .color-dark button overrides *
                background-color: rgba(34, 34, 34, 0.5);
                border: 1px solid rgba(255, 255, 255, 0.6);
                color: rgba(255, 255, 255, 0.6);
            }

            .wp-custom-header .wp-custom-header-video-button:hover,
            .wp-custom-header .wp-custom-header-video-button:focus { /* Specificity prevents .color-dark button overrides *
                border-color: rgba(255, 255, 255, 0.8);
                background-color: rgba(34, 34, 34, 0.8);
                color: #fff;
            }
            */


            @media screen and (min-width: 48em) {

                /* Site Branding */

                .site-title,
                .site-title a {
                    font-size: {$site_title_font_size};
                }

                .site-description {
                    font-size: {$site_desc_font_size};
                } 

            }

            /*--------------------------------------------------------------
            13.2 Front Page
            --------------------------------------------------------------*/

            .panel-image:before {
                /* Permalink - use to edit and share this gradient: http://colorzilla.com/gradient-editor/#000000+0,000000+100&0+0,0.3+100 */ /* FF3.6-15 *
                background: -webkit-linear-gradient(to top, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0.3) 100%); /* Chrome10-25,Safari5.1-6 *
                background: -webkit-gradient(linear, left top, left bottom, from(rgba(0, 0, 0, 0)), to(rgba(0, 0, 0, 0.3)));
                background: -webkit-linear-gradient(to top, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0.3) 100%);
                background: linear-gradient(to bottom, rgba(0, 0, 0, 0) 0%, rgba(0, 0, 0, 0.3) 100%); /* W3C, IE10+, FF16+, Chrome26+, Opera12+, Safari7+ *
                filter: progid:DXImageTransform.Microsoft.gradient(startColorstr="#00000000", endColorstr="#4d000000", GradientType=0); /* IE6-9 */
            }

            .twentyseventeen-front-page article:not(.has-post-thumbnail):not(:first-child) {
                border-top-color: {$secondary_border_color};
            }

            /* Front Page - Recent Posts */

            .twentyseventeen-front-page .panel-content .recent-posts article {
                color: {$main_text_color};
            }

            .twentyseventeen-panel .recent-posts .entry-header .edit-link {
                color: {$main_text_color};
            }

            /*--------------------------------------------------------------
            13.3 Regular Content
            --------------------------------------------------------------*/

            .site-content-contain {
                background-color: {$background_color};
            }

            /*--------------------------------------------------------------
            13.4 Posts
            --------------------------------------------------------------*/

            /* Post Landing Page */

            .page .panel-content .entry-title,
            .page-title,
            body.page:not(.twentyseventeen-front-page) .entry-title {
                color: {$main_text_color};
            }

            .entry-title a {
                color: {$main_text_color};
            }

            .entry-meta {
                color: {$secondary_text_color};
            }

            .entry-meta a {
                color: {$secondary_text_color};
            }

            .pagination,
            .comments-pagination {
                border-top-color: {$border_color};
            }

            .page-numbers.current {
                color: {$first_main_active_color};
            }

            .prev.page-numbers,
            .next.page-numbers {
                background-color: {$secondary_background_color};
                -webkit-border-radius: {$border_radius};
                border-radius: {$border_radius};
            }

            .prev.page-numbers:focus,
            .prev.page-numbers:hover,
            .next.page-numbers:focus,
            .next.page-numbers:hover {
                background-color: {$first_main_active_color};
                color: {$main_bg_text_color}; 
            }


            /* Aligned blockquotes */

            .entry-content blockquote.alignleft,
            .entry-content blockquote.alignright {
                color: {$secondary_text_color};
            }

            /* Blog landing, search, archives */

            .blog .entry-meta a.post-edit-link,
            .archive .entry-meta a.post-edit-link,
            .search .entry-meta a.post-edit-link {
                color: {$main_text_color};
            }

            .taxonomy-description {
                color: {$secondary_text_color};
            }

            /* Single Post */

            .single-featured-image-header {
                background-color: {$secondary_background_color};
                border-bottom-color: {$border_color}; 
            }

            .page-links .page-number {
                color: {$first_main_active_color};
            }

            .page-links a .page-number {
                color: {$main_text_color};
            }

            /* Entry footer */ 

            .entry-footer {
                border-bottom-color: {$border_color};
                border-top-color: {$border_color};
                border-collapse: initial;
            }

            .entry-footer .cat-links a,
            .entry-footer .tags-links a {
                color: {$main_text_color};
            }

            .entry-footer .cat-links .icon,
            .entry-footer .tags-links .icon {
                color: {$first_main_color};
            }

            .entry-footer .edit-link a.post-edit-link {
                background-color: {$button_bg};
                -webkit-border-radius: {$form_control_border_radius};
                border-radius: {$form_control_border_radius};
                color: {$button_color};
                -webkit-box-shadow: none;
                box-shadow: none;
            }
 
            .entry-footer .edit-link a.post-edit-link:hover,
            .entry-footer .edit-link a.post-edit-link:focus {
                background: {$button_active_bg};
            }

            /* Post Formats */

            .format-quote blockquote {
                color: {$main_text_color};
            }

            /* Post Navigation */

            .nav-subtitle {
                background: transparent;
                color: {$first_main_active_color};
            }

            .nav-title {
                color: {$main_text_color};
            }

            /*--------------------------------------------------------------
            13.6 Footer
            --------------------------------------------------------------*/

            .site-footer {
                border-top-color: {$footer_border};
                background-color: {$footer_background};
            }

            /* Social nav */

            .social-navigation a {
                background-color: {$social_bg};
                color: {$social_color};
            }

            .social-navigation a:hover,
            .social-navigation a:focus {
                background-color: {$social_active_bg};
            }
            /* Site info */

            .site-info a {
                color: {$site_info_color}; 
            }

            /*--------------------------------------------------------------
            14.0 Comments
            --------------------------------------------------------------*/

            .comment-metadata {
                color: {$secondary_text_color};
            }

            .comment-metadata a {
                color: {$secondary_text_color};
            }

            .comment-metadata a.comment-edit-link {
                color: {$main_text_color};
            }

            .comment-body {
                color: {$main_text_color};
            }

            .comment-reply-link .icon {
                color: {$main_text_color};
            }

            .bypostauthor > .comment-body > .comment-meta > .comment-author .avatar {
                border-color: {$border_color};
            }

            .no-comments,
            .comment-awaiting-moderation {
                color: {$secondary_text_color};
            }


            /*--------------------------------------------------------------
            15.0 Widgets
            --------------------------------------------------------------*/

            .widget ul li a,
            .widget a,
            .widget {
                color: {$widget_color};
            }

            h2.widget-title {
                color: {$widget_title_color};
                font-weight: {$widget_title_font_weight};
                text-transform: {$widget_title_text_transform};    
            }

            @media screen and (min-width: 48em) {
                h2.widget-title {
                    font-size: {$widget_title_font_size};
                }
            }

            /* widget lists */

            .widget ul li {
                border-bottom: {$widget_border_width} {$widget_border_style} {$widget_border_color};
                border-top: {$widget_border_width} {$widget_border_style} {$widget_border_color};
                padding: {$widget_lists_padding};
            }

            .widget ul li + li {
                margin-top: -{$widget_border_width};
            }

            .widget_rss .rss-date,
            .widget_rss li cite {
                color: {$secondary_widget_color};
            }

            /* Tag cloud widget */

            .widget .tagcloud a,
            .widget.widget_tag_cloud a,
            .wp_widget_tag_cloud a {
                border: {$widget_border_width} solid {$widget_border_color};
                -webkit-border-radius: {$widget_border_radius};
                border-radius:         {$widget_border_radius}; 
            }

            .widget .tagcloud a:hover,
            .widget .tagcloud a:focus,
            .widget.widget_tag_cloud a:hover,
            .widget.widget_tag_cloud a:focus,
            .wp_widget_tag_cloud a:hover,
            .wp_widget_tag_cloud a:focus {
                border-color: {$secondary_border_color}; 
            }
 
            /* Calendar widget */

            .widget_calendar thead th, 
            .widget_calendar tr {
                border-color: {$widget_border_color}; 
            }



            /*--------------------------------------------------------------
            15.0 Widgets for Background Dark
            --------------------------------------------------------------*/

            .sed-dark-bg-widget .widget ul li a,
            .sed-dark-bg-widget .widget a,
            .sed-dark-bg-widget .widget {
                color: {$dark_bg_widget_color};
            }

            .sed-dark-bg-widget .widget ul li a:hover,
            .sed-dark-bg-widget .widget a:hover {
                color: {$first_main_color};
            }

            .sed-dark-bg-widget h2.widget-title {
                color: {$dark_bg_widget_title_color};   
            }

            /* widget lists */

            .sed-dark-bg-widget .widget ul li {
                border-bottom-color: {$dark_bg_widget_border_color};
                border-top-colo: {$dark_bg_widget_border_color};
            }

            /* RSS Widget */

            .sed-dark-bg-widget .widget_rss .rss-date,
            .sed-dark-bg-widget .widget_rss li cite {
                color: {$dark_bg_secondary_widget_color};
            }

            /* Tag cloud widget */

            .sed-dark-bg-widget .widget .tagcloud a,
            .sed-dark-bg-widget .widget.widget_tag_cloud a,
            .sed-dark-bg-widget .wp_widget_tag_cloud a {
                border-color: {$dark_bg_widget_border_color};
            }

            .sed-dark-bg-widget .widget .tagcloud a:hover,
            .sed-dark-bg-widget .widget .tagcloud a:focus,
            .sed-dark-bg-widget .widget.widget_tag_cloud a:hover,
            .sed-dark-bg-widget .widget.widget_tag_cloud a:focus,
            .sed-dark-bg-widget .wp_widget_tag_cloud a:hover,
            .sed-dark-bg-widget .wp_widget_tag_cloud a:focus {
                border-color: {$dark_bg_widget_border_color}; 
            }

            /* Calendar widget */

            .sed-dark-bg-widget .widget_calendar thead th, 
            .sed-dark-bg-widget .widget_calendar tr {
                border-color: {$dark_bg_widget_border_color}; 
            }

            /* Forms */

            .sed-dark-bg-widget select,
            .sed-dark-bg-widget input[type="text"],
            .sed-dark-bg-widget input[type="email"],
            .sed-dark-bg-widget input[type="url"],
            .sed-dark-bg-widget input[type="password"],
            .sed-dark-bg-widget input[type="search"],
            .sed-dark-bg-widget input[type="number"],
            .sed-dark-bg-widget input[type="tel"],
            .sed-dark-bg-widget input[type="range"],
            .sed-dark-bg-widget input[type="date"],
            .sed-dark-bg-widget input[type="month"],
            .sed-dark-bg-widget input[type="week"],
            .sed-dark-bg-widget input[type="time"],
            .sed-dark-bg-widget input[type="datetime"],
            .sed-dark-bg-widget input[type="datetime-local"],
            .sed-dark-bg-widget input[type="color"],
            .sed-dark-bg-widget textarea {
                color: {$dark_bg_form_control_color};
                background: {$dark_bg_form_control_bg}; 
                border-color: {$dark_bg_form_control_border};
            }

            .sed-dark-bg-widget select:focus,
            .sed-dark-bg-widget input[type="text"]:focus,
            .sed-dark-bg-widget input[type="email"]:focus,
            .sed-dark-bg-widget input[type="url"]:focus,
            .sed-dark-bg-widget input[type="password"]:focus,
            .sed-dark-bg-widget input[type="search"]:focus,
            .sed-dark-bg-widget input[type="number"]:focus,
            .sed-dark-bg-widget input[type="tel"]:focus,
            .sed-dark-bg-widget input[type="range"]:focus,
            .sed-dark-bg-widget input[type="date"]:focus,
            .sed-dark-bg-widget input[type="month"]:focus,
            .sed-dark-bg-widget input[type="week"]:focus,
            .sed-dark-bg-widget input[type="time"]:focus,
            .sed-dark-bg-widget input[type="datetime"]:focus,
            .sed-dark-bg-widget input[type="datetime-local"]:focus,
            .sed-dark-bg-widget input[type="color"]:focus,
            .sed-dark-bg-widget textarea:focus {
                color: {$dark_bg_form_control_color};
                background: {$dark_bg_form_control_active_bg}; 
                border-color: {$dark_bg_form_control_active_border}; 
            }

            /* Placeholder text color -- selectors need to be separate to work. */
            .sed-dark-bg-widget ::-webkit-input-placeholder {
                color: {$dark_bg_placeholder_color};
            }

            .sed-dark-bg-widget :-moz-placeholder {
                color: {$dark_bg_placeholder_color};
            }

            .sed-dark-bg-widget ::-moz-placeholder {
                color: {$dark_bg_placeholder_color};
                /* Since FF19 lowers the opacity of the placeholder by default */
            }

            .sed-dark-bg-widget :-ms-input-placeholder {
                color: {$dark_bg_placeholder_color};
            }


            /* Placeholder text color -- selectors need to be separate to work. */
            .sed-dark-bg-widget ::-webkit-input-placeholder:focus {
                color: {$dark_bg_placeholder_color};
            }

            .sed-dark-bg-widget :-moz-placeholder:focus {
                color: {$dark_bg_placeholder_color};
            }

            .sed-dark-bg-widget ::-moz-placeholder:focus {
                color: {$dark_bg_placeholder_color};
                /* Since FF19 lowers the opacity of the placeholder by default */
            }

            .sed-dark-bg-widget :-ms-input-placeholder:focus {
                color: {$dark_bg_placeholder_color};
            }


            /*--------------------------------------------------------------
            16.0 Media
            --------------------------------------------------------------*/


            /* Make sure embeds and iframes fit their containers. */

            .wp-caption,
            .gallery-caption {
                color: {$secondary_text_color};
            }

            /* Playlist Color Overrides: Light *

            .site-content .wp-playlist-light {
                border-color: {$border_color};
                color: {$main_text_color};
            }

            .site-content .wp-playlist-light .wp-playlist-current-item .wp-playlist-item-album {
                color: {$main_text_color};
            }

            .site-content .wp-playlist-light .wp-playlist-current-item .wp-playlist-item-artist {
                color: {$secondary_text_color};
            }

            .site-content .wp-playlist-light .wp-playlist-item {
                border-bottom-color: {$border_color}; 
            }

            */

            .site-content .wp-playlist-light .wp-playlist-item:hover,
            .site-content .wp-playlist-light .wp-playlist-item:focus {
                border-bottom-color: rgba(0, 0, 0, 0);
                background-color: {$playlist_item_active_bg};
                color: {$playlist_item_active_color};
            }

            .site-content .wp-playlist-light a.wp-playlist-caption:hover,
            .site-content .wp-playlist-light .wp-playlist-item:hover a,
            .site-content .wp-playlist-light .wp-playlist-item:focus a {
                color: {$playlist_item_active_color};
            }

            /* Playlist Color Overrides: Dark *

            .site-content .wp-playlist-dark {
                background: #222;
                border-color: #333;
            }

            .site-content .wp-playlist-dark .mejs-container .mejs-controls {
                background-color: #333;
            }

            .site-content .wp-playlist-dark .wp-playlist-caption {
                color: #fff;
            }

            .site-content .wp-playlist-dark .wp-playlist-current-item .wp-playlist-item-album {
                color: #eee;
            }

            .site-content .wp-playlist-dark .wp-playlist-current-item .wp-playlist-item-artist {
                color: #aaa;
            }

            .site-content .wp-playlist-dark .wp-playlist-playing {
                background-color: #333;
            }

            .site-content .wp-playlist-dark .wp-playlist-item {
                border-bottom: 1px dotted #555;
            }
            */

            .site-content .wp-playlist-dark .wp-playlist-item:hover,
            .site-content .wp-playlist-dark .wp-playlist-item:focus {
                border-bottom-color: rgba(0, 0, 0, 0);
                background-color: {$playlist_item_active_bg};
                color: {$playlist_item_active_color};
            }

            .site-content .wp-playlist-dark a.wp-playlist-caption:hover,
            .site-content .wp-playlist-dark .wp-playlist-item:hover a,
            .site-content .wp-playlist-dark .wp-playlist-item:focus a {
                color: {$playlist_item_active_color};;
            }

            .site-content .wp-playlist-item a,
            .site-content .wp-playlist-item a:focus,
            .site-content .wp-playlist-item a:hover {
                -webkit-box-shadow: none;
                box-shadow: none;
            }     


            /*--------------------------------------------------------------
            16.1 Galleries
            --------------------------------------------------------------*/

            .gallery-item a,
            .gallery-item a:hover,
            .gallery-item a:focus {
                -webkit-box-shadow: none;
                box-shadow: none;
            }
                          

            /*--------------------------------------------------------------
            18.0 SVGs Fallbacks
            --------------------------------------------------------------*/

            /* Social Menu fallbacks */

            .no-svg .social-navigation a {
                background: transparent;
                color: {$main_text_color};
            }


            /*--------------------------------------------------------------
            21.0 Layout
            --------------------------------------------------------------*/



            .page:not(.home) #content {
                padding-bottom: {$rps_page_content_padding_bottom};
            }         

            /* Front Page */

            .panel-content .wrap {
                padding-bottom: {$rps_home_content_padding_bottom};
                padding-top: {$rps_home_content_padding_top};
            }

            /* Posts */

            .site-content {
                padding: {$rps_site_content_padding_top} 0 0;
            }

            /* 404 page */

            .error404 .page-content {
                padding-bottom: {$rps_page404_content_padding_bottom};
            } 

            /* Footer */

            .site-footer {
                margin-top: 0em;
            }



            /* Layout */


            @media screen and (min-width: 0) and (min-width: 30em) and (min-width: 48em) {

                .wrap,
                .sed-row-boxed ,
                .navigation-top .wrap ,
                .page-one-column .panel-content .wrap ,
                .single-post:not(.has-sidebar) #primary,
                .page.page-one-column:not(.twentyseventeen-front-page) #primary,
                .archive.page-one-column:not(.has-sidebar) .page-header,
                .archive.page-one-column:not(.has-sidebar) #primary {
                    max-width: {$sheet_width}; 
                }

            }

            @media screen and (min-width: 0) and (min-width: 30em) and (min-width: 48em) {

                .sed-row-boxed .sed-row-boxed,
                .wrap-layout-fixed-width .wrap .sed-row-boxed , 
                .sed-main-content-layout-module .wrap-layout-full-width .wrap,
                .page-one-column .sed-main-content-layout-module .wrap-layout-full-width .panel-content .wrap ,
                .single-post:not(.has-sidebar) .sed-main-content-layout-module .wrap-layout-full-width #primary,
                .page.page-one-column:not(.twentyseventeen-front-page) .sed-main-content-layout-module .wrap-layout-full-width #primary,
                .archive.page-one-column:not(.has-sidebar) .sed-main-content-layout-module .wrap-layout-full-width .page-header,
                .archive.page-one-column:not(.has-sidebar) .sed-main-content-layout-module .wrap-layout-full-width #primary,
                .sed-row-boxed .wrap,
                .sed-row-boxed .navigation-top .wrap ,
                .page-one-column .sed-row-boxed .panel-content .wrap ,
                .single-post:not(.has-sidebar) .sed-row-boxed #primary,
                .page.page-one-column:not(.twentyseventeen-front-page) .sed-row-boxed #primary,
                .archive.page-one-column:not(.has-sidebar) .sed-row-boxed .page-header,
                .archive.page-one-column:not(.has-sidebar) .sed-row-boxed #primary {
                    max-width: none !important; 
                }

            }            

            .sed-row-boxed ,   
            .wrap {
                padding-left: {$rps_wrap_padding_left_right};
                padding-right: {$rps_wrap_padding_left_right};  
            }
   
            .sed-row-boxed .wrap , 
            .sed-row-boxed .sed-row-boxed ,
            .wrap-layout-fixed-width .wrap .sed-row-boxed ,
            .sed-main-content-layout-module .wrap-layout-full-width .wrap {
                padding-right: 0px;
                padding-left: 0px;  
            }

            @media screen and (min-width: 48em) {

                .sed-row-boxed ,
                .wrap {
                    padding-left: {$wrap_padding_left_right};
                    padding-right: {$wrap_padding_left_right};
                }

                .page:not(.home) #content {
                    padding-bottom: {$page_content_padding_bottom};
                }    

                /* Front Page */

                .panel-content .wrap {
                    padding-bottom: {$home_content_padding_bottom};
                    padding-top: {$home_content_padding_top};
                }

                /* Posts */

                .site-content {
                    padding: {$site_content_padding_top} 0 0;
                }

                /* 404 page */

                .error404 .page-content {
                    padding-bottom: {$page404_content_padding_bottom};
                } 

                /* Footer */

                .site-footer {
                    margin-top: {$site_footer_margin_top};
                }

            }   


            /* error 404 */ /* search results */ /* blog */


            body.sed-no-sidebar .wrap > header.page-header ,
            body.sed-no-sidebar .wrap > #primary ,
            body.sed-no-sidebar.blog #primary article, 
            body.sed-no-sidebar.archive:not(.page-one-column) #primary article, 
            body.sed-no-sidebar.search #primary article,            
            body.sed-no-sidebar .site-main > .post.type-post, 
            body.search.sed-no-sidebar .wrap > header.page-header ,  
            body.search.sed-no-sidebar .wrap > #primary ,
            body.sed-no-sidebar.error404 #primary .sed-404-one-column > .page-header ,
            body.sed-no-sidebar.error404 #primary .sed-404-one-column > .page-content , 
            body.error404:not(.has-sidebar) #primary .sed-404-one-column > .page-header , 
            body.has-sidebar.error404 #primary .sed-404-one-column > .page-header ,
            .error404:not(.has-sidebar) #primary .sed-404-one-column > .page-content ,
            .error404.has-sidebar #primary .sed-404-one-column > .page-content {
                float: none;
                width: 100%; 
            }

            body.search.sed-no-sidebar .wrap > #primary form.search-form,
            .error404:not(.has-sidebar) #primary .sed-404-one-column > .page-content form.search-form,
            .error404.has-sidebar #primary .sed-404-one-column > .page-content form.search-form {
                width:400px;
            }



CSS;



            /*.page.type-page > .panel-content > .wrap > .entry-header ,
            .page.type-page > .entry-header {
                display: pages_and_front_page_title;
            }*/