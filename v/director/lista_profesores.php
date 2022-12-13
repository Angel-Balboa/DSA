<?php
include_once ("../../init.php");

use dsa\api\controller\sesion\CSesion;
use dsa\api\model\usuario\Usuario;
use dsa\api\model\carrera\Carrera;

$sesion = CSesion::inits();
$tipo = $sesion->tipo_usuario;
$usuario = Usuario::get_usuario_by_id($sesion->id_usuario);
$carrera = Carrera::get_carrera_by_director($usuario);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <?php
  include_once ("../components/metas.php");
  include_once ("../components/links.php");
  ?>
  <title>
    Lista de Profesores
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
          <div class="col-lg-12 col-md-12">
            <div class="row card">
              <div class="card-header card-header-primary">
                <h4 class="card-title">Profesores Adscritos</h4>
              </div> <!-- end card-header -->
              <div class="card-body">
                <div id="accordion" role="tablist">
                  <div class="card">
                    <div class="card-header" role="tab" id="headingOne">
                      <h6 class="mb-0">
                        <a data-toggle="collapse" href="#collapseOne" aria-expanded="false" aria-controls="collapesOne">
                          Profesores de Tiempo completo
                        </a>
                      </h6>
                    </div><!-- end card-header -->
                    <a id="collapseOne" class="collapse hide" role="tabpanel" aria-labelledby="headingOne" data-parent="#accordion">
                      <div class="card-body table-responsive">
                        <table class="table table-hover">
                          <thead class="text-primary">
                          <tr>
                            <th>Id</th>
                            <th>Nombre</th>
                            <th>Acciones</th>
                          </tr>
                          </thead>
                          <tbody id="tbProfesoresPTC"></tbody>
                        </table>
                      </div><!-- end card-body -->
                    </a>
                  </div><!-- end card -->
                  <div class="card">
                    <div class="card-header" role="tab" id="headingTwo">
                      <h6 class="mb-0">
                        <a data-toggle="collapse" href="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                          Profesores por Asignatura
                        </a>
                      </h6>
                    </div><!-- end card-header -->
                    <a id="collapseTwo" class="collapse" role="tabpanel" aria-labelledby="headingTwo" data-parent="#accordion">
                      <div class="card-body table-responsive">
                        <table class="table table-hover">
                          <thead class="text-primary">
                          <tr>
                            <th>Id</th>
                            <th>Nombre</th>
                            <th>Acciones</th>
                          </tr>
                          </thead>
                          <tbody id="tbProfesoresPA"></tbody>
                        </table>
                      </div><!-- end card-body -->
                    </a>
                  </div><!-- end card -->
                </div><!-- end accordion -->

              </div> <!-- end card-body -->
            </div> <!-- end card-->
          </div> <!-- end col -->
        </div> <!-- End row -->
        <div class="row">
          <div class="col-lg-12 col-md-12">
            <div class="card row">
              <div class="card-header card-header-info">
                <div class="card-title">
                  <h4>Profesores Compartidos</h4>
                </div>
              </div><!-- end card-header -->
              <div class="card-body">
                <div id="accordion2" role="tablist">
                </div><!-- end accordion2 -->
              </div><!-- end card-body -->
              <div class="card-footer">
                <div class="row">
                  <div class="col-md-3">
                    <button type="button" class="btn btn-warning" id="btnSolicitarPrestamo">Solicitar Profesor</button>
                  </div>
                </div>
              </div>
            </div>
          </div><!-- end col-12 -->
        </div><!-- end row -->
      </div> <!-- end container-fluid-->
    </div> <!-- end content -->

    <?php include_once ("../components/footer.php"); ?>
  </div>
</div>

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

<!-- Modal Vista Rápida -->
<div class="modal fade" id="mdlVistaRapidaProfesor" tabindex="-1" role="dialog"
     aria-labelledby="mdlVistaRapidaProfesorLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="AgregarNuevoPlanLabel"></h5>
        <button type="button" class="close" data-dismiss="modal"
                aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-sm-6">
            <div class="card">
              <div class="card-header card-header-primary">
                <span class="card-title" id="qvNombre"></span>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-sm" id="qvEmail"></div>
                </div>
                <div class="row">
                  <div class="col-sm">Teléfono:</div>
                  <div class="col-sm" id="qvTelefono"></div>
                  <div class="col-sm">Extensión:</div>
                  <div class="col-sm" id="qvExtension"></div>
                </div>
                <div class="row">
                  <div class="col-sm-3">Contrato</div>
                  <div class="col-sm" id="qvTipoContrato"></div>
                </div>
                <div class="row">
                  <div class="col-sm">Inicio Contrato</div>
                  <div class="col-sm" id="qvInicioContrato"></div>
                </div>
                <div class="row">
                  <div class="col-sm">Fin Contrato</div>
                  <div class="col-sm" id="qvFinContrato"></div>
                </div>
              </div>
            </div>
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

<!--   Core JS Files   -->
<?php include_once ("../components/core_js.php"); ?>
<script src="../../static/js/director/lista_profesores.js"></script>
<!-- End Core JS Files -->
<script>
  $(document).ready(function() {
    $().ready(function() {
      $sidebar = $('.sidebar');

      $sidebar_img_container = $sidebar.find('.sidebar-background');

      $full_page = $('.full-page');

      $sidebar_responsive = $('body > .navbar-collapse');

      window_width = $(window).width();
    });
    sss("id_carrera", <?php echo $carrera->get_data("id") ?>, false);
    initd();
  });
</script>
</body>

</html>