<?php

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Api extends REST_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model("api_model");
		$token = $this->input->get_request_header("X-PARK-USER");
		if ($this->check_user($token) == FALSE) {
			$this->response(
				['error' => "user not found", "data" => null]
				, REST_Controller::HTTP_BAD_REQUEST);
		}
	}

	private function check_user($token)
	{
		$user = "";
		try {
			$user = AUTHORIZATION::validateToken($token);
		} catch (Exception $ex) {
			return FALSE;
		}
		return $user;
	}

	//api/users/[limit = 20 ]/[offset = 0]?id=[user_id]?is_active=[true/]
	public function users_get()
	{
		$active = $this->input->get();

		if (!isset($active['active'])) {
			try {
				$data = $this->api_model->get_all_users();
				$this->response(['data' => $data, "error" => null], REST_Controller::HTTP_OK);
			} catch (Exception $ex) {
				$this->response(['error' => "internal error", "data" => null], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
			}
		}

		if ((boolean) $active['active'] == FALSE) {
			try {
				$data = $this->api_model->get_all_users(false);
				$this->response(['data' => $data, "error" => null], REST_Controller::HTTP_OK);
			} catch (Exception $ex) {
				$this->response(['error' => "internal error", "data" => null], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
			}
		}

		try {
			$data = $this->api_model->get_all_users(true);
			$this->response(['data' => $data, "error" => null], REST_Controller::HTTP_OK);
		} catch (Exception $ex) {
			$this->response(['error' => "internal error", "data" => null], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
		}

	}

	//api/vehicles/[limit =  100]/[ offset = 0 ]
	//?id=[car_id]&date-from=[date = today]&date-to=[ date =today ]&type[ all , cleared , not-cleared ]
	public function vehicles_get()
	{
		$options = $this->input->get();

		if (!isset($options['status']) || $options['status'] == "not-cleared") {
			try {
				$data = $this->api_model->get_vehicles_still_in();
				$this->response(["data" => $data, "error" => null], REST_Controller::HTTP_OK);

			} catch (Exception $ex) {
				$this->response(['error' => "fetch failed", "data" => null], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
			}
		}

		$limit = $this->uri->segment(3, 100);
		$offset = $this->uri->segment(4, 0);
		$date_from = "";
		$date_to = "";

		try {
			$date_from = isset($options['date_from']) ? $options['date_from'] :
				$this->create_date("today midnight");

			$date_to = isset($options['date_to']) ? $options['date_to'] :
				$this->create_date("tomorrow midnight");
		} catch (Exception $ex) {
			$this->response(["data" => null, "error" => "invalid date"], REST_Controller::HTTP_BAD_REQUEST);
		}

		if ($options['status'] == "all") {
			try {
				$data = $this->api_model->get_all_vehicles($limit, $offset, $date_from, $date_to);
				$this->response(["data" => $data, "error" => null], REST_Controller::HTTP_OK);
			} catch (Exception $ex) {
				$this->response(["error" => "internal error", "data" => null], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
			}
		}

		try {
			$data = $this->api_model->get_all_vehicles($limit, $offset, $date_from, $date_to, TRUE);
			$this->response(['data' => $data, "error" => null], REST_Controller::HTTP_OK);
		} catch (Exception $ex) {
			$this->response(["error" => "internal error", "data" => null], REST_Controller::HTTP_INTERNAL_SERVER_ERROR);
		}

		$this->response(["error" => "invalid format", "data" => null], REST_Controller::HTTP_NOT_FOUND);
	}

	private function create_date($string)
	{
		return date("Y-m-d H:i:s", strtotime($string));
	}
}
