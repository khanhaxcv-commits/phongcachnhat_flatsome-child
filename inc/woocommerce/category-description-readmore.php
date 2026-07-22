<?php

defined('ABSPATH') || exit;

/*
 * Thêm nút Xem thêm vào phần mô tả của danh mục sản phẩm
 */
add_action('wp_footer', 'devvn_readmore_taxonomy_flatsome');

function devvn_readmore_taxonomy_flatsome()
{
    if (is_woocommerce() && is_tax('product_cat')):
?>
        <style>
            .term-description {
                overflow: hidden;
                position: relative;
                margin-bottom: 10px;
                padding-bottom: 20px;
            }

            .devvn_readmore_taxonomy_flatsome {
                text-align: center;
                cursor: pointer;
                position: absolute;
                z-index: 10;
                bottom: -5px;
                width: 100%;
                background: #fff;
            }

            .devvn_readmore_taxonomy_flatsome:before {
                content: '';
                background: linear-gradient(to bottom, rgba(255 255 255/0), rgba(255 255 255/62.5), rgba(255 255 255/1));
                bottom: -30px;
                height: 88px;
                left: 0;
                position: absolute;
                width: 100%;
                margin-bottom: 3px;
            }

            .devvn_readmore_taxonomy_flatsome a {
                font-weight: bold;
                color: #2D348F;
                display: block;
                margin: 0 auto;
                text-align: center;
                position: relative;
            }

            .devvn_readmore_taxonomy_flatsome a::after {
                content: "\f078";
                font-family: "Font Awesome 7 Pro";
                font-weight: 900;
                font-size: 12px;
                color: #2D348F;
                margin-left: 6px;
                display: inline-block;
            }

            .devvn_readmore_taxonomy_flatsome_less:before {
                display: none;
            }

            .devvn_readmore_taxonomy_flatsome_less a:after {
                transform: rotate(180deg);
            }
        </style>

        <script>
            (function($) {
                $(window).on('load', function() {
                    if ($('.term-description').length > 0) {
                        var wrap = $('.term-description');
                        var current_height = wrap.height();
                        var your_height = 300;

                        if (current_height > your_height) {
                            wrap.css('height', your_height + 'px');

                            wrap.append(function() {
                                return '<div class="devvn_readmore_taxonomy_flatsome devvn_readmore_taxonomy_flatsome_show"><a title="Xem thêm" href="javascript:void(0);">Xem thêm</a></div>';
                            });

                            wrap.append(function() {
                                return '<div class="devvn_readmore_taxonomy_flatsome devvn_readmore_taxonomy_flatsome_less" style="display: none"><a title="Thu gọn" href="javascript:void(0);">Thu gọn</a></div>';
                            });

                            $('body').on(
                                'click',
                                '.devvn_readmore_taxonomy_flatsome_show',
                                function() {
                                    wrap.removeAttr('style');
                                    $('body .devvn_readmore_taxonomy_flatsome_show').hide();
                                    $('body .devvn_readmore_taxonomy_flatsome_less').show();
                                }
                            );

                            $('body').on(
                                'click',
                                '.devvn_readmore_taxonomy_flatsome_less',
                                function() {
                                    wrap.css('height', your_height + 'px');
                                    $('body .devvn_readmore_taxonomy_flatsome_show').show();
                                    $('body .devvn_readmore_taxonomy_flatsome_less').hide();
                                }
                            );
                        }
                    }
                });
            })(jQuery);
        </script>
<?php
    endif;
}
