<?php

/**
 * Category archive template.
 *
 * WordPress loads this file for post category archives instead of the
 * default Flatsome index template.
 *
 * @package Phongcachnhat
 */

defined('ABSPATH') || exit;

get_header();
?>

<main id="content" class="blog-wrapper blog-archive page-wrapper !pt-0">
    <!-- <?php if (class_exists('Blog_Category_Archive')) : ?>
        <?php Blog_Category_Archive::render_hero(); ?>
    <?php endif; ?> -->

    <div class="container pb-[15px] pt-7 sm:pt-[43px]">
        <?php do_action('flatsome_before_blog'); ?>

        <?php get_template_part('template-parts/posts/category-post-list'); ?>

        <?php do_action('flatsome_after_blog'); ?>
    </div>
</main>

<?php get_footer(); ?>
