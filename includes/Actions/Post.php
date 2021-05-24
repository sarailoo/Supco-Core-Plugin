<?php

namespace Actions;

use Helper\Core;

class Post
{

    public static function hook_into_wordpress(){

        add_action( 'init', ['Actions\Post', 'create_taxonomy'] );
        add_action( 'init', ['Actions\Post', 'create_posttype'] );
        add_action( 'add_meta_boxes', array( 'Actions\Post', 'create_metabox' ) ); 
        add_action( 'save_post_supcoproduct', array( 'Actions\Post', 'save_metaboxes' ) );
        add_action( 'admin_menu', array( 'Actions\Post', 'add_admin_menu' ) );
        add_filter( 'manage_supcoproduct_posts_columns', array('Actions\Post','set_custom_edit_supcoproduct_columns' ));
        add_action( 'manage_supcoproduct_posts_custom_column' , array('Actions\Post','custom_supcoproduct_column'), 10, 20 );
	    add_filter('save_post_supcoproduct', array('Actions\Post','post_updated'));

    }
    public static function create_posttype(){
        register_post_type('supcoproduct', array(
            'supports' => array('title'),
            'public' => true,
            // 'exclude_from_search' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'labels' => array(
                'name' => __( 'Supco Product','supco-core' ),
                'add_new_item' => __( 'Add New Product','supco-core' ),
                'edit_item' => __( 'Edit Product','supco-core' ),
                'all_items' =>  __( 'All Products','supco-core' ),
                'singular_name' => __( 'Product','supco-core' ),
            ),
            'menu_icon' => 'dashicons-dashboard',
        ));
    }

    public static function create_taxonomy(){
        $labels = array(
            'name'                       => _x( 'Categories', 'Taxonomy General Name', 'supco-core' ),
            'singular_name'              => _x( 'Category', 'Taxonomy Singular Name', 'supco-core' ),
            'menu_name'                  => __( 'Categories', 'supco-core' ),
            'all_items'                  => __( 'All Items', 'supco-core' ),
            'parent_item'                => __( 'Parent Item', 'supco-core' ),
            'parent_item_colon'          => __( 'Parent Item:', 'supco-core' ),
            'new_item_name'              => __( 'New Item Name', 'supco-core' ),
            'add_new_item'               => __( 'Add 
New Item', 'supco-core' ),
            'edit_item'                  => __( 'Edit Item', 'supco-core' ),
            'update_item'                => __( 'Update Item', 'supco-core' ),
            'view_item'                  => __( 'View Item', 'supco-core' ),
            'separate_items_with_commas' => __( 'Separate items with commas', 'supco-core' ),
            'add_or_remove_items'        => __( 'Add or remove items', 'supco-core' ),
            'choose_from_most_used'      => __( 'Choose from the most used', 'supco-core' ),
            'popular_items'              => __( 'Popular Items', 'supco-core' ),
            'search_items'               => __( 'Search Items', 'supco-core' ),
            'not_found'                  => __( 'Not Found', 'supco-core' ),
            'no_terms'                   => __( 'No items', 'supco-core' ),
            'items_list'                 => __( 'Items list', 'supco-core' ),
            'items_list_navigation'      => __( 'Items list navigation', 'supco-core' ),
        );
        $args = array(
            'labels'                     => $labels,
            'hierarchical'               => true,
            'public'                     => true,
            'show_ui'                    => true,
            'show_admin_column'          => true,
            'show_in_nav_menus'          => true,
            'show_tagcloud'              => true,
        );
        register_taxonomy( 'Categories', array( 'supcoproduct' ), $args );
    }


    public static function create_metabox(){
        add_meta_box(
			'name',
			__('Name','supco-core'),
			array('Helper\Core' , 'metabox_form_name' ),
			'supcoproduct'
		);

	    add_meta_box(
		    'supcosku',
		    __('SUPCO SKU','supco-core'),
		    array('Helper\Core' , 'metabox_form_supcosku' ),
		    'supcoproduct',
            'advanced',
	        'high'
	    );

        add_meta_box(
            'code',
            __('OEM SKU','supco-core'),
            array('Helper\Core' , 'metabox_form_code' ),
            'supcoproduct',
	        'advanced',
	        'high'
        );
        add_meta_box(
            'description',
            __('Description','supco-core'),
            array('Helper\Core' , 'metabox_form_description' ),
            'supcoproduct',
            'advanced',
	        'low'
        );
    }
    
    public static function save_metaboxes($post_id){
        if (array_key_exists('wporg_field_name', $_POST)) {
            update_post_meta(
                $post_id,
                'meta-key-name',
                $_POST['wporg_field_name']
            );
        }

	    if (array_key_exists('wporg_field_supcosku', $_POST)) {
		    update_post_meta(
			    $post_id,
			    'meta-key-supcosku',
			    $_POST['wporg_field_supcosku']
		    );
	    }

        if (array_key_exists('wporg_field_code', $_POST)) {

	        // args to query for your key
	        $args = array(
		        'post_type' => 'supcoproduct',
		        'meta_query' => array(
			        array(
				        'key' => 'meta-key-code',
				        'value' => $_POST['wporg_field_code']
			        )
		        )
	        );
	        // perform the query
	        $query = new \WP_Query( $args );
	        $duplicates = $query->posts;

	        // do something if the key-value-pair not exists in another post
	        if ( empty( $duplicates ) ) {
		        update_post_meta(
			        $post_id,
			        'meta-key-code',
			        $_POST['wporg_field_code']
		        );

	        }

        }

        if (array_key_exists('wporg_field_description', $_POST)) {
            update_post_meta(
                $post_id,
                'meta-key-description',
                $_POST['wporg_field_description']
            );
        }

    }



// Add the custom columns to the supcoproduct post type:
    public static function set_custom_edit_supcoproduct_columns($columns) {
        unset( $columns['product_code'] );
        $columns['product_code'] = __( 'OEM SKU', 'supco-core' );

        return $columns;
    }

    // Add the data to the custom columns for the supcoproduct post type:
    public static function custom_supcoproduct_column( $column, $post_id ) {
        switch ( $column ) {

            case 'product_code' :
                $code = get_post_meta( $post_id ,'meta-key-code');
	            echo $code[0];


        }
    }


	public static function post_updated($post) {

            if ( get_post_type($post->ID) == 'supcoproduct' && $post->post_type ) {

			$product_code = get_post_meta( $post->ID ,'meta-key-code',true);
			$product_name = get_post_meta( $post->ID,'meta-key-name',true);
			$delimiter = "-";
			$str = $product_name;
			$product_code_sanitized = trim($product_code);
			$product_code_sanitized = str_replace("/","-",$product_code_sanitized);
			$product_code_sanitized = str_replace(".","-",$product_code_sanitized);
			$product_code_sanitized = str_replace(" ","",$product_code_sanitized);

			$slug = strtolower($product_code_sanitized)."-".strtolower(trim(preg_replace('/[\s-]+/', $delimiter, preg_replace('/[^A-Za-z0-9-]+/', $delimiter, preg_replace('/[&]/', 'and', preg_replace('/[\']/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $str))))), $delimiter));

			if(substr($slug,-1)=="-"){
				$slug = str_replace("-","",$slug);
			}
			$data = array(
				'ID' => $post->ID,
				'post_name' => $slug
			);

			wp_update_post( $data );
		}

    }


	public static function add_admin_menu(){
        
        add_menu_page(esc_html__('Supco Import','supco-core'),esc_html__('Supco Import','supco-core'),'read','supco_import_main',array('Actions\Post','supco_import_main_page'));
    }

    public static function supco_import_main_page()
    {
        global $wpdb;

        // Import CSV
        if(isset($_POST['butimport'])){

            // File extension
            $extension = pathinfo($_FILES['import_file']['name'], PATHINFO_EXTENSION);

            // If file extension is 'csv'
            if(!empty($_FILES['import_file']['name']) && $extension == 'csv'){

                // Open file in read mode
                $csvFile = fopen($_FILES['import_file']['tmp_name'], 'r');

                fgetcsv($csvFile); // Skipping header row

                // Read file
                $line=1;
                while(($csvData = fgetcsv($csvFile)) !== FALSE){
                    $line++;
                    $csvData = array_map("utf8_encode", $csvData);

                    // Row column length
                    $dataLen = count($csvData);

                    // Skip row if length != 7
                    if( !($dataLen == 7) ) continue;

                    // Assign value to variables
                    $product_code = trim($csvData[1]);
	                $supco_code = trim($csvData[2]);
	                $cat_id = trim($csvData[3]);
                    $product_name = trim($csvData[5]);
                    $product_description = trim($csvData[6]);
                    $post_title = $product_name;
                    //$post_name_temp = str_replace(' ', '-', strtolower($product_name));
                    //$post_name = strtolower($product_code)."-".preg_replace("/[^A-Za-z0-9\-\']/", '', $post_name_temp);
                    $delimiter = "-";
                    $str = $product_name;
                    $product_code_sanitized = trim($product_code);
	                $product_code_sanitized = str_replace("/","-",$product_code_sanitized);
	                $product_code_sanitized = str_replace(".","-",$product_code_sanitized);
	                $product_code_sanitized = str_replace(" ","",$product_code_sanitized);
	                $post_name = strtolower($product_code_sanitized)."-".strtolower(trim(preg_replace('/[\s-]+/', $delimiter, preg_replace('/[^A-Za-z0-9-]+/', $delimiter, preg_replace('/[&]/', 'and', preg_replace('/[\']/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $str))))), $delimiter));

	                if(substr($post_name,-1)=="-"){
		                $post_name = str_replace("-","",$post_name);
                    }
	                
                    // Check product code already exists or not
                    $tablename = $wpdb->prefix."postmeta";
                    $cntSQL = "SELECT count(*) as count FROM {$tablename} where meta_key = 'meta-key-code' AND meta_value='".$product_code."'";
                    $record = $wpdb->get_results($cntSQL, OBJECT);

                    if($record[0]->count==0){

                        // Check if variable is empty or not
                        if(!empty($product_code)) {
                                if (!empty($cat_id)) {

                                    // Table name
                                    $tablename = $wpdb->prefix."posts";

                                    $date = current_time( 'mysql' );
                                    $date_gmt = current_time( 'mysql',1);
                                    $post_author = 1;
                                    $post_type = "supcoproduct";

                                    $wpdb->insert($tablename, array(
                                        'post_author' => $post_author,
                                        'post_date' => $date,
                                        'post_date_gmt' => $date_gmt,
                                        'post_title' => $post_title,
                                        'post_name' => $post_name,
                                        'post_modified' => $date,
                                        'post_modified_gmt' => $date_gmt,
                                        'post_type' => $post_type
                                    ));

                                    $lastid = $wpdb->insert_id;

                                    $guid = get_site_url()."/?post_type=supcoproduct&#038;p=".$lastid;

                                    $wpdb->update( $tablename, array( 'guid' => $guid),array('ID'=>$lastid));

                                    update_post_meta(
                                        $lastid,
                                        'meta-key-name',
	                                    wptexturize($product_name)
                                    );

                                    update_post_meta(
                                        $lastid,
                                        'meta-key-code',
	                                    wptexturize($product_code)
                                    );

                                    update_post_meta(
                                        $lastid,
                                        'meta-key-description',
	                                    wptexturize($product_description)
                                    );

	                                update_post_meta(
		                                $lastid,
		                                'meta-key-supcosku',
		                                wptexturize($supco_code)
	                                );

	                                if(!empty($product_description)){
		                                $meta_desc = $product_description;
                                    }
	                                else{
		                                $meta_desc = $post_title;
                                    }

	                                update_post_meta( $lastid, '_yoast_wpseo_metadesc',wptexturize($meta_desc) );


	                                $tablename = $wpdb->prefix."termmeta";
                                    $cntSQL = "SELECT term_id FROM {$tablename} where meta_key = 'cat-id' AND meta_value='".$cat_id."'";
                                    $records = $wpdb->get_results($cntSQL, OBJECT);

                                    $term_cat_id = $records[0]->term_id;


                                    $tablename =$wpdb->prefix."terms";

                                    $cntSQLterm = $wpdb->prepare("SELECT term_id FROM {$tablename} where term_id = %s",$term_cat_id);
                                    $records = $wpdb->get_results($cntSQLterm);
                                    $term_id = 0;
                                    foreach ( $records as $record )
                                    {
                                        $term_id =  $record->term_id;
                                    }

                                    if($term_id!=0) {
                                        $lasttermid = $term_id;

                                        $tablename = "wp_term_taxonomy";

                                        $cntSQL = "SELECT term_taxonomy_id FROM {$tablename} where term_id ='".$lasttermid."'";
                                        $records = $wpdb->get_results($cntSQL);

                                        foreach ( $records as $record )
                                        {
                                            $lasttermtaxid =  $record->term_taxonomy_id;
                                        }

                                        $tablename = "wp_term_relationships";

                                        $wpdb->insert($tablename, array(
                                            'object_id' => $lastid,
                                            'term_taxonomy_id' => $lasttermtaxid,
                                        ));
                                    }

                                    else{
                                        echo "<h3 style='color: #ff0000;'>Line ".$line.": Subcategory with ID ".$cat_id." does not exist! </h3>";
                                    }


                                } else {
                                    echo "<h3 style='color: #ff0000;'>Line ".$line.": Category name cannot be empty! </h3>";

                                }
                        }
                        else{
                            echo "<h3 style='color: #ff0000;'>Line ".$line.": OEM SKU cannot be empty! </h3>";

                        }

                    }
                    else{
                        echo "<h3 style='color: #ff0000;'>Line ".$line.": Product with OEM SKU ".$product_code." already exists!</h3>";
                    }

                }

            }else{
                echo "<h3 style='color: #ff0000;'>Invalid Extension</h3>";
            }

        }


        // Import CSV
        if(isset($_POST['butimportcat'])){

            // File extension
            $extension = pathinfo($_FILES['import_file_cat']['name'], PATHINFO_EXTENSION);

            // If file extension is 'csv'
            if(!empty($_FILES['import_file_cat']['name']) && $extension == 'csv'){

                // Open file in read mode
                $csvFile = fopen($_FILES['import_file_cat']['tmp_name'], 'r');

                fgetcsv($csvFile); // Skipping header row

                // Read file
                $line=1;
                while(($csvData = fgetcsv($csvFile)) !== FALSE){
                    $line++;
                    $csvData = array_map("utf8_encode", $csvData);

                    // Row column length
                    $dataLen = count($csvData);

                    // Skip row if length != 5
                    if( !($dataLen == 5) ) continue;

                    // Assign value to variables
                    $cat_id = trim($csvData[0]);
                    $cat_name = trim($csvData[1]);
                    $subcat_id = trim($csvData[2]);
                    $subcat_name = trim($csvData[3]);

                    //Add Parent Category

                    $delimiter = "-";
                    $str = $cat_name;
                    $parent_slug = strtolower(trim(preg_replace('/[\s-]+/', $delimiter, preg_replace('/[^A-Za-z0-9-]+/', $delimiter, preg_replace('/[&]/', 'and', preg_replace('/[\']/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $str))))), $delimiter));


                    $tablename = $wpdb->prefix."termmeta";
                    $cntSQL = "SELECT count(*) as count FROM {$tablename} where meta_key = 'cat-id' AND meta_value='".$cat_id."'";
                    $records = $wpdb->get_results($cntSQL, OBJECT);


                    $tablename =$wpdb->prefix."terms";
                    if($records[0]->count==0){


                        $resp = wp_insert_term(
	                        wptexturize($cat_name),   // the term
                            'Categories', // the taxonomy
                            array(
                                'description' => '',
                                'slug'        => $parent_slug,
                                'parent'      => 0,
                            )
                        );

                        $last_term_id = $resp['term_id'];
                        $parent_term_id = $last_term_id;
                        $meta_key = "cat-id";
                        $meta_value = $cat_id;
                        add_term_meta( $last_term_id, $meta_key, $meta_value, true );

                    }
                    else{
                        $tablename = $wpdb->prefix."termmeta";
                        $cntSQL = "SELECT term_id FROM {$tablename} where meta_key = 'cat-id' AND meta_value='".$cat_id."'";
                        $records = $wpdb->get_results($cntSQL, OBJECT);

                        $parent_term_id = $records[0]->term_id;
                    }

                    //Add Sub Category

                    $delimiter = "-";
                    $str = $subcat_name;
                    $slug = strtolower(trim(preg_replace('/[\s-]+/', $delimiter, preg_replace('/[^A-Za-z0-9-]+/', $delimiter, preg_replace('/[&]/', 'and', preg_replace('/[\']/', '', iconv('UTF-8', 'ASCII//TRANSLIT', $str))))), $delimiter));


                    $tablename = $wpdb->prefix."termmeta";
                    $cntSQL = "SELECT count(*) as count FROM {$tablename} where meta_key = 'cat-id' AND meta_value='".$subcat_id."'";
                    $records = $wpdb->get_results($cntSQL, OBJECT);
                    $tablename =$wpdb->prefix."terms";
                    if($records[0]->count==0){

                        $res = wp_insert_term(
	                        wptexturize($subcat_name),   // the term
                            'Categories', // the taxonomy
                            array(
                                'description' => '',
                                'slug'        => $slug."-".$parent_slug,
                                'parent'      => $parent_term_id,
                            )
                        );

                        $last_term_id = $res['term_id'];
                        $meta_key = "cat-id";
                        $meta_value = $subcat_id;
                        add_term_meta( $last_term_id, $meta_key, $meta_value, true );

                    }


                }

            }else{
                echo "<h3 style='color: #ff0000;'>Invalid Extension</h3>";
            }

        }
        ?>
        <h2>Import Supco Products</h2>

        <!-- Form -->
        <form method='post' action='<?= $_SERVER['REQUEST_URI']; ?>' enctype='multipart/form-data'>
            <input  type="file" name="import_file" >
            <input class="button-primary" type="submit" name="butimport" value="Import CSV">
        </form>
        <br>
        <a target="_blank" href="https://supco.ca/wp-content/uploads/2020/10/supcoproductlist.csv">Download Sample For Products</a>
        <br>
        <br>
        <br>
        <br>
        <h2>Import Supco Categories</h2>

        <!-- Form -->
        <form method='post' action='<?= $_SERVER['REQUEST_URI']; ?>' enctype='multipart/form-data'>
            <input  type="file" name="import_file_cat" >
            <input class="button-primary" type="submit" name="butimportcat" value="Import CSV">
        </form>
        <br>
        <a target="_blank" href="https://supco.ca/wp-content/uploads/2020/10/Category.csv">Download Sample For Categories</a>
<?php
    }

}