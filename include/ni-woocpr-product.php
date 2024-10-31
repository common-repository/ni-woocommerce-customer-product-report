<?php 
if ( !class_exists( 'Ni_WooCPR_Product' ) ) {
	include_once("ni-woocpr-function.php");
	class Ni_WooCPR_Product  extends Ni_WooCPR_Function{
		function __construct() {
			
			add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		}
		public function add_meta_box( $post_type ) {
			$post_types = array('product');     //limit meta box to certain post types
			global $post;
			$product = wc_get_product( $post->ID );
			if ( in_array( $post_type, $post_types )) {
				add_meta_box(
					'wf_child_letters'
					,__( 'Product Customer', 'woocommerce' )
					,array( $this, 'render_meta_box_content' )
					,$post_type
					,'advanced'
					,'high'
				);
			}
		}
		function render_meta_box_content($post ){
			
			$this->get_customer_product_table();
			//print_r($post->ID);
			//echo "dsadasdsa";
		}
		function get_customer_product_table(){
			global $post;
			$columns = array();
			//echo $post->post_type;
			
			
			$columns = $this->get_selected_columns();
			if (count($columns)==0){
				$columns = $this->get_customer_product_columns();
			}
			
			$rows = $this->get_customer_product_query();
			//$this->ni_print_r($rows);
			//$this->ni_print_r($columns);
			//product
			
			//$this->ni_print_r($post);
			
			
			?>
            <div style="overflow-x:auto;">
            <table id="example" class="display" style="width:100%">
            	<thead>
					<?php foreach($columns as $key=>$value): ?>
                        <th><?php echo $value; ?></th>
                    <?php endforeach; ?>
            	</thead>
                <tbody>
                	<?php foreach($rows as $row_key=>$row_value): ?>
                    	<tr>
                        	<?php foreach($columns as $col_key=>$col_value): ?>
                            	<?php switch($col_key): case "a" :break; ?>
                                	<?php default; ?>
                                    <?php $td_value = isset($row_value->$col_key)?$row_value->$col_key:'a'; ?>
                                    <td><?php echo  $td_value; ?></td>
                                <?php endswitch; ?>
                            <?php endforeach; ?>
                        </tr>
					<?php endforeach; ?>
                </tbody>
                <tfoot>
                	<?php foreach($columns as $key=>$value): ?>
                        <th><?php echo $value; ?></th>
                    <?php endforeach; ?>
                </tfoot>
            </table>
            </div>
            
            
            <?php
		}
		function get_customer_product_query(){
			global $wpdb;
			global $post;
			//echo $post->post_type;
			$product_id =  $post->ID;
				
			$query = "";
			$query .= " SELECT ";
			$query .= " posts.ID as  order_id ";
			$query .= " ,date_format( posts.post_date, '%Y-%m-%d') as  order_date ";
			$query .= " ,posts.post_status as order_status ";
			$query .= " FROM {$wpdb->prefix}posts as posts	";
			
			$query .= " LEFT JOIN  {$wpdb->prefix}woocommerce_order_items as order_items ON order_items.order_id=posts.ID  ";
			
			$query .= " LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as product_id ON product_id.order_item_id=order_items.order_item_id  ";
			
			$query .= " WHERE 1=1 ";
			$query .= " AND posts.post_type ='shop_order'  ";
			$query .= " AND order_items.order_item_type ='line_item'";
			$query .= " AND product_id.meta_key ='_product_id'";
			
			$query .= " AND product_id.meta_value ='{$product_id}'";
			
			
			$row = $wpdb->get_results( $query );
			//$this->ni_print_r($row);
			$extra_meta_keys 	= array();
			//$columns 			= $this->get_columns();
			$columns 			= array();
			$post_ids 			= $this->get_items_id_list($row,'order_id');
			$postmeta_datas 	= $this->get_postmeta($post_ids, $columns,$extra_meta_keys);
			
			foreach($row as $row_key => $row_value){
				$order_id =$row_value->order_id;
				$postmeta_data 	= isset($postmeta_datas[$order_id]) ? $postmeta_datas[$order_id] : array();
				foreach($postmeta_data as $postmeta_key => $postmeta_value){
					$row[$row_key]->{$postmeta_key}	= $postmeta_value;
				}	
			}
			
			
			return $row ;
		}
		public static function get_postmeta($order_ids = '0', $columns = array(), $extra_meta_keys = array(), $type = 'all'){
			
			global $wpdb;
			
			$post_meta_keys = array();
			
			if(count($columns)>0)
			foreach($columns as $key => $label){
				$post_meta_keys[] = $key;
			}
			
			foreach($extra_meta_keys as $key => $label){
				$post_meta_keys[] = $label;
			}
			
			foreach($post_meta_keys as $key => $label){
				$post_meta_keys[] = "_".$label;
			}
			
			$post_meta_key_string = implode("', '",$post_meta_keys);
			
			$sql = " SELECT * FROM {$wpdb->postmeta} AS postmeta";
			
			$sql .= " WHERE 1*1";
			
			if(strlen($order_ids) >0){
				$sql .= " AND postmeta.post_id IN ($order_ids)";
			}
			
			if(strlen($post_meta_key_string) >0){
				$sql .= " AND postmeta.meta_key IN ('{$post_meta_key_string}')";
			}
			
			if($type == 'total'){
				$sql .= " AND (LENGTH(postmeta.meta_value) > 0 AND postmeta.meta_value > 0)";
			}
			
			$sql .= " ORDER BY postmeta.post_id ASC, postmeta.meta_key ASC";
			
			//echo $sql;return '';
			
			$order_meta_data = $wpdb->get_results($sql);			
			
			if($wpdb->last_error){
				echo $wpdb->last_error;
			}else{
				$order_meta_new = array();	
					
				foreach($order_meta_data as $key => $order_meta){
					
					$meta_value	= $order_meta->meta_value;
					
					$meta_key	= $order_meta->meta_key;
					
					$post_id	= $order_meta->post_id;
					
					$meta_key 	= ltrim($meta_key, "_");
					
					$order_meta_new[$post_id][$meta_key] = $meta_value;
					
				}
			}
			
			return $order_meta_new;
			
		}
		function get_items_id_list($order_items = array(),$field_key = 'order_id', $return_default = '-1' , $return_formate = 'string'){
			$list 	= array();
			$string = $return_default;
			if(count($order_items) > 0){
				foreach ($order_items as $key => $order_item) {
					if(isset($order_item->$field_key))
						$list[] = $order_item->$field_key;
				}
				
				$list = array_unique($list);
				
				if($return_formate == "string"){
					$string = implode(",",$list);
				}else{
					$string = $list;
				}
			}
			return $string;
		}
		function get_customer_product_columns(){
			/*
			$columns = array(					
				 "order_id"				=>"#ID"
				,"order_date"			=>"Order Date"
				,"billing_first_name"	=>"Billing First Name"
				,"billing_email"		=>"Billing Email"
				,"billing_country"		=>"Billing Country"
				,"order_status"			=>"Status"
				,"order_currency"		=>"Order Currency"
				,"payment_method_title" =>"Payment Method"
				,"order_total"			=>"Order Total"
				
			  );
			  */
			$columns["order_id"] = __("#ID");
			$columns["order_date"] = __("Order Date");
			$columns["billing_first_name"] = __("Billing First Name");
			$columns["billing_email"] = __("Billing Email");
			$columns["billing_country"] = __("Billing Country");
			$columns["order_status"] = __("Status");
			$columns["order_currency"] = __("Order Currency");
			$columns["payment_method_title"] = __("Payment Method");
			$columns["order_total"] = __("order_total");
			
			  
			return $columns;  
		}
		function ni_print_r($thing,$description=false){
			echo '<pre style="background:#fff; padding:10px; color:#111; font-family:monospace; font-size:12px; border:1px solid #555">';
			if($description) echo '<strong>'.$description.'</strong><br><br>';
			print_r($thing);
			echo '</pre>';
		}	
	}
}
?>