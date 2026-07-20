<?php

defined('ABSPATH') || exit;

add_action('wp_footer', 'devvn_readmore_flatsome');

function devvn_readmore_flatsome()
{
?>
    <style>
        .woocommerce-Tabs-panel--description {
            overflow: hidden;
            position: relative;
            padding-bottom: 25px;
        }

        .fix_height {
            max-height: 800px;
            overflow: hidden;
            position: relative;
        }

        .single-product .tab-panels div#tab-description.panel:not(.active) {
            height: 0 !important;
        }

        .devvn_readmore_flatsome {
            text-align: center;
            cursor: pointer;
            position: absolute;
            z-index: 10;
            bottom: 0;
            width: 100%;
            background: #fff;
        }

        .devvn_readmore_flatsome:before {
            height: 55px;
            margin-top: -45px;
            content: "";
            background: -moz-linear-gradient(top, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 1) 100%);
            background: -webkit-linear-gradient(top, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 1) 100%);
            background: linear-gradient(to bottom, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 1) 100%);
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#ffffff00', endColorstr='#ffffff', GradientType=0);
            display: block;
        }

        .devvn_readmore_flatsome a {
            color: #ff5a14;
            display: block;
        }

        .devvn_readmore_flatsome a:after {
            content: '';
            width: 0;
            right: 0;
            border-top: 6px solid #ff5a14;
            border-left: 6px solid transparent;
            border-right: 6px solid transparent;
            display: inline-block;
            vertical-align: middle;
            margin: -2px 0 0 5px;
        }

        .devvn_readmore_flatsome_less a:after {
            border-top: 0;
            border-left: 6px solid transparent;
            border-right: 6px solid transparent;
            border-bottom: 6px solid #ff5a14;
        }

        .devvn_readmore_flatsome_less:before {
            display: none;
        }
    </style>

    <script>
        (function($) {
            $(document).ready(function() {
                $(window).on('load', function() {
                    if ($('.woocommerce-Tabs-panel--description').length > 0) {
                        let wrap = $('.woocommerce-Tabs-panel--description');
                        let current_height = wrap.height();
                        let your_height = 650;

                        if (current_height > your_height) {
                            wrap.addClass('fix_height');

                            wrap.append(function() {
                                return '<div class="devvn_readmore_flatsome devvn_readmore_flatsome_more"><a title="Xem thêm" href="javascript:void(0);">Xem thêm</a></div>';
                            });

                            wrap.append(function() {
                                return '<div class="devvn_readmore_flatsome devvn_readmore_flatsome_less" style="display: none;"><a title="Xem thêm" href="javascript:void(0);">Thu gọn</a></div>';
                            });

                            $('body').on('click', '.devvn_readmore_flatsome_more', function() {
                                wrap.removeClass('fix_height');
                                $('body .devvn_readmore_flatsome_more').hide();
                                $('body .devvn_readmore_flatsome_less').show();
                            });

                            $('body').on('click', '.devvn_readmore_flatsome_less', function() {
                                wrap.addClass('fix_height');
                                $('body .devvn_readmore_flatsome_less').hide();
                                $('body .devvn_readmore_flatsome_more').show();
                            });
                        }
                    }
                });
            });
        })(jQuery);
    </script>
<?php
}
