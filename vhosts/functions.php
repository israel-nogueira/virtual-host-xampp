<?
include('./config.php');

if($_POST['funcao']=='globals::returnVhosts'){
	header('Content-Type: application/json');
	echo json_encode(globals::exec($_POST['funcao']),JSON_PRETTY_PRINT);
}
if($_POST['funcao']=='globals::salvarHost'){
	globals::exec($_POST['funcao']);
}
if($_POST['funcao']=='globals::addHost'){
	globals::exec($_POST['funcao']);
}
if($_POST['funcao']=='globals::deleteHost'){
	globals::exec($_POST['funcao']);
}

