<?php
defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . "libraries/customs/User.php";

use customs\User;

class UserModel extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * @param $firstname
	 * @param $lastname
	 * @param $email
	 * @param $gender
	 * @param $password
	 * @param $contact
	 * @return bool
	 * @throws Exception
	 */

	public function insert($firstname, $lastname, $email, $gender, $password, $contact)
	{
		$user = new User($firstname, $lastname, $gender, $email, $contact, $password);
		if ($this->db->insert("users", $user))
			return true;
		else throw new Exception("Failed to insert");
	}

	public function get_user_email( $email ){
		$data = array(
			"email" => $email
		);

		$this->db->select("
		`user_id` , `is_active` ,CONCAT(`first_name`, ' ' , `last_name`) AS `name` , `email` , `contact` , `password`");
		return $this->db->get_where("users", $data )->row_array();
	}

}

/* End of file .php */
