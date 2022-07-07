<?php

namespace App\Services;

use App\Kernel\Ferramenta;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class BeneficioService {

	public static function insert($beneficio, $data) {
		try {
			$beneficio->beneficio_nome = $data['beneficio_nome'];
			$beneficio->beneficio_codigo = $data['beneficio_codigo'];
			$beneficio->beneficio_operadora = $data['beneficio_operadora'];
			$beneficio->beneficio_tipo = $data['beneficio_tipo'];
			$beneficio->beneficio_valor = Ferramenta::formatarDinheiro($data['beneficio_valor']);
			$beneficio->beneficio_dt_vencimento = Ferramenta::formatarData($data['beneficio_dt_vencimento']);
			$beneficio->save();
		} catch (Exception $ex) {
			$logger = new Logger('web');
			$logger->pushHandler(new StreamHandler(PATH_LOG, Logger::WARNING));
			$logger->warning('Não foi possível inserir a categoria!', $data);
		}
	}

	public static function update($beneficio, $data) {
		try {
			$beneficio->beneficio_nome = $data['beneficio_nome'];
			$beneficio->beneficio_codigo = $data['beneficio_codigo'];
			$beneficio->beneficio_operadora = $data['beneficio_operadora'];
			$beneficio->beneficio_tipo = $data['beneficio_tipo'];
			$beneficio->beneficio_valor = Ferramenta::formatarDinheiro($data['beneficio_valor']);
			$beneficio->beneficio_dt_vencimento = Ferramenta::formatarData($data['beneficio_dt_vencimento']);
			$beneficio->save();
		} catch (Exception $ex) {
			$logger = new Logger('web');
			$logger->pushHandler(new StreamHandler(PATH_LOG, Logger::WARNING));
			$logger->warning('Não foi possível editar a categoria!', $data);
		}
	}

}
