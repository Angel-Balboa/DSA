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
<html lang="es">

<head>
  <?php
  include_once("../components/metas.php");
  include_once("../components/links.php");
  ?>
  <title>
    Listado de Cargas Académicas
  </title>
</head>

<body class="">
<div class="wrapper ">
  <!-- sidebar -->
  <?php include_once("../components/sidebar.php"); ?>
  <!-- end sidebar -->
  <div class="main-panel">
    <!-- Navbar -->
    <?php include_once("../components/navbar.php"); ?>
    <!-- End Navbar -->
    <div class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-12 col-md-12">
            <div class="card">
              <div class="card-body">
                <div class="row d-flex justify-content-center">
                  <div class="col-lg-2">
                    <h4 class="title">Carga Académica</h4>
                  </div>
                </div>
                <div class="row text-center font-weight-bold">
                  <div class="col-lg-4 col-md-4" id="clave_plan"></div>
                  <div class="col-lg-4 col-md-4" id="periodo_anio_carga"></div>
                  <div class="col-lg-4 col-md-4">
                    Del <span id="fechaInicio"></span> al <span id="fechaFin"></span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div> <!-- End row -->
        <div class="row">
          <div class="col-lg-4 col-md-4">
            <div class="card">
              <div class="card-header card-header-primary">
                <h4 class="card-title">Grupos</h4>
              </div>
              <div class="card-body">
                <div class="row">
                  <div class="col-lg col-md">
                    <form class="form form-inline">
                      <div class="form-group">
                        <label for="sctListaGrupos" class="control-label">Grupo</label>
                        <select id="sctListaGrupos" class="form-control selectpicker" data-style="btn btn-primary btn-link" data-width="35%"></select>
                        <button type="button" class="btn btn-fab btn-fab-mini btn-success btn-round" title="Crea Nuevo Grupo" data-toggle="modal" data-target="#mdlAgregaGrupo">
                          <i class="material-icons">add_card</i>
                        </button>
                        <button id="btnAgregaMateriaAGrupo" type="button" class="btn btn-fab btn-fab-mini btn-info btn-round" title="Agrega Materia al Grupo">
                          <i class="material-icons">addchart</i>
                        </button>
<!--                        <button type="button" class="btn btn-fab btn-fab-mini btn-warning btn-round" title="Agrega Materia Compartida" data-toggle="modal" data-target="#mdlAgregaMateriaCompartida">-->
<!--                          <i class="material-icons">group_add</i>-->
<!--                        </button>-->
                      </div><!-- end form-group -->
                    </form>
                  </div><!-- end col -->
                </div><!-- end row -->
                <hr>
                <div class="row">
                  <div class="col-lg col-md table-responsive">
                    <table class="table table-hover">
                      <tbody id="tbListaMateriasEnGrupo">
                      </tbody>
                    </table>
                  </div><!-- end col -->
                </div><!-- end row -->
              </div><!-- end card-body -->
            </div><!-- end card -->
          </div> <!-- end col-lg-4 -->
          <div class="col-lg-8 col-md-8">
            <div class="row">
              <div class="col-lg col-md">
                <div class="card">
                  <div class="card-header card-header-warning">
                    <h4 class="card-title">Detalle del Grupo</h4>
                  </div>
                  <div class="card-body">
                    <div class="row text-center">
                      <div class="col-md-4">
                        Clave: <span id="clave_grupo" class="font-weight-bold"></span>
                      </div>
                      <div class="col-md-4">
                        Cuatrimestre: <span id="cuatrimestre_grupo" class="font-weight-bold"></span>
                      </div>
                      <div class="col-md-4">
                        Turno: <span id="turno_grupo" class="font-weight-bold"></span>
                      </div>
                    </div>
                    <hr>
                    <div class="row text-center">
                      <div class="col-md-4">
                        Inicia: <span id="inicio_grupo" class="font-weight-bold"></span>
                      </div>
                      <div class="col-md-4">
                        Finaliza: <span id="finaliza_grupo" class="font-weight-bold"></span>
                      </div>
                      <div class="col-md-4">
                        Semanas: <span id="semanas_grupo" class="font-weight-bold"></span>
                      </div>
                    </div>
                  </div><!-- end card-body -->
                  <div class="card-footer">
                    <div class="col-md d-flex justify-content-start">
                      <button type="button" class="btn btn-sm btn-success" id="btnFinalizarGrupo" data-id-grupo="-1">Finalizar</button>
                    </div>
                    <div class="col-md-6 d-flex justify-content-end">
                      <button type="button" class="btn btn-sm btn-warning">Actualizar</button>
                      <button type="button" class="btn btn-sm btn-danger">Eliminar</button>
                    </div>
                  </div>
                </div><!--end card -->
              </div><!-- end col -->
            </div><!-- end row -->
            <div class="row">
              <div class="col-lg col-md">
                <div class="card">
                  <div class="card-header card-header-success">
                    <h4 class="card-title">Detalles de la Materia en el Grupo</h4>
                  </div><!-- end card-header -->
                  <div class="card-body">
                    <div class="row">
                      <div class="col-md-3 col-sm-3">
                        Materia
                      </div>
                      <div class="col-md-6 col-sm-6" id="nombreMeg"></div>
                      <div class="col-md-3 col-sm-3" id="dvBtnCambiaMateria">
                        <button type="button" class="btn btn-warning btn-round btn-sm" title="Cambiar Materia" id="btnCambiarMateriaEnMeg">Cambiar</button>
                      </div>
                    </div>
                    <hr>
                    <div class="row">
                      <div class="col-md-3 col-sm-3">
                        Horas Por semana
                      </div>
                      <div class="col-md-9 col-sm-9" id="horasXSemanaMeg">
                      </div>
                    </div>
                    <hr>
                    <div class="row">
                      <div class="col-md-3 col-sm-3">
                        Alumnos Estimados
                      </div>
                      <div class="col-md-9 col-sm-9" id="alumnosEstimadosMeg">
                      </div>
                    </div>
                    <hr>
                    <div class="row">
                      <div class="col-md-3 col-sm-3">
                        Profesor Asignado
                      </div>
                      <div class="col-md-6 col-sm-6" id="profesorAsignadoMeg">
                      </div>
                      <div class="col-md-3 col-sm-3">
                        <button type="button" class="btn btn-warning btn-round btn-sm" title="Cambiar profesor" id="btnCambiaProfesorEnMeg">Cambiar</button>
                      </div>
                    </div>
                    <hr>
                    <div class="row">
                      <div class="col-md-3 col-sm-3">
                        ¿Materia compartida?
                      </div>
                      <div class="col-md-6 col-sm-6" id="esCompartidaMeg">
                      </div>
                      <div class="col-md-3 col-sm-3" id="dvVerMateriaOriginal">
                        <button type="button" id="btnVerMateriaOriginal" class="btn btn-info btn-round btn-sm" title="Ver Materia Original">Ver Original</button>
                      </div>
                    </div>
                  </div><!-- end card-body -->
<!--                  <div class="card-footer">-->
<!--                    <div class="row d-flex justify-content-lg-start">-->
<!--                      <div class="col-md col-sm">-->
<!--                        <button type="button" class="btn btn-warning" title="Actualizar Materia">Actualizar</button>-->
<!--                      </div>-->
<!--                      <div class="col-md col-sm">-->
<!--                        <button type="button" class="btn btn-info" title="Fusionar Materia">Fusionar</button>-->
<!--                      </div>-->
<!--                    </div>-->
<!--                  </div>-->
                </div><!-- end card -->
              </div><!-- end col -->
            </div><!-- end row -->
          </div><!-- end col-8 -->
        </div><!-- end row -->
      </div> <!-- end container-fluid-->

    </div> <!-- end content -->

    <?php include_once("../components/footer.php"); ?>
  </div>
</div>

<!-- modal: AgregarNuevoPlan-->
<div class="modal fade" id="mdlAgregaGrupo" tabindex="-1" role="dialog" aria-labelledby="mdlAgregaGrupoLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="AgregarNuevoPlanLabel">Agregar Nuevo Grupo</h5>
        <button type="button" class="close" data-dismiss="modal"
                aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="frmAgregaGrupo" method="post" action="">
          <input type="hidden" id="hdnClaveCargaAcademica" name="id_carga" value="-1">
          <div class="form-group form-row">
            <div class="col-md-6 col-sm-6">
              <label for="txtClaveNuevoGrupo" class="control-label">Clave</label>
              <input type="text" id="txtClaveNuevoGrupo" name="clave_grupo" class="form-control" maxlength="50" placeholder="Clave del grupo" required>
            </div>
          </div>
          <div class="form-group form-row">
            <div class="col-md-6 col-sm-6">
              <label for="sctListaCuatriGrupo" class="control-label">Cuatrimestre</label>
              <select id="sctListaCuatriGrupo" name="cuatrimestre" class="form-control selectpicker" data-style="btn btn-link">
                <?php
                for ($i=1; $i<=10; $i++) echo "<option value='$i'>$i</option>";
                ?>
              </select>
            </div>
            <div class="col-md-6 col-sm-6">
              <label for="sctListaTurnosGrupo" class="control-label">Turno</label>
              <select id="sctListaTurnosGrupo" name="turno" class="form-control selectpicker" data-style="btn btn-link">
                <option value="1">Matutino</option>
                <option value="2">Vespertino</option>
                <option value="3">Nocturno</option>
                <option value="4">Sabatino</option>
              </select>
            </div>
          </div>
          <div class="form-group form-row">
            <div class="form-check form-check-inline">
              <label class="form-check-label">
                <input class="form-check-input" type="checkbox" id="chkInicioDefault" name="inicioPorDefecto" value="true" checked>¿Inicio por defecto?
                <span class="form-check-sign">
                  <span class="check"></span>
                </span>
              </label>
            </div>
            <div class="form-check form-check-inline">
              <label class="form-check-label">
                <input class="form-check-input" type="checkbox" id="chkCierreDefault" name="cierrePorDefecto" value="true" checked>¿Cierre por defecto?
                <span class="form-check-sign">
                  <span class="check"></span>
                </span>
              </label>
            </div>
          </div>
          <div class="form-row" id="dvFechasNoDefault">
            <div class="col-md-6 col-sm-6" id="dvFechaNoDefaultInicio">
              <label for="dteFechaInicio" class="control-label">Fecha de inicio</label>
              <input type="text" class="datepicker" id="dteFechaNoDefaultInicio" name="fechaNoDefaultInicio">
            </div>
            <div class="col-md-6 col-sm-6" id="dvFechaNoDefaultCierre">
              <label for="dteFechaInicio" class="control-label">Fecha de cierre</label>
              <input type="text" class="datepicker" id="dteFechaNoDefaultCierre" name="fechaNoDefaultCierre">
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

<!-- modal: AgregarNuevaMateria-->
<div class="modal fade" id="mdlAgregaNuevaMateria" tabindex="-1" role="dialog" aria-labelledby="mdlAgregaNuevaMateriaLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Agrega Materia al Grupo</h5>
        <button type="button" class="close" data-dismiss="modal"
                aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row d-flex justify-content-center">
          <div class="col-lg-3 col-md-3">
            Grupo <span class="font-weight-bold" id="clv_grupo">ITI 1-1</span>
          </div>
        </div>
        <hr>
        <div class="row">
          <form id="frmAgregaMateriaAGrupo" method="post" action="">
            <div class="form-group form-row">
              <div class="col-md col-sm">
                <label for="sctMateriaParaAgregar" class="control-label">Materia</label>
                <select id="sctMateriaParaAgregar" name="id_materia" class="form-control selectpicker" data-style="btn btn-link" data-width="75%" required></select>
              </div>
            </div>
            <div class="form-group form-row">
              <div class="col-md col-sm">
                <label for="sctProfesorParaMateria" class="control-label">Profesor</label>
                <select id="sctProfesorParaMateria" name="id_profesor" class="form-control selectpicker" data-style="btn btn-link" data-width="fit" required>
                </select>
              </div>
            </div>
            <div class=" form-group form-row">
              <div class="col-md col-sm">
                <label for="numModificador" class="control-label">Modificador</label>
                <input type="number" class="form-control w-25" id="numModificador" name="modificador" min="-5" max="5" value="0">
              </div>
              <div class="col-md col-sm">
                <label for="numAlumnosEstimados" class="control-label"># Alumnos</label>
                <input type="number" class="form-control w-25" id="numAlumnosEstimados" name="alumnos_estimados" min="0" max="45" value="30">
              </div>
            </div>
            <div class="form-group form-row">
              <input type="submit" class="btn btn-primary" data-toggle="modal" value="Guardar" />
              <button type="button" class="btn btn-secondary" data-dismiss="modal">
                Cancelar
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div> <!-- end modal AregarNuevoPlan -->

<!-- modal: AgregarNuevaMateria-->
<div class="modal fade" id="mdlAgregaMateriaCompartida" tabindex="-1" role="dialog" aria-labelledby="mdlAgregaMateriaCompartidaLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Agrega Materia Compartida</h5>
        <button type="button" class="close" data-dismiss="modal"
                aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row d-flex justify-content-center">
          <div class="col-lg-3 col-md-3">
            Grupo <span class="font-weight-bold" id="clv_grupo">ITI 1-1</span>
          </div>
        </div>
        <hr>
        <div class="row">
          <div class="col-lg">
            <form id="frmAgregaMateriaAGrupo" method="post" action="">
              <input type="hidden" id="hdnClaveGrupo" name="clv_grupo" value="-1">
              <div class="form-group row">
                <div class="col-md col-sm">
                  <label for="sctMateriaParaAgregar" class="control-label">Materia</label>
                  <select id="sctMateriaParaAgregar" class="form-control selectpicker" data-style="btn btn-link" data-width="75%">
                    <optgroup label="IM">
                      <option value="Ing-01">Inglés I</option>
                      <option value="ITI-01">Introducción a las Tecnologías de la Información</option>
                    </optgroup>
                    <optgroup label="ISA">
                      <option value="Ing-02">Inglés II</option>
                      <option value="Prog-01">Programación</option>
                    </optgroup>
                  </select>
                </div>
              </div>
              <div class="row">
                <div class="col-md-3 col-sm-3">
                  Profesor:
                </div>
                <div class="col-md col-sm">
                  Dr. Yahir Hernández Mier
                </div>
              </div>
              <div class="row">
                <div class="col-md-6 col-sm-6">
                  Modificador <span id="modificador" class="font-weight-bold">0</span>
                </div>
                <div class="col-md-6 col-sm-6">
                  <label for="numAlumnosEstimadosCompartidos" class="control-label"># Alumnos</label>
                  <input type="number" id="numAlumnosEstimadosCompartidos" class="form-control w-25" min="0" max="30" value="0">
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
    </div>
  </div>
</div> <!-- end modal AregarNuevoPlan -->

<!-- modal: AgregarNuevaMateria-->
<div class="modal fade" id="mdlCambiarProfesorMateria" tabindex="-1" role="dialog" aria-labelledby="mdlCambiarProfesorMateriaLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Cambiar Profesor a Materia</h5>
        <button type="button" class="close" data-dismiss="modal"
                aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <form id="frmCambiarProfesorMateria" method="post" action="">
              <input type="hidden" class="id_meg" name="id_meg" value="-1">
              <input type="hidden" class="materia" name="materia" value="-1">
              <input type="hidden" class="grupo" name="grupo" value="-1">
              <input type="hidden" class="modificador_horas" name="modificador_horas" value="-1">
              <input type="hidden" class="alumnos_estimados" name="alumnos_estimados" value="-1">

              <div class="form-group form-row">
              <div class="col-md col-sm">
                <label for="LP" class="control-label">Profesor</label>
                <select id="sctListaProfesoresEnCambio" name="id_profesor" class="form-control selectpicker" data-style="btn btn-link" data-width="fit"></select>
              </div>
            </div>
            <div class="form-group form-row">
              <input type="submit" class="btn btn-primary" data-toggle="modal" value="Guardar" />
              <button type="button" class="btn btn-secondary" data-dismiss="modal">
                Cancelar
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div> <!-- end modal AregarNuevoPlan -->

<!-- modal: AgregarNuevaMateria-->
<div class="modal fade" id="mdlCambiarMateriaEnMeg" tabindex="-1" role="dialog" aria-labelledby="mdlCambiarMateriaEnMegLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Cambiar Materia</h5>
        <button type="button" class="close" data-dismiss="modal"
                aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <form id="frmCambiarMateriaEnMeg" method="post" action="">
            <div class="form-group form-row">
              <div class="col-md col-sm">
                  <input type="hidden" class="id_meg" name="id_meg" value="-1">
                  <input type="hidden" class="id_profesor" name="id_profesor" value="-1">
                  <input type="hidden" class="grupo" name="grupo" value="-1">
                  <input type="hidden" class="modificador_horas" name="modificador_horas" value="-1">
                  <input type="hidden" class="alumnos_estimados" name="alumnos_estimados" value="-1">                  <label for="sctListaMateriasEnCambio" class="control-label">Materia</label>
                <select id="sctListaMateriasEnCambio" name="materia" class="form-control selectpicker" data-style="btn btn-link" data-width="fit"></select>
              </div>
            </div>
            <div class="form-group form-row">
              <input type="submit" class="btn btn-primary" data-toggle="modal" value="Guardar" />
              <button type="button" class="btn btn-secondary" data-dismiss="modal">
                Cancelar
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</div> <!-- end modal AregarNuevoPlan -->

<!--   Core JS Files   -->
<?php include_once("../components/core_js.php"); ?>
<!-- End Core JS Files -->
<script src="../../static/js/director/detalle_carga_academica.js"></script>
<script>
  $(document).ready(function () {
    $().ready(function () {
      $sidebar = $('.sidebar');

      $sidebar_img_container = $sidebar.find('.sidebar-background');

      $full_page = $('.full-page');

      $sidebar_responsive = $('body > .navbar-collapse');

      window_width = $(window).width();
    });

    // $("#dvFechasNoDefault").hide();
    $("#dvFechaNoDefaultInicio").hide();
    $("#dvFechaNoDefaultCierre").hide();

    $('#dteFechaNoDefaultInicio').datepicker({footer: true, modal: true, header: true});
    $('#dteFechaNoDefaultCierre').datepicker({footer: true, modal: true, header: true});

    sss("id_carrera", <?php echo $carrera->get_data("id"); ?>, false);

    initd();
  });
</script>
</body>

</html>
