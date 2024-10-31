<?php 
if ( !class_exists( 'Ni_WooCPR_Setting' ) ) {
	include_once("ni-woocpr-function.php");
	class Ni_WooCPR_Setting  extends Ni_WooCPR_Function{
		var $options = array();
		function __construct() {
		}
		function page_init(){
		$input_type = "text";
		$input_type = "hidden";	
		?>
        
        <form method="post" name="frm_woocpr_setting" id="frm_woocpr_setting">
        	<input type="<?php echo $input_type; ?>" name="action" value="ni_woocpr_action">
            <input type="<?php echo $input_type; ?>" name="sub_action" value="ni_woocpr_setting_save">
            <input type="<?php echo $input_type; ?>" name="page" value="<?php echo $this->get_request("page"); ?>" />
            <input type="submit" value="Save" class="wooreport_button2">
            <?php $this->get_column_table(); ?>
        </form>
        <div class="_ajax_woocpr_setting"></div>
        <?php
		}
		function get_column_table(){
			$selected_columns  = array();
			$this->options	   = get_option('ni_woocpr_option');
			
			$columns			   = $this->get_ni_woocpr_columns();
			$selected_columns  = $this->get_selected_columns();
			
			$columns = array_merge($columns,$selected_columns);
			//$this->print_array($selected_columns);
			//$people = array("Peter", "Joe", "Glenn", "Cleveland");
			//$this->niwoocust_print($people);
			//$this->print_array($columns);		
			?> <ul id="sortable"><?php	
			foreach($columns as $key=>$value){
				if (array_key_exists($key , $selected_columns)):
				 ?>
                 <li class="ui-state-default">
                 <input type="checkbox" name="ni_woocpr_columns[<?php echo $key ?>]" checked="checked" value="<?php echo $value ?>" ><?php echo $value; ?> 			</li>
                 <?php
				 else: 
				  ?>
                 <li class="ui-state-default">
                 <input type="checkbox" name="ni_woocpr_columns[<?php echo $key ?>]" value="<?php echo $value ?>" ><?php echo $value; ?>
                 </li>
                 <?php
				 endif;
			}	
			?></ul><?php
		}
		function page_ajax(){
			//$this->print_data($_REQUEST);
			update_option("ni_woocpr_option",$_REQUEST);		
			echo "Record Saved.";
		}		
	}
}