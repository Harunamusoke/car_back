<?php

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Mobile extends REST_Controller
{

	public function __construct()
	{
		parent::__construct();
		$this->load->model("mobile_model");
		$token = $this->input->get_request_header("X-PARK-USER");
		$this->validateUser($token);
	}

	public function add_post()
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
		$data = $this->input->post();
		$this->form_validation->set_data($data);
		if ($this->form_validation->run("vehicle") == FALSE) {

			$errors = $this->form_validation->error_array();

			if (isset($errors['license']) && strpos($errors['license'], "unique value") >= 0) {
				$id = $this->getVehicleId($data['license']);
				$this->processParking($id);
				return;
			}

			$this->response(["error" => $this->form_validation->error_string(), "data" => null]
				, REST_Controller::HTTP_BAD_REQUEST);
		}

		$id = $this->mobile_model->add_vehicle($data['name'], $data['license']);
		$this->processParking($id);

	}

	// with cash or mobile
	public function out_get()
	{
		//TODO :: CHECK FOR PAYMENT AND ACT ACCORDINGLY
		$type = null;
		if( !isset( $_GET['type'] ) )
			$type = "cash";
		else
			$type = $this->input->get("type");

		$detail = $this->park_get_status();
		if( $detail['pay'] == 0 ){
			$this->exit_parking( $detail['park_id'] , $this->id );
		}



		if( $type == "cash" )
			$this->proceed_with_cash(  );
		else $this->proceed_with_mm( );
	}

	public function status_get()
	{
		$detail = $this->park_get_status();

		$this->response($detail, 200);
	}

	private function processParking($vehicle)
	{
		$park = null;
		try {
			$park = $this->mobile_model->add_parking($vehicle, $this->id);
		} catch (Exception $exception) {
			$park = $this->response(["error" => "server error", "data" => null],
				REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
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
			$this->response(['error' => "vehicle not found.", "data" => null],
				REST_Controller::HTTP_BAD_REQUEST);
		}
	}

	private function validateUser($token)
	{
		$user = $this->checkToken($token);
		if (empty($user) || !is_int((int)$user))
			$this->response(['error' => "user not found.", "data" => null],
				REST_Controller::HTTP_BAD_REQUEST);
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
		return ($this->db->get_where("rates", $rule)->row_array())['rate'];
	}

	/**
	 * @return array mixed
	 */
	private function park_get_status()
	{
		if (!isset($_GET['token']))
			$this->response(['data' => null, "error" => "invalid token"], REST_Controller::HTTP_BAD_REQUEST);

		$token = $this->input->get("token");
		$token = $this->checkToken($token);

		if (!isset($token->park) || !isset($token->vehicle))
			$this->response(['data' => null, "error" => "invalid token"], REST_Controller::HTTP_BAD_REQUEST);


		$detail = $this->mobile_model->get_parking_detail($token->park);
		if (empty($detail))
			$this->response(["error" => "no vehicle parked", "data" => null], REST_Controller::HTTP_BAD_REQUEST);
		if (isset($detail['cleared']) && $detail['cleared'] !== null || $detail['exit_by'] !== null)
			$this->response(["error" => "token used and  vehicle already taken", "data" => $detail], REST_Controller::HTTP_BAD_REQUEST);

		$detail['time_taken'] = $this->calculate_time($detail['checked_at']);
		$detail['pay'] = $this->calculate_pay($detail['checked_at']);
		return $detail;
	}

	private function proceed_with_cash( $amount )
	{

	}

	private function proceed_with_mm($number)
	{

	}

	private function exit_parking($park_id , $id )
	{

	}

}
