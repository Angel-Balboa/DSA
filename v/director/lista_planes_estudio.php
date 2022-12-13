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
    Admin Detalle de Carrera
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
          <div class="col-lg-6 col-md-6">
            <div class="card">
              <div class="card-header card-header-primary">
                <h4 class="card-title">Planes de Estudio</h4>
              </div> <!-- end card-header -->
              <div class="card-body table-responsive">
                <table class="table table-hover">
                  <thead class="text-primary">
                  <tr>
                    <th>Clave</th>
                    <th>Nombre</th>
                    <th>Acciones</th>
                  </tr>
                  </thead>
                  <tbody id="tbody_lista_planes" class="text-center">
                  <tr>
                    <td>ITI-2010</td>
                    <td>Ingeniería en Tecnologías de la Información</td>
                    <td>
                      <button type="button" title="Actualizar" class="btn btn-link" data-toggle="modal" data-target="#mdlActualizarPlan">
                        <i class="material-icons">
                          edit
                        </i>
                      </button>
                      <button type="button" title="Vista a Detalle" class="btn btn-primary btn-link">
                        <i class="material-icons">
                          display_settings
                        </i>
                      </button>
                    </td>
                  </tr>
                  </tbody>
                </table>
              </div> <!-- end card body -->
              <div class="card-footer">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#AgregarNuevoPlan" title="Agregar Nuevo Plan de Estudios">Nuevo Plan
                </button>
              </div>
            </div>
          </div> <!-- end col-6 -->
          <div class="col-lg-6 col-md-6" id="divForCargasAcademicas">
            <div class="card">
              <div class="card-header card-header-info">
                <h4 class="card-title">Cargas Académicas | <span id="clvPlanEstudio"></span></h4>
              </div> <!-- end card-header -->
              <div class="card-body table-responsive">
                <div class="row">
                  <div class="col-lg float-right">
                    <form class="form-inline">
                      <label class="control-label" for="sctFiltroPeriodo">Periodo</label>
                      <select id="sctFiltroPeriodo" class="form-control selectpicker" data-style="btn btn-link">
                        <option value="1">Ene-Abr</option>
                        <option value="2">May-Ago</option>
                        <option value="3">Sep-Dic</option>
                      </select>
                      <label class="control-label" for="sctFiltroAnio">Año</label>
                      <select id="sctFiltroAnio" class="form-control selectpicker" data-style="btn btn-link">
                        <option value="2022">2022</option>
                        <option value="2021">2021</option>
                      </select>
                    </form>
                  </div>
                </div>
                <div class="row table-responsive">
                  <table class="table table-hover">
                    <thead class="text-primary">
                    <tr>
                      <th>Id</th>
                      <th>Periodo</th>
                      <th>Año</th>
                      <th>Inicio</th>
                      <th>Término</th>
                      <th>Acciones</th>
                    </tr>
                    </thead>
                    <tbody id="tbCargasAcademicasDePlan"></tbody>
                  </table>
                </div>
              </div>
              <div class="card-footer">
                <button type="button" class="btn btn-info" id="btnAgregarCargaAcademica">Nueva Carga Académica</button>
              </div>
            </div>
          </div><!-- end col-6 -->
        </div> <!-- End row -->
        <div class="row" id="divForMateriasDePlan">
          <div class="col-lg-12 col-md-12">
            <div class="card">
              <div class="card-header card-header-info">
                <h4 class="card-title">Materias del plan: <span>ITI-2010</span></h4>
              </div> <!-- end card-header -->
              <div class="card-body">
                <div class="row justify-content-end">
                  <form class="form-inline">
                    <div class="form-group">
                      <label for="sctFiltroTipoMateria" class="control-label">Tipo</label>
                      <select id="sctFiltroTipoMateria" class="form-control selectpicker" data-style="btn btn-link">
                        <option value="Inglés">Inglés</option>
                        <option value="esp">Especialidad</option>
                        <option value="basica">Básica</option>
                      </select>
                    </div>
                    <div class="form-group">
                      <label for="sctFiltroCuatriMateria" class="control-label">Cuatrimestre</label>
                      <select id="sctFiltroCuatriMateria" class="form-control selectpicker" data-style="btn btn-link">
                        <?php
                        for ($i=1; $i<=10; $i++) echo "<option value='$i'>$i</option>";
                        ?>
                      </select>
                    </div>
                  </form>
                </div>
                <div class="row table-responsive">
                  <table class="table table-hover">
                    <thead class="text-primary">
                    <tr>
                      <th>Clave</th>
                      <th>Nombre</th>
                      <th>Cuatri.</th>
                      <th>Tipo</th>
                      <th>Créditos</th>
                      <th>Horas</th>
                      <th class="text-center">Acciones</th>
                    </tr>
                    </thead>
                    <tbody id="tbListaMateriasPlan">
                    </tbody>
                  </table>
                </div>
              </div><!-- end card-body -->
              <div class="card-footer">
                <div class="row">
                  <div class="col-md">
                    <button type="button" class="btn btn-info" title="Agregar Materia al Plan" id="btnAgregarMaterialAlPlan">Nueva Materia</button>
                  </div>
                </div>
              </div>
            </div>
          </div><!-- end col-6 -->
        </div>
      </div> <!-- end container-fluid-->

    </div> <!-- end content -->

    <?php include_once("../components/footer.php"); ?>
  </div>
</div>

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

<!-- modal: AgregarCargaAcademica-->
<div class="modal fade" id="mdlAgregarCargaAcademica" tabindex="-1" role="dialog" aria-labelledby="mdlAgregarCargaAcademicaLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="ActualizarDatosCargaLabel">Agregar Carga Academica</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="frmAgregarCargaAcademica" action="" method="">
        <input type="hidden" id="hdnClavePlan" name="clave_plan_de_estudio" value="-1">
        <div class="modal-body">
          <div class="form-row form-group">
            <label class="col-md-2 control-label" for="sctNuevoPeriodoCarga">Periodo</label>
            <div class="col-md-4">
              <select id="sctNuevoPeriodoCarga" class="form-control selectpicker" data-style="btn btn-link" name="periodo">
                <option value="1">Enero - Abril</option>
                <option value="2">Mayo - Agosto</option>
                <option value="3">Septiembre - Diciembre</option>
              </select>
            </div>
            <label class="col-md-2 control-label" for="sctNuevoAnioCarga">Año</label>
            <div class="col-md-4">
              <select id="sctNuevoAnioCarga" class="form-control selectpicker" data-style="btb btn-link" name="anio">
                <?php
                for ($i=intval(date("Y")); $i <= intval(date("Y")) + 2; $i++) {
                  if ($i == intval(date("Y"))) {
                    echo "<option value=\"$i\" selected>$i</option>";
                  } else {
                    echo "<option value=\"$i\">$i</option>";
                  }
                }
                ?>
              </select>
            </div>
          </div>
          <div class="form-row form-group">
            <div class="col-md-6">
              <label for="dteFechaInicioCarga" class="control-label">Fecha de Inicio</label>
              <input type="text" class="form-control datepicker" id="dteFechaInicioCarga" required name="fecha_inicio">
            </div>
            <div class="col-md-6">
              <label for="dteFechaFinCarga" class="control-label">Fecha de cierre</label>
              <input type="text" class="form-control datepicker" id="dteFechaFinCarga" required name="fecha_cierre">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <input type="submit" class="btn btn-primary" value="Guardar" />
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- modal: AgregarMateria-->
<div class="modal fade" id="mdlAgregarMateria" tabindex="-1" role="dialog" aria-labelledby="mdlAgregarMateriaLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="AgregarMateriaLabel">Agregar Materia</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="frmAgregarMateria" action="" method="post">
        <div class="modal-body">
          <div class="form-row form-group">
            <div class="col-md-6">
              <label for="txtClaveMateria" class="control-label">Clave</label>
              <input type="text" class="form-control text-uppercase" id="txtClaveMateria" name="clave_materia" maxlength="20" required>
            </div>
            <div class="col-md-6">
              <label for="txtNombreMateria" class="control-label">Nombre</label>
              <input type="text" class="form-control" id="txtNombreMateria" name="nombre_materia" maxlength="150" required>
            </div>
          </div>
          <div class="form-row form-group">
            <div class="col-md-4">
              <label for="sctCreditosMateria" class="control-label">Creditos</label>
              <select class="form-control selectpicker" id="sctCreditosMateria" name="creditos_materia" data-style="btn btn-link">
                <option value="60">60</option>
                <option value="90">90</option>
                <option value="120">120</option>
              </select>
            </div>
            <div class="col-md-4">
              <label for="sctTipoMateria" class="control-label">Tipo</label>
              <select id="sctTipoMateria" name="tipo_materia" class="form-control selectpicker" data-style="btn btn-link" required>
                <option value="Básica">Básica</option>
                <option value="Inglés">Inglés</option>
                <option value="Especialidad">Especialidad</option>
                <option value="Valores">Valores</option>
              </select>
            </div>
            <div class="col-md-4">
              <label for="sctHorasMateria" class="control-label">Horas Tot.</label>
              <select id="sctHorasMateria" name="horas_totales" class="form-control selectpicker" data-style="btn btn-link" required>
                <option value="60">60</option>
                <option value="90">90</option>
                <option value="120">120</option>
              </select>
            </div>
          </div>
          <div class="form-row form-group">
            <div class="col-md-6">
              <label for="sctCuatrimestreMateria" class="control-label">Cuatrimestre</label>
              <select class="form-control selectpicker" id="sctCuatrimestreMateria" name="cuatrimestre_materia" data-style="btn btn-link">
                <?php
                for ($i=1; $i <= 10; $i++) echo "<option value=\"$i\">$i</option>";
                ?>
              </select>
            </div>
            <div class="col-md-6">
              <label for="sctPosicionHMateria" class="control-label">Posición</label>
              <select class="form-control selectpicker" id="sctPosicionHMateria" name="posicion_horizontal" data-style="btn btn-link">
                <?php
                for ($i=1; $i <= 7; $i++) echo "<option value=\"$i\">$i</option>";
                ?>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <input type="submit" class="btn btn-primary" value="Guardar" />
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- modal: ActualizarDatosMateria-->
<div class="modal fade" id="mdlActualizarDatosMateria" tabindex="-1" role="dialog" aria-labelledby="mdlActualizarDatosMateriaLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="ActualizarDatosMateriaLabel">Actualizar Datos de Materia</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="frmActualizarDatosMateria" action="" method="post">
        <input type="hidden" id="hdnIdMateria" name="id_materia" value="-1">
        <div class="modal-body">
          <div class="form-row form-group">
            <div class="col-md-6">
              <label for="txtNuevaClaveMateria" class="control-label">Clave</label>
              <input type="text" class="form-control text-uppercase" id="txtNuevaClaveMateria" name="nueva_clave" maxlength="20" required>
            </div>
            <div class="col-md-6">
              <label for="txtNuevoNombreMateria" class="control-label">Nombre</label>
              <input type="text" class="form-control" id="txtNuevoNombreMateria" name="nuevo_nombre" maxlength="150" required>
            </div>
          </div>
          <div class="form-row form-group">
            <div class="col-md-4">
              <label for="sctNuevoCreditosMateria" class="control-label">Creditos</label>
              <select class="form-control selectpicker" id="sctNuevoCreditosMateria" name="nuevos_creditos" data-style="btn btn-link">
                <option value="60">60</option>
                <option value="90">90</option>
                <option value="120">120</option>
              </select>
            </div>
            <div class="col-md-4">
              <label for="sctNuevoTipoMateria" class="control-label">Tipo</label>
              <select id="sctNuevoTipoMateria" name="nuevo_tipo" class="form-control selectpicker" data-style="btn btn-link" required>
                <option value="Básica">Básica</option>
                <option value="Inglés">Inglés</option>
                <option value="Especialidad">Especialidad</option>
                <option value="Valores">Valores</option>
              </select>
            </div>
            <div class="col-md-4">
              <label for="sctNuevasHorasMateria" class="control-label">Horas Tot.</label>
              <select id="sctNuevasHorasMateria" name="nuevas_horas_totales" class="form-control selectpicker" data-style="btn btn-link" required>
                <option value="60">60</option>
                <option value="90">90</option>
                <option value="120">120</option>
              </select>
            </div>
          </div>
          <div class="form-row form-group">
            <div class="col-md-6">
              <label for="sctNuevoCuatrimestreMateria" class="control-label">Cuatrimestre</label>
              <select class="form-control selectpicker" id="sctNuevoCuatrimestreMateria" name="nuevo_cuatrimestre" data-style="btn btn-link">
                <?php
                for ($i=1; $i <= 10; $i++) echo "<option value=\"$i\">$i</option>";
                ?>
              </select>
            </div>
            <div class="col-md-6">
              <label for="sctNuevaPosicionHMateria" class="control-label">Posición</label>
              <select class="form-control selectpicker" id="sctNuevaPosicionHMateria" name="nueva_posicion_h" data-style="btn btn-link">
                <?php
                for ($i=0; $i < 8; $i++) echo "<option value=\"$i\">$i</option>";
                ?>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <input type="submit" class="btn btn-primary" value="Guardar" />
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- modal: VistaMateria-->
<div class="modal fade" id="mdlVistaRapidaMateria" tabindex="-1" role="dialog" aria-labelledby="VistaMateriaLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="VistaMateriaLabel">Materia</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-4 col-sm-6">Clave</div>
          <div class="col-md-8 col-sm-6" id="qvClaveMateria"></div>
        </div>
        <hr>
        <div class="row">
          <div class="col-md-4 col-sm-6">Nombre</div>
          <div class="col-md-8 col-sm-6" id="qvNombreMateria"></div>
        </div>
        <hr>
        <div class="row">
          <div class="col-md-4 col-sm-6">Tipo</div>
          <div class="col-md-4 col-sm-6" id="qvTipoMateria">Inglés</div>
        </div>
        <hr>
        <div class="row">
          <div class="col-md-4 col-sm-6">Creditos</div>
          <div class="col-md-8 col-sm-6" id="qvCreditosMateria">120</div>
        </div>
        <hr>
        <div class="row">
          <div class="col-md-4 col-sm-6">Cuatrimestre</div>
          <div class="col-md-8 col-sm-6" id="qvCuatrimestreMateria">3</div>
        </div>
        <hr>
        <div class="row">
          <div class="col-md-4 col-sm-6">Horas Totales</div>
          <div class="col-md-4 col-sm-6" id="qvHorasTotalesMateria">120</div>
        </div>
        <hr>
        <div class="row">
          <div class="col-md-4 col-sm-6">Posición Horizontal</div>
          <div class="col-md-4 col-sm-6" id="qvPosicionHMateria">3</div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
      </div>
    </div>
  </div>
</div>

<!--   Core JS Files   -->
<?php include_once("../components/core_js.php"); ?>
<!-- End Core JS Files -->
<script src="../../static/js/director/lista_planes_estudio.js"></script>
<script>
  $(document).ready(function () {
    $().ready(function () {
      $sidebar = $('.sidebar');

      $sidebar_img_container = $sidebar.find('.sidebar-background');

      $full_page = $('.full-page');

      $sidebar_responsive = $('body > .navbar-collapse');

      window_width = $(window).width();
    });

    $("#dteFechaInicioCarga").datepicker({format: "yyyy/mm/dd", footer: true, modal: true, header: true,  uiLibrary: 'materialdesign' });
    $("#dteFechaFinCarga").datepicker({format: "yyyy/mm/dd", footer: true, modal: true, header: true,  uiLibrary: 'materialdesign' });
    $("#dteNuevaFechaInicioCarga").datepicker({format: "yyyy/mm/dd", footer: true, modal: true, header: true,  uiLibrary: 'materialdesign' });
    $("#dteNuevaFechaFinCarga").datepicker({format: "yyyy/mm/dd", footer: true, modal: true, header: true,  uiLibrary: 'materialdesign' });

    sss("id_carrera", <?php echo $carrera->get_data("id"); ?>, false);
    initd();
  });


</script>
</body>

</html>

