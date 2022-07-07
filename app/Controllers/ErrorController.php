<?php

namespace App\Controllers;

class ErrorController extends Controller {

	public function notFound() {
		$this->load('error/404');
	}

}
