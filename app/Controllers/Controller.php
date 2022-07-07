<?php

namespace App\Controllers;

class Controller {

	protected function load($view, $params = []) {
		$twig = new \Twig\Environment(new \Twig\Loader\FilesystemLoader('app/View/'));
		$twig->addGlobal('URL_BASE', URL_BASE);
		echo $twig->render($view . '.twig.php', $params);
	}

}
