<?php
$config = array(
	"signup" => array(
		array(
			"field" => "firstname",
			"label" => "firstname",
			"rules" => "required|alpha"
		),
		array(
			"field" => "lastname",
			"label" => "lastname",
			"rules" => "required|alpha"
		),
		array(
			"field" => "gender",
			"label" => "sex",
			"rules" => "required|alpha|exact_length[1]"
		),
		array(
			"field" => "email",
			"label" => "email",
			"rules" => "required|valid_email|is_unique[users.email]",
			"errors" => array(
				"is_unique" => "email already exists."
			)
		),
		array(
			"field" => "contact",
			"label" => "contact",
			"rules" => "required|numeric|min_length[9]|is_unique[users.contact]",
			"errors" => array(
				"is_unique" => "contact already exists."
			)
		),
		array(
			"field" => "password",
			"label" => "password",
			"rules" => "required"
		)
	),
	"login" => array(
		array(
			'field' => 'email',
			'label' => 'email',
			'rules' => 'required|valid_email'
		),
		array(
			"field" => "password",
			"label" => "password",
			"rules" => "required"
		)
	)
);
