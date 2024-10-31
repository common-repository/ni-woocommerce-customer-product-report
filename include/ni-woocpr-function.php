<?php 
if ( !class_exists( 'Ni_WooCPR_Function' ) ) {
	class Ni_WooCPR_Function {
		var $options = array();
		function __construct() {
		}
		public function get_request($name,$default = NULL,$set = false){
			if(isset($_REQUEST[$name])){
				$newRequest = $_REQUEST[$name];
			
			if(is_array($newRequest)){
				$newRequest = implode(",", $newRequest);
			}else{
				$newRequest = trim($newRequest);
			}
			
			if($set) $_REQUEST[$name] = $newRequest;
			
			return $newRequest;
				}else{
					if($set) 	$_REQUEST[$name] = $default;
				return $default;
			}
		}
		function get_selected_columns(){
			$this->options	   = get_option('ni_woocpr_option');
			$selected_columns  = isset($this->options["ni_woocpr_columns"])?$this->options["ni_woocpr_columns"]:array();
			
			return $selected_columns;
		}
		function print_array($thing,$description=false){
			echo '<pre style="background:#fff; padding:10px; color:#111; font-family:monospace; font-size:12px; border:1px solid #555">';
			if($description) echo '<strong>'.$description.'</strong><br><br>';
			print_r($thing);
			echo '</pre>';
		}
		function daily_sales_report(){
			 $last_30_days = array();
		 	for ($i = 0; $i <= 30; $i++) {
				$date = date_i18n("Y-m-d", strtotime('-'. $i .' days'));;
				
				$last_30_days[$date] = $date;
			}
			 
			 //$this->print_data($last_30_days);
			  
			 global $wpdb;
			 $today = date_i18n("Y-m-d");
			 $query = " SELECT date_format( posts.post_date, '%Y-%m-%d') as order_date 
			 			,COUNT(*) as order_count 
						,SUM(ROUND(order_total.meta_value,2)) as 'order_total'
						";
			 $query .= " FROM {$wpdb->prefix}posts as posts ";
			 $query .= "	LEFT JOIN  {$wpdb->prefix}postmeta as order_total ON order_total.post_id=posts.ID ";
			 $query .= " WHERE 1=1 ";
			 $query .= " AND posts.post_type ='shop_order' ";
			 $query .= " AND order_total.meta_key ='_order_total' ";
			 $query .= " AND  date_format( posts.post_date, '%Y-%m-%d') BETWEEN date_format(DATE_SUB(CURDATE(), INTERVAL 30 DAY), '%Y-%m-%d') AND   '{$today}' 			";
			 
			$query .= "		AND posts.post_status IN ('wc-processing','wc-on-hold', 'wc-completed')";
			 
			 $query .= " GROUP BY date_format( posts.post_date, '%Y-%m-%d')  ";
			
			 $new_array= array();
			 $results = $wpdb->get_results( $query);		
			 //$this->print_data($results);
			  foreach( $results  as $key=>$value){
				 $new_array[$value->order_date]["order_date"] =$value->order_date;
				 $new_array[$value->order_date]["order_count"] =$value->order_count;
				 $new_array[$value->order_date]["order_total"] =$value->order_total;
			 }
			
			$i = 0;
			$graph_array = array();
			foreach( $last_30_days  as $key=>$value){
				if (isset($new_array[$value])){
					$graph_array[$i]["order_date"] = $new_array[$value]["order_date"]; 
					$graph_array[$i]["order_count"] =  "#".$new_array[$value]["order_count"]; 
					$graph_array[$i]["order_total"] =  $new_array[$value]["order_total"]; 
				}else{
					$graph_array[$i]["order_date"] = $value; 
					$graph_array[$i]["order_count"] = 0; 
					$graph_array[$i]["order_total"] =0; 
				}
				$i++;
				
			}
			return $graph_array ;
		 }	
		function get_ni_woocpr_columns(){
			$column =  array();
			/*Billing Details*/
			$column["order_id"]   			=__("Order ID","niwoocpr");
			$column["order_status"]   		=__("Order Status","niwoocpr");
			$column["order_date"]   		=__("Order Date","niwoocpr");
			
			//$column["product_name"]  		=__("Product Name","niwoocpr");
			
			$column["billing_first_name"]   =__("Billing First Name","niwoocpr");
			$column["billing_last_name"]  	=__("Billing Last Name","niwoocpr");
			$column["billing_company"]  	=__("Billing Company","niwoocpr");
			$column["billing_address_1"]  	=__("Billing Address 1","niwoocpr");
			$column["billing_address_2"]  	=__("Billing Address 2","niwoocpr");
			$column["billing_city"]  		=__("Billing City","niwoocpr");
			$column["billing_state"]  		=__("Billing State","niwoocpr");
			$column["billing_postcode"]  	=__("Billing Postcode","niwoocpr");
			$column["billing_country"]  	=__("Billing Country","niwoocpr");
			$column["billing_email"]  		=__("Billing Email","niwoocpr");
			$column["billing_phone"]  		=__("Billing Phone","niwoocpr");
			
			/*Shipping Details*/
			$column["shipping_first_name"]  =__("Shipping First Name","niwoocpr");
			$column["shipping_last_name"]  	=__("Shipping Last Name","niwoocpr");
			$column["shipping_company"]  	=__("Shipping Company","niwoocpr");
			$column["shipping_address_1"]  	=__("Shipping Address 1","niwoocpr");
			$column["shipping_address_2"]  	=__("Shipping Address 2","niwoocpr");
			$column["shipping_city"]  		=__("Shipping City","niwoocpr");
			$column["shipping_state"]  		=__("Shipping State","niwoocpr");
			$column["shipping_postcode"]  	=__("Shipping Postcode","niwoocpr");
			$column["shipping_country"]  	=__("Shipping Country","niwoocpr");
			
			/*Currency*/
			$column["order_currency"]  		=__("Order Currency","niwoocpr");
			$column["cart_discount"]  		=__("Cart Discount","niwoocpr");
			$column["cart_discount_tax"]  	=__("Cart Discount Tax","niwoocpr");
			$column["order_shipping"]  		=__("Order shipping","niwoocpr");
			$column["order_shipping_tax"]  	=__("Order Shipping tax","niwoocpr");
			$column["order_tax"]  			=__("Order Tax","niwoocpr");
			$column["order_total"]  		=__("Order Total","niwoocpr");
			
		
			
			
			//$column["order_product"]  		=__("order_product1","niwoocpr");
			
			return apply_filters('ni_woocpr_product_report_columns', $column );
			//return $column;
		}
		function get_top_customer_query(){
		    global $wpdb;
			$customer  =array();
			$query = "";
			$query .= " SELECT  ";
			$query .= " COUNT(*) as order_count  ";
			$query .= " ,SUM(ROUND(order_total.meta_value,2)) as 'order_total'";
			$query .= " , billing_email.meta_value  as billing_email ";
			$query .= " , billing_first_name.meta_value as billing_first_name  ";
			$query .= " FROM {$wpdb->prefix}posts as posts ";
			
			$query .= "	LEFT JOIN  {$wpdb->prefix}postmeta as order_total ON order_total.post_id=posts.ID ";
			$query .= "	LEFT JOIN  {$wpdb->prefix}postmeta as billing_email ON billing_email.post_id=posts.ID ";
			$query .= "	LEFT JOIN  {$wpdb->prefix}postmeta as billing_first_name ON billing_first_name.post_id=posts.ID ";
			
			
		    $query .= " WHERE 1=1 ";
			$query .= " AND posts.post_type ='shop_order' ";
			$query .= " AND order_total.meta_key ='_order_total' ";
			$query .= " AND billing_email.meta_key ='_billing_email' ";
			$query .= " AND billing_first_name.meta_key ='_billing_first_name' ";
			
			$query .= "		AND posts.post_status IN ('wc-processing','wc-on-hold', 'wc-completed')";
			
			 $query .= " GROUP BY billing_email.meta_value  ";
			 $query .= " ORDER BY order_total DESC";
			 
			  $query .= " LIMIT 10 ";
			
			$results = $wpdb->get_results( $query);
			//$this->print_array($results );	
			return $results; 
			 	
		}
		function get_top_product_query(){
		    global $wpdb;
			$customer  =array();
			$query = "";
			$query .= " SELECT  ";
			//$query .= " COUNT(*) as order_count  ";
			$query .= " SUM(ROUND(line_total.meta_value,2)) as 'line_total'";
			
			$query .= " ,SUM(qty.meta_value) as 'qty'";
			
			$query .= " , product_id.meta_value  as product_id ";
			$query .= " , variation_id.meta_value as variation_id  ";
			$query .= " , order_items.order_item_name as product_name  ";
			$query .= " FROM {$wpdb->prefix}posts as posts ";
			
			$query .= "	LEFT JOIN  {$wpdb->prefix}woocommerce_order_items as order_items ON order_items.order_id=posts.ID ";
			$query .= "	LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as product_id ON product_id.order_item_id=order_items.order_item_id ";
			$query .= "	LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as variation_id ON variation_id.order_item_id=order_items.order_item_id";
			
			$query .= "	LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as line_total ON line_total.order_item_id=order_items.order_item_id ";
			
			$query .= "	LEFT JOIN  {$wpdb->prefix}woocommerce_order_itemmeta as qty ON qty.order_item_id=order_items.order_item_id ";
			

			
			
		    $query .= " WHERE 1=1 ";
			$query .= " AND posts.post_type ='shop_order' ";
			$query .= " AND order_items.order_item_type ='line_item' ";
			$query .= " AND product_id.meta_key ='_product_id' ";
			$query .= " AND variation_id.meta_key ='_variation_id' ";
			$query .= " AND line_total.meta_key ='_line_total' ";
			$query .= " AND qty.meta_key ='_qty' ";

			
			$query .= "		AND posts.post_status IN ('wc-processing','wc-on-hold', 'wc-completed')";

			
			
			$query .= " GROUP BY  variation_id.meta_value, product_id.meta_value  ";
			$query .= " ORDER BY line_total DESC";
			 
			$query .= " LIMIT 10 ";
			
			$results = $wpdb->get_results( $query);
			//$this->print_array($results );	
			return $results; 
			 	
		}
		function get_top_customer_columns(){
			$column = array();
			$column["billing_first_name"] = "First Name";
			$column["billing_email"] = "Billing Email";
			$column["order_count"] = "Order Count";
			$column["order_total"] = "Order Total";			
			
			return apply_filters('ni_woocpr_top_customer_columns', $column );
		}
		function get_top_product_columns(){
			$column = array();
			$column["product_name"] = "Product Name";
			$column["qty"] = "Purchases Items";
			$column["line_total"] = "Line Total";
			return apply_filters('ni_woocpr_top_product_columns', $column );
		}
	}
}
?>