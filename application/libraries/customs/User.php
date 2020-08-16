<?php


namespace customs;
use customs\Serialiser;
require "Serialiser.php";

class User
{
	public $first_name;
	public $last_name;
	public $gender;
	public $email;
	public $contact;
	public $password;
	public $date_created;

	/**
	 * User constructor.
	 * @param $first_name
	 * @param $last_name
	 * @param $gender
	 * @param $email
	 * @param $contact
	 * @param $password
	 * @param $created_by
	 */
	public function __construct( $first_name , $last_name , $gender , $email , $contact , $password)
	{
		$this->first_name = $first_name;
		$this->last_name = $last_name;
		$this->gender = $gender;
		$this->email = $email;
		$this->contact = $contact;
		$this->password = Serialiser::passwordHash($password);
		$this->date_created = date("Y-m-d H:i:s");
	}


}

