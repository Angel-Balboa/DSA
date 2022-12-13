<?php

include_once ("../../init.php");

use dsa\api\controller\sesion\CSesion;

$sesion = CSesion::inits();
$tipo = $sesion->tipo_usuario;
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <?php include_once ("../components/metas.php"); ?>
    <?php include_once ("../components/links.php"); ?>
    <title>
        Director|Plan de Estudio
    </title>
</head>

<body class="">
<div class="wrapper ">
    <!-- Sidebar -->
    <?php include_once ("../components/sidebar.php"); ?>
    <!-- End sidebar -->
    <div class="main-panel">
        <!-- Navbar -->
        <?php include_once ("../components/navbar.php");?>
        <!-- End Navbar -->
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-4 col-md-4">
                        <div class="card">
                            <div class="card-header card-header-primary">
                                <h4 class="card-title">Datos del Plan de Estudios</h4>
                            </div>
                            <div class="card-body table-responsive">
                                <table class="table table-hover">
                                    <tbody>
                                    <tr>
                                        <td>Clave</td>
                                        <td id="clave_plan" data-id-plan="-1"></td>
                                    </tr>
                                    <tr>
                                        <td>Nombre</td>
                                        <td id="nombre_plan"></td>
                                    </tr>
                                    <tr>
                                        <td>Año</td>
                                        <td id="anio_plan"></td>
                                    </tr>
                                    <tr>
                                        <td>Nivel</td>
                                        <td id="nivel_plan"></td>
                                    </tr>
                                    <tr>
                                        <td>Carrera</td>
                                        <td id="nombre_carrera_plan" data-id-carrera-plan="-1"></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div><!-- end card-body -->
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-primary" id="btnActualizarDatosPlan">Actualizar</button>
                                    </div>
                                </div>
                            </div>
                        </div><!-- end card-->
                    </div><!-- end col-4 -->
                    <div class="col-lg-8 col-md-8">
                        <div class="card">
                            <div class="card-header card-header-primary">
                                <h4 class="card-title">Cargas Académicas</h4>
                            </div>
                            <div class="card-body table-responsive">
                                <table class="table table-hover">
                                    <thead class="text-primary">
                                    <tr style="text-align: center;">
                                        <th>Periodo</th>
                                        <th>Año</th>
                                        <th>Iniciar</th>
                                        <th>Finaliza</th>
                                        <th>Acciones</th>
                                    </tr>
                                    </thead>
                                    <tbody class="text-center" id="tbListaCargasAcademicas">
                                    </tbody>
                                </table>
                            </div><!-- end card-body -->
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-md-auto">
                                        <button type="button" class="btn btn-primary" title="Agregar Nueva Carga Academica" id="btnAgregarCargaAcademica">Agregar</button>
                                    </div>
                                </div>
                            </div>
                        </div><!-- end card -->
                    </div><!-- end col-8 -->
                </div><!-- end row -->
                <div class="row">
                    <div class="col-lg col-md">
                        <div class="card">
                            <div class="card-header card-header-primary">
                                <h4 class="card-title">Materias</h4>
                            </div>
                            <div class="card-body table-responsive">
                                <table class="table table-hover">
                                    <thead class="text-primary">
                                    <tr style="text-align: center;">
                                        <th>Clave</th>
                                        <th>Nombre</th>
                                        <th>Cuatrimestre</th>
                                        <th>Tipo</th>
                                        <th>Acciones</th>
                                    </tr>
                                    </thead>
                                    <tbody class="text-center" id="tbListaMateriasPlan">
                                    </tbody>
                                </table>
                            </div><!-- end card-body -->
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-md-auto">
                                        <button type="button" class="btn btn-primary" id="btnAgregarMaterialAlPlan">Agregar</button>
                                    </div>
                                </div>
                            </div>
                        </div><!-- end card -->
                    </div><!-- end col-4 -->
                </div><!-- end row -->
            </div> <!-- end container-fluid -->
        </div> <!-- end content -->


        <?php include_once ("../components/footer.php"); ?>
    </div> <!-- end main-panel -->
</div> <!-- end wrapper -->

<!-- modales -->
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

<!-- modal: ActualizarDatosCarga-->
<div class="modal fade" id="mdlActualizarDatosCarga" tabindex="-1" role="dialog" aria-labelledby="mdlActualizarDatosCargaLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ActualizarDatosCargaLabel">Actualizar Datos de Carga Académica</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="frmActualizarDatosCarga" action="" method="">
                <input type="hidden" id="id_carga" name="id_carga" value="-1">
                <div class="modal-body">
                    <div class="form-row form-group">
                        <label class="col-md-2 control-label" for="sctNuevoPeriodoCarga">Periodo</label>
                        <div class="col-md-4">
                            <select id="sctNuevoPeriodoCarga" class="form-control selectpicker" name="nuevo_periodo" data-style="btn btn-link">
                                <option value="1">Enero - Abril</option>
                                <option value="2">Mayo - Agosto</option>
                                <option value="3">Septiembre - Diciembre</option>
                            </select>
                        </div>
                        <label class="col-md-2 control-label" for="sctNuevoAnioCarga">Año</label>
                        <div class="col-md-4">
                            <select id="sctNuevoAnioCarga" name="nuevo_anio" class="form-control selectpicker" data-style="btb btn-link">
                                <?php
                                for ($i=2010; $i <= intval(date("Y")) + 1; $i++) {
                                    echo "<option value=\"$i\">$i</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-row form-group">
                        <div class="col-md-6">
                            <label for="dteNuevaFechaInicioCarga" class="control-label">Fecha de Inicio</label>
                            <input type="text" class="form-control datepicker" id="dteNuevaFechaInicioCarga" name="nueva_fecha_inicio" required>
                        </div>
                        <div class="col-md-6">
                            <label for="dteNuevaFechaFinCarga" class="control-label">Fecha de cierre</label>
                            <input type="text" class="form-control datepicker" id="dteNuevaFechaFinCarga" name="nueva_fecha_final" required>
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

<!-- modal: DatosPlan-->
<div class="modal fade" id="mdlDatosPlanEstudio" tabindex="-1" role="dialog" aria-labelledby="mdlDatosPlanEstudioLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="DatosPlanLabel">Actualizar Plan de Estudio</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="frmDatosPlanEstudio" action="" method="">
                <div class="modal-body">
                    <div class="form-row form-group">
                        <label class="col-md control-label" for="txtNuevaClavePlan">Clave</label>
                        <div class="col-md">
                            <input type="text" id="txtNuevaClavePlan" name="nueva_clave_plan" class="form-control" maxlength="50" required>
                        </div>
                    </div>
                    <div class="form-row form-group">
                        <label class="col-md control-label" for="txtNuevoNombrePlan">Nombre</label>
                        <div class="col-md">
                            <input type="text" id="txtNuevoNombrePlan" name="nuevo_nombre_plan" class="form-control" maxlength="250" required>
                        </div>
                    </div>
                    <div class="form-row form-group">
                        <label class="col-md-2 control-label" for="sctNuevoAnioPlan">Año de Registro</label>
                        <div class="col-md-4">
                            <select class="form-control selectpicker" id="sctNuevoAnioPlan" name="nuevo_anio_plan" data-style="btn btn-link">
                                <?php
                                for ($i=2010; $i <= intval(date("Y")) + 2; $i++) {
                                    echo "<option value=\"$i\">$i</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <label class="col-md-2 control-label" for="sctNuevoNivelPlan">Nivel académico</label>
                        <div class="col-md-4">
                            <select class="form-control selectpicker" id="sctNuevoNivelPlan" name="nuevo_nivel_plan" data-style="btn btn-link">
                                <option value="Esp">Especialidad</option>
                                <option value="P.A.">Profesional Asociado</option>
                                <option value="Lic">Licenciatura</option>
                                <option value="Ing">Ingeniería</option>
                                <option value="M.I.">Maestría en Ingeniería</option>
                            </select>
                        </div>
                    </div>
                </div><!-- end modal-body -->
                <div class="modal-footer">
                    <input type="submit" class="btn btn-primary" value="Guardar" />
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- end modales -->

<!--   Core JS Files   -->
<?php include_once ("../components/core_js.php"); ?>
<script src="../../static/js/director/detalle_plan_estudio.js"></script>
<!-- end Core JS Files -->
<script>
    $(document).ready(function() {
        $().ready(function() {
            $sidebar = $('.sidebar');

            $sidebar_img_container = $sidebar.find('.sidebar-background');

            $full_page = $('.full-page');

            $sidebar_responsive = $('body > .navbar-collapse');

            window_width = $(window).width();
        });

        initd();
        $("#dteFechaInicioCarga").datepicker({format: "yyyy/mm/dd", footer: true, modal: true, header: true,  uiLibrary: 'materialdesign' });
        $("#dteFechaFinCarga").datepicker({format: "yyyy/mm/dd", footer: true, modal: true, header: true,  uiLibrary: 'materialdesign' });
        $("#dteNuevaFechaInicioCarga").datepicker({format: "yyyy/mm/dd", footer: true, modal: true, header: true,  uiLibrary: 'materialdesign' });
        $("#dteNuevaFechaFinCarga").datepicker({format: "yyyy/mm/dd", footer: true, modal: true, header: true,  uiLibrary: 'materialdesign' });
    });
</script>
</body>
</html>
