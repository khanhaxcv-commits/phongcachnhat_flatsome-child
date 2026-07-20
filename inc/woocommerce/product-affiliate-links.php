<?php

defined('ABSPATH') || exit;

add_action('woocommerce_single_product_summary', 'san', 31);

function san()
{ ?>
    <?php if (get_field('shopee') || get_field('tiki') || get_field('lazada')): ?>
        <div class="theme-aff">
            <ul>

                <li class="text"> MUA SẢN PHẨM TẠI </li>

                <?php if (get_field('shopee')): ?>
                    <li class="shopee">
                        <a href="<?php the_field('shopee'); ?>" target="_Blank" alt="Demo web">
                            <img src="/wp-content/uploads/2023/02/shopee.png" atl="logo shopee" />
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (get_field('lazada')): ?>
                    <li class="2">
                        <a href="<?php the_field('lazada'); ?>" target="_Blank" alt="Demo web">
                            <img src="/wp-content/uploads/2023/02/Lazada_29_icon.png" atl="logo Lazada" />
                        </a>
                    </li>
                <?php endif; ?>

                <?php if (get_field('tiki')): ?>
                    <li class="3">
                        <a href="<?php the_field('tiki'); ?>" target="_Blank" alt="Demo web">
                            <img src="/wp-content/uploads/2023/02/tiki.png" atl="logo shopee" />
                        </a>
                    </li>
                <?php endif; ?>

            </ul>
        </div>
    <?php endif; ?>

    <style>
        .theme-aff ul {
            display: flex;
        }

        li.text {
            width: 80%;
        }

        .theme-aff ul li {
            display: block;
            list-style: none;
            margin-left: 1px;
            align-self: center;
        }

        .theme-aff {
            display: block;
            align-items: center;
            text-align: center;
            font-size: 112%;
        }

        .theme-aff img {
            width: 70px;
            height: auto;
            object-fit: cover;
            border-radius: 99px;
            text-align: -webkit-center;
            align-content: center;
            float: right;
            padding: 3px;
        }

        .theme-aff {
            display: flex;
            align-items: center;
            text-align: center;
            font-size: 112%;
        }

        .theme-aff .shopee img {
            width: 82px;
        }
    </style>
<?php
}
