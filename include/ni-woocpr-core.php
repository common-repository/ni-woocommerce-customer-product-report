<?php 
if ( !class_exists( 'Ni_WooCPR_Core' ) ) {
	class Ni_WooCPR_Core {
		function __construct() {
			$this->add_core_page();	
			add_action('admin_menu', 		array($this,'admin_menu'));	
			add_action( 'admin_enqueue_scripts',  array(&$this,'admin_enqueue_scripts' ));
			add_action( 'wp_ajax_ni_woocpr_action',  array(&$this,'ni_woocpr_action' )); /*used in form field name="action" value="my_action"*/
		}
		function admin_enqueue_scripts(){
			global $post;
			$page  = isset($_REQUEST["page"])?$_REQUEST["page"]:"";
	
			if ($page =="ni-woocpr-setting"){
				wp_enqueue_script('ni-woocpr-setting', plugins_url( '../admin/js/ni-woocpr-setting.js', __FILE__ ), array('jquery') );	
			}
			if (isset($post->post_type)) :
				if ($post->post_type=="product"):
					wp_enqueue_script( 'ni-woocpr-product', plugins_url( '../admin/js/ni-woocpr-product.js', __FILE__ ), array('jquery') );
					
					wp_enqueue_script( 'ni-woocpr-datatables',     plugins_url('../admin/js/dataTables/jquery.dataTables.min.js', __FILE__ ) );
					wp_enqueue_script( 'ni-woocpr-datatables_02',  plugins_url('../admin/js/dataTables/dataTables.buttons.min.js', __FILE__ ) );
					wp_enqueue_script( 'ni-woocpr-datatables_03',  plugins_url('../admin/js/dataTables/jszip.min.js', __FILE__ ) );
					wp_enqueue_script( 'ni-woocpr-datatables_04',  plugins_url('../admin/js/dataTables/pdfmake.min.js', __FILE__ ) );
					wp_enqueue_script( 'ni-woocpr-datatables_05',  plugins_url('../admin/js/dataTables/vfs_fonts.js', __FILE__ ) );
					wp_enqueue_script( 'ni-woocpr-datatables_06',  plugins_url('../admin/js/dataTables/buttons.html5.min.js', __FILE__ ) );
					
					wp_register_style('ni-woocpr-datatables-css', plugins_url('../admin/css/dataTables/jquery.dataTables.min.css', __FILE__ ) );
					wp_enqueue_style('ni-woocpr-datatables-css' );
					
					wp_register_style('ni-woocpr-datatables-css_02', plugins_url('../admin/css/dataTables/buttons.dataTables.min.css', __FILE__ ) );
					wp_enqueue_style('ni-woocpr-datatables-css_02' );
				endif;
			endif;
			
			if ( $page =="ni-woocpr"){
				wp_enqueue_script( 'ajax-ni-woocpr-dashboard', plugins_url('../admin/js/ni-woocpr-dashboard.js', __FILE__ ), array('jquery') );
				
				
				
			}
			if ($page =="ni-woocpr-setting" || $page =="ni-woocpr"){
				
					wp_register_style( 'nidsrfw-font-awesome-css', plugins_url( '../admin/css/font-awesome.css', __FILE__ ));
		 			wp_enqueue_style( 'nidsrfw-font-awesome-css' );
					
					wp_register_script( 'nidsrfw-amcharts-script', plugins_url( '../admin/js/amcharts/amcharts.js', __FILE__ ) );
					wp_enqueue_script('nidsrfw-amcharts-script');
				
		
					wp_register_script( 'nidsrfw-light-script', plugins_url( '../admin/js/amcharts/light.js', __FILE__ ) );
					wp_enqueue_script('nidsrfw-light-script');
				
					wp_register_script( 'nidsrfw-pie-script', plugins_url( '../admin/js/amcharts/pie.js', __FILE__ ) );
					wp_enqueue_script('nidsrfw-pie-script');
					
					wp_register_style('nidsrfw-bootstrap-css', plugins_url('../admin/css/lib/bootstrap.min.css', __FILE__ ));
		 			wp_enqueue_style('nidsrfw-bootstrap-css' );
				
					wp_enqueue_script('nidsrfw-bootstrap-script', plugins_url( '../admin/js/lib/bootstrap.min.js', __FILE__ ));
					wp_enqueue_script('nidsrfw-popper-script', plugins_url( '../admin/js/lib/popper.min.js', __FILE__ ));
				
				
				
				wp_register_style('ni-woocpr-style', plugins_url('../admin/css/ni-woocpr.css', __FILE__ ));
				wp_enqueue_style('ni-woocpr-style' );
				
				wp_enqueue_script( 'ajax-ni-woocpr', plugins_url('../admin/js/script.js', __FILE__ ), array('jquery') );
				wp_localize_script( 'ajax-ni-woocpr', 'ni_woocpr_object',array( 'ni_woocpr_ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
			}
			
		
		}
		function add_core_page(){
			include_once("ni-woocpr-product.php");
			$core = new Ni_WooCPR_Product();
		}
		function admin_menu(){
		add_menu_page(__(  'Product Customer', 'ni-woocpr')
			,__(  'Product Customer', 'ni-woocpr')
			,'manage_options'
			,'ni-woocpr'
			,array(&$this,'add_page')
			,'dashicons-media-document'
			,59.14);
			add_submenu_page('ni-woocpr'
			,__( 'Dashboard', 'ni-woocpr' )
			,__( 'Dashboard', 'ni-woocpr' )
			,'manage_options'
			,'ni-woocpr' 
			,array(&$this,'add_page'));
			
			add_submenu_page('ni-woocpr'
			,__( 'Setting', 'niwoocpr' )
			,__( 'Setting', 'ni-woocpr' )
			,'manage_options'
			,'ni-woocpr-setting' 
			,array(&$this,'add_page'));
		
		
		}
		function ni_woocpr_action(){
			$sub_action   = isset($_REQUEST["sub_action"])?$_REQUEST["sub_action"]:"";
			if ($sub_action =="ni_woocpr_setting_save"){
				include_once("ni-woocpr-setting.php");
				$obj = new Ni_WooCPR_Setting();
				$obj->page_ajax();
			}
			//echo json_encode($_REQUEST);
			wp_die();
			
		}
		function add_page(){
			$page  = isset($_REQUEST["page"])?$_REQUEST["page"]:"";
			
			if ($page =="ni-woocpr"){
				include_once("ni-woocpr-dashboard.php");
				$onj = new Ni_WooCPR_Dashboard();
				$onj->page_init();
			}
			if ($page =="ni-woocpr-setting"){
				include_once("ni-woocpr-setting.php");
				$obj = new Ni_WooCPR_Setting();
				$obj->page_init();
			}
			wp_die();
		}
	}
}
?>