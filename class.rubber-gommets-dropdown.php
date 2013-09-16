<?php
/*
 * Plugin Name: Rubber Gommets Dropdown
 * Author: Mahibul Hasan
 * */

class RubberGommetsDropDown{
	
	private $db, $xls_parser;
	
	public $keys = array(
						'ID' => 'ID - Inner Diameter',
						'GW' => 'GW - Groove Width',
						'GD' => 'GD - Groove Diameter',
						'OD' => 'OD - Outer Diameter',
						'OT' => 'O.T',
						'ES' => 'ES P/N',
						'STD' => 'STD. P/N',
					);
	
	function __construct(){
				
		//admin menu
		add_action('admin_menu', array(&$this, 'admin_menu'), 100);
		
		//plugin activated
		register_activation_hook(__FILE__, array(&$this, 'during_activation'));
		
		//shortcode
		add_shortcode('rubber_gommets', array(&$this, 'parse_shortcode'));
		
		//script inclusion
		//add_action('wp_enqueue_scripts', array(&$this, 'enqueue_scripts'));
		add_action('wp_head', array(&$this, 'enqueue_scripts'));
	}
	
	
	//admin menu
	function admin_menu(){
		add_submenu_page('manage-product', 'Rubber gommets Drop Down', 'Rubber Gommets', 'manage_options', 'rubber-gommets-dropdown', array(&$this, 'submenu_page_content'));
	}
	
	
	//submenu page content
	function submenu_page_content(){
		include $this->get_base_dir() . 'includes/submenu-xls-parser.php';
	}
	
	
	//plugins' base directory including slash
	function get_base_dir(){
		return dirname(__FILE__) . '/';
	}
	
	
	//plugin's base url including slash
	function get_base_uri(){
		return plugins_url('/', __FILE__);
	}
	
	
	//during activation
	function during_activation(){
		$this->db = $this->get_db();
		return $this->db->sync_db();
	}

	
	//db instance
	function get_db(){
		if($this->db) return $this->db;
		
		include $this->get_base_dir() . 'classes/class.db.php';
		$this->db = new RubberGommetsDb();
		return $this->db;
	}
	
	
	//get csv parser
	function get_php_excel($file = ''){
		if($this->xls_parser) return $this->xls_parser;

		include $this->get_base_dir() . 'classes/phpexcel/PHPExcel/IOFactory.php';
		try {		
			$this->xls_parser = PHPExcel_IOFactory::load($file);
		}
		catch(Exception $e){
			die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
		}
		return $this->xls_parser;
	}
	
	
	//parse shortcode
	function parse_shortcode(){
		global $post;
		$permalink = get_permalink($post->ID); 
		$drop_down = "<form action='' method='post'> <table>";
		foreach($this->keys as $key => $title){
			$drop_down .= "<tr>
				<td>$title</td>
				<td> " . $this->get_form_field($key, $title) . " </td>
			</tr>";
		}
		
		$drop_down .= "</table> <p><a href='$permalink'>Reset</a> &nbsp; <input type='submit' value='View All Parts '> </p> </form>";
		
		//$drop_down .= $this->get_submitted_response();
		
		return $drop_down;
	}
	
	
	//get form field
	function get_form_field($key, $title){
		
		$field = "<select class='drop-down-select' key='$key' name='dropdown[$key]'>";
		
		$options = $this->get_options($key);
		$field .= '<option value="">Any</option>';
		if($options){
			foreach($options as $opt){
				//var_dump($_REQUEST[$key]);
				$field .= '<option ' . selected($opt, $_REQUEST[$key], false) . ' value="'.$opt.'">' . $opt . '</option>';
			}
		}
		
		$field .= "</select>";
		
		return $field;
	}
	
	
	//get options using differnt query
	function get_options($key){
		$this->db = $this->get_db();
		$table = $this->db->table;
		$sql = "select distinct $key from $table";
		if(isset($_REQUEST)){
			$set_keys = array();
			foreach($this->keys as $k => $l){
				if(isset($_REQUEST[$k]) && !empty($_REQUEST[$k])){
					if($k == $key) continue;
					$set_keys[$k] = $_REQUEST[$k];
				}
			}

			if(count($set_keys) > 0){
				$sql_new = array();
				foreach($set_keys as $k => $value){					
					$sql_new[] = "$k = '$value'";
				}
				
				//var_dump($sql_new);
				
				$sql .= " where " . implode(' and ', $sql_new);
			}
		}
		
		//var_dump($sql);
		
		$this->db = $this->get_db();
		$col = $this->db->wpdb->get_col($sql);
		return $col;
	}
	
	
	//if form is submitted, it will return a string
	function get_submitted_response(){
		$result = '';
		if(!(empty($_POST['dropdown']))){
			$this->db = $this->get_db();
			$table = $this->db->table;
			$sql = "select * from $table where";
			
			foreach($_POST['dropdown'] as $key => $value){
				$sql .= " $key = '$value'";
			}
			
			$results = $this->db->wpdb->get_results($sql);
			
			if($results){
				$result .= '<table>';
				foreach($results as $r){
					$result .= "";
				}
			}
		}
	}
	
	
	//scripts inclusion
	function enqueue_scripts(){
		//wp_enqueue script own't work with the them. So we have find a duplicate solution
		return $this->modified_enqueue_scripts();
		/*
		wp_register_script('url-library-script', $this->get_base_uri() . '/js/url.js', array('jquery'));
		wp_enqueue_script('url-library-script');
		
		wp_register_script('drop-down-script', $this->get_base_uri() . '/js/drop-down.js', array('jquery'));
		wp_enqueue_script('drop-down-script');
		
		wp_register_style('drop-down-style', $this->get_base_uri() . 'css/drop-down.css');
		wp_enqueue_style('drop-down-style');
		*/
	}
	
	
	//scripts inclusion
	function modified_enqueue_scripts(){
		
		echo '<script type="text/javascript" src="' . $this->get_base_uri() . 'js/url.js' . '"></script>';
		echo '<script type="text/javascript" src="' . $this->get_base_uri() . 'js/drop-down.js' . '"></script>';
		echo '<link href="' . $this->get_base_uri() . 'css/drop-down.css" >';
				
	}
	
	
}

global $rubber_gommets;
$rubber_gommets = new RubberGommetsDropDown();