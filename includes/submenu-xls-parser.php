<?php 
	//global $rubber_gommets;
	
	if(!empty($_FILES['rubber-gomments-xls'])){
				
		$php_excel = $this->get_php_excel($_FILES['rubber-gomments-xls']['tmp_name']);
		
		$xlsx = $php_excel->getActiveSheet()->toArray(null,true,true,true);

		$imported = 0;
		$skipped = 0;
		$this->db = $this->get_db();
		
		//clearing previous records
		$this->db->clear_previous_records();
		
		foreach($xlsx as $key => $xl){
			
			if($key == 1){
				continue;
			}

			$post = array(
						'ID' => !empty($xl['A']) ? $xl['A'] : '',
						'GW' => !empty($xl['B']) ? $xl['B'] : '',
						'GD' => !empty($xl['C']) ? $xl['C'] : '',
						'OD' => !empty($xl['D']) ? $xl['D'] : '',
						'OT' => !empty($xl['E']) ? $xl['E'] : '',
						'ES' => !empty($xl['F']) ? $xl['F'] : '',
						'STD' => !empty($xl['G']) ? $xl['G'] : '',
					);
			
			if($this->db->insert_record($post)){
				$imported ++;
			}
			else{
				$skipped ++;
			}
		}
		
	}
?>

<div class="wrap">
	<h2>Rubber Gommets Drop Down contents</h2>
	
	<?php if(!empty($_FILES['rubber-gomments-xls'])): ?>
		<div class="updated">
			<p>Imported: <?php echo $imported; ?> and skipped: <?php echo $skipped; ?></p>
		</div>
	<?php endif; ?>
	
	<p>Upload a xls sheet. Make sure the colums shoud be exaclty same order as mentioned</p>
	<strong>  </strong>	ID - Inner Diameter, GW - Groove Width, GD - Groove Diameter, OD - Outer Diameter, O.T., ES P/N, STD. P/N
	
	<form action="" method="post" enctype="multipart/form-data">
		<p> 
			<input type="file" name="rubber-gomments-xls" /> 
			<input type="submit" name="submit" value="Upload" class="button button-primary" />
		</p>
	</form>
	
</div>