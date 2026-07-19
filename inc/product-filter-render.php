<?php


function render_product_filter_bar()
{


    $filters = get_dynamic_product_filters();



    if (empty($filters)) {
        return;
    }


?>


    <div class="product-filter-wrapper">



        <!-- ======================================
         DESKTOP FILTER
    ======================================= -->

        <div class="product-filter-desktop">


            <!-- Bộ lọc -->
            <div class="product-filter-title">

                <i class="fa-light fa-sliders filter-icon"></i>

                <span>
                    Bộ lọc
                </span>

            </div>





            <!-- Filter chính -->
            <div class="product-filter-options">


                <div class="product-filter-grid">


                    <?php foreach ($filters as $filter) : ?>


                        <div class="product-filter-item">


                            <select
                                class="product-filter-select"
                                data-filter="<?php echo esc_attr($filter['key']); ?>">



                                <option value="">

                                    <?php echo esc_html($filter['label']); ?>

                                </option>



                                <?php foreach ($filter['terms'] as $term) : ?>


                                    <option
                                        value="<?php echo esc_attr($term->term_id); ?>"
                                        <?php

                                        if (
                                            isset($_GET['cs_' . $filter['key']])
                                            &&
                                            $_GET['cs_' . $filter['key']] == $term->term_id
                                        ) {
                                            echo 'selected';
                                        }

                                        ?>>


                                        <?php echo esc_html($term->name); ?>


                                    </option>



                                <?php endforeach; ?>


                            </select>


                        </div>


                    <?php endforeach; ?>


                </div>


            </div>






            <!-- Sorting -->
            <div class="product-filter-sort">


                <?php render_custom_catalog_ordering(); ?>


            </div>



        </div>








        <!-- ======================================
         MOBILE BUTTON
    ======================================= -->


        <button class="open-filter-drawer">


            <i class="fa-light fa-sliders"></i>


            <span>
                Bộ lọc
            </span>


        </button>









        <!-- ======================================
         MOBILE OVERLAY
    ======================================= -->


        <div class="filter-drawer-overlay"></div>








        <!-- ======================================
         MOBILE DRAWER
    ======================================= -->


        <div class="filter-drawer">





            <div class="filter-drawer-header">


                <strong>
                    Bộ lọc
                </strong>



                <button type="button" class="close-filter-drawer">


                    <i class="fa-light fa-xmark"></i>


                </button>



            </div>







            <div class="drawer-content">





                <?php foreach ($filters as $filter) : ?>



                    <div class="drawer-item">


                        <label>

                            <?php echo esc_html($filter['label']); ?>


                        </label>




                        <select class="mobile-filter-select" data-filter="<?php echo esc_attr($filter['key']); ?>">



                            <option value="">

                                <?php echo esc_html($filter['label']); ?>

                            </option>




                            <?php foreach ($filter['terms'] as $term) : ?>



                                <option value="<?php echo esc_attr($term->term_id); ?>" <?php if (
                                                                                            isset($_GET['cs_' .
                                                                                                $filter['key']]) && $_GET['cs_' . $filter['key']] == $term->term_id
                                                                                        ) {
                                                                                            echo 'selected';
                                                                                        }

                                                                                        ?>>


                                    <?php echo esc_html($term->name); ?>


                                </option>



                            <?php endforeach; ?>



                        </select>



                    </div>



                <?php endforeach; ?>






            </div>







            <!-- FOOTER BUTTON -->


            <div class="drawer-footer">



                <button type="button" class="clear-mobile-filter">


                    Hủy bộ lọc


                </button>





                <button type="button" class="apply-mobile-filter">


                    Áp dụng


                </button>




            </div>







        </div>






    </div>



<?php


}
