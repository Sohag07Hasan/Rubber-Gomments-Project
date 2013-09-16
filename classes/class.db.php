<?php
class RubberGommetsDb{
	
	var $table, $wpdb;
	
	function __construct(){
		global $wpdb;
		$this->table = $wpdb->prefix . 'rubber_gommets';
		$this->wpdb = $wpdb;
	}
	
	
	//synchronize the database. If tabe does not exists it will create one
	function sync_db(){
		$sql = "create table if not exists $this->table(
			i_d bigint not null auto_increment primary key,
			ID varchar(200) not null,
			GW varchar(200) not null,
			GD varchar(200) not null,
			OD varchar(200) not null,
			OT varchar(200) not null,
			ES varchar(200) not null,
			STD varchar(200) not null
		)";
		
		$this->wpdb->query($sql);
	}
	
	
	//insert a record
	function insert_record($info){
		if(empty($info['ID']) || empty($info['GD'])) return false;		
		$this->wpdb->insert($this->table, $info);
		return $this->wpdb->insert_id > 0 ? true : false;
	}
	
	
	//clear records
	function clear_previous_records(){
		return $this->wpdb->query("delete from $this->table");
	}
	
}