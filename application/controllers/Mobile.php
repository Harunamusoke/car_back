<?php

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Mobile extends REST_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model("mModel");
		$token = $this->input->get_request_header("X-PARK-USER");
		$this->validateUser($token);
	}

	public function add_get()
	{

		if (isset($_GET['token'])) {
			$token = $this->input->get("token");

			$vehicle = $this->checkToken($token);
			if (!isset($vehicle->vehicle))
				$this->response(["error" => "invalid token", "data" => null], REST_Controller::HTTP_BAD_REQUEST);

			$this->processParking($vehicle);
			return;
		}

		$this->load->library('form_validation');
		$data = $this->input->get();
		$this->form_validation->set_data($data);
		if ($this->form_validation->run("vehicle") == FALSE) {

			$errors = $this->form_validation->error_array();

			if (isset($errors['license']) && strpos($errors['license'], "unique value") >= 0) {
				$id = $this->getVehicleId($data['license']);
				$this->processParking($id);
				return;
			}

			$this->response(
				["error" => $this->form_validation->error_string(), "data" => null],
				REST_Controller::HTTP_BAD_REQUEST
			);
		}

		$id = $this->mModel->add_vehicle($data['name'], $data['license']);
		$this->processParking($id);
	}

	// with cash or mobile
	public function out_get()
	{
		//TODO :: CHECK FOR PAYMENT AND ACT ACCORDINGLY
		$type = null;
		if (!isset($_GET['token']))
			$this->response(["error" => "", "data" => null], REST_Controller::HTTP_BAD_REQUEST);

		$token = $this->input->get("token");
		$token = $this->checkToken($token);
		if (!isset($token->vehicle))
			$this->response(["error" => "no such parked vehicle", "data" => null], REST_Controller::HTTP_BAD_REQUEST);

		if (!isset($_GET['type']))
			$type = "cash";
		else
			$type = $this->input->get("type");

		$detail = $this->park_get_status($token);
		if ($detail['pay'] == 0) {
			// $parkid, $user, $amount
			$this->mModel->exit_parking($token->park, $this->id, 0);
		}
		if ($type == "cash") {
			// $parkid, $user, $amount
			try {
				$this->mModel->finish_with_cash($token->park, $this->id, $detail['pay']);
				$this->response(["data" => "payment succesful , thank you", "error" => null], Rest_Controller::HTTP_OK);
			} catch (Exception $ex) {
				$this->response(["error" => "server error", "data" => null], Rest_Controller::HTTP_INTERNAL_SERVER_ERROR);
			}
		} else {

			if (!isset($_GET['number']))
				$this->response(["error" => "number is required", "data" => null], Rest_Controller::HTTP_BAD_REQUEST);

			$number = $this->input->get("number");
			if (strlen($number != 12))
				$this->response(["error" => "number is not a tel number", "data" => null], Rest_Controller::HTTP_BAD_REQUEST);

			//$parkid, $user, $amount, $number
			$pay = $this->mModel->finish_with_mm($token->park, $detail['pay'], $number);
			if ($pay['error'] != null)
				$this->response(["error" => $pay['"error'], "wait" => null], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);

			$wait = array(
				"pay" => $pay,
				"user" => $this->id
			);
			$this->response(["data" => AUTHORIZATION::generateToken($wait), "error" => null], Rest_Controller::HTTP_CREATED);
		}
	}

	public function status_get()
	{
		if (!isset($_GET['token']))
			$this->response(['data' => null, "error" => "invalid token"], REST_Controller::HTTP_BAD_REQUEST);

		$token = $this->input->get("token");
		$token = $this->checkToken($token);

		if (!isset($token->park) || !isset($token->vehicle))
			$this->response(['data' => null, "error" => "invalid token"], REST_Controller::HTTP_BAD_REQUEST);


		$detail = $this->park_get_status($token);

		$this->response($detail, 200);
	}

	public function confirm_get()
	{
		if (!isset($_GET['token']))
			$this->response(["error" => "no resource to confirm", "data" => null], Rest_Controller::HTTP_BAD_REQUEST);

		$token = $this->checkToken($this->input->get("token"));
		if (!isset($token->pay))
			$this->response(["error" => "unknown token, please try again with a valid one", "data" => null], Rest_Controller::HTTP_BAD_REQUEST);

		if (!isset($token->user))
			$this->response(["error" => "unknown token, please try again with a valid one", "data" => null], Rest_Controller::HTTP_BAD_REQUEST);
		if ($this->id != $token->user)
			$this->response(["error" => "unknown token, please try again with a valid one", "data" => null], Rest_Controller::HTTP_BAD_REQUEST);

		$wait = $this->check_wait_token($token->pay);
		$confirm = $this->mModel->confirm_tran($wait['ref'], $token->pay);

		if ($confirm["value"] == null && $confirm['error'] != null)
			$this->response(["error" => $confirm['error'], "data" => null], Rest_Controller::HTTP_INTERNAL_SERVER_ERROR);

		if ($confirm['error'] != null)
			$this->response(["error" => $confirm['error'], "data" => $confirm['value']], Rest_Controller::HTTP_OK);

		$this->response(["data" => "transaction succesful", "error" => null], Rest_Controller::HTTP_OK);
	}

	private function processParking($vehicle)
	{
		$park = null;
		try {
			$park = $this->mModel->add_parking($vehicle, $this->id);
		} catch (Exception $exception) {
			$park = $this->response(
				["error" => "server error", "data" => null],
				REST_Controller::HTTP_INTERNAL_SERVER_ERROR
			);
		}

		$token = AUTHORIZATION::generateToken(array(
			"vehicle" => $vehicle,
			"park" => $park
		));

		$this->response(["data" => ["token" => $token], "error" => null], REST_Controller::HTTP_OK);
	}

	private function checkToken($token)
	{
		try {
			$validateToken = AUTHORIZATION::validateToken($token);
			return $validateToken;
		} catch (Exception $exception) {
			$this->response(
				['error' => "vehicle not found.", "data" => null],
				REST_Controller::HTTP_BAD_REQUEST
			);
		}
	}

	private function validateUser($token)
	{
		$user = $this->checkToken($token);
		if (empty($user) || !is_int((int)$user))
			$this->response(
				['error' => "user not found.", "data" => null],
				REST_Controller::HTTP_BAD_REQUEST
			);
		$this->id = $user;
	}

	/**
	 * @param string $data
	 */
	private function getVehicleId($license)
	{
		$this->db->select("vehicle_id");
		return ($this->db->get_where("vehicles", ["license_plate" => $license])->row_array())['vehicle_id'];
	}

	private function calculate_time($checked_at)
	{
		$now = strtotime("now");
		$checked = strtotime($checked_at);

		return ((abs($now - $checked) / 60) / 60);
	}

	private function calculate_pay($checked_at)
	{
		$time = $this->calculate_time($checked_at);
		//		$rate = $this->db->query("
		//			SELECT * FROM car_res_park.rates WHERE (`rates`.`from` <=".$time." AND `rates`.`to` > ".$time.")
		//			 AND `rates`.`is_enabled` = 1 LIMIT 1;
		//			")->row_array();

		$rule = array(
			"from <=" => $time,
			"to >" => $time
		);
		$this->db->select("rate");
		$rate = $this->db->get_where("rates", $rule)->row_array();
		if (empty($rate))
			$this->response(["error" => "no rate designed for you", "data" => null, "msg" => "no rate designed for you"], REST_Controller::HTTP_NOT_FOUND);

		return $rate['rate'];
	}

	/**
	 * @return array mixed
	 */
	private function park_get_status($token)
	{

		$detail = $this->mModel->get_parking_detail($token->park);

		if (empty($detail))
			$this->response(["error" => "no vehicle parked", "data" => null], REST_Controller::HTTP_BAD_REQUEST);
		if (isset($detail['cleared']) && $detail['cleared'] !== null || $detail['exit_by'] !== null)
			$this->response(["error" => "token used and  vehicle already taken", "data" => $detail], REST_Controller::HTTP_BAD_REQUEST);

		$detail['time_taken'] = $this->calculate_time($detail['checked_at']);
		$detail['pay'] = $this->calculate_pay($detail['checked_at']);
		return $detail;
	}

	private function check_wait_token($pay)
	{
		$this->db->select("pay_id,park_id,external_ref AS ref , status");
		$data = $this->db->get_where("payments", ["pay_id" => $pay, "status" => 0])->row_array();
		if (empty($data))
			$this->response(["error" => "no such waiting payment", "data" => null], 400);

		return $data;
	}
}
