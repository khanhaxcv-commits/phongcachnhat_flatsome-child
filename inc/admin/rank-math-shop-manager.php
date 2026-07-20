<?php

/**
 * Phân quyền Rank Math cho shop_manager.
 */

defined('ABSPATH') || exit;

function add_rankmath_caps_to_shop_manager()
{
    $role = get_role('shop_manager'); // role Quản lý cửa hàng (WooCommerce)

    if ($role) {

        // Cho phép chỉnh SEO trên bài viết & sản phẩm
        $role->add_cap('edit_posts');
        $role->add_cap('edit_products');

        // Quan trọng: quyền dùng metabox Rank Math
        $role->add_cap('rank_math_onpage_general');
        $role->add_cap('rank_math_onpage_advanced');
        $role->add_cap('rank_math_onpage_snippet');
        $role->add_cap('rank_math_onpage_social');

        $role->add_cap('rank_math_onpage');
        $role->add_cap('rank_math_onpage_general');
        $role->add_cap('rank_math_onpage_analysis'); // QUAN TRỌNG cho chấm điểm
        $role->add_cap('rank_math_onpage_advanced');

        // Các quyền cơ bản của Rank Math
        $role->add_cap('rank_math_general');
        $role->add_cap('rank_math_titles');
        $role->add_cap('rank_math_sitemap');
        $role->add_cap('rank_math_404_monitor');
        $role->add_cap('rank_math_redirections');
        $role->add_cap('rank_math_role_manager');
        $role->add_cap('rank_math_analytics');
    }
}

add_action('init', 'add_rankmath_caps_to_shop_manager');
