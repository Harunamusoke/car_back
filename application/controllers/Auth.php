<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';
require APPPATH . '/libraries/Format.php';

class Auth extends REST_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->library("form_validation");
		$this->load->model("userModel");
	}

	public function signup_post()
	{
		$data = $this->input->post();
		if (empty($data))
			$this->response(
				['user' => null, "error" => 'bad request'],
				REST_Controller::HTTP_BAD_REQUEST
			);

		$data = $this->clean($data);

		if ($this->form_validation->run("signup") == FALSE)
			$this->response(
				['error' =>  $this->form_validation->error_string(), 'user' => null],
				REST_Controller::HTTP_BAD_REQUEST
			);

		try {
			$this->userModel->insert(
				$data['firstname'],
				$data['lastname'],
				$data['email'],
				$data['gender'],
				$data['password'],
				$data['contact']
			);
		} catch (Exception $exception) {
			$this->response(
				["error" => $exception->getMessage(), "user" => null],
				REST_Controller::HTTP_INTERNAL_SERVER_ERROR
			);
		}

		$this->response(
			array(
				"user" =>
				$data['firstname'] . " " . $data['lastname'] . " created successfully. Waiting for verification",
				"error" => null
			),
			REST_Controller::HTTP_CREATED
		);
	}

	// http://[domain]/park/auth/login?password=[password]&email=[email]
	public function login_get()
	{
		if (isset($_GET['user']))
			return $this->user_session($this->input->get("user"));

		$data = $this->input->get();
		if (empty($data))
			$this->response(['error' => 'bad request', "user" => null], REST_Controller::HTTP_BAD_REQUEST);

		$data = $this->clean($data);
		$this->form_validation->set_data($data);

		if ($this->form_validation->run("login") == FALSE)
			$this->response(["error" => $this->form_validation->error_string(), "user" => null], REST_Controller::HTTP_BAD_REQUEST);

		$user = $this->userModel->get_user_email($data['email']);
		if (empty($user) || empty($user['user_id']))
			$this->response(["error" => "not found", 'user' => null], REST_Controller::HTTP_NOT_FOUND);

		if ((int) $user['is_active'] === 0)
			$this->response(
				["error" => "not verified yet. user wait for verify.", "user" => null],
				REST_Controller::HTTP_FORBIDDEN
			);

		if (!$this->checkPassword($data['password'], $user['password']))
			$this->response(['error' => "password mismatch", 'user' => null], REST_Controller::HTTP_NOT_FOUND);

		$this->response(
			["user" => array(
				"id" => AUTHORIZATION::generateToken($user['user_id']),
				"name" => $user['name'],
				"email" => $user['email'],
				"contact" => $user['contact']
			), "error" => null],
			REST_Controller::HTTP_OK
		);
	}

	private function clean(array $data)
	{
		foreach ($data as $key => $value)
			$data[$key] = $this->security->xss_clean($value);

		return $data;
	}

	// http://localhost/park/auth/user_session?user=[token]
	private function user_session($session_token)
	{

		if ($session_token == null)
			$this->response(['errors' => "user token failed", "user" => null]);

		$user = null;
		try {
			$user = AUTHORIZATION::validateToken($session_token);
		} catch (Exception $ex) {
			$this->response(["error" => $ex->getMessage(), "user" => null], REST_Controller::HTTP_NOT_FOUND);
		}

		$this->response(["user" => AUTHORIZATION::generateToken($user)], REST_Controller::HTTP_OK);
	}

	/**
	 * @param $password:Password
	 * @
	 * @param $hash:Hash
	 * @return bool
	 */
	private function checkPassword($password, $hash)
	{
		return (md5($password) === $hash);
	}
}
