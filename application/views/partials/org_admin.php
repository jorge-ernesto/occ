			<h3>Organizaciones <button type="button" class="btn btn-success add-org">Agregar</button></h3>
				<hr>
				<table class="table table-striped">
					<thead>
						<tr>
							<th>#</th>
							<th>Nombre</th>
							<th>Descripciòn</th>
							<th>Cliente</th>
							<th>Dirección</th>
							<th>Iniciales</th>
							<th>Activo</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($result_c_org as $key => $org) { ?>
						<tr>
							<th scope="row"><?php echo $org->c_org_id ?></th>
							<td><?php echo $org->name ?></td>
							<td><?php echo $org->description ?></td>
							<td><?php echo $org->client_name ?></td>
							<td><?php echo $org->postaladdress ?></td>
							<td><?php echo $org->initials ?></td>
							<td><?php echo $org->isactive == '1' ? 'SI' : 'NO' ?></td>
							<td>
								<!-- Single button -->
								<div class="btn-group">
									<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									Opciones <span class="caret"></span>
									</button>
									<ul class="dropdown-menu">
										<li class="edit-org" data-id="<?php echo $org->c_org_id ?>"><a>Editar</a></li>
										<li role="separator" class="divider"></li>
										<li><a href="#">Eliminar</a></li>
									</ul>
								</div>
							</td>
						</tr>
						<?php } ?>
					</tbody>
				</table>