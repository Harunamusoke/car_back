<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Api_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
	}

	public function get_vehicles_still_in()
	{
		return $this->db->query(" CALL CARS_STILL_IN();")->result_array();
	}

	public function get_all_vehicles($limit, $offset, $date_from, $date_to, $cleared = NULL)
	{
		$where = " `checked_at` >= '" . $date_from . "' AND `checked_at` <= '" . $date_to . "'";
		if ($cleared === FALSE)
			$where .= " AND `cleared` IS NULL;";
		else if ($cleared === TRUE)
			$where .= " AND `cleared` IS NOT NULL";

		$this->db->where($where);
		$this->db->limit($limit, $offset);
		return $this->db->get("cars_parking")->result_array();
	}

	public function get_all_users( $active = FALSE )
	{
		$this->db->select(
			" `u`.user_id, CONCAT( `u`.`first_name` , ' ' , `u`.`last_name`) AS `name` ,
			`u`.`gender` , `u`.`email` , `u`.`contact` , `u`.`permissions` , `u`.`is_active` AS `is active` ,
		    `u`.`date_created` AS `registered on` , CONCAT( `a`.`first_name` , ' ' , `a`.`last_name`)  AS `verified by`
		  ");
		$this->db->from('users u');
		$this->db->join('users a', 'u.created_by = a.user_id',"left");
		$this->db->where("`u`.`is_deleted`",0);
		if( $active === TRUE )
			$this->db->where(" `u`.`is_active`",1);
		return $this->db->get()->result_array();

	}

	public function add_rate(  $name, $rate, $from, $to, $is_enabled ){
		$rateData = array(
			"name" => $name,
			"rate" => $rate, "from" => $from,
			"to" => $to, "is_enabled" => $is_enabled, "date_added" => date("Y-m-d H:i:s")
		);
		$this->db->insert("rates",$rateData);
		return $this->db->insert_id();
	}

}
