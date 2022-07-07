<?php

namespace App\Models;

use CoffeeCode\DataLayer\DataLayer;

class Beneficio extends DataLayer {

	public function __construct() {
		parent::__construct('beneficios', ['beneficio_nome', 'beneficio_codigo'], 'beneficio_id', false);
	}

}
