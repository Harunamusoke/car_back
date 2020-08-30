<?php


class MModel extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->helper("mm");
	}
	public function add_vehicle($name, $license_plate)
	{
		$data = array(
			"name" => $name,
			"license_plate" => $license_plate,
			"date_reg" => date("Y-m-d H:i:s")
		);

		$this->db->insert("vehicles", $data);
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

	public function get_parking_detail($parkID)
	{
		return $this->db->get_where("cars_parking", ['park_id' => $parkID])->row_array();
	}

	public function finish_with_cash($parkid, $user, $amount)
	{
		// INSERT INTO `car_res_park`.`payments`
		// (`pay_id`,
		// `park_id`,
		// `sys_id`,
		// `number`,
		// `amount`,
		// `date_initiated`,
		// `date_completed`,
		// `external_ref`,
		// `status`)


		$data = array(
			"park_id" => $parkid,
			"amount" => $amount,
			"date_initiated" => date("Y-m-d H:i:s"),
			"date_completed" => date("Y-m-d H:i:s"),
			"status" => 1
		);

		$this->db->insert("payments", $data);

		return $this->exit_parking($parkid, $user, $amount);
	}

	public function finish_with_mm($parkid,  $amount, $number)
	{
		$ref = null;
		try {
			$ref = MM::deposit($number, $amount);
		} catch (Exception $ex) {
			return ["error" => "transcation could not be initiated", "value" => null];
		}

		$data = array(
			"park_id" => $parkid,
			"number" => $number,
			"amount" => $amount,
			"date_initiated" => date("Y-md H:i:s"),
			"external_ref" => $ref['external'],
			"status" => 0
		);
		$this->db->insert("payments", $data);
		return	["value" => $this->db->insert_id(), "error" => null];
	}

	public function confirm_tran($ref,  $payid)
	{
		$status = null;
		try {
			$status = MM::status($ref);
		} catch (\Throwable $th) {
			return ["error" => "transaction could not proceed", "value" => null];
		}

		if (strtolower($status['status']) == "pending")
			return ["error" => "transaction not confirmed yet", "value" => "pending"];

		if (strtolower($status['status']) == "failed") {
			$this->complete_payment($payid, 2);
			return ["error" => "transaction not confirmed yet", "value" => "failed"];
		}

		$this->complete_payment($payid, 1);
		return ["value" => 1, "error" => null];
	}

	public function exit_parking($parkid, $user, $amount)
	{
		$this->db->where(["park_id" => $parkid]);
		$data = array(
			"is_cleared" => 1,
			"amount" => $amount,
			"exit_by" => $user,
			"exit_at" => date("Y-m-d H:i:s")
		);
		$this->db->update("parking", $data);
	}

	public function complete_payment($payid, $status)
	{
		$array = array(
			"date_completed" => date("Y-m-d H:id:s"),
			"status" => $status
		);
		$this->db->where(["pay_id" => $payid]);
		$this->db->update("payments", $array);
	}
}
