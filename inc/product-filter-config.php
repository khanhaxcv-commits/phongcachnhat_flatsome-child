<?php

return [

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
