<?php

/*
Plugin Name: Add to cart upsell product
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
    wp_enqueue_style('styletailwind', plugin_dir_url(__FILE__) . "css/output.css", false);
    wp_enqueue_style('styleflowbite', "https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.6.5/flowbite.min.css", false);
    wp_enqueue_script('styleflowscript', "https://cdnjs.cloudflare.com/ajax/libs/flowbite/1.6.5/flowbite.min.js", false);
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

function insert_my_footer()
{
    ?>
 
<!-- Modal toggle -->
<button data-modal-target="defaultModal" data-modal-toggle="defaultModal" class="block text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800" type="button">
  Toggle modal
</button>

<!-- Main modal -->
<div id="defaultModal" tabindex="-1" aria-hidden="true" class="fixed top-0 left-0 right-0 z-50 hidden w-full p-4 overflow-x-hidden overflow-y-auto md:inset-0 h-[calc(100%-1rem)] max-h-full">
    <div class="relative w-full max-w-2xl max-h-full">
        <!-- Modal content -->
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <!-- Modal header -->
            <div class="flex items-start justify-between p-4 border-b rounded-t dark:border-gray-600">
            <div class="w-12 h-12 rounded-full bg-green-100 dark:bg-green-900 p-2 flex items-center justify-center mx-auto mb-3.5">
                
                <svg aria-hidden="true" class="w-8 h-8 text-green-500 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                <span class="sr-only">Success</span>
            </div>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white" style="margin: auto;">
                    The folowing product was added to your cart:
                </h3>
                <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="deleteModal">
                    <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                    <span class="sr-only">Close modal</span>
                </button>
            </div>
            <!-- Modal body -->
            <div class="p-6 space-y-6">
          
            <div class="relative p-4 text-center bg-white rounded-lg shadow dark:bg-gray-800 sm:p-5" style="display:flex; flex-direction:row; align-items:center; gap: 1em;">
            
            
            <div class="w-12 items-center justify-center  mb-3.5" style="width:175px">
            <img src="http://add-to-cart-upsell-popup.local/wp-content/uploads/2023/05/download.png"/></div>
            
            <p class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">Asus IdeaPad 23</p>`
            <p class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">1x</p>
            <p class="mb-4 text-lg font-semibold text-gray-900 dark:text-white">2300 euro incl BTW</p>
        </div>
        
        <div class="flex justify-center items-center space-x-4" style="flex-direction: column-reverse;">
                <button id="deleteModal" data-modal-toggle="deleteModal" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    Naar winkelwagen
                </button>
                
            </div>

            <h3 class="text-xl font-semibold text-gray-900 dark:text-white" style="text-align:center;">
                    Dit product wordt vaak gekocht in combinatie met:
                </h3>
    <div class="">
        <!-- Start coding here -->
        <div class="bg-white dark:bg-gray-800 relative shadow-md sm:rounded-lg overflow-hidden">
            <div class="flex flex-col md:flex-row items-center justify-between space-y-3 md:space-y-0 md:space-x-4 p-4" style="justify-content: center;">
               
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-4 py-3">Name</th>
                            <th scope="col" class="px-4 py-3">Thumbnail</th>
                            <th scope="col" class="px-4 py-3">Price</th>
                            <th scope="col" class="px-4 py-3">Toevoegen </th>
                            <th scope="col" class="px-4 py-3">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b dark:border-gray-700">
                            <th scope="row" class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">Apple iMac 27&#34;</th>
                            <td class="px-4 py-3">PC</td>
                            <td class="px-4 py-3">Apple</td>
                            <td class="px-4 py-3"><button data-modal-toggle="deleteModal" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                            Toevoegen 
                </button></td>
                            <td class="px-4 py-3 flex items-center justify-end">
                                <button id="apple-imac-27-dropdown-button" data-dropdown-toggle="apple-imac-27-dropdown" class="inline-flex items-center p-0.5 text-sm font-medium text-center text-gray-500 hover:text-gray-800 rounded-lg focus:outline-none dark:text-gray-400 dark:hover:text-gray-100" type="button">
                                    <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                    </svg>
                                </button>
                                <div id="apple-imac-27-dropdown" class="hidden z-10 w-44 bg-white rounded divide-y divide-gray-100 shadow dark:bg-gray-700 dark:divide-gray-600">
                                    <ul class="py-1 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="apple-imac-27-dropdown-button">
                                        <li>
                                            <a href="#" class="block py-2 px-4 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Show</a>
                                        </li>
                                        <li>
                                            <a href="#" class="block py-2 px-4 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Edit</a>
                                        </li>
                                    </ul>
                                    <div class="py-1">
                                        <a href="#" class="block py-2 px-4 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">Delete</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr class="border-b dark:border-gray-700">
                            <th scope="row" class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">Apple iMac 20&#34;</th>
                            <td class="px-4 py-3">PC</td>
                            <td class="px-4 py-3">Apple</td>
                            <td class="px-4 py-3"><button data-modal-toggle="deleteModal" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                            Toevoegen 
                </button></td>
                            <td class="px-4 py-3 flex items-center justify-end">
                                <button id="apple-imac-20-dropdown-button" data-dropdown-toggle="apple-imac-20-dropdown" class="inline-flex items-center p-0.5 text-sm font-medium text-center text-gray-500 hover:text-gray-800 rounded-lg focus:outline-none dark:text-gray-400 dark:hover:text-gray-100" type="button">
                                    <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                    </svg>
                                </button>
                                <div id="apple-imac-20-dropdown" class="hidden z-10 w-44 bg-white rounded divide-y divide-gray-100 shadow dark:bg-gray-700 dark:divide-gray-600">
                                    <ul class="py-1 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="apple-imac-20-dropdown-button">
                                        <li>
                                            <a href="#" class="block py-2 px-4 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Show</a>
                                        </li>
                                        <li>
                                            <a href="#" class="block py-2 px-4 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Edit</a>
                                        </li>
                                    </ul>
                                    <div class="py-1">
                                        <a href="#" class="block py-2 px-4 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">Delete</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr class="border-b dark:border-gray-700">
                            <th scope="row" class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">Apple iPhone 14</th>
                            <td class="px-4 py-3">Phone</td>
                            <td class="px-4 py-3">Apple</td>
                            <td class="px-4 py-3"><button data-modal-toggle="deleteModal" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                            Toevoegen 
                </button></td>
                            <td class="px-4 py-3 flex items-center justify-end">
                                <button id="apple-iphone-14-dropdown-button" data-dropdown-toggle="apple-iphone-14-dropdown" class="inline-flex items-center p-0.5 text-sm font-medium text-center text-gray-500 hover:text-gray-800 rounded-lg focus:outline-none dark:text-gray-400 dark:hover:text-gray-100" type="button">
                                    <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                    </svg>
                                </button>
                                <div id="apple-iphone-14-dropdown" class="hidden z-10 w-44 bg-white rounded divide-y divide-gray-100 shadow dark:bg-gray-700 dark:divide-gray-600">
                                    <ul class="py-1 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="apple-iphone-14-dropdown-button">
                                        <li>
                                            <a href="#" class="block py-2 px-4 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Show</a>
                                        </li>
                                        <li>
                                            <a href="#" class="block py-2 px-4 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Edit</a>
                                        </li>
                                    </ul>
                                    <div class="py-1">
                                        <a href="#" class="block py-2 px-4 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">Delete</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr class="border-b dark:border-gray-700">
                            <th scope="row" class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">Apple iPad Air</th>
                            <td class="px-4 py-3">Tablet</td>
                            <td class="px-4 py-3">Apple</td>
                            <td class="px-4 py-3"><button data-modal-toggle="deleteModal" type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                            Toevoegen 
                </button></td>
                            <td class="px-4 py-3 flex items-center justify-end">
                                <button id="apple-ipad-air-dropdown-button" data-dropdown-toggle="apple-ipad-air-dropdown" class="inline-flex items-center p-0.5 text-sm font-medium text-center text-gray-500 hover:text-gray-800 rounded-lg focus:outline-none dark:text-gray-400 dark:hover:text-gray-100" type="button">
                                    <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewbox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M6 10a2 2 0 11-4 0 2 2 0 014 0zM12 10a2 2 0 11-4 0 2 2 0 014 0zM16 12a2 2 0 100-4 2 2 0 000 4z" />
                                    </svg>
                                </button>
                                <div id="apple-ipad-air-dropdown" class="hidden z-10 w-44 bg-white rounded divide-y divide-gray-100 shadow dark:bg-gray-700 dark:divide-gray-600">
                                    <ul class="py-1 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="apple-ipad-air-dropdown-button">
                                        <li>
                                            <a href="#" class="block py-2 px-4 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Show</a>
                                        </li>
                                        <li>
                                            <a href="#" class="block py-2 px-4 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Edit</a>
                                        </li>
                                    </ul>
                                    <div class="py-1">
                                        <a href="#" class="block py-2 px-4 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">Delete</a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        
                    </tbody>
                </table>
            </div>
            
        </div>
    </div>
            </div>
        </div>
    </div>
</div>

 <?php
}

add_action('wp_footer', 'insert_my_footer');
