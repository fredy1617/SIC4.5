                  <?php   $estatus = '<span class="new badge red" data-badge-caption="Pendiente"></span>';//CREAMOS EL ESTATUS DEL REPORTE POR DEFAULT EN PENDIENTE
                          #VERIFICAMOS QUE EL ID SE ENCUENTRE EN LA TABLA DE REPORTES
                          if ((mysqli_num_rows(mysqli_query($conn, "SELECT * FROM reportes WHERE id_reporte = $id_reporte"))) == 0){
                            #SI NO ESTA EN LA TABLA DE REPORTES LA BUSACMOS EN LA TABLA ORDENES_SERVICIOS
                            $sql = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM orden_servicios WHERE id = $id_reporte")); 
                            #SACANMOS LA INFORMACION NESECITADSA Y LA ASIGNAMOS A CADA VARIABLE CORRESPONDIENTE
                            $id = $sql['id'];
                            $Descripcion = $sql['solicitud'];
                            $Solucion = $sql['trabajo']; 
                            $fecha = ($sql['fecha_s'] == '')? $sql['fecha_r']: $sql['fecha_s'];  
                            $Hora = ($sql['hora_s'] == '')? $sql['hora_r']: $sql['hora_s'];  
                            #VERIFICAMOS EN QUE ESTATUS SE ENCUENTRA LA ORDEN PARA MOSRTARLO
                            if ($sql['estatus'] == 'Cotizar' OR $sql['estatus'] == 'Cotizado' OR $sql['estatus'] == 'Pedir' OR $sql['estatus'] == 'Realizar') {
                              $estatus = '<span class="new badge orange" data-badge-caption="Revisado"></span>';
                            }else if ($sql['estatus'] == 'Facturar' OR $sql['estatus'] == 'Facturado') {
                              $estatus = '<span class="new badge green" data-badge-caption="Terminado"></span>';
                            }
                          }else{
                            #SI SI ESTA EN LA TABLA DE REPORTES LO SELECCIONAMOS
                            $sql = mysqli_fetch_array(mysqli_query($conn, "SELECT * FROM reportes WHERE id_reporte = $id_reporte")); 
                            #SACANMOS LA INFORMACION NESECITADSA Y LA ASIGNAMOS A CADA VARIABLE CORRESPONDIENTE
                            $id = $sql['id_reporte'];
                            $Descripcion = $sql['descripcion'];
                            $Solucion = $sql['solucion']; 
                            $fecha = $sql['fecha_solucion'];
                            $Hora = $sql['hora_atendido'];
                            #VERIFICAMOS EL ESTATUS DEL REPORTE PARA VER SI SE CAMBIA O QUEDA POR DEFAUL PENDIENTE
                            if ($sql['atendido'] == 1) {
                              $estatus = '<span class="new badge green" data-badge-caption="Terminado"></span>';
                            }
                          }
                      	  
                          $id_cliente = $sql['id_cliente'];//SACAMOS EL ID DEL CLIENTE
                          #BUSCAMOS EN LA LISTA DE CLIENTES NORMALES EL ID
                          $sql = mysqli_query($conn, "SELECT * FROM clientes WHERE id_cliente=$id_cliente");
                          #VERIFICAMOS SI ENCONTRO EL ID EN LA TABLA CLIENTES
                          if (mysqli_num_rows($sql) == 0) {
                            #SI NO ENCONTRO EL ID LO BUSCAMOS EN LA TABLA ESPECIALES
                            $sql = mysqli_query($conn, "SELECT * FROM especiales WHERE id_cliente=$id_cliente");
                          }
                          $cliente = mysqli_fetch_array($sql);//SACAMOS LA INFORMACION DEL CLIENTE ARRAY
                          $id_comunidad = $cliente['lugar'];//ID DEL LA COMUNIDAD
                          #SELECCIONAMOS LA COMUNIDAD DEL ID
        			           	$comunidad = mysqli_fetch_array(mysqli_query($conn, "SELECT nombre FROM comunidades WHERE id_comunidad=$id_comunidad"));
                  ?>
                      <tr>
                        <td><?php echo $id; ?></td>
                        <td><?php echo $cliente['nombre']; ?></td>
                        <td><?php echo $Descripcion; ?></td>
                        <td><?php echo $comunidad['nombre'];?></td>
                        <td><?php echo $Solucion; ?></td>
                        <td><?php echo $fecha; ?></td>
                        <td><?php echo $Hora; ?></td>
                        <td><?php echo $estatus; ?></td>
                        <td><br><form action="<?php echo ($id > 100000)? 'atender_orden.php':'atender_reporte.php'; ?>" method="post"><input type="hidden" name="<?php echo ($id > 100000)? 'id_orden':'id_reporte'; ?>" value="<?php echo $id; ?>"><button type="submit" class="btn-floating btn-tiny waves-effect waves-light pink"><i class="material-icons">send</i></button></form></td>
                      </tr>
                  <?php