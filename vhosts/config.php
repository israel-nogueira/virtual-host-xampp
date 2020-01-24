<?

class globals {
	const ip_local = '127.0.0.1';
	$vhosts_conf	= null;


	public function __construct() {
			$this->vhosts_conf ='/apache/conf/extra/httpd-vhosts.conf';
	}


	public static function rootPath() {
		return realpath(__DIR__.'/../../');
	}
	public static function getVhosts() {
		return globals::rootPath() . $this->vhosts_conf;
	}
	public static function getDashboardPermition() {
		$filename = globals::get_hostsfile_dir().'\hosts';
		if (!is_writable($filename)) {
		    echo '<div class="alert alert-danger" role="alert">
					  O arquivo <b>'.$filename.'</b> não tem permissão de acesso!
					</div>';
		} 
		$filename = globals::getVhosts();
		if (!is_writable($filename)) {
		    echo '<div class="alert alert-danger" role="alert">
					  O arquivo <b>'.$filename.'</b> não tem permissão de acesso!
					</div>';
		} 

	}
	public static function returnTemplateVHost($_NEWHOST) {
		return 	'<VirtualHost ' . $_NEWHOST['domain'] . '>' . PHP_EOL .
				'	ServerAdmin 	webmaster@' . $_NEWHOST['domain'] . PHP_EOL .
				'	DocumentRoot 	"' . $_NEWHOST['diretorio'] . '"' . PHP_EOL .
				'	ServerName 		' . $_NEWHOST['domain'] . PHP_EOL .
				'	ErrorLog 		"' . $_NEWHOST['diretorio'] . '/error.log"' . PHP_EOL .
				'	CustomLog 		"' . $_NEWHOST['diretorio'] . '/access.log" common' . PHP_EOL .
				'	<Directory 		"' . $_NEWHOST['diretorio'] . '">' . PHP_EOL .
				implode(array_map(function ($opt) {
					return "		" . trim($opt);
				}, explode("\n", $_NEWHOST['permissions'])) , "\n") . PHP_EOL . 
				'	</Directory>' . PHP_EOL . 
				'</VirtualHost>' . PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL;
	}
	public static function deleteHost() {
		$_POST['type'] = 'vhosts';
		$_VHOSTS = globals::returnVhosts();
		$_INDEX = $_POST['index'];
		unset($_VHOSTS[$_INDEX]);
		$_VHOSTS_FINIT = array_map(function ($domain) {
			return globals::returnTemplateVHost($domain);
		}
		, $_VHOSTS);
		$_HOST_WINDOW = array();
		foreach ($_VHOSTS as $value) {
			$_HOST_WINDOW[] = globals::ip_local . '	' . $value['domain'];
		};
		file_put_contents(globals::get_hostsfile_dir() . '\hosts', implode($_HOST_WINDOW, PHP_EOL));
		file_put_contents(globals::getVhosts(), $_VHOSTS_FINIT);
	}
	public static function salvarHost() {
		$_POST['type'] = 'vhosts';
		$_VHOSTS = globals::returnVhosts();
		$_INDEX = $_POST['index'];
		$_DOMAIN = $_POST['domain'];
		$_PATH = $_POST['diretorio'];
		$_OPTIONS = $_POST['permissions'];
		$_NEWHOST = array(
			"index" => $_INDEX,
			"domain" => $_DOMAIN,
			"diretorio" => $_PATH,
			"permissions" => $_OPTIONS
		);
		$_VHOSTS[$_INDEX] = $_NEWHOST;
		$_VHOSTS_FINIT = array_map(function ($domain) {
			return globals::returnTemplateVHost($domain);
		}
		, $_VHOSTS);
		$_HOST_WINDOW = array();
		foreach ($_VHOSTS as $value) {
			$_HOST_WINDOW[] = globals::ip_local . '	' . $value['domain'];
		};
		file_put_contents(globals::get_hostsfile_dir() . '\hosts', implode($_HOST_WINDOW, PHP_EOL));
		file_put_contents(globals::getVhosts(), $_VHOSTS_FINIT);
	}
	public static function addHost() {
		$_POST['type'] = 'vhosts';
		$_VHOSTS = globals::returnVhosts();
		$_DOMAIN = $_POST['domain'];
		$_PATH = $_POST['diretorio'];
		$_OPTIONS = $_POST['permissions'];
		$_NEWHOST = array(
			"domain" => $_DOMAIN,
			"diretorio" => $_PATH,
			"permissions" => $_OPTIONS
		);
		$_VHOSTS[] = $_NEWHOST;
		$_VHOSTS_FINIT = array_map(function ($domain) {
			return globals::returnTemplateVHost($domain);
		}
		, $_VHOSTS);
		$_HOST_WINDOW = array();
		foreach ($_VHOSTS as $value) {
			$_HOST_WINDOW[] = globals::ip_local . '	' . $value['domain'];
		};
		if(!file_exists($_PATH)){
			die(false);
		}else{
			file_put_contents(globals::get_hostsfile_dir() . '\hosts', implode($_HOST_WINDOW, PHP_EOL));
			file_put_contents(globals::getVhosts(), $_VHOSTS_FINIT);
			die(true);
		}
	}

	public static function returnVhosts() {
		$vhosts_content = @file_get_contents(globals::getVhosts());
		if ($_POST['type'] == 'vhosts') {
			$matches = array();
			$_RETURN_ARRAY = array();
			preg_match_all("'(#|)<VirtualHost(.*?)<\/VirtualHost>'si", $vhosts_content, $matches);
			foreach ($matches[0] as $key => $vhost) {
				preg_match("'DocumentRoot(.*?)\"(.*?)\"'si", $vhost, $documentroot);
				preg_match("'ServerName (.*?)\n'si", $vhost, $servername);
				preg_match_all("/<Directory(.*?)>(.*?)<\/Directory>/s", $vhost, $Directory);
				$_OPTIONS = trim($Directory[2][0]);
				$_PATH = trim($documentroot[2]);
				$_DOMAIN = trim($servername[1]);
				$_RETURN_ARRAY[] = array(
					"index" => $key,
					"domain" => $_DOMAIN,
					"diretorio" => $_PATH,
					"permissions" => $_OPTIONS
				);
			}

			return $_RETURN_ARRAY;
		}

		if ($_POST['type'] == 'servernames') {
			$matches = array();
			$servernames_array = array();
			preg_match_all("'(#|)<VirtualHost(.*?)<\/VirtualHost>'si", $vhosts_content, $matches);
			foreach ($matches[0] as $key => $vhost) {
				preg_match("'ServerName (.*?)\n'si", $vhost, $servername);
				$servernames_array[] = trim($servername[1]);
			}
			return $servernames_array;
		}
		if ($_POST['type'] == 'file') return $vhosts_content;
	}

	public static function get_hostsfile_dir() {
		$hostlist = array(
			// 95, 98/98SE, Me 	%WinDir%\
			// NT, 2000, and 32-bit versions of XP, 2003, Vista, 7 	%SystemRoot%\system32\drivers\etc\
			// 64-bit versions 	%SystemRoot%\system32\drivers\etc\
			'' => '#Windows 95|Win95|Windows_95#i',
			'' => '#Windows 98|Win98#i',
			'' => '#Windows ME#i',
			'\system32\drivers\etc' => '#Windows NT 4.0|WinNT4.0|WinNT|Windows NT#i',
			'\system32\drivers\etc' => '#Windows NT 5.0|Windows 2000#i',
			'\system32\drivers\etc' => '#Windows NT 5.1|Windows XP#i',
			'\system32\drivers\etc' => '#Windows NT 5.2#i',
			'\system32\drivers\etc' => '#Windows NT 6.0#i',
			'\system32\drivers\etc' => '#Windows NT 7.0#i',
		);
		foreach ($hostlist as $hostdir => $regex) {
			if (preg_match($regex, $_SERVER['HTTP_USER_AGENT'])) break;
		}
		// Return FALSE is hosts cannot be opened
		$hosts_path = $_SERVER['WINDIR'] . $hostdir;
		return $hosts_path;
	}

	public static function check_hash($server_name) {
		$hostsfile_array = read_hostsfile('file');
		foreach ($hostsfile_array as $line) {
			$pos_hash = stripos($line, '#');
			$pos_127001 = stripos($line, '127.0.0.1');
			$pos_servername = stripos($line, $server_name);
			if (($pos_127001 !== false) and ($pos_servername !== false)) {
				if (($pos_hash !== false) and ($pos_hash < $pos_127001)) {
					$hash = "on";
				}
				else {
					$hash = "off";
				}
			}
		}
		return $hash;
	}

	public static function read_hostsfile($part) {
		$hostsfile_array = array();
		$hosts_array = array();
		$hostsfile = @file_get_contents(globals::get_hostsfile_dir() . '\hosts', 'r');
		$hostsfile_array = explode("\n", $hostsfile);
		foreach ($hostsfile_array as $line) {
			if ((stripos($line, '127.0.0.1') !== false) and ((stripos($line, '127.0.0.1')) < 3)) {
				$line_array = explode('127.0.0.1', $line);
				$hosts_array[] = trim($line_array[1]);
			}
		}
		if ($part == 'file') return $hostsfile_array;
		if ($part == 'hosts') return $hosts_array;
	}

	public static function exec($funcao = null) {
		$whitelistFn = get_defined_functions();
		$whitelistCls = array_map('strtolower', get_declared_classes());
		if ($funcao == null) {
			die('Variável de função nao existe (function): ' . $funcao);
		}
		else {
			if (strpos($funcao, '::') === false) {
				if (in_array(strtolower($funcao) , $whitelistFn['user']) && function_exists($funcao)) {
					return call_user_func($funcao);
				}
				else {
					die('Função chamada não existe ou é ilegal');
				}
			}
			else {
				$funcao = explode('::', $funcao);
				if (in_array(strtolower($funcao[0]) , $whitelistCls) && method_exists($funcao[0], $funcao[1])) {
					return call_user_func($funcao);
				}
				else {
					die('Método estatico chamado não existe ou é ilegal');
				}
			}
		}
	}

}

