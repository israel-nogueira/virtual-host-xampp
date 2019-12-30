	$(document).ready(function () {
			$.ajax({
				method: "POST",
				url: "./functions.php",
				data: {'funcao':'globals::returnVhosts','type':'vhosts'}
			}).done(function( data ) {
				var table = $('#example').DataTable({
					 "data": data,
					 paging: false,
					 select:false,
					 "columns": [
						 {
							 "className": 'details-control',
							 "orderable": true,
							 "data": null,
							 "defaultContent": '',
							 "render": function () { },
							 width:"15px"
						 },

						 { "data": "domain", "name": "domain",
						        fnCreatedCell: function (nTd, sData, oData, iRow, iCol) {
						            $(nTd).html("<a href='http://"+oData.domain+"' target='_blank'>"+oData.domain+"</a>");
						        }
						    }


					 ],
					 "order": [[1, 'asc']]
				 });
				 $('#example tbody').on('click', 'td.details-control', function () {
					 var tr = $(this).closest('tr');
					 var row = table.row(tr);

					 if (row.child.isShown()) {
						 row.child.hide();
						 tr.removeClass('shown');
					 }else {
						 row.child(format(row.data())).show();
						 tr.addClass('shown');
					 }
				 });
				 table.on("user-select", function (e, dt, type, cell, originalEvent) {
					 if ($(cell.node()).hasClass("details-control")) {
						 e.preventDefault();
					 }
				 });
			});
	 });

	function format(d){
		 return '<table cellpadding="0" cellspacing="0" border="0" style="width: 100%;">' +
			 '<tr>' +
				 '<td>Domínio:</td>' +
				 '<td class="domain">' + d.domain + '</td>' +
			 '</tr>' +
			 '<tr>' +
				 '<td>Diretório:</td>' +
				 '<td class="diretorio">' + d.diretorio + '</td>' +
			 '</tr>' +
			 '<tr>' +
				 '<td>Permissões:</td>' +
				 `<td class="permissions">`+d.permissions.split("\n").join('<br>')+`</td>` +
			 '</tr>'+
			 '<tr>' +
				 `<td></td>` +
				 `<td>
				 <button type="button" class="editar-dominio btn btn-success"	data-toggle="modal" data-target="#editarDominio"  style="margin-right: 10px;"  data-index="` + d.index + `" >Editar domínio</button>
				 <button type="button" class="deletar-dominio btn btn-danger"	data-toggle="modal" data-target="#excluirDominio" data-index="` + d.index + `">Excluir domínio</button></td>` +
			 '</tr>'+
		 '</table>';  
	}

$(document).on('click','.deletar-dominio',function (e) {
	var tabelaElemento = $(this).parent().parent().parent();
	var indexDomain 	= $(this).data('index');
	var domain			= tabelaElemento.find('.domain').html();

	$('#excluirDominio').one('shown.bs.modal', function (e) {
		var modal = $(this)
		modal.find('.modal-body span').html("<b>"+domain+"</b>");
		modal.find('.btn-secondary').unbind('click').bind('click', function(e){ modal.modal('hide');})
		modal.find('.deletar-dominio').unbind('click').bind('click', function(e){
			$.ajax({
				method: "POST",
				url: "./functions.php",
				data: {
					'funcao':'globals::deleteHost',
					'index':indexDomain,
				}
			}).done(function( data ) {
				window.location.reload()
			})
		}); 
	}).one('hidden.bs.modal', function(e) {}).modal('show');
})


$(document).on('click','.editar-dominio',function (e) {
	var tabelaElemento = $(this).parent().parent().parent();
	var indexDomain 	= $(this).data('index');
	var domain			= tabelaElemento.find('.domain').html();
	var diretorio		= tabelaElemento.find('.diretorio').html();
	var permissions		= tabelaElemento.find('.permissions').html().split('	').join('').split('<br>').join("\n");
	$('#editarDominio').one('shown.bs.modal', function (e) {
		var modal = $(this)
		modal.find('.modal-title').html('Editar domínio <b>'+domain+'</b>');
		modal.find('input[name="index"]').val(indexDomain);
		modal.find('input[name="dominio"]').val(domain);
		modal.find('input[name="diretorio"]').val(diretorio);
		modal.find('textarea[name="permissoes"]').val(permissions);
		modal.find('.btn-secondary').unbind('click').bind('click', function(e){ modal.modal('hide');})
		modal.find('.btn-success').unbind('click').bind('click', function(e){
			e.preventDefault(); 
			$.ajax({
				method: "POST",
				url: "./functions.php",
				data: {
					'funcao':'globals::salvarHost',
					'index':modal.find('input[name="index"]').val(),
					'diretorio':modal.find('input[name="diretorio"]').val(),
					'domain':modal.find('input[name="dominio"]').val(),
					'permissions':modal.find('textarea[name="permissoes"]').val()
				}
			}).done(function( data ) {
				$($( "td:contains('"+domain+"')" )[0]).html(modal.find('input[name="dominio"]').val())
				tabelaElemento.find('.domain').html(modal.find('input[name="dominio"]').val());
				tabelaElemento.find('.diretorio').html(modal.find('input[name="diretorio"]').val());
				tabelaElemento.find('.permissions').html(modal.find('textarea[name="permissoes"]').val().split("\n").join("<br>"))
				console.log(modal.find('textarea[name="permissoes"]').val())
			})
			tabelaElemento.find('.domain').html(modal.find('input[name="dominio"]').val());
			tabelaElemento.find('.diretorio').html(modal.find('input[name="diretorio"]').val());
			tabelaElemento.find('.permissions').html(modal.find('textarea[name="permissoes"]').val())
			modal.modal('hide');
		}); 
	}).one('hidden.bs.modal', function(e) {}).modal('show');
})

$(document).on('click','.adicionar-dominio',function (e) {
	$('#adicionarDominio').one('shown.bs.modal', function (e) {
		var modal = $(this)	
		 modal.find('.btn-secondary').unbind('click').bind('click', function(e){modal.modal('hide');})
		 modal.find('.btn-success').unbind('click').bind('click', function(e){
				 	$.ajax({
				 		method: "POST",
				 		url: "./functions.php",
				 		data: {
				 			'funcao':'globals::addHost',
				 			'diretorio':modal.find('input[name="diretorio"]').val(),
				 			'domain':modal.find('input[name="dominio"]').val(),
				 			'permissions':modal.find('textarea[name="permissoes"]').val()
				 		}
				 	}).done(function( data ) {
				 		if(data==true){
				 			$('#parabensCriado').find('span.domain').html('<b>'+modal.find('input[name="dominio"]').val()+'</b>');
				 			$('#parabensCriado').find('.entendi').click(function(){$('#parabensCriado').modal('hide');});
				 			$('#parabensCriado').modal('show');
				 			$('#adicionarDominio').modal('hide');
				 		}else{
				 			$('#alertaDiretorio').find('span.domain').html('<b>'+modal.find('input[name="diretorio"]').val()+'</b>');
				 			$('#alertaDiretorio').find('.entendi').click(function(){
				 					$('#adicionarDominio').modal('show')
				 					$('#alertaDiretorio').modal('hide');
				 			});
				 			$('#alertaDiretorio').modal('show');
				 			$('#adicionarDominio').modal('hide');
				 		}
				 	})
			})
	}).one('hidden.bs.modal', function(e) {}).modal('show');
})







