<?
include('./config.php');

	
if($_REQUEST['funcao']=='globals::returnVhosts'){
	header('Content-Type: application/json');
	echo json_encode(globals::exec($_REQUEST['funcao']),JSON_PRETTY_PRINT);
}
if($_REQUEST['funcao']=='globals::salvarHost'){
	globals::exec($_REQUEST['funcao']);
}
if($_REQUEST['funcao']=='globals::addHost'){
	globals::exec($_REQUEST['funcao']);
}
if($_REQUEST['funcao']=='globals::deleteHost'){
	globals::exec($_REQUEST['funcao']);
}

