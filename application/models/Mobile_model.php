<?php


class Mobile_model extends CI_Model
{

	public function __construct()
	{
		parent::__construct();
	}
	public function add_vehicle($name, $license_plate)
	{
		$data = array(
			"name" => $name,
			"license_plate" => $license_plate,
			"date_reg" => date("Y-m-d H:i:s")
		);

		$this->db->insert("vehicles",$data);
		return $this->db->insert_id();
	}

	public function add_parking($vehicle, $created_by)
	{
		$data = array(
			"vehicle_id" => $vehicle,
			"created_by" => $created_by,
			"created_at" => date("Y-m-d H:i:s")
		);

		$this->db->insert("parking", $data);
		$id = $this->db->insert_id();
		return $id;
	}

	public function get_parking_detail($parkID){
		return $this->db->get_where("cars_parking",['park_id' => $parkID])->row_array();
	}

}
