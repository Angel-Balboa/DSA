<?php
include_once ("../../init.php");

use dsa\api\controller\sesion\CSesion;
use dsa\api\model\usuario\Usuario;
use dsa\api\model\carrera\Carrera;

$sesion = CSesion::inits();
$tipo = $sesion->tipo_usuario;

$usuario  = Usuario::get_usuario_by_id($sesion->id_usuario);
$carrera = Carrera::get_carrera_by_director($usuario);
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <?php
    include_once ("../components/metas.php");
    include_once ("../components/links.php");
    ?>
    <title>
        Detalle de Carrera
    </title>
  <style>

    table#tblHorarioQuickView tbody tr {
      width: 1.2em;
    }

    .topics tr {
      line-height: 0.2em;
    }

    tr.separator > td {
      height: 0.4em;
    }
    tr.non_separator > td {
      height: 1em;
    }

    .ocupado {
      background-color: #551713;
    }
  </style>
</head>

<body class="">
<div class="wrapper ">
    <!-- sidebar -->
    <?php include_once ("../components/sidebar.php"); ?>
    <!-- end sidebar -->
    <div class="main-panel">
        <!-- Navbar -->
        <?php include_once ("../components/navbar.php"); ?>
        <!-- End Navbar -->
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                  <div class="col-lg-6 col-md-6">
                      <div class="card">
                          <div class="card-header card-header-primary">
                              <h4 class="card-title">Planes de Estudio</h4>
                          </div> <!-- end card-header -->
                          <div class="card-body table-responsive">
                              <table class="table table-hover">
                                  <thead class="text-primary" style="text-align: center;">
                                  <tr>
                                      <th>Clave</th>
                                      <th>Nombre</th>
                                      <th>Acciones</th>
                                  </tr>
                                  </thead>
                                  <tbody id="tbody_lista_planes" class="text-center">
                                  </tbody>
                              </table>
                          </div> <!-- end card body -->
                          <div class="card-footer">
                              <button type="button" class="btn btn-primary" data-toggle="modal"
                                      data-target="#AgregarNuevoPlan">Agregar
                              </button>
                          </div>
                      </div>
                  </div> <!-- end col-6 -->
                  <div class="col-lg-6 col-md-6">
                    <div class="card">
                      <div class="card-header card-header-primary">
                        <h4 class="card-title">Profesores Tiempo Completo</h4>
                      </div> <!-- end card-header -->
                      <div class="card-body table-responsive">
                        <table class="table table-hover">
                          <thead class="text-primary" style="text-align: center;">
                          <tr>
                            <th>Nombre</th>
                            <th>Acciones</th>
                          </tr>
                          </thead>
                          <tbody id="tbody_profesores_ptc" class="text-center">
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div><!-- end col-6 -->
                </div> <!-- End row -->
                <div class="row">
                  <div class="col-lg-6 col-md-6">
                    <div class="card">
                      <div class="card-header card-header-primary">
                        <h4 class="card-title">Profesores Tiempo Parcial</h4>
                      </div> <!-- end card-header -->
                      <div class="card-body table-responsive">
                        <table class="table table-hover">
                          <thead class="text-primary" style="text-align: center;">
                          <tr>
                            <th>Nombre</th>
                            <th>Acciones</th>
                          </tr>
                          </thead>
                          <tbody style="text-align: center;" id="tbody_profesores_pa">
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div><!-- end col-6 -->
                  <div class="col-lg-6 col-md-6">
                    <div class="card">
                      <div class="card-header card-header-primary">
                        <h4 class="card-title">Otros Profesores que imparten en la carrera</h4>
                      </div> <!-- end card-header -->
                      <div class="card-body table-responsive">
                        <table class="table table-hover">
                          <thead class="text-primary" style="text-align: center;">
                          <tr>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Carrera</th>
                            <th>Acciones</th>
                          </tr>
                          </thead>
                          <tbody class="text-center" id="tbody_profesores_prestamo">
                          </tbody>
                        </table>
                      </div><!-- end card-body -->
                      <div class="card-footer">
                        <div class="row">
                          <div class="col-md-3">
                            <button type="button" class="btn btn-warning" id="btnSolicitarPrestamo">Solicitar Prestamo</button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div><!-- end col-6 -->
                </div>
            </div> <!-- end container-fluid-->

        </div> <!-- end content -->

        <?php include_once ("../components/footer.php"); ?>
    </div>
</div>

<!-- solicitud prestamo -->
<div class="modal fade" id="mdlSolicitudPrestamo" tabindex="-1" role="dialog"
     aria-labelledby="mdlSolicitudPrestamoLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="AgregarNuevoPlanLabel">Solicitar Prestamo de Profesor</h5>
        <button type="button" class="close" data-dismiss="modal"
                aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-sm-6">
            <form id="frmSolicitarPrestamo" action="" method="post">
              <input type="hidden" id="hdnClvCarrera" name="clv_carrera" value="<?php echo $carrera->get_data("clave"); ?>">
              <div class="form-row">
                <div class="col-sm">
                  <label for="sctCarreraObjetivo" class="control-label">Carrera</label>
                  <select id="sctCarreraObjetivo" name="carrera_objetivo" class="form-control selectpicker" data-style="btn btn-link">
                  </select>
                </div>
              </div>
              <div class="form-row">
                <div class="col-sm">
                  <label for="sctProfesorObjetivo" class="control-label">Profesor</label>
                  <select id="sctProfesorObjetivo" name="profesor_objetivo" class="form-control selectpicker" data-style="btn btn-link">
                  </select>
                </div>
              </div>
              <div class="form-row">
                <div class="col-sm">
                  <button type="submit" class="btn btn-primary">Solicitar</button>
                  <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
              </div>
            </form>
          </div><!-- end col-6 -->
          <div class="col-sm-6 table-responsive">
            <table id="tblHorarioQuickView" class="table-bordered">
              <thead>
                <tr class="small">
                  <th>Hora</th>
                  <th>Lunes</th>
                  <th>Martes</th>
                  <th>Miércoles</th>
                  <th>Jueves</th>
                  <th>Viernes</th>
                </tr>
              </thead>
              <tbody id="quick_view_disponibilidad">
                <tr class="non_separator">
                  <td style="font-size: .5em; padding: 0; margin: 0">07:00-07:55</td>
                  <td class="qv-disp" data-day="0" data-hour="0"></td>
                  <td class="qv-disp" data-day="1" data-hour="0"></td>
                  <td class="qv-disp" data-day="2" data-hour="0"></td>
                  <td class="qv-disp" data-day="3" data-hour="0"></td>
                  <td class="qv-disp" data-day="4" data-hour="0"></td>
                </tr>
                <tr class="non_separator">
                  <td style="font-size: .5em; padding: 0; margin: 0">07:55-08:50</td>
                  <td class="qv-disp" data-day="0" data-hour="1"></td>
                  <td class="qv-disp" data-day="1" data-hour="1"></td>
                  <td class="qv-disp" data-day="2" data-hour="1"></td>
                  <td class="qv-disp" data-day="3" data-hour="1"></td>
                  <td class="qv-disp" data-day="4" data-hour="1"></td>
                </tr>
                <tr class="non_separator">
                  <td style="font-size: .5em; padding: 0; margin: 0">08:50-09:45</td>
                  <td class="qv-disp" data-day="0" data-hour="2"></td>
                  <td class="qv-disp" data-day="1" data-hour="2"></td>
                  <td class="qv-disp" data-day="2" data-hour="2"></td>
                  <td class="qv-disp" data-day="3" data-hour="2"></td>
                  <td class="qv-disp" data-day="4" data-hour="2"></td>
                </tr>
                <tr class="non_separator">
                  <td style="font-size: .5em; padding: 0; margin: 0">09:45-10:40</td>
                  <td class="qv-disp" data-day="0" data-hour="3"></td>
                  <td class="qv-disp" data-day="1" data-hour="3"></td>
                  <td class="qv-disp" data-day="2" data-hour="3"></td>
                  <td class="qv-disp" data-day="3" data-hour="3"></td>
                  <td class="qv-disp" data-day="4" data-hour="3"></td>
                </tr>
                <tr class="separator">
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                </tr>
                <tr class="non_separator">
                  <td style="font-size: .5em; padding: 0; margin: 0">11:10-12:05</td>
                  <td class="qv-disp" data-day="0" data-hour="4"></td>
                  <td class="qv-disp" data-day="1" data-hour="4"></td>
                  <td class="qv-disp" data-day="2" data-hour="4"></td>
                  <td class="qv-disp" data-day="3" data-hour="4"></td>
                  <td class="qv-disp" data-day="4" data-hour="4"></td>
                </tr>
                <tr class="non_separator">
                  <td style="font-size: .5em; padding: 0; margin: 0">12:05-13:00</td>
                  <td class="qv-disp" data-day="0" data-hour="5"></td>
                  <td class="qv-disp" data-day="1" data-hour="5"></td>
                  <td class="qv-disp" data-day="2" data-hour="5"></td>
                  <td class="qv-disp" data-day="3" data-hour="5"></td>
                  <td class="qv-disp" data-day="4" data-hour="5"></td>
                </tr>
                <tr class="non_separator">
                  <td style="font-size: .5em; padding: 0; margin: 0">13:00-13:55</td>
                  <td class="qv-disp" data-day="0" data-hour="6"></td>
                  <td class="qv-disp" data-day="1" data-hour="6"></td>
                  <td class="qv-disp" data-day="2" data-hour="6"></td>
                  <td class="qv-disp" data-day="3" data-hour="6"></td>
                  <td class="qv-disp" data-day="4" data-hour="6"></td>
                </tr>
                <tr class="separator">
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                </tr>
                <tr class="non_separator">
                  <td style="font-size: .5em; padding: 0; margin: 0">14:00-14:55</td>
                  <td class="qv-disp" data-day="0" data-hour="7"></td>
                  <td class="qv-disp" data-day="1" data-hour="7"></td>
                  <td class="qv-disp" data-day="2" data-hour="7"></td>
                  <td class="qv-disp" data-day="3" data-hour="7"></td>
                  <td class="qv-disp" data-day="4" data-hour="7"></td>
                </tr>
                <tr class="non_separator">
                  <td style="font-size: .5em; padding: 0; margin: 0">14:55-15:50</td>
                  <td class="qv-disp" data-day="0" data-hour="8"></td>
                  <td class="qv-disp" data-day="1" data-hour="8"></td>
                  <td class="qv-disp" data-day="2" data-hour="8"></td>
                  <td class="qv-disp" data-day="3" data-hour="8"></td>
                  <td class="qv-disp" data-day="4" data-hour="8"></td>
                </tr>
                <tr class="non_separator">
                  <td style="font-size: .5em; padding: 0; margin: 0">15:50-16:45</td>
                  <td class="qv-disp" data-day="0" data-hour="9"></td>
                  <td class="qv-disp" data-day="1" data-hour="9"></td>
                  <td class="qv-disp" data-day="2" data-hour="9"></td>
                  <td class="qv-disp" data-day="3" data-hour="9"></td>
                  <td class="qv-disp" data-day="4" data-hour="9"></td>
                </tr>
                <tr class="non_separator">
                  <td style="font-size: .5em; padding: 0; margin: 0">16:45-17:40</td>
                  <td class="qv-disp" data-day="0" data-hour="10"></td>
                  <td class="qv-disp" data-day="1" data-hour="10"></td>
                  <td class="qv-disp" data-day="2" data-hour="10"></td>
                  <td class="qv-disp" data-day="3" data-hour="10"></td>
                  <td class="qv-disp" data-day="4" data-hour="10"></td>
                </tr>
                <tr class="separator">
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                </tr>
                <tr class="non_separator">
                  <td style="font-size: .5em; padding: 0; margin: 0">18:00-18:55</td>
                  <td class="qv-disp" data-day="0" data-hour="11"></td>
                  <td class="qv-disp" data-day="1" data-hour="11"></td>
                  <td class="qv-disp" data-day="2" data-hour="11"></td>
                  <td class="qv-disp" data-day="3" data-hour="11"></td>
                  <td class="qv-disp" data-day="4" data-hour="11"></td>
                </tr>
                <tr class="non_separator">
                  <td style="font-size: .5em; padding: 0; margin: 0">18:55-19:50</td>
                  <td class="qv-disp" data-day="0" data-hour="12"></td>
                  <td class="qv-disp" data-day="1" data-hour="12"></td>
                  <td class="qv-disp" data-day="2" data-hour="12"></td>
                  <td class="qv-disp" data-day="3" data-hour="12"></td>
                  <td class="qv-disp" data-day="4" data-hour="12"></td>
                </tr>
                <tr class="non_separator">
                  <td style="font-size: .5em; padding: 0; margin: 0">19:50-20:45</td>
                  <td class="qv-disp" data-day="0" data-hour="13"></td>
                  <td class="qv-disp" data-day="1" data-hour="13"></td>
                  <td class="qv-disp" data-day="2" data-hour="13"></td>
                  <td class="qv-disp" data-day="3" data-hour="13"></td>
                  <td class="qv-disp" data-day="4" data-hour="13"></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div> <!-- end modal Solicitud prestamo -->

<!-- modal: AgregarNuevoPlan-->
<div class="modal fade" id="AgregarNuevoPlan" tabindex="-1" role="dialog"
     aria-labelledby="AgregarNuevoPlanLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="AgregarNuevoPlanLabel">Agregar Nuevo Plan de
                    Estudio</h5>
                <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="frmAgregaPlan" method="post" action="">
                    <input type="hidden" id="hdnClaveCarrera" name="clv_carrera" value="<?php echo $carrera->get_data("clave"); ?>">
                    <div class="form-group row">
                        <label class="col-form-label col-lg" for="txtClaveNuevoPlan">Clave</label>
                        <div class="col-lg">
                            <input type="text" class="form-control" id="txtClaveNuevoPlan" name="clave_plan" maxlength="50" minlength="5" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-lg" for="txtNombreNuevoPlan">Nombre</label>
                        <div class="col-lg">
                            <input type="text" class="form-control" id="txtNombreNuevoPlan" name="nombre_plan" maxlength="250" minlength="10" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-lg" for="sctAnioNuevoPlan">Año de registro</label>
                        <div class="col-lg">
                            <select id="sctAnioNuevoPlan" class="form-control" name="anio_plan">
                                <?php
                                for ($i=2010; $i<= intval(date("Y"))+1; $i++) {
                                    if ($i != intval(date("Y"))) {
                                        echo "<option value=\"$i\">$i</option>";
                                    } else {
                                        echo "<option value=\"$i\" selected='selected'>$i</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="sctNivelNuevoPlan" class="col-form-label col-lg">Nivel</label>
                        <div class="col-lg">
                            <select id="sctNivelNuevoPlan" name="nivel_plan" class="form-control">
                                <option value="P.A.">Profesional Asociado</option>
                                <option value="Esp">Especialidad</option>
                                <option value="Lic">Licenciatura</option>
                                <option value="Ing" selected="selected">Ingeniería</option>
                                <option value="M.I.">Maestría en Ingeniería</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <input type="submit" class="btn btn-primary" data-toggle="modal" value="Guardar" />
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Cancelar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div> <!-- end modal AregarNuevoPlan -->

<!-- modal ActualizarPlanEstudios -->
<div class="modal fade" id="mdlActualizarPlanEstudios" tabindex="-1" role="dialog"
     aria-labelledby="ActualizarPlanEstudiosLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ActualizarPlanEstudiosLabel">Actualizar Plan
                    de Estudios <span class="font-weight-bold" id="clvPlanInModalTitle"></span></h5>
                <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="frmEditarPlanEstudios" method="post" action="">
                <input type="hidden" id="hdnClavePlanEstudios" name="clv_plan_de_estudios" value="-1">
                <div class="modal-body">
                    <div class="form-group row">
                        <label for="txtEditarNombrePlan" class="col-lg col-form-label">Nombre Plan de Estudios</label>
                        <div class="col-lg">
                            <input type="text" class="form-control" id="txtEditarNombrePlan" name="nuevo_nombre_plan" required maxlength="250" minlength="10">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="sctEditAnioPlan" class="col-lg col-form-label">Año de registro</label>
                        <div class="col-lg">
                            <select id="sctEditAnioPlan" name="nuevo_anio_plan" class="form-control">
                                <?php
                                for ($i=2010; $i <= intval(date("Y")) + 1; $i++) {
                                    echo "<option value=\"$i\">$i</option>\n";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="sctEditNivelPlan" class="col-lg col-form-label">Nivel</label>
                        <div class="col-lg">
                            <select id="sctEditNivelPlan" name="nuevo_nivel_plan" class="form-control">
                                <option value="P.A.">Profesional Asociado</option>
                                <option value="Esp">Especialidad</option>
                                <option value="Lic">Licenciatura</option>
                                <option value="Ing">Ingeniería</option>
                                <option value="M.I.">Maestría en Ingeniería</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-lg">
                        <input type="submit" class="btn btn-primary" value="Guardar" id="btnActualizarPlan" />
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Cancelar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div> <!-- end modal ActualizarPlanEstudios -->

<!--   Core JS Files   -->
<?php include_once ("../components/core_js.php"); ?>
<!-- End Core JS Files -->
<script src="../../static/js/director/detalle_carrera.js"></script>
<script>
    $(document).ready(function () {
        $().ready(function () {
            $sidebar = $('.sidebar');

            $sidebar_img_container = $sidebar.find('.sidebar-background');

            $full_page = $('.full-page');

            $sidebar_responsive = $('body > .navbar-collapse');

            window_width = $(window).width();
        });
        sss("id_carrera", <?php echo $carrera->get_data("id"); ?>, false);
        initd();
    });
</script>
</body>

</html>
