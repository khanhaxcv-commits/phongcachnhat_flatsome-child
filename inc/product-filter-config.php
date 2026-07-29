<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Thứ tự ưu tiên của bộ lọc
    |--------------------------------------------------------------------------
    |
    | Taxonomy đứng trước sẽ được hiển thị trước. Các taxonomy không có trong
    | mảng này vẫn giữ nguyên thứ tự tự động hoặc thứ tự trong override.
    |
    */

    'priority' => [
        'pa_thuong-hieu',
        'pa_chung-loai-san-pham',
        'pa_chieu-cao-toi-da',
        'pa_mau-sac',
        'pa_kich-thuoc',
        'pa_vat-lieu-canh-tu',
        'pa_dung-tich',
        'pa_trong-luong-giat',
        'pa_cong-suat-hut-am',
        'pa_kieu-dang',
    ],


    /*
    |--------------------------------------------------------------------------
    | Tên hiển thị tùy chỉnh của bộ lọc
    |--------------------------------------------------------------------------
    |
    | Dùng khi muốn đổi tên ngoài giao diện mà không thay đổi tên attribute
    | trong WooCommerce. Có thể thêm các cặp taxonomy => tên hiển thị tại đây.
    |
    */

    'labels' => [
        'pa_chung-loai-san-pham' => 'Chủng loại sản phẩm',
    ],

    /*
    |--------------------------------------------------------------------------
    | Attribute không muốn hiển thị trong filter
    |--------------------------------------------------------------------------
    */

    'exclude' => [

        /*
         * Ví dụ:
         * 'pa_dien-ap',
         * 'pa_kich-thuoc',
         */
        // 'pa_chieu-cao-toi-da',
        'pa_chuc-nang-chinh',
        // 'pa_chung-loai-san-pham',
        'pa_cong-nghe-loc',
        'pa_cong-nghe-say-hut',
        'pa_cong-suat',
        // 'pa_cong-suat-hut-am',
        'pa_dien-ap',
        'pa_dien-tich-hut-am',
        'pa_dien-tich-lam-lanh',
        'pa_dien-tich-lam-nong',
        'pa_dien-tich-loc',
        'pa_dong-co',
        // 'pa_dung-tich',
        // 'pa_kich-thuoc',
        // 'pa_kieu-dang',
        'pa_luu-luong-tao-am',
        // 'pa_mau-sac',
        'pa_tam-dien-cuc',
        // 'pa_thuong-hieu',
        // 'pa_trong-luong-giat',
        'pa_trong-luong-say',
        // 'pa_vat-lieu-canh-tu'

    ],




    /*
    |--------------------------------------------------------------------------
    | Override riêng cho category
    |--------------------------------------------------------------------------
    |
    | Nếu category nào cần chỉ định riêng filter
    | thì khai báo ở đây.
    |
    | Nếu không có:
    | hệ thống tự lấy attribute.
    |
    |--------------------------------------------------------------------------
    */


    'override' => [


        /*
        'tu-lanh' => [

            'pa_thuong-hieu',
            'pa_dung-tich',
            'pa_mau-sac',
            'pa_kich-thuoc',

        ],
        */],


];
