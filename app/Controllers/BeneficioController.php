<?php

namespace App\Controllers;

use App\Models\Beneficio;
use App\Services\BeneficioService;

class BeneficioController extends Controller {

	public function __construct($router) {
		$this->router = $router;
	}

	public function dashboard() {
		$beneficio = new Beneficio;
		$beneficios = $beneficio->find()->order('beneficio_nome')->fetch(true);
		$this->load('beneficios/lstBeneficio', ['beneficios' => $beneficios]);
	}

	public function create() {
		$this->load('beneficios/frmBeneficio');
	}

	public function edit($data) {
		$beneficio = (new Beneficio)->findById($data['id']);
		$this->load('beneficios/frmBeneficio', ['beneficio' => $beneficio]);
	}

	public function insert($data) {
		BeneficioService::insert(new Beneficio, $data);
		$this->router->redirect('beneficio.dashboard');
	}

	public function update($data) {
		$beneficio = (new Beneficio)->findById($data['id']);
		BeneficioService::update($beneficio, $data);
		$this->router->redirect('beneficio.dashboard');
	}

	public function delete($data) {
		$beneficio = (new Beneficio)->findById($data['id']);
		$beneficio->destroy();
		$this->router->redirect('beneficio.dashboard');
	}

}
