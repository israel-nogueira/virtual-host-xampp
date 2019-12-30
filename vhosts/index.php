<? include('./config.php'); ?>



<!DOCTYPE html>
<html>
<head>
<meta name="description" content="Child Rows">
	<title></title>
	<meta charset="utf-8" />
	<link href="https://nightly.datatables.net/css/jquery.dataTables.css" rel="stylesheet" type="text/css" />
	<link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Work+Sans&display=swap" rel="stylesheet">
	<link href="./bootstrap-4.4.1-dist/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
	<link href="./style.css?v=<?=rand(1,99999999999)?>" rel="stylesheet" type="text/css" />
	
	<script src="https://code.jquery.com/jquery-3.1.0.js"></script>
	<script src="https://cdn.datatables.net/1.10.13/js/jquery.dataTables.js"></script>
	<script src="https://cdn.datatables.net/select/1.2.1/js/dataTables.select.min.js" type="text/javascript"></script>
	<script src="./bootstrap-4.4.1-dist/js/bootstrap.min.js"></script>
	<script src="./script.js?v=<?=rand(1,99999999999)?>" type="text/javascript"></script>
</head>
<body>
	
<?
	globals::getDashboardPermition();
?>


<!-- Botão para acionar modal -->
<button type="button" class="adicionar-dominio btn btn-primary">
  Adicionar domínio VHOSTSs
</button>

<div class="modal fade" id="adicionarDominio" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
	<div class="modal-content">
	  <div class="modal-header">
		<h5 class="modal-title">Adicionar um vHost</h5>
		<button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
		  <span aria-hidden="true">&times;</span>
		</button>
	  </div>
	  <div class="modal-body">
			<form>
				<input type="hidden" class="form-control" name="index">
				<div class="form-group">
					<label for="recipient-name" class="col-form-label">Domínio:</label>
					<input type="text" class="form-control" name="dominio">
				</div>
				<div class="form-group">
					<label for="recipient-name" class="col-form-label">Diretório:</label>
					<input type="text" class="form-control" name="diretorio">
				</div>
				<div class="form-group">
					<label for="message-text" class="col-form-label">Permissões:</label>
					<textarea class="form-control" name="permissoes" rows="6" >Options FollowSymLinks Indexes ExecCGI
AllowOverride All
Order deny,allow
Allow from 127.0.0.1
Deny from all
Require all granted</textarea>
				</div>
			</form>
	  </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-success">Salvar alterações</button>
		<button type="button" class="btn btn-secondary" >Cancelar</button>
	  </div>
	</div>
  </div>
</div>





<div class="modal fade" id="excluirDominio" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
	<div class="modal-content">
	  <div class="modal-header">
		<h5 class="modal-title">Excluir domínio</h5>
		<button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
		  <span aria-hidden="true">&times;</span>
		</button>
	  </div>
	  <div class="modal-body">
		Você quer excluir o domínio <span></span>?<br>
		Essa ação será irreversível!
	  </div>
	  <div class="modal-footer">
		<button type="button" class="deletar-dominio btn btn-danger" data-dismiss="modal">Sim, excluir</button>
		<button type="btn btn-info" class="btn btn-secondary" >Cancelar</button>
	  </div>
	</div>
  </div>
</div>


<div class="modal fade" id="editarDominio" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
	<div class="modal-content">
	  <div class="modal-header">
		<h5 class="modal-title">Editar domínio</h5>
		<button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
		  <span aria-hidden="true">&times;</span>
		</button>
	  </div>
	  <div class="modal-body">
			<form>
				<input type="hidden" class="form-control" name="index">
				<div class="form-group">
					<label for="recipient-name" class="col-form-label">Domínio:</label>
					<input type="text" class="form-control" name="dominio">
				</div>
				<div class="form-group">
					<label for="recipient-name" class="col-form-label">Diretório:</label>
					<input type="text" class="form-control" name="diretorio">
				</div>
				<div class="form-group">
					<label for="message-text" class="col-form-label">Permissões:</label>
					<textarea class="form-control" name="permissoes" rows="6" ></textarea>
				</div>
			</form>
	  </div>
	  <div class="modal-footer">
		<button type="button" class="btn btn-success">Salvar alterações</button>
		<button type="button" class="btn btn-secondary" >Cancelar</button>
	  </div>
	</div>
  </div>
</div>

<div class="modal fade" id="alertaDiretorio" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
	<div class="modal-content">
	  <div class="modal-header">
		<h5 class="modal-title">Path inexistente</h5>
	  </div>
	  <div class="modal-body">
		O Path que você digitou não existe.<br> 
		Isso impedirá o APACHE de iniciar corretamente.<br>
		Diretório: <span class="domain"></span>
	  </div>
	  <div class="modal-footer">
		<button type="button" 		class="entendi btn btn-info" data-dismiss="modal">Ok, entendi</button>
	  </div>
	</div>
  </div>
</div>
<div class="modal fade" id="parabensCriado" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
	<div class="modal-content">
	  <div class="modal-header">
		<h5 class="modal-title">Sucesso!</h5>
	  </div>
	  <div class="modal-body">
		O dominio <span class="domain"></span> foi registrado com sucesso em seu vhost.conf <br>
		Reinicie seu apache para que seu dominio funcione corretamente

	  </div>
	  <div class="modal-footer">
		<button type="button" 		class="entendi btn btn-info" data-dismiss="modal">Ok, entendi</button>
	  </div>
	</div>
  </div>
</div>




	<table width="100%" class="display" id="example" cellspacing="0">
		<thead>
			<tr>
				<th></th>
				<th>Domínio</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th></th>
				<th>Domínio</th>
			</tr>
		</tfoot>
	</table>
</body>
</html>