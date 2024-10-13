<?php
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );
define( 'WP_DEBUG_DISPLAY', false );

function themify_child_enqueue_styles() {
    // Charger les styles du thème parent
    wp_enqueue_style( 'themify-parent-style', get_template_directory_uri() . '/style.css' );
    
    // Charger les styles du thème enfant
    wp_enqueue_script("htmltopdf" , get_stylesheet_directory_uri()."/dist/pdf/html2pdf.bundle.min.js");
    wp_enqueue_style( 'themify-child-style', get_stylesheet_directory_uri() . '/style.css', array('themify-parent-style')  , "0.1");
    wp_enqueue_script("epkJs" , get_stylesheet_directory_uri()."/epk.js" , [] , "1.12");
    wp_localize_script('epkJs','epk',['ajaxurl'=>admin_url( 'admin-ajax.php' ),"url" => get_home_url()]);
}
add_action( 'wp_enqueue_scripts', 'themify_child_enqueue_styles' );

function pre($content){
    echo "<pre>";
    print_r($content);
    echo "</pre>";
}


function getSortedCourseList(){
    $product_array = get_transient("courseList6");
    if (empty($product_array)){

        $products = wc_get_products(array(
            'status' => 'publish', // Récupérer uniquement les produits publiés
            'limit' => -1, // Récupérer tous les produits
        ));
        $product_array = [];
        
        foreach ($products as $product) {
            // Ajouter les détails du produit au tableau
            $dateStr = substr($product->get_name() , -10);
            $dateList = explode("/" , $dateStr) ;
    
            if (count($dateList) != 3){
                // ???
            }
            else{
                if (strlen($dateList[2]) == 2){
                    $dateList[2] = "20".$dateList[2];
                    $dateList[0] = substr($dateList[0],-2);
                }
                $date = new DateTime($dateList[2]."-".$dateList[1]."-".$dateList[0]);
                $today = new DateTime();
                $twoWeeks = $today->modify("-1 year");
                $id = $product->get_id();
                if ($date > $twoWeeks){
                    $product_array[$id] = array(
                        'id' => $id,
                        'name' => $product->get_name(),
                        'price' => $product->get_price(),
                        'date' => $date->format("Y-m-d"),
                        "orders" => [],
                    );
                }
            }
        }
        uasort($product_array, function($a, $b) {
            return strcmp($a['date'], $b['date']); // Comparer les dates sous forme de chaînes
        });
        $date_one_year_ago = date('Y-m-d H:i:s', strtotime('-1 year'));
        $args = array(
            'status' => 'any', // Changez le statut selon vos besoins
            'limit' => -1, // Récupérer toutes les commandes
            'date_created' => '>' . $date_one_year_ago, // Filtrer par date
        );
        
        $orders = wc_get_orders($args);
        foreach ($orders as $order){
            foreach($order->get_items() as $item){
                if (isset($product_array[$item->get_product_id()])){
                    
                    $product_array[$item->get_product_id()]["orders"][$order->get_customer_id()][] = [
                        "email" => $order->get_billing_email(),
                        "name" => $order->get_billing_first_name() . ' ' . $order->get_billing_last_name(),
                        "status" => $order->get_status(),
                        "customer_id" => $order->get_customer_id(),
                        "order_id" => $order->get_id(),
                        "total" => $order->get_total(),
                    ];
                }
            }
        }
    
    
        set_transient("courseList6" , $product_array , 3600);
    }
    return $product_array;
    
}

function add_admin_submenu_item($items, $args) {
    // Vérifier si l'utilisateur est connecté et a le rôle d'administrateur
    if (is_user_logged_in() && current_user_can('administrator')) {
        // Chercher le parent sous-menu (remplacez 'Parent Menu' par le nom de votre menu)
        foreach ($items as $item) {
            // Vérifiez si l'élément de menu est celui que vous souhaitez utiliser comme parent
            if ($item->title === 'Mon compte') { // Remplacez 'Parent Menu' par le titre de votre menu parent
                // Ajouter l'élément de sous-menu
                $new_item = new stdClass();
                $new_item->ID = 9999; // ID unique pour le nouvel élément
                $new_item->title = 'Présences';
                $new_item->url = home_url('admin');
                $new_item->menu_item_parent = $item->ID; // Associer au parent
                $new_item->type = 'custom'; // Type de menu
                $new_item->type_label = 'Custom Link';
                $items[] = $new_item; // Ajouter à la liste des éléments de menu
                break; // Sortir de la boucle une fois l'élément ajouté
            }
        }
    }
    return $items;
}
add_filter('wp_nav_menu_objects', 'add_admin_submenu_item', 10, 2);

add_action("wp_ajax_refresh_transient" , "ajax_refresh_transient");

function ajax_refresh_transient(){
    delete_transient("courseList6");
    wp_send_json_success();


}