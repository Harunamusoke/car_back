<?php


namespace customs;


class Serialiser
{

	/**
	 * @param string Password
	 * @returns string Hash
	 */
	public static function passwordHash($password){
		return md5($password);
}

}
