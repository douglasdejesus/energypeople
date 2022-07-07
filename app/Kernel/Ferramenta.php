<?php

namespace App\Kernel;

class Ferramenta {

	public $salvarNoLog = false;
	private $tab = array();
	public $log = array();
	public $dados = array();
	protected $parametroAdicional = array();
	protected $listaDeModulo = array();

	public static function obterDados() {
//		return $GLOBALS['param'];
		return array();
	}

	/*
	 * Endereços absolutos dos arquivos
	 */

	protected function enderecoAbsolutoDoArquivoCache() {
		return DIRETORIO_TEMPORARIO . 'cache' . session_id() . '.json';
	}

	protected function enderecoAbsolutoDoArquivoMenuPrincipal($empresaId = null) {
		if (!is_null($empresaId)) {
			return DIRETORIO_TEMPORARIO . 'menu' . session_id() . '_' . EMPRESA_ATUAL . '_' . Sessao::obterSessaoDoSistema('usuario_id') . '.html';
		}
	}

	protected function enderecoAbsolutoDoArquivoModulo() {
		return DIRETORIO_MODULO . 'modulo.json';
	}

	public static function enderecoAbsolutoDoArquivoPermissao($empresaId, $usuarioId) {
		return DIRETORIO_PERMISSAO . $empresaId . '_' . $usuarioId . '.json';
	}

	protected static function enderecoAbsolutoDoArquivoUpload($contratanteId, $registroId, $diretorioReferencia, $pacote = PACOTE_ATUAL, $modulo = MODULO_ATUAL) {
		$endereco = DIRETORIO_UPLOAD;
		$endereco .= $contratanteId . DS;
		$endereco .= $pacote . DS;
		$endereco .= $pacote . '_' . $modulo . DS;
		$endereco .= $registroId . DS;
		$endereco .= $diretorioReferencia . DS;
		return $endereco;
	}

	protected function listaDeArquivoParaSerBaixado($listaDeArquivo, $registroId, $diretorioReferencia, $pacote = PACOTE_ATUAL, $modulo = MODULO_ATUAL) {
		$listaDeArquivoParaSerBaixado = array();
		foreach ($listaDeArquivo as $i => $arquivo) {
			$listaDeArquivoParaSerBaixado[$i]['arquivoEndereco'] = $this->enderecoAbsolutoDoArquivoUpload(Sessao::obterSessaoDoSistema('contratante_id'), $registroId, $diretorioReferencia, $pacote, $modulo) . $arquivo['arquivo_nome'];
			$listaDeArquivoParaSerBaixado[$i]['arquivoNome'] = $arquivo['arquivo_nome_local'];
		}
		return $listaDeArquivoParaSerBaixado;
	}

	/*
	 * Cache
	 */

	private function verificarExistenciaDoArquivoDeCache() {
		if (!Diretorio::verificarExistenciaDoArquivo($this->enderecoAbsolutoDoArquivoCache())) {
			file_put_contents($this->enderecoAbsolutoDoArquivoCache(), json_encode(array()));
		}
	}

	protected function carregarCache() {
		return json_decode(file_get_contents($this->enderecoAbsolutoDoArquivoCache()), true);
	}

	protected function obterCache() {
		$this->verificarExistenciaDoArquivoDeCache();
		$cache = $this->carregarCache();
		if (isset($cache[MODULO_ATUAL])) {
			$this->__setAll($cache[MODULO_ATUAL]);
		}
	}

	protected function definirCache() {
		$this->verificarExistenciaDoArquivoDeCache();
		$cache = $this->carregarCache();
		foreach ($this->dados as $i => $v) {
			$cache[MODULO_ATUAL][$i] = $v;
		}
		file_put_contents($this->enderecoAbsolutoDoArquivoCache(), json_encode($cache));
	}

	protected function definirOrdenacaoPadrao($ordenacaoColunaPadrao, $ordenacaoOrientacao = 'ASC') {
		if (!$this->validarParametro(ORDENACAO_COLUNA)) {
			$this->__set(ORDENACAO_COLUNA, $ordenacaoColunaPadrao);
			$this->__set(ORDENACAO_ORIENTACAO, $ordenacaoOrientacao);
		}
	}

	protected static function gerarLinkDoArquivoNoUpload($registroId, $diretorioReferencia, $nomeDoArquivo, $pacote = PACOTE_ATUAL, $modulo = MODULO_ATUAL) {
		$param = array();
		$param[] = $pacote;
		$param[] = self::urlAmingavel($modulo);
		$param[] = Seguranca::criptografar($registroId);
		$param[] = self::urlAmingavel($diretorioReferencia);
		$param[] = $nomeDoArquivo;
		return self::gerarLink(PACOTE_SYS, MODULO_SYS_ARQUIVO, 'exibir-arquivo', $param);
	}

	protected function verificar($texto) {
		$html = '<p><i class="help-block">(Não informado)</i></p>';
		if (!empty($texto)) {
			$html = '<p>' . $texto . '</p>';
		}
		return $html;
	}

	protected function definirCampoObrigatorio($campo) {
		$obrigatorio = array();
		if ($this->validarParametro('obrigatorio')) {
			$obrigatorio = $this->__get('obrigatorio');
		}
		$obrigatorio[] = $campo;
		$this->__set('obrigatorio', $obrigatorio);
	}

	protected function definirNotificacao($indice, $valor) {
		$notificacao = array();
		if ($this->validarParametro('notificacao')) {
			$notificacao = $this->__get('notificacao');
		}
		$notificacao[$this->definirIndiceDaNotificacao($indice)] = $valor;
		$this->__set('notificacao', $notificacao);
	}

	protected function definirIndiceDaNotificacao($indice) {
		return '[__' . $indice . '__]';
	}

	protected function definirListagem($indiceListagem, $modelResultado) {
		if (isset($modelResultado[BD_QUANTIDADE_DE_REGISTRO]) && $modelResultado[BD_QUANTIDADE_DE_REGISTRO] > 0) {
			$this->__set($indiceListagem, $modelResultado[BD_DADOS]);
		}
	}

	protected function definirDados($modelResultado) {
		if (isset($modelResultado[BD_QUANTIDADE_DE_REGISTRO]) && $modelResultado[BD_QUANTIDADE_DE_REGISTRO] > 0) {
			$this->__setAll($modelResultado[BD_DADOS]);
		}
	}

	protected function obterLarguraDaImagem($enderecoDaImagem) {
		list($largura, $altura) = getimagesize($enderecoDaImagem);
		return $largura;
	}

	protected function obterAlturaDaImagem($enderecoDaImagem) {
		list($largura, $altura) = getimagesize($enderecoDaImagem);
		return $altura;
	}

	protected function obterPrimeiroNome($nomeCompleto) {
		$listaDeNome = explode(' ', $nomeCompleto);
		return $listaDeNome[0];
	}

	protected function definirPaginacaoPaginaAtual() {
		if ($this->validarParametro(4)) {
			$this->__set(PAGINACAO_PAGINA_ATUAL, $this->__get(4));
		} else if (!$this->validarParametro(PAGINACAO_PAGINA_ATUAL)) {
			$this->__set(PAGINACAO_PAGINA_ATUAL, 1);
		}
	}

	protected function definirPaginacaoQuantidadeDeRegistroPorPagina() {
		if ($this->validarParametro(5)) {
			$this->__set(PAGINACAO_QUANTIDADE_DE_REGISTRO_POR_PAGINA, $this->__get(5));
		} else if (!$this->validarParametro(PAGINACAO_QUANTIDADE_DE_REGISTRO_POR_PAGINA)) {
			$this->__set(PAGINACAO_QUANTIDADE_DE_REGISTRO_POR_PAGINA, QUANTIDADE_DE_REGISTRO_POR_PAGINA);
		}
	}

	protected function exibirIconePorExtensaoDeArquivo($extensaoDeArquivo) {
		$icone = '';
		switch ($extensaoDeArquivo) {
			case 'pdf':
				$icone = ICONE_PDF;
				break;

			case 'png':
			case 'jpg':
				$icone = ICONE_IMAGEM;
				break;
		}
		return $icone;
	}

	public static function valorPorExtenso($valor = 0, $bolExibirMoeda = true, $bolPalavraFeminina = false) {
		$plural = null;
		$singular = null;
		if ($bolExibirMoeda) {
			$plural = array('centavos', 'reais', 'mil', 'milhões', 'bilhões', 'trilhões', 'quatrilhões');
			$singular = array('centavo', 'real', 'mil', 'milhão', 'bilhão', 'trilhão', 'quatrilhão');
		} else {
			$plural = array('', '', 'mil', 'milhões', 'bilhões', 'trilhões', 'quatrilhões');
			$singular = array('', '', 'mil', 'milhão', 'bilhão', 'trilhão', 'quatrilhão');
		}
		$c = array('', 'cem', 'duzentos', 'trezentos', 'quatrocentos', 'quinhentos', 'seiscentos', 'setecentos', 'oitocentos', 'novecentos');
		$d = array('', 'dez', 'vinte', 'trinta', 'quarenta', 'cinquenta', 'sessenta', 'setenta', 'oitenta', 'noventa');
		$d10 = array('dez', 'onze', 'doze', 'treze', 'quatorze', 'quinze', 'dezesseis', 'dezesete', 'dezoito', 'dezenove');
		$u = array('', 'um', 'dois', 'três', 'quatro', 'cinco', 'seis', 'sete', 'oito', 'nove');
		if ($bolPalavraFeminina) {
			if ($valor == 1) {
				$u = array('', 'uma', 'duas', 'três', 'quatro', 'cinco', 'seis', 'sete', 'oito', 'nove');
			} else {
				$u = array('', 'um', 'duas', 'três', 'quatro', 'cinco', 'seis', 'sete', 'oito', 'nove');
			}
			$c = array('', 'cem', 'duzentas', 'trezentas', 'quatrocentas', 'quinhentas', 'seiscentas', 'setecentas', 'oitocentas', 'novecentas');
		}
		$z = 0;
		$valor = number_format($valor, 2, '.', '.');
		$inteiro = explode('.', $valor);
		for ($i = 0; $i < count($inteiro); $i++) {
			for ($ii = mb_strlen($inteiro[$i]); $ii < 3; $ii++) {
				$inteiro[$i] = '0' . $inteiro[$i];
			}
		}
		$rt = null;
		$fim = count($inteiro) - ($inteiro[count($inteiro) - 1] > 0 ? 1 : 2);
		for ($i = 0; $i < count($inteiro); $i++) {
			$valor = $inteiro[$i];
			$rc = (($valor > 100) && ($valor < 200)) ? 'cento' : $c[$valor[0]];
			$rd = ($valor[1] < 2) ? '' : $d[$valor[1]];
			$ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : '';
			$r = $rc . (($rc && ($rd || $ru)) ? ' e ' : '') . $rd . (($rd && $ru) ? ' e ' : '') . $ru;
			$t = count($inteiro) - 1 - $i;
			$r .= $r ? ' ' . ($valor > 1 ? $plural[$t] : $singular[$t]) : '';
			if ($valor == '000') {
				$z++;
			} elseif ($z > 0) {
				$z--;
			}
			if (($t == 1) && ($z > 0) && ($inteiro[0] > 0)) {
				$r .= ( ($z > 1) ? ' de ' : '') . $plural[$t];
			}
			if ($r) {
				$rt = $rt . ((($i > 0) && ($i <= $fim) && ($inteiro[0] > 0) && ($z < 1)) ? (($i < $fim) ? ', ' : ' e ') : ' ') . $r;
			}
		}
		$rt = mb_substr($rt, 1);
		return($rt ? trim($rt) : 'zero');
	}

	public function vetorSimOuNao($indice = null) {
		$vetor = array();
		$vetor[SIM] = 'Sim';
		$vetor[NAO] = 'Não';
		return $this->retornarVetorOuIndice($vetor, $indice);
	}

	public function vetorAtivoOuInativo($indice = null) {
		$vetor = array();
		$vetor[NAO] = 'Ativo';
		$vetor[SIM] = 'Inativo';
		$vetor['A'] = 'Ambos';
		return $this->retornarVetorOuIndice($vetor, $indice);
	}

	public function vetorSexo($indice = null) {
		$vetor = array();
		$vetor[PESSOA_SEXO_AMBOS] = 'Ambos';
		$vetor[PESSOA_SEXO_FEMININO] = 'Feminino';
		$vetor[PESSOA_SEXO_MASCULINO] = 'Masculino';
		return $this->retornarVetorOuIndice($vetor, $indice);
	}

	protected function retornarVetorOuIndice($vetor, $indice) {
		if (is_null($indice)) {
			return $vetor;
		} else if (isset($vetor[$indice])) {
			return $vetor[$indice];
		}
	}

	protected function setRegistroAntigo() {
		$this->log = $this->dados;
	}

	protected function getIconeDoModulo() {
		return $this->__get('iconeDoModulo');
	}

	protected function definirTituloDoModulo($tituloDoModulo) {
		$this->__set('tituloDoModulo', $tituloDoModulo);
	}

	protected function definirDescricaoDoModulo($descricaoDoModulo) {
		$this->__set('descricaoDoModulo', $descricaoDoModulo);
	}

	protected function definirIconeDoModulo($iconeDoModulo) {
		$this->__set('iconeDoModulo', $iconeDoModulo);
	}

	protected function getTituloDoModulo() {
		return $this->__get('tituloDoModulo');
	}

	protected function getDescricaoDoModulo() {
		return $this->__get('descricaoDoModulo');
	}

	protected function definirTituloDoMetodo($tituloDoMetodo) {
		$this->__set('tituloDoMetodo', $tituloDoMetodo);
	}

	protected function obterTituloDoMetodo() {
		return $this->__get('tituloDoMetodo');
	}

	protected function definirItemDoMenu($verificarPermissaoDeAcessoAoMetodo, $titulo, $link, $icone = null) {
		$html = '';
		if ($verificarPermissaoDeAcessoAoMetodo) {
			$html .= '<li><a href="' . $link . '" target="_blank">';
			$html .= (!is_null($icone) ? '<i class="' . $icone . '"></i> ' : '') . $titulo;
			$html .= '</a></li>' . PHP_EOL;
		}
		return $html;
	}

	protected function definirMenuTopoDaLista($menu) {
		$menu = array_filter($menu);
		if (is_array($menu) && count($menu) > 0) {
			$this->__set('listaDeMenuTopoDaLista', $menu);
		}
	}

	public static function obterEnderecoAbsolutoDoAvatar($usuarioAvatar = null, $usuarioId = null) {
		$usuarioId = (is_null($usuarioId) ? Sessao::obterSessaoDoSistema('usuario_id') : $usuarioId);
		$enderecoAbsolutoDoArquivo = self::enderecoAbsolutoDoArquivoUpload(Sessao::obterSessaoDoSistema('contratante_id'), $usuarioId, 'avatar', PACOTE_SYS, MODULO_SYS_USUARIO);
		if (!empty($usuarioAvatar) && file_exists($enderecoAbsolutoDoArquivo . $usuarioAvatar)) {
			return self::gerarLinkDoArquivoNoUpload($usuarioId, 'avatar', $usuarioAvatar, PACOTE_SYS, MODULO_SYS_USUARIO);
		} else {
			return WWW_SUBDOMINIO_ATUAL . 'images/noavatar.png';
		}
	}

	public function converterTimestampParaDataHora($timestamp) {
		return date('d/m/Y H:i:s', $timestamp);
	}

	private function definirConstanteAtual($nomeDaClasseController) {
		define('CONTROLLER_ATUAL', $nomeDaClasseController);
		define('MODEL_ATUAL', str_replace('Controller', '', $nomeDaClasseController) . 'Model');
		define('TABELA_ATUAL', PACOTE_ATUAL . '_' . MODULO_ATUAL);
		define('FORMULARIO_ATUAL', 'frm_' . PACOTE_ATUAL . '_' . MODULO_ATUAL);
		define('FORMULARIO_DE_PESQUISA_ATUAL', 'frm_pesquisa_' . PACOTE_ATUAL . '_' . MODULO_ATUAL);
		define('LISTA_ATUAL', 'list_' . PACOTE_ATUAL . '_' . MODULO_ATUAL);
		define('MODAL_ATUAL', 'modal_' . PACOTE_ATUAL . '_' . MODULO_ATUAL);
		define('COLUNA_CHAVE_PRIMARIA_ATUAL', MODULO_ATUAL . '_id');
		define('COLUNA_INATIVO_ATUAL', MODULO_ATUAL . '_inativo');
		define('COLUNA_LIXEIRA_ATUAL', MODULO_ATUAL . '_lixeira');
		define('COLUNA_DELETADO_ATUAL', MODULO_ATUAL . '_deletado');
		if (!$this->validarParametro('formulario_atual')) {
			$this->__set('formulario_atual', FORMULARIO_ATUAL);
		}
	}

	public static function gerarNumeroRandomico() {
		$arquivoNome = '';
		$arquivoNome .= date('YmdHis');
		$arquivoNome .= rand(1000000000, 9999999999);
		return $arquivoNome;
	}

	public static function obterOrigemDaRequisicao() {
		return basename($_SERVER['SCRIPT_FILENAME'], '.php');
	}

	protected function carregarMetodo($classe, $nomeDaClasseController, $sufixoDoMetodo) {
		$origemDaRequisicao = self::obterOrigemDaRequisicao();
		switch ($origemDaRequisicao) {
			case 'index':
				$nomeDoMetodo = $this->construirMetodoView($nomeDaClasseController, $sufixoDoMetodo);
				break;
			case 'action':
				$nomeDoMetodo = $this->construirMetodoAction($nomeDaClasseController, $sufixoDoMetodo);
				break;
			case 'webservice':
				$nomeDoMetodo = $this->construirMetodoWebservice($nomeDaClasseController, $sufixoDoMetodo);
				break;
			case 'routine':
				$nomeDoMetodo = $this->construirMetodoRoutine($nomeDaClasseController, $sufixoDoMetodo);
				break;
			default:
				return false;
				break;
		}

		new Permissao(Sessao::obterSessaoDoSistema('usuario_id'));

//		die($origemDaRequisicao);
//		if (!permissao::verificarPermissaoDeAcessoAoMetodo($nomeDoMetodo)) {
//			switch ($origemDaRequisicao) {
//				case 'index':
//					Sessao::sysLoginExcluirSessaoNoSistema();
//					break;
//				case 'action':
//					json::alertarErro(MENSAGEM_PADRAO_PERMISSAO_NEGADA_PRINCIPAL, MENSAGEM_PADRAO_PERMISSAO_NEGADA_SECUNDARIA);
//					break;
//			}
//		}

		if (self::obterOrigemDaRequisicao() != 'routine' && Sessao::obterQuantidadeDeSessaoRegistradaNoSistema()) {
			$listaDeEmpresa = Sessao::obterSessaoDoSistema('empresa');
			if (!isset($listaDeEmpresa[EMPRESA_ATUAL])) {
				Sessao::sysLoginExcluirSessaoNoSistema();
			}
		}


		$this->carregarListaDeModulo();
		$this->definirConstanteAtual($nomeDaClasseController);
		$this->definirTituloDoModulo($this->obterTituloDoModulo(PACOTE_ATUAL, MODULO_ATUAL));
		$this->definirDescricaoDoModulo($this->obterDescricaoDoModulo(PACOTE_ATUAL, MODULO_ATUAL));
		$this->definirIconeDoModulo($this->obterIconeDoModulo(PACOTE_ATUAL, MODULO_ATUAL));
//		die();
//		if (!method_exists($classe, $nomeDoMetodo)) {
//			switch ($sufixoDoMetodo) {
//				case METODO_VIEW_LISTAR:
//					$nomeDoMetodoPadrao = 'padraoViewListar';
//					break;
//				case METODO_VIEW_FAQ:
//					$nomeDoMetodoPadrao = 'padraoViewFaq';
//					break;
//				case METODO_VIEW_LIXEIRA:
//					$nomeDoMetodoPadrao = 'padraoViewLixeira';
//					break;
//				case METODO_ACTION_INCLUIR_LISTA:
//					$nomeDoMetodoPadrao = 'padraoActionIncluirLista';
//					break;
//				case METODO_ACTION_EXIBIR_MODAL:
//					$nomeDoMetodoPadrao = 'padraoActionExibirModal';
//					break;
//				case METODO_ACTION_INCLUIR_VISUALIZACAO:
//					$nomeDoMetodoPadrao = 'padraoActionIncluirVisualizacao';
//					break;
//				case METODO_ACTION_ATIVAR_OU_INATIVAR:
//					$nomeDoMetodoPadrao = 'padraoActionAtivarOuInativar';
//					break;
//				case METODO_ACTION_ATIVAR_OU_INATIVAR_MULTIPLO:
//					$nomeDoMetodoPadrao = 'padraoActionAtivarOuInativarMultiplo';
//					break;
//				case METODO_ACTION_HISTORICO_DE_ACAO:
//					$nomeDoMetodoPadrao = 'padraoActionHistoricoDeAcao';
//					break;
//				case METODO_ACTION_MOVER_PARA_LIXEIRA:
//					$nomeDoMetodoPadrao = 'padraoActionMoverParaLixeira';
//					break;
//				case METODO_ACTION_MOVER_PARA_LIXEIRA_MULTIPLO:
//					$nomeDoMetodoPadrao = 'padraoActionMoverParaLixeiraMultiplo';
//					break;
//				case METODO_ACTION_EXCLUIR:
//					$nomeDoMetodoPadrao = 'padraoActionExcluir';
//					break;
//				case METODO_ACTION_EXCLUIR_MULTIPLO:
//					$nomeDoMetodoPadrao = 'padraoActionExcluirMultiplo';
//					break;
//				case METODO_ACTION_RESTAURAR:
//					$nomeDoMetodoPadrao = 'padraoActionRestaurar';
//					break;
//				case METODO_ACTION_RESTAURAR_MULTIPLO:
//					$nomeDoMetodoPadrao = 'padraoActionRestaurarMultiplo';
//					break;
//				default:
//					if (ferramenta::obterOrigemDaRequisicao() == 'webservice') {
//						ws::retornarMensagemDeErro('Método não encontrado!');
//					} else {
//						Sessao::sysLoginExcluirSessaoNoSistema();
//					}
//					break;
//			}
//			$padraoController = new padraoController($this->dados);
//			$padraoController->{$nomeDoMetodoPadrao}();
//		} else {
//			call_user_func_array(array($classe, $nomeDoMetodo), array());
//		}
//		die();
	}

	private function carregarListaDeModulo() {
		if (Diretorio::verificarExistenciaDoArquivo($this->enderecoAbsolutoDoArquivoModulo())) {
			$this->listaDeModulo = json_decode(file_get_contents($this->enderecoAbsolutoDoArquivoModulo()), true);
		}
	}

	protected function obterDescricaoDoModulo($pacote, $modulo) {
		if (isset($this->listaDeModulo[$pacote . '_' . $modulo])) {
			return $this->listaDeModulo[$pacote . '_' . $modulo]['descricao'];
		}
	}

	protected function obterTituloDoModulo($pacote, $modulo) {
		if (isset($this->listaDeModulo[$pacote . '_' . $modulo])) {
			return $this->listaDeModulo[$pacote . '_' . $modulo]['titulo'];
		}
	}

	protected function obterIconeDoModulo($pacote, $modulo) {
		if (isset($this->listaDeModulo[$pacote . '_' . $modulo])) {
			return $this->listaDeModulo[$pacote . '_' . $modulo]['icone'];
		}
	}

	protected function construirMetodoView($nomeDaClasse, $nomeDoMetodo) {
		$a = explode('\\', $nomeDaClasse);
		$prefixoDaClasse = str_replace('Controller', '', end($a));
		$metodo = PACOTE_ATUAL . $prefixoDaClasse . 'View';
		$dados = explode('-', $nomeDoMetodo);
		foreach ($dados as $valor) {
			$metodo .= ucfirst($valor);
		}
		return $metodo;
	}

	protected function construirMetodoAction($nomeDaClasse, $nomeDoMetodo) {
		$a = explode('\\', $nomeDaClasse);
		$prefixoDaClasse = str_replace('Controller', '', end($a));
		return PACOTE_ATUAL . $prefixoDaClasse . 'Action' . ucfirst($nomeDoMetodo);
	}

	protected function construirMetodoWebservice($nomeDaClasse, $nomeDoMetodo) {
		$prefixoDaClasse = str_replace('Controller', '', $nomeDaClasse);
		$metodo = $prefixoDaClasse . 'Ws';
		$dados = explode('-', $nomeDoMetodo);
		foreach ($dados as $valor) {
			$metodo .= ucfirst($valor);
		}
		return $metodo;
	}

	protected function construirMetodoRoutine($nomeDaClasse, $nomeDoMetodo) {
		$prefixoDaClasse = str_replace('Controller', '', $nomeDaClasse);
		$metodo = $prefixoDaClasse . 'Routine';
		$dados = explode('-', $nomeDoMetodo);
		foreach ($dados as $valor) {
			$metodo .= ucfirst($valor);
		}
		return $metodo;
	}

	protected function parametroAdicionalSelecionarCampo($campo) {
		$this->parametroAdicional['select'] = $campo;
	}

	private static function nameModule($str) {
		$dados = explode('_', $str);
		return implode('', array_map(function($word) {
				return ucfirst($word);
			}, $dados));
	}

	public function incluirLayout($arquivoView) {
		require(DIRETORIO_INCLUDE . $arquivoView . '.php');
	}

	public function incluirFotoOuThumbnail($fotoOuThumbnailID) {
		$this->__set('fotoOuThumbnailID', $fotoOuThumbnailID);
		$this->incluirLayout('viewIncFotoOuThumbnail');
	}

	public function incluirColunaOrdenavel($titulo, $nomeDaColuna, $tamanhoDaColuna, $alinhamentoDoTexto = '') {
		$tagData = 'data-toggle="true"';
		return $this->incluirColuna($tagData, $titulo, $nomeDaColuna, $tamanhoDaColuna, $alinhamentoDoTexto);
	}

	public function incluirColunaOrdenavelResponsivo($titulo, $nomeDaColuna, $tamanhoDaColuna, $alinhamentoDoTexto = '') {
		$tagData = 'data-hide="phone"';
		return $this->incluirColuna($tagData, $titulo, $nomeDaColuna, $tamanhoDaColuna, $alinhamentoDoTexto);
	}

	private function incluirColuna($tagData, $titulo, $nomeDaColuna, $tamanhoDaColuna, $alinhamentoDoTexto = '') {
		$html = '';
		$html .= '<th data-sort-ignore="true" ' . $tagData . ' class="' . $tamanhoDaColuna . ' ' . $alinhamentoDoTexto . '">' . PHP_EOL;
		$html .= '<a href="' . Js::void() . '" onclick="' . Js::ordenarLista($nomeDaColuna) . '">';
		$html .= $titulo . PHP_EOL;
		if ($this->__get(ORDENACAO_COLUNA) == $nomeDaColuna) :
			$html .= '<i class="' . ($this->__get(ORDENACAO_ORIENTACAO) == 'DESC' ? ICONE_ORDENACAO_DECRESCENTE : ICONE_ORDENACAO_CRESCENTE) . '"></i>' . PHP_EOL;
		endif;
		$html .= '</a>' . PHP_EOL;
		$html .= '</th>' . PHP_EOL;
		return $html;
	}

	public function incluirTab($nome, $html, $exibirApenasNaEdicao = false) {
		$qtd = count($this->tab);
		$this->tab[$qtd]['nome'] = $nome;
		$this->tab[$qtd]['html'] = $html;
		$this->tab[$qtd]['editar'] = $exibirApenasNaEdicao;
	}

	public function exibirTab() {
		$html = '';
		$tabNome = '';
		$tabHTML = '';
		$tabUnico = $this->gerarNumeroRandomico();
		if (count($this->tab)) {
			foreach ($this->tab as $iTab => $tab) {
				$tabNome .= '<li' . ($this->__get('tabAtiva') == ($iTab + 1) ? ' class="active"' : '') . '><a data-toggle="tab" href="#tab-' . $tabUnico . '-' . ($iTab + 1) . '" onclick="setTabActive(' . ($iTab + 1) . ')">' . $tab['nome'] . '</a></li>' . PHP_EOL;
				$tabHTML .= '<div id="tab-' . $tabUnico . '-' . ($iTab + 1) . '" class="modal-body tab-pane' . ($this->__get('tabAtiva') == ($iTab + 1) ? ' active' : '') . '">' . PHP_EOL;
				$tabHTML .= $tab['html'] . PHP_EOL;
				$tabHTML .= '</div>' . PHP_EOL;
			}
		}

		$html .= '<div class="tabs-container">' . PHP_EOL;
		$html .= '<ul class="nav nav-tabs">' . PHP_EOL;
		$html .= $tabNome;
		$html .= '</ul>' . PHP_EOL;
		$html .= '<div class="tab-content">' . PHP_EOL;
		$html .= $tabHTML;
		$html .= '</div>' . PHP_EOL;
		$html .= '</div>' . PHP_EOL;
		return $html;
	}

	public static function redirecionarParaSSL($dominio) {
		if ($dominio != WWW_DOMINIO_LOCALHOST && ($_SERVER['REQUEST_SCHEME'] == 'http' || $_SERVER['SERVER_PORT'] != 443)) {
			header('Location: ' . WWW_SUBDOMINIO_ATUAL, true, 301);
			die();
		}
	}

	public function incluirView($arquivoView, $buffer = false, $pacote = PACOTE_ATUAL, $modulo = MODULO_ATUAL) {
		$enderecoView = DIRETORIO_SISTEMA . 'View' . DS . self::nameModule($pacote) . DS . self::nameModule($modulo) . DS . ucfirst($arquivoView) . '.php';
		if (Diretorio::verificarExistenciaDoArquivo($enderecoView)) {
			if ($buffer) {
				ob_start();
				require_once($enderecoView);
				return ob_get_clean();
			} else {
				$this->incluirLayout('viewIncPadraoCabecalho');
				require_once($enderecoView);
				$this->incluirLayout('viewIncPadraoRodape');
			}
		} else {
			$this->incluirLayout('viewIncPadraoCabecalho');
			$this->incluirLayout('viewIncPadraoConteudo');
			$this->incluirLayout('viewIncPadraoRodape');
		}
	}

	public function incluirView2($arquivoView, $buffer = false, $pacote = PACOTE_ATUAL, $modulo = MODULO_ATUAL) {
		$enderecoView = DIRETORIO_SISTEMA . 'View' . DS . self::nameModule($pacote) . DS . self::nameModule($modulo) . DS . ucfirst($arquivoView) . '.php';
		if (Diretorio::verificarExistenciaDoArquivo($enderecoView)) {
			if ($buffer) {
				ob_start();
				require_once($enderecoView);
				return ob_get_clean();
			} else {
				require_once($enderecoView);
			}
		} else {
			$this->incluirLayout('viewIncPadraoCabecalho');
			$this->incluirLayout('viewIncPadraoConteudo');
			$this->incluirLayout('viewIncPadraoRodape');
		}
	}

	public function incluirViewModalMedio($arquivoView, $pacote = PACOTE_ATUAL, $modulo = MODULO_ATUAL) {
		$this->__set('modalDoTamanho', 'modal-sm');
		return $this->incluirViewModal($arquivoView, $pacote, $modulo);
	}

	public function incluirViewModalGrande($arquivoView, $pacote = PACOTE_ATUAL, $modulo = MODULO_ATUAL) {
		$this->__set('modalDoTamanho', 'modal-lg');
		return $this->incluirViewModal($arquivoView, $pacote, $modulo);
	}

	public function incluirViewModal($arquivoView, $pacote = PACOTE_ATUAL, $modulo = MODULO_ATUAL, $tituloDoModulo = null) {
		$enderecoView = DIRETORIO_SISTEMA . 'View' . DS . ucfirst($pacote) . DS . ucfirst($modulo) . DS . 'Modal' . DS . mb_strtolower($pacote) . self::nameModule($modulo) . 'ViewModal' . $arquivoView . '.php';
		ob_start();
		if (Diretorio::verificarExistenciaDoArquivo($enderecoView)) {
			if (!is_null($tituloDoModulo)) {
				$this->definirTituloDoModulo($tituloDoModulo);
			}
			$this->incluirLayout('viewIncModalCabecalho');
			require_once($enderecoView);
		} else {
			$this->incluirLayout('viewIncModalCabecalho');
			$this->incluirLayout('viewIncModalCorpo');
		}
		$this->incluirLayout('viewIncModalRodape');
		return ob_get_clean();
	}

	public function incluirViewInc($arquivoView, $buffer = false, $pacote = PACOTE_ATUAL, $modulo = MODULO_ATUAL) {
		$enderecoView = DIRETORIO_SISTEMA . 'View' . DS . self::nameModule($pacote) . DS . self::nameModule($modulo) . DS . ucfirst($arquivoView) . '.php';
		if (Diretorio::verificarExistenciaDoArquivo($enderecoView)) {
			if ($buffer) {
				ob_start();
				require_once($enderecoView);
				$html = ob_get_clean();
				$this->gerarArquivoDoBuffer($enderecoView, $html);
				return $html;
			} else {
				require($enderecoView);
			}
		}
	}

	public function gerarArquivoDoBuffer($enderecoView, $html) {
		if (MODO_DESENVOLVIMENTO) {
			file_put_contents(DIRETORIO_TEMPORARIO . basename($enderecoView, '.php') . '.html', $html);
		}
	}

	public function validarTabAtiva() {
		$tabAtiva = 1;
		if ($this->validarParametro('tabAtiva')) {
			$tabAtiva = $this->__get('tabAtiva');
		}
		return $tabAtiva;
	}

	public static function validarRequisicaoAjax() {
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && (strtolower($_SERVER['HTTP_X_REQUESTED_WITH'])) == 'xmlhttprequest') {
			return true;
		} else {
			return false;
		}
	}

	public static function validarExtensaoHabilitadaNoPHP() {
		if (!extension_loaded('fileinfo')) {
			print 'Extensão fileinfo não carregada.';
			die();
		}

		if (!extension_loaded('PDO')) {
			print 'Extensão PDO não carregada.';
			die();
		}
	}

	public function mostrarDados($dados = null) {
		print '<pre>';
		if (is_array($dados)) {
			print_r($dados);
		} else {
			print_r($this->dados);
		}
		print '</pre>';
		die();
	}

	public function __get($indice) {
		if (array_key_exists($indice, $this->dados)) {
			return $this->dados[$indice];
		} else {
			return '';
		}
	}

	public function __set($indice, $valor) {
		if (!empty($valor)) {
			if (!is_array($valor) && Seguranca::validarCriptografia($valor)) {
				$valor = Seguranca::descriptografar($valor);
				$valor = $this->removerEspacoEmBrancoDuplicado($valor);
			} else if (!is_array($valor)) {
				$valor = $this->removerEspacoEmBrancoDuplicado($valor);
			} else if (is_array($valor)) {
//                foreach ($valor as $i => $v) {
//                    if (is_array($v)) {
//                        foreach ($v as $ii => $vv) {
//                            $vv = Seguranca::descriptografar($vv);
//                            $valor[$i][$ii] = $this->removerEspacoEmBrancoDuplicado($vv);
//                        }
//                    } else {
//                        $v = Seguranca::descriptografar($v);
//                        $valor[$i] = $this->removerEspacoEmBrancoDuplicado($v);
//                    }
//                }
			}

			if ($this->salvarNoLog) {
				$this->log[$indice] = $valor;
			} else {
				$this->dados[$indice] = $valor;
			}
		} else {
			if ($this->salvarNoLog) {
				$this->log[$indice] = null;
			} else {
				$this->dados[$indice] = null;
			}
		}
	}

	public function __setAll($dados) {
		foreach ($dados as $chave => $valor) {
			$this->__set($chave, Seguranca::descriptografar($valor));
		}
	}

	public static function gerarLink($pacote, $modulo, $metodo, $param = array(), $empresa = EMPRESA_ATUAL) {
		$url = WWW_SUBDOMINIO_ATUAL . $pacote . '/' . str_replace('_', '-', $modulo) . '/' . $metodo . '/' . Seguranca::criptografar($empresa);
		if (count($param)) {
			foreach ($param as $valor) {
				$url .= '/' . $valor;
			}
		}
		return $url;
	}

	public static function gerarLink2($pacote, $modulo, $metodo, $param = array(), $empresa = EMPRESA_ATUAL) {
		$url = $pacote . '/' . str_replace('_', '-', $modulo) . '/' . $metodo . '/' . Seguranca::criptografar($empresa);
		return $url;
	}

	public function getParam($paramRemover = array()) {
		$param = explode('/', $_SERVER['REQUEST_URI']);
		$param = array_diff($param, $paramRemover);

		$param = array_values($param);
		return $param;
	}

	public function obterDataPorExtenso($data) {
		return $this->obterDia($data) . ' de ' . $this->obterNomeDoMes($this->obterMes($data)) . ' de ' . $this->obterAno($data);
	}

	public function obterSemanaPorExtenso($data) {
		$diaDaSemana = array('Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado');
		$diaDaSemanaNumero = date('w', strtotime($data));
		return $diaDaSemana[$diaDaSemanaNumero];
	}

	public function vetorAno($ano = '') {
		$vetor = array();
		if (empty($ano)) {
			for ($i = (date('Y') - 5); $i <= (date('Y') + 5); $i++) {
				$vetor[$i] = $i;
			}
		} else {
			for ($i = ($ano - 5); $i <= ($ano + 5); $i++) {
				$vetor[$i] = $i;
			}
		}
		return $vetor;
	}

	public function vetorNomeDoMes($indice = null) {
		$vetor = array();
		$vetor['01'] = 'janeiro';
		$vetor['02'] = 'fevereiro';
		$vetor['03'] = 'março';
		$vetor['04'] = 'abril';
		$vetor['05'] = 'maio';
		$vetor['06'] = 'junho';
		$vetor['07'] = 'julho';
		$vetor['08'] = 'agosto';
		$vetor['09'] = 'setembro';
		$vetor['10'] = 'outubro';
		$vetor['11'] = 'novembro';
		$vetor['12'] = 'dezembro';
		return $this->retornarVetorOuIndice($vetor, str_pad($indice, 2, 0, STR_PAD_LEFT));
	}

	public function obterNomeDoMes($mes) {
		switch ($mes) {
			case '01':
				$nomeDoMes = 'janeiro';
				break;
			case '02':
				$nomeDoMes = 'fevereiro';
				break;
			case '03':
				$nomeDoMes = 'março';
				break;
			case '04':
				$nomeDoMes = 'abril';
				break;
			case '05':
				$nomeDoMes = 'maio';
				break;
			case '06':
				$nomeDoMes = 'junho';
				break;
			case '07':
				$nomeDoMes = 'julho';
				break;
			case '08':
				$nomeDoMes = 'agosto';
				break;
			case '09':
				$nomeDoMes = 'setembro';
				break;
			case '10':
				$nomeDoMes = 'outubro';
				break;
			case '11':
				$nomeDoMes = 'novembro';
				break;
			case '12':
				$nomeDoMes = 'dezembro';
				break;
		}
		return $nomeDoMes;
	}

	public static function urlAmingavel($url) {
		$normalize = array(
			'&' => '-e-', 'Ç' => 'c', 'ç' => 'c', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A',
			'Å' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I',
			'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ù' => 'U', 'Ú' => 'U',
			'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a',
			'å' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i',
			'ï' => 'i', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ù' => 'u',
			'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'ý' => 'y', 'ÿ' => 'y', '_' => '-'
		);
		$url = strip_tags($url, '<(.*?)>');
		$url = strtr($url, $normalize);
		$url = strtolower($url);
		$url = preg_replace('/[^a-zA-Z0-9 -]/', '', $url);
		$url = trim($url);
		$url = strtr($url, ' ', '-');
		for ($i = 0; $i < strlen($url); $i++) {
			$url = str_replace('--', '-', $url);
		}
		$url = trim($url, '-');
		return $url;
	}

	private function removerEspacoEmBrancoDuplicado($txt) {
		if (is_string($txt) || is_numeric($txt)) {
			return implode(chr(32), array_filter(explode(chr(32), $txt), 'strlen'));
		}
	}

	public function removerAcento($texto) {
		$array1 = array("á", "à", "â", "ã", "ä", "é", "è", "ê", "ë", "í", "ì", "î", "ï", "ó", "ò", "ô", "õ", "ö", "ú", "ù", "û", "ü", "ç"
			, "Á", "À", "Â", "Ã", "Ä", "É", "È", "Ê", "Ë", "Í", "Ì", "Î", "Ï", "Ó", "Ò", "Ô", "Õ", "Ö", "Ú", "Ù", "Û", "Ü", "Ç");
		$array2 = array("a", "a", "a", "a", "a", "e", "e", "e", "e", "i", "i", "i", "i", "o", "o", "o", "o", "o", "u", "u", "u", "u", "c"
			, "A", "A", "A", "A", "A", "E", "E", "E", "E", "I", "I", "I", "I", "O", "O", "O", "O", "O", "U", "U", "U", "U", "C");
		return str_replace($array1, $array2, $texto);
	}

	public function validarParametro($indiceParametro, $deslogar = false, $novoIndiceParametro = null) {
		if (!isset($this->dados[$indiceParametro]) || empty($this->dados[$indiceParametro])) {
			if ($deslogar) {
				Sessao::sysLoginExcluirSessaoNoSistema();
			} else {
				$this->parametroAdicionalSelecionarCampo($indiceParametro);
				return false;
			}
		} else {
			if (!is_null($novoIndiceParametro)) {
				$this->__set($novoIndiceParametro, $this->__get($indiceParametro));
			}
			return true;
		}
	}

	public function validarParamentoExcluirSessaoNoSistema($indiceParametro) {
		if (isset($this->dados[$indiceParametro]) && !empty($this->dados[$indiceParametro])) {
			Sessao::sysLoginExcluirSessaoNoSistema();
		}
	}

	public function confimarParametro() {
		return $this->validarParametro('confirmar');
	}

	/**
	 * Obtém o e-mail fornecido pelo usuário e retorna se ele é verdadeiro ou falso.
	 *
	 * @param string $email e-mail fornecido pelo usuário para validação
	 * @return boolean
	 */
	public static function validarEmail($email) {
		if (filter_var($email, FILTER_VALIDATE_EMAIL, FILTER_FLAG_SCHEME_REQUIRED)) {
			list($alias, $dominio) = explode('@', $email);
			return true;
			if (checkdnsrr($dominio, 'MX')) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public static function formatarEmail($email) {
		return trim(strtolower($email));
	}

	protected function formatarCheckbox($formatarCheckbox, $modelResultado, $colunaChavePrimaria) {
		if ($formatarCheckbox) {
			$listaSelecionado = array();
			if ($modelResultado[BD_QUANTIDADE_DE_REGISTRO] > 0) {
				foreach ($modelResultado[BD_DADOS] as $dados) {
					$listaSelecionado[BD_DADOS][] = $dados[$colunaChavePrimaria];
				}
				$listaSelecionado[BD_QUANTIDADE_DE_REGISTRO] = $modelResultado[BD_QUANTIDADE_DE_REGISTRO];
			}
			return $listaSelecionado;
		} else {
			return $modelResultado;
		}
	}

	public static function formatarDinheiroComRS($dinheiro, $dinheiroPorExtenso = false) {
		$dinheiroFormatado = '<span class="text-muted" style="font-size: 11px;">R$ </span>';
		$dinheiroFormatado .= self::formatarDinheiroParaPadraoBrasileiro($dinheiro);
		if ($dinheiroPorExtenso) {
			$dinheiroFormatado .= ' <i class="text-muted" style="font-size: 11px;">(' . self::valorPorExtenso($dinheiro) . ')</i>';
		}
		return $dinheiroFormatado;
	}

	public static function formatarDinheiro($dinheiro, $padrao = null) {
		$dinheiroFormatado = '';
		if (!empty($dinheiro)) {
			switch ($padrao) {
				case PADRAO_BD:
					$dinheiroFormatado = self::formatarDinheiroParaPadraoBD($dinheiro);
					break;
				case PADRAO_BRASILEIRO:
					$dinheiroFormatado = self::formatarDinheiroParaPadraoBrasileiro($dinheiro);
					break;
				default :
					$dinheiroFormatado = (strpos($dinheiro, ',') ? self::formatarDinheiroParaPadraoBD($dinheiro) : self::formatarDinheiroParaPadraoBrasileiro($dinheiro));
					break;
			}
		}
		return $dinheiroFormatado;
	}

	private static function formatarDinheiroParaPadraoBrasileiro($dinheiro) {
		return (!strpos($dinheiro, ',') ? number_format($dinheiro, 2, ',', '.') : $dinheiro);
	}

	private static function formatarDinheiroParaPadraoBD($dinheiro) {
		return (strpos($dinheiro, ',') ? str_replace(',', '.', str_replace(array('R$', ' ', '.'), '', $dinheiro)) : $dinheiro);
	}

	public static function formatarUnidade($unidade, $padrao = null) {
		$unidadeFormatado = '';
		if (!empty($unidade)) {
			switch ($padrao) {
				case PADRAO_BD:
					$unidadeFormatado = self::formatarUnidadeParaPadraoBD($unidade);
					break;
				case PADRAO_BRASILEIRO:
					$unidadeFormatado = self::formatarUnidadeParaPadraoBrasileiro($unidade);
					break;
				default :
					$unidadeFormatado = (strpos($unidade, ',') ? self::formatarUnidadeParaPadraoBD($unidade) : self::formatarUnidadeParaPadraoBrasileiro($unidade));
					break;
			}
		}
		return $unidadeFormatado;
	}

	private static function formatarUnidadeParaPadraoBrasileiro($unidade) {
		return (!strpos($unidade, ',') ? number_format($unidade, 3, ',', '.') : $unidade);
	}

	private static function formatarUnidadeParaPadraoBD($unidade) {
		return (strpos($unidade, ',') ? str_replace(',', '.', str_replace(array('R$', ' ', '.'), '', $unidade)) : $unidade);
	}

	public static function formatarTelefone($telefone, $padrao = null) {
		$telefoneFormatado = '';
		if (!empty($telefone)) {
			switch ($padrao) {
				case PADRAO_BD:
					$telefoneFormatado = self::formatarTelefoneParaPadraoBD($telefone);
					break;
				case PADRAO_BRASILEIRO:
					$telefoneFormatado = self::formatarTelefoneParaPadraoBrasileiro($telefone);
					break;
				default :
					$telefoneFormatado = (strlen($telefone) >= 14 ? self::formatarTelefoneParaPadraoBD($telefone) : self::formatarTelefoneParaPadraoBrasileiro($telefone));
					break;
			}
		}
		return $telefoneFormatado;
	}

	private static function formatarTelefoneParaPadraoBrasileiro($telefone) {
		$telefoneFormatado = '';
		if (strlen($telefone) == 10) {
			$telefoneFormatado .= '(';
			$telefoneFormatado .= substr($telefone, 0, 2) . ')';
			$telefoneFormatado .= ' ';
			$telefoneFormatado .= substr($telefone, 2, 4) . '-';
			$telefoneFormatado .= substr($telefone, 6, 4);
		} else if (strlen($telefone) == 11) {
			$telefoneFormatado .= '(';
			$telefoneFormatado .= substr($telefone, 0, 2) . ')';
			$telefoneFormatado .= ' ';
			$telefoneFormatado .= substr($telefone, 2, 1);
			$telefoneFormatado .= substr($telefone, 3, 4) . '-';
			$telefoneFormatado .= substr($telefone, 7, 4);
		} else {
			$telefoneFormatado = $telefone;
		}
		return $telefoneFormatado;
	}

	private static function formatarTelefoneParaPadraoBD($telefone) {
		$telefoneFormatado = '';
		if (strlen($telefone) >= 14) {
			$telefoneFormatado = str_replace(array('(', ')', '-', ' '), '', $telefone);
		} else {
			$telefoneFormatado = $telefone;
		}
		return $telefoneFormatado;
	}

	public static function formatarTexto($texto) {
		$paragrafoDoTexto = explode(PHP_EOL, $texto);
		$paragrafoDoTexto = array_filter($paragrafoDoTexto);

		$texto = '';
		foreach ($paragrafoDoTexto as $paragrafo) {
			$texto .= '<p>' . $paragrafo . '</p>';
		}
		return $texto;
	}

	public function localizarEnderecoPorCEP($cep) {
		$cep = str_replace(array('.', '-'), '', $cep);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_URL, 'http://viacep.com.br/ws/' . $cep . '/json/');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$dados = curl_exec($ch);
		curl_close($ch);
		return json_decode($dados, true);
	}

	/**
	 *
	 * Formata o cep de (00000-000) para (00000000) ou
	 * formata o cep de (00000000) para (00000-000).
	 *
	 * Obtém o cep fornecido pelo usuário e verifica se o comprimento é igual a 9 (nove) e
	 * se há existência do caracter hífen (-), se está condição for satisfeita remove o caracter hífen (-),
	 * se não for satisfeita verifica se o comprimento é igual a 8 (oito), se está condição for
	 * satisfeita inclui o caracter hífen (-) entre o quinto e sexto caracteres. No final retorna o cep
	 * formatado.
	 *
	 * @param string $cep cep fornecido pelo usuário ou pelo banco de dados
	 * @return string
	 */
	public static function formatarCEP($cep) {
		$cepFormatado = '';
		if ((strlen($cep) == 9) && strpos($cep, '-')) {
			$cepFormatado = str_replace('-', '', $cep);
		} elseif (strlen($cep) == 8) {
			$cepFormatado .= substr($cep, 0, 5) . '-';
			$cepFormatado .= substr($cep, 5, 3);
		}
		return $cepFormatado;
	}

	/**
	 *
	 * Primeiramente verifica a existência do caracter barra (/), se houver a existência
	 * é porque a data está no padrão brasileiro (dd/mm/aaaa). Se esta condição for satisfeita
	 * é feita conversão de string para array separando dia, mês e ano, após isto chama a função
	 * checkdate para validar a data. Agora se não houver a existência do caracter barra(/), verifica
	 * a existência do caracter hífen (-) subentendendo que está no padrão norte-americano (aaaa-mm-dd),
	 * então, faz o mesmo processo de conversão e de validação.
	 * No final retorna se a data é verdadeira ou falsa.
	 *
	 * @param string $data data de entrada fornecida pelo usuário ou pelo banco de dados
	 * @return boolean
	 */
	public static function validarData($data) {
		if (strpos($data, '/')) {
			list($dia, $mes, $ano) = explode('/', $data);
			if (checkdate($mes, $dia, $ano)) {
				return true;
			} else {
				return false;
			}
		} elseif (strpos($data, '-')) {
			list($ano, $mes, $dia) = explode('-', $data);
			if (checkdate($mes, $dia, $ano)) {
				return true;
			} else {
				return false;
			}
		}
	}

	public static function formatarTempo($tempo) {
		$dataFormatada = '';
		if (!empty($tempo)) {
			if (self::validarTempo($tempo)) {
				$a = explode(':', $tempo);
				$hora = $a[0];
				$minuto = $a[1];
				$dataFormatada = $hora . ':' . $minuto;
			}
		}
		return $dataFormatada;
	}

	public static function validarTempo($tempo) {
		$a = explode(':', $tempo);
		$hora = $a[0];
		$minuto = $a[1];
		$segundo = (isset($a[2]) ? $a[2] : '00');
		if (($hora >= 0 && $hora <= 23) && ($minuto >= 0 && $minuto <= 59) && ($segundo >= 0 && $segundo <= 59)) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 *
	 * Formata a data do padrão brasileiro (dd/mm/aaaa) para o padrão de banco de dados (aaaa-mm-dd) ou virse-versa.
	 *
	 * Primeiramente verifica a existência do caracter barra (/), se houver a existência
	 * é porque a data está no padrão brasileiro (dd/mm/aaaa). Se esta condição for satisfeita
	 * é feita conversão de string para array separando dia, mês e ano e retorna a data no padrão
	 * norte-americano (aaaa-mm-dd). Agora se já estiver no padrão norte-americano faz o mesmo processo
	 * de conversão de string para array e retorna no padrão brasileiro.
	 *
	 * @param string $data obtém a data fornecida pelo usuário seja ela padrão brasileiro ou norte-americano
	 * @return string
	 */
	public static function formatarData($data, $padrao = null) {
		$dataFormatada = '';
		if (self::validarData($data)) {
			switch ($padrao) {
				case PADRAO_BD:
					$dataFormatada = self::formatarDataParaPadraoBD($data);
					break;
				case PADRAO_BRASILEIRO:
					$dataFormatada = self::formatarDataParaPadraoBrasileiro($data);
					break;
				default :
					$dataFormatada = (strpos($data, '/') ? self::formatarDataParaPadraoBD($data) : self::formatarDataParaPadraoBrasileiro($data));
					break;
			}
		}
		return $dataFormatada;
	}

	private static function formatarDataParaPadraoBD($data) {
		$dataFormatada = '';
		if (strpos($data, '/')) {
			list($dia, $mes, $ano) = explode('/', $data);
			$dataFormatada = $ano . '-' . $mes . '-' . $dia;
		} else {
			$dataFormatada = $data;
		}
		return $dataFormatada;
	}

	private static function formatarDataParaPadraoBrasileiro($data) {
		$dataFormatada = '';
		if (strpos($data, '-')) {
			list($ano, $mes, $dia) = explode('-', $data);
			$dataFormatada = $dia . '/' . $mes . '/' . $ano;
		} else {
			$dataFormatada = $data;
		}
		return $dataFormatada;
	}

	/**
	 *
	 * Formata a data do padrão brasileiro (dd/mm/aaaa) para o padrão de banco de dados (aaaa-mm-dd)
	 * ou virse-versa.
	 *
	 * Primeiramente verifica a existência do caracter barra (/), se houver a existência
	 * é porque a data está no padrão brasileiro (dd/mm/aaaa). Se esta condição for satisfeita
	 * é feita conversão de string para array separando dia, mês e ano e retorna a data no padrão
	 * norte-americano (aaaa-mm-dd). Agora se já estiver no padrão norte-americano faz o mesmo processo
	 * de conversão de string para array e retorna no padrão brasileiro.
	 *
	 * @param string $data obtém a data fornecida pelo usuário seja ela padrão brasileiro ou norte-americano
	 * @return string
	 */
	public static function formatarDataHora($dataHora) {
		$arrDataHora = explode(' ', $dataHora);
		$data = $arrDataHora[0];
		$hora = $arrDataHora[1];
		if (self::validarData($data)) {
			if (strpos($data, '/')) {
				$array = array();
				list($dia, $mes, $ano) = explode('/', $data);
				$data = $ano . '-' . $mes . '-' . $dia;
			} elseif (strpos($data, '-')) {
				$array = array();
				list($ano, $mes, $dia) = explode('-', $data);
				$data = $dia . '/' . $mes . '/' . $ano;
			}
			return $data . ' ' . self::formatarTempo($hora);
		}
	}

	public static function obterHora($tempo) {
		$hora = '';
		if (strpos($tempo, ' ')) {
			list($data, $tempo) = explode(' ', $tempo);
			if (self::validarTempo($tempo)) {
				list($hora, $minuto, $segundo) = explode(':', $tempo);
			}
		} else {
			if (strlen($tempo) == 8) {
				if (self::validarTempo($tempo)) {
					list($hora, $minuto, $segundo) = explode(':', $tempo);
				}
			} else if (strlen($tempo) == 5) {
				if (self::validarTempo($tempo)) {
					list($hora, $minuto) = explode(':', $tempo);
				}
			}
		}
		return $hora;
	}

	public static function obterMinuto($tempo) {
		$minuto = '';
		if (strpos($tempo, ' ')) {
			list($data, $tempo) = explode(' ', $tempo);
			if (self::validarTempo($tempo)) {
				list($hora, $minuto, $segundo) = explode(':', $tempo);
			}
		} else {
			if (strlen($tempo) == 8) {
				if (self::validarTempo($tempo)) {
					list($hora, $minuto, $segundo) = explode(':', $tempo);
				}
			} else if (strlen($tempo) == 5) {
				if (self::validarTempo($tempo)) {
					list($hora, $minuto) = explode(':', $tempo);
				}
			}
		}
		return $minuto;
	}

	/**
	 * Obtém o dia de uma data
	 *
	 * @param string $data
	 * @return string
	 */
	public static function obterDia($data) {
		$dia = '';
		if (self::validarData($data)) {
			if (strpos($data, '/')) {
				list($dia, $mes, $ano) = explode('/', $data);
			} elseif (strpos($data, '-')) {
				list($ano, $mes, $dia) = explode('-', $data);
			}
		}
		return $dia;
	}

	/**
	 * Obtém o mês de uma data
	 *
	 * @param string $data
	 * @return string
	 */
	public static function obterMes($data) {
		$mes = '';
		if (self::validarData($data)) {
			if (strpos($data, '/')) {
				list($dia, $mes, $ano) = explode('/', $data);
			} elseif (strpos($data, '-')) {
				list($ano, $mes, $dia) = explode('-', $data);
			}
		}
		return $mes;
	}

	public static function obterPrimeiroDiaMes($mes, $ano) {
		$data = $ano . '-' . $mes . '-01';
		if (self::validarData($data)) {
			return $data;
		} else {
			return date('Y-m-01');
		}
	}

	public static function obterUltimoDiaMes($mes, $ano) {
		return date('Y-m-t', mktime(0, 0, 0, $mes, 1, $ano));
	}

	/**
	 * Obtém o ano de uma data
	 *
	 * @param string $data
	 * @return string
	 */
	public static function obterAno($data) {
		$ano = '';
		if (self::validarData($data)) {
			if (strpos($data, '/')) {
				list($dia, $mes, $ano) = explode('/', $data);
			} elseif (strpos($data, '-')) {
				list($ano, $mes, $dia) = explode('-', $data);
			}
		}
		return $ano;
	}

	public function compararData($data1, $comparador, $data2 = null) {
		$data2 = is_null($data2) ? date('Y-m-d') : $data2;
		$timestampData1 = mktime(0, 0, 0, self::obterMes($data1), self::obterDia($data1), self::obterAno($data1));
		$timestampData2 = mktime(0, 0, 0, self::obterMes($data2), self::obterDia($data2), self::obterAno($data2));
		switch ($comparador) {
			case DATA_IGUAL:
				$timestampResultado = ($timestampData1 == $timestampData2 ? true : false);
				break;
			case DATA_MAIOR:
				$timestampResultado = ($timestampData1 > $timestampData2 ? true : false);
				break;
			case DATA_MENOR:
				$timestampResultado = ($timestampData1 < $timestampData2 ? true : false);
				break;
			case DATA_MAIOR_OU_IGUAL:
				$timestampResultado = ($timestampData1 >= $timestampData2 ? true : false);
				break;
			case DATA_MENOR_OU_IGUAL:
				$timestampResultado = ($timestampData1 <= $timestampData2 ? true : false);
				break;
			case DATA_DIFERENTE:
				$timestampResultado = ($timestampData1 != $timestampData2 ? true : false);
				break;
			default:
				$timestampResultado = false;
				break;
		}
		return $timestampResultado;
	}

	public static function intervaloEntreData($data1, $data2) {
		$dataInicial = new DateTime($data1);
		$dataFinal = new DateTime($data2);
		$dataIntervalo = $dataInicial->diff($dataFinal);
		return $dataIntervalo->days;
	}

	/**
	 * @access public
	 * @param string $str
	 * @return string @str
	 *
	 * Este método retorna a string passada no @param $str e converte
	 * em um texto de URL AMIGÁVEL
	 * Ex.:
	 * Texto Origional		:	Método para geração de Permalink ( URL AMIGÁVEL )
	 * Texto Convertido		:	metodo-para-geracao-de-permalink-url-amigavel
	 *
	 */
	public function formatarPermalink($str) {
		$normalize = array(
			'&' => '-e-', 'Ç' => 'c', 'ç' => 'c', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A',
			'Å' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I',
			'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ù' => 'U', 'Ú' => 'U',
			'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a',
			'å' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i',
			'ï' => 'i', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ù' => 'u',
			'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'ý' => 'y', 'ÿ' => 'y',
		);

		$str = strip_tags($str, '<(.*?)>');
		$str = strtr($str, $normalize);
		$str = strtolower($str);
		$str = preg_replace('/[^a-zA-Z0-9 -]/', '', $str);
		$str = trim($str);
		$str = strtr($str, ' ', '-');
		for ($i = 0; $i < strlen($str); $i++) {
			$str = str_replace('--', '-', $str);
		}
		$str = trim($str, '-');

		return $str;
	}

	/**
	 *
	 * Formata o cnpj de (00.000.000/0000-00) para (00000000000000)
	 * ou formata o cnpj de (00000000000000) para (00.000.000/0000-00)
	 *
	 * Obtém o cnpj e verifica se o comprimento da string é igual a 18 (dezoito) e se
	 * há existência dos caracteres ponto (.), barra (/) e hífen (-) se todas as condições
	 * forem satisfeitas então remove todos os caracteres deixando apenas os numéros.
	 * Agora se não for satisfeita verifica se o comprimento é igual a 14 incluindo os caracteres
	 * ponto (.), barra (/) e hífen (-) nos seus respectivos lugares no cnpj.
	 * No final do método retorna o cnpj já formatado.
	 *
	 * @param string $cnpj cnpj com apenas numéros ou com os caracteres ponto (.), barra (/) e hífen (-)
	 * @return string
	 */
	public static function formatarCNPJ($cnpj, $padrao = null) {
		$cnpjFormatado = '';
		switch ($padrao) {
			case PADRAO_BD:
				$cnpjFormatado = self::formatarCNPJParaPadraoBD($cnpj);
				break;
			case PADRAO_BRASILEIRO:
				$cnpjFormatado = self::formatarCNPJParaPadraoBrasileiro($cnpj);
				break;
			default :
				$cnpjFormatado = ((strlen($cnpj) == 18) && (strpos($cnpj, '.') && strpos($cnpj, '/') && strpos($cnpj, '-')) ? self::formatarCNPJParaPadraoBD($cnpj) : self::formatarCNPJParaPadraoBrasileiro($cnpj));
				break;
		}
		return $cnpjFormatado;
	}

	private static function formatarCNPJParaPadraoBD($cnpj) {
		$cnpjFormatado = '';
		if ((strlen($cnpj) == 18) && (strpos($cnpj, '.') && strpos($cnpj, '/') && strpos($cnpj, '-'))) {
			$cnpjFormatado = str_replace(array('.', '/', '-'), '', $cnpj);
		} else {
			$cnpjFormatado = $cnpj;
		}
		return $cnpjFormatado;
	}

	private static function formatarCNPJParaPadraoBrasileiro($cnpj) {
		$cnpjFormatado = '';
		if (strlen($cnpj) == 14) {
			$cnpjFormatado .= substr($cnpj, 0, 2) . '.';
			$cnpjFormatado .= substr($cnpj, 2, 3) . '.';
			$cnpjFormatado .= substr($cnpj, 5, 3) . '/';
			$cnpjFormatado .= substr($cnpj, 8, 4) . '-';
			$cnpjFormatado .= substr($cnpj, 12, 2);
		} else {
			$cnpjFormatado = $cnpj;
		}
		return $cnpjFormatado;
	}

	/**
	 *
	 * @param string $cnpj
	 * @return boolean
	 */
	public static function validarCNPJ($cnpj) {
		$j = 0;
		$num = array();
		for ($i = 0; $i < (strlen($cnpj)); $i++) {
			if (is_numeric($cnpj[$i])) {
				$num[$j] = $cnpj[$i];
				$j++;
			}
		}
		if (count($num) != 14) {
			$isCnpjValid = false;
		}
		if ($num[0] == 0 && $num[1] == 0 && $num[2] == 0 && $num[3] == 0 && $num[4] == 0 && $num[5] == 0 && $num[6] == 0 && $num[7] == 0 && $num[8] == 0 && $num[9] == 0 && $num[10] == 0 && $num[11] == 0) {
			$isCnpjValid = false;
		} else {
			$j = 5;
			for ($i = 0; $i < 4; $i++) {
				$multiplica[$i] = $num[$i] * $j;
				$j--;
			}
			$soma = array_sum($multiplica);
			$j = 9;
			for ($i = 4; $i < 12; $i++) {
				$multiplica[$i] = $num[$i] * $j;
				$j--;
			}
			$soma = array_sum($multiplica);
			$resto = $soma % 11;
			if ($resto < 2) {
				$dg = 0;
			} else {
				$dg = 11 - $resto;
			}
			if ($dg != $num[12]) {
				$isCnpjValid = false;
			}
		}
		if (!isset($isCnpjValid)) {
			$j = 6;
			for ($i = 0; $i < 5; $i++) {
				$multiplica[$i] = $num[$i] * $j;
				$j--;
			}
			$soma = array_sum($multiplica);
			$j = 9;
			for ($i = 5; $i < 13; $i++) {
				$multiplica[$i] = $num[$i] * $j;
				$j--;
			}
			$soma = array_sum($multiplica);
			$resto = $soma % 11;
			if ($resto < 2) {
				$dg = 0;
			} else {
				$dg = 11 - $resto;
			}
			if ($dg != $num[13]) {
				$isCnpjValid = false;
			} else {
				$isCnpjValid = true;
			}
		}
		return $isCnpjValid;
	}

	public static function formatarCPF($cpf, $padrao = null) {
		$cpfFormatado = '';
		switch ($padrao) {
			case PADRAO_BD:
				$cpfFormatado = self::formatarCPFParaPadraoBD($cpf);
				break;
			case PADRAO_BRASILEIRO:
				$cpfFormatado = self::formatarCPFParaPadraoBrasileiro($cpf);
				break;
			default :
				$cpfFormatado = ((strlen($cpf) == 14) && (strpos($cpf, '.') && strpos($cpf, '-')) ? self::formatarCPFParaPadraoBD($cpf) : self::formatarCPFParaPadraoBrasileiro($cpf));
				break;
		}
		return $cpfFormatado;
	}

	private static function formatarCPFParaPadraoBD($cpf) {
		$cpfFormatado = '';
		if ((strlen($cpf) == 14) && (strpos($cpf, '.') && strpos($cpf, '-'))) {
			$cpfFormatado = str_replace(array('.', '-', '_'), '', $cpf);
		} else {
			$cpfFormatado = $cpf;
		}
		return $cpfFormatado;
	}

	private static function formatarCPFParaPadraoBrasileiro($cpf) {
		$cpfFormatado = '';
		if (strlen($cpf) == 11) {
			$cpfFormatado .= substr($cpf, 0, 3) . '.';
			$cpfFormatado .= substr($cpf, 3, 3) . '.';
			$cpfFormatado .= substr($cpf, 6, 3) . '-';
			$cpfFormatado .= substr($cpf, 9, 2);
		} else {
			$cpfFormatado = $cpf;
		}
		return $cpfFormatado;
	}

	/**
	 *
	 * @param string $cpf
	 * @return boolean
	 */
	public static function validarCPF($cpf) {
		if (empty($cpf)) {
			return false;
		}
		$cpf2 = str_pad(str_replace(array('.', '-', '_'), '', $cpf), 11, 0, STR_PAD_LEFT);
		if (strlen($cpf2) != 11 || (in_array($cpf2, array('00000000000', '11111111111', '22222222222', '33333333333', '44444444444', '55555555555', '66666666666', '77777777777', '88888888888', '99999999999')))) {
			return false;
		} else {
			for ($t = 9; $t < 11; $t++) {
				for ($d = 0, $c = 0; $c < $t; $c++) {
					$d += substr($cpf2, $c, 1) * (($t + 1) - $c);
				}
				$d = ((10 * $d) % 11) % 10;
				if (substr($cpf2, $c, 1) != $d) {
					return false;
				}
			}
			return true;
		}
	}

	public static function formatarCPFouCNPJ($pessoaTipo, $CPFouCNPJ) {
		switch ($pessoaTipo) {
			case PESSOA_FISICA:
				return self::formatarCPF($CPFouCNPJ);
				break;
			case PESSOA_JURIDICA:
				return self::formatarCNPJ($CPFouCNPJ);
				break;
		}
	}

	public static function ziparArquivo($arquivoZIP, $listaDeArquivo, $senhaDoArquivo = null) {
		$zip = new ZipArchive();
		if ($zip->open($arquivoZIP, ZIPARCHIVE::CREATE) == true) {
			if (!is_null($senhaDoArquivo)) {
				$zip->setPassword($senhaDoArquivo);
			}
			foreach ($listaDeArquivo as $arquivo) {
				$zip->addFile($arquivo['arquivoEndereco'], $arquivo['arquivoNome']);
				if (!is_null($senhaDoArquivo)) {
//					$zip->setEncryptionName($arquivo['arquivoNome'], ZipArchive::EM_AES_256);
				}
			}
		}
		$zip->close();
	}

	public static function baixarArquivo($arquivoNome, $arquivoEndereco) {
		if (Diretorio::verificarExistenciaDoArquivo($arquivoEndereco)) {
			$listaDemimeType = upload::mimeType();
			$extensao = upload::obterArquivoExtensao($arquivoEndereco);
			header('Content-Description: File Transfer');
			header('Content-Type: ' . $listaDemimeType[$extensao]);
			header('Content-Disposition: attachment; filename=' . $arquivoNome);
			header('Content-Length: ' . filesize($arquivoEndereco));
			header('Content-Transfer-Encoding: binary');
			header('Connection: Keep-Alive');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: public');
			readfile($arquivoEndereco);
		}
	}

	public function textAreaClearSimpleHtmlDom($str) {

		$html = new simple_html_dom();
		$html->load($str);

		foreach ($html->find('img') as $key => $value) {
			foreach ($value as $keyA => $valueA) {
				if ($keyA == 'attr') {
					foreach ($valueA as $keyB => $valorB) {
						if (
							$keyB != 'src' and
							$keyB != 'title' and
							$keyB != 'alt'
						) {
							unset($html->find('img')[$key]->$keyB);
						}
					}
				}
			}
		}

		foreach ($html->find('a') as $key => $value) {
			foreach ($value as $keyA => $valueA) {
				if ($keyA == 'attr') {
					foreach ($valueA as $keyB => $valorB) {
						if (
							$keyB != 'href' and
							$keyB != 'target'
						) {
							unset($html->find('a')[$key]->$keyB);
						}
					}
				}
			}
		}

		foreach ($html->nodes as $key => $value) {
			foreach ($value as $keyA => $valueA) {
				if (
					$keyA == 'tag' and
					$valueA != 'img' and
					$valueA != 'a' and
					$valueA != 'root' and
					$valueA != 'text'
				) {
					foreach ($html->find($valueA) as $keyB => $valueB) {
						$html->find($valueA)[$keyB]->attr = array();
					}
				}
			}
		}

		$search = array(
			'@<title[^>]*?>.*?</title>@si',
			'@<script[^>]*?>.*?</script>@si',
			'@<style[^>]*?>.*?</style>@siU'
		);
		$html = preg_replace($search, '', $html);

		$html = trim(strip_tags($html, '<h1><h2><h3><h4><h5><hgroup><p><hr><blockquote><ol><ul><li><dl><dt><dd><figure><figcaption><a><strong><small><s><cite><q><abbr><data><time><i><em><b><u><br><img><video><audio><table><tr><td><th>'));

		// Emoticons
		$regexEmoticons = '/[\x{1F600}-\x{1F64F}]/u';
		$html = preg_replace($regexEmoticons, '', $html);

		// Símbolos e pictogramas diversos
		$regexSymbols = '/[\x{1F300}-\x{1F5FF}]/u';
		$html = preg_replace($regexSymbols, '', $html);

		// Transportes e símbolos de mapa
		$regexTransport = '/[\x{1F680}-\x{1F6FF}]/u';
		$html = preg_replace($regexTransport, '', $html);

		// Símbolos Diversos
		$regexMisc = '/[\x{2600}-\x{26FF}]/u';
		$html = preg_replace($regexMisc, '', $html);

		// Dingbats
		$regexDingbats = '/[\x{2700}-\x{27BF}]/u';
		$html = preg_replace($regexDingbats, '', $html);

		$html = str_replace("\r", '', $html);
		$html = str_replace("\n", ' ', $html);
		$html = str_replace("\t", ' ', $html);
		$html = preg_replace("/> {2,}</is", "><", $html);

		return $html;
	}

}

?>
