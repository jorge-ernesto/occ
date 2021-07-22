			<h3>Almacenes <button type="button" class="btn btn-success add-warehouse">Agregar</button></h3>
				<hr>
				<table class="table table-striped">
					<thead>
						<tr>
							<th>#</th>
							<th>Nombre</th>
							<th>Descripciòn</th>
							<th>Org</th>
							<th>Es Interno</th>
							<th>Es Proveedor</th>
							<th>Activo</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($result_i_warehouse as $key => $warehouse) { ?>
						<tr>
							<th scope="row"><?php echo $warehouse->i_warehouse_id ?></th>
							<td><?php echo $warehouse->name ?></td>
							<td><?php echo $warehouse->description ?></td>
							<td><?php echo $warehouse->org_name ?></td>
							<td><?php echo $warehouse->isinternal == '1' ? 'SI' : 'NO' ?></td>
							<td><?php echo $warehouse->isprovider == '1' ? 'SI' : 'NO' ?></td>
							<td><?php echo $warehouse->isactive == '1' ? 'SI' : 'NO' ?></td>
							<td>
								<!-- Single button -->
								<div class="btn-group">
									<button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
									Opciones <span class="caret"></span>
									</button>
									<ul class="dropdown-menu">
										<li class="edit-warehouse" data-id="<?php echo $warehouse->i_warehouse_id ?>"><a>Editar</a></li>
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