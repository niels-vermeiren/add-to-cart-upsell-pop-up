<?php

/*
Plugin Name: Pop-up add to cart
Description: Shows a pop-up after adding items to cart
Version: 1.0
Author: Niels Vermeiren
*/



add_filter('woocommerce_add_cart_item_data', 'wp_kama_woocommerce_add_cart_item_data_filter', 10, 4);
function wp_kama_woocommerce_add_cart_item_data_filter($cart_item_data, $product_id, $variation_id, $quantity)
{


    if (isset($_COOKIE['itemsincart'])) {
        $products = $_COOKIE['itemsincart'];
        $products = str_replace("\\", "", $products);
        $products = json_decode($products, true);
    } else {
        $products = array();
    }

    $product = wc_get_product($product_id);
    $upsells = $product->get_upsell_ids();

    if (count($upsells) == 0) {
        $products[] = array(
            "product_id" => $product_id,
            "quantity"   => $quantity
        );
        setcookie('itemsincart', json_encode($products));
        $_COOKIE['itemsincart'] = json_encode($products);
    } else {

        setcookie("newtocart", json_encode(array(
            "product_id" => $product_id,
            "quantity"   => $quantity
        )));
        $_COOKIE['newtocart'] = json_encode(array(
            "product_id" => $product_id,
            "quantity"   => $quantity
        ));
    }
    return $cart_item_data;
}

//Include custom scripts
function themeslug_enqueue_script()
{
    global $woocommerce;
    $upsellsShow = array();
    $products = array();

    //Scripts
    wp_enqueue_style('styleicons', "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css", false);
    wp_enqueue_style('style', "https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css", false);
    wp_enqueue_script('jsboot', plugin_dir_url(__FILE__) . "js/bootstrap.min.js", false);
    wp_enqueue_script('jsmodal', plugin_dir_url(__FILE__) . "js/bootstrap-show-modal.js", false);
    wp_enqueue_script('jscookie', plugin_dir_url(__FILE__) . "js/jquery.cookie.min.js", false);
    wp_enqueue_script('jslanpopup', plugin_dir_url(__FILE__) . "js/main.js", false);

    if(isset($_COOKIE['newtocart'])) {
        //get head cart item info
        $head_product_obj = $_COOKIE['newtocart'];
        $head_product_obj = str_replace("\\", "", $head_product_obj);
        $head_product_obj = json_decode($head_product_obj, true);

        if($head_product_obj) {
            $head_product = wc_get_product($head_product_obj['product_id']);
            $name = $head_product->get_name();
            $price = $head_product->get_price();
            $product_image_url = wp_get_attachment_image_src(get_post_thumbnail_id($head_product->get_id()), 'single-post-thumbnail')[0];

            $products[] = array(
                "id" => $head_product_obj['product_id'],
                "name" => $name,
                "price" => $price,
                "image" => $product_image_url,
                "quantity" => $head_product_obj['quantity']
            );


            //Other items in cart
            $items_in_cart_obj = $_COOKIE['itemsincart'];
            $items_in_cart_obj = str_replace("\\", "", $items_in_cart_obj);
            $items_in_cart_obj = json_decode($items_in_cart_obj, true);

            foreach($items_in_cart_obj as $item) {
                $items_in_cart = wc_get_product($item['product_id']);
                $name = $items_in_cart->get_name();
                $price = $items_in_cart->get_price();
                $product_image_url = wp_get_attachment_image_src(get_post_thumbnail_id($items_in_cart->get_id()), 'single-post-thumbnail')[0];

                $products[] = array(
                    "id" => $item['product_id'],
                    "name" => $name,
                    "price" => $price,
                    "image" => $product_image_url,
                    "quantity" => $item['quantity']
                );
            }

            //get upsells
            $upsells = $head_product->get_upsell_ids();
            $upsells = array_slice($upsells, 0, 3);
            foreach ($upsells as $key => $upsell) {
                $upsellProd = wc_get_product($upsell);
                $upsellName  = $upsellProd->get_name();
                $upsellPrice = $upsellProd->get_price();
                $upsellImageUrl = wp_get_attachment_image_src(get_post_thumbnail_id($upsell), 'single-post-thumbnail')[0];
                $link = get_permalink($upsell);
                $upsellsShow[] = array(
                    'id' => $upsell,
                    'name' => $upsellName,
                    'price' => $upsellPrice,
                    'image' => $upsellImageUrl,
                    'link' => $link
                );
            }
        }
    }

    //Variables
    wp_localize_script("jslanpopup", "products", $products);
    wp_localize_script("jslanpopup", "upsells", $upsellsShow);
    wp_localize_script("jslanpopup", "is_product_page", is_product());
    wp_localize_script('jslanpopup', 'ajax_object', array( 'ajaxurl' => admin_url('admin-ajax.php')));
}

add_action('wp_enqueue_scripts', 'themeslug_enqueue_script');

add_action("wp_ajax_get_productinfo", "get_productinfo");
add_action("wp_ajax_nopriv_get_productinfo", "get_productinfo");
function get_productinfo()
{

    $product_id = $_POST['product_id'];

    $product2 = wc_get_product($product_id);
    $name = $product2->get_name();
    $price = $product2->get_price();
    $product_image_url = wp_get_attachment_image_src(get_post_thumbnail_id($product2->get_id()), 'single-post-thumbnail')[0];

    //get upsells
    $upsells = $product2->get_upsell_ids('edit');
    $upsells = array_slice($upsells, 0, 3);
    $upsellsShow = array();

    foreach ($upsells as $key => $upsell) {
        $upsellProd = wc_get_product($upsell);
        $upsellName  = $upsellProd->get_name();
        $upsellPrice = $upsellProd->get_price();
        $upsellImageUrl = wp_get_attachment_image_src(get_post_thumbnail_id($upsell), 'single-post-thumbnail')[0];
        $link = get_permalink($upsell);
        $upsellsShow[] = array(
            'id' => $upsell,
            'name' => $upsellName,
            'price' => $upsellPrice,
            'image' => $upsellImageUrl,
            'link' => $link
        );
    }

    $add_to_cart = $_GET['add-to-cart'];

    $res_array = array(
        'name' => $name,
        'price' => $price,
        'image' =>  $product_image_url,
        'upsells' => $upsellsShow
    );

    echo json_encode($res_array);

    wp_die();
}
