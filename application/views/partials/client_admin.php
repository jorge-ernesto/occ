				<h3>Clientes <button type="button" class="btn btn-success add-client">Agregar</button></h3>
				<hr>
				<table class="table table-striped">
					<thead>
						<tr>
							<th>#</th>
							<th>Nombre</th>
							<th>Descripciòn</th>
							<th>RUC</th>
							<th>Dirección</th>
							<th>Conexión</th>
							<th>Activo</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($result_c_client as $key => $client) { ?>
						<tr>
							<th scope="row"><?php echo $client->c_client_id ?></th>
							<td><?php echo $client->name ?></td>
							<td><?php echo $client->description ?></td>
							<td><?php echo $client->taxid ?></td>
							<td><?php echo $client->postaladdress ?></td>
							<td><button type="button" class="btn btn-default">Ver</button></td>
							<td><?php echo $client->isactive == '1' ? 'SI' : 'NO' ?></td>
							<td>
								<!-- Single button -->
								<div class="btn-group">
									<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									Opciones <span class="caret"></span>
									</button>
									<ul class="dropdown-menu">
										<li class="edit-client" data-id="<?php echo $client->c_client_id ?>"><a>Editar</a></li>
										<li role="separator" class="divider"></li>
										<li><a href="#">Eliminar</a></li>
									</ul>
								</div>
							</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
				<br>