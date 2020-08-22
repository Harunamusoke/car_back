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
	),
	"rates" => array(
		array(
			"field" => "name",
			"label" => "name",
			"rules" => "required|alpha_numeric|is_unique[rates.name]"
		),
		array(
			"field" => "rate",
			"label" => "rate",
			"rules" => "numeric"
		),
		array(
			"field" => "from",
			"label" => "start",
			"rules" => "numeric"
		),
		array(
			"field" => "to",
			"label" => "end",
			"rules" => "numeric"
		),
		array(
			"field" => "is_enabled",
			"label" => "status",
			"rules" => "required|exact_length[1]|numeric"
		)
	),
	"vehicle" => array(
		array(
			"field" => "license",
			"label" => "license",
			"rules" => "required|alpha_numeric|is_unique[vehicles.license_plate]"
		),
		array(
			"field" => "name",
			"label" => "name",
			"rules" => "required|alpha_numeric"
		)
	)
);
