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
                    <div class="col-lg-10 col-md-10">
                        <div class="card">
                            <div class="card-header card-header-info">
                                <h4 class="card-title">Cargas Académicas</h4>
                            </div> <!-- end card-header -->
                            <div class="card-body">
                              <div class="row">
                                <div class="col-md d-flex justify-content-center">
                                  <form class="form-inline">
                                    <label for="sctPlanesEstudio">Plan de estudios</label>
                                    <select id="sctPlanesEstudio" class="form-control selectpicker" data-width="auto" data-style="btn btn-link">
                                    </select>
                                  </form>
                                </div>
                              </div>
                              <div class="row">
                                <div class="col-lg col-md col-sm" id="dvAccordionsCargas"></div>
                              </div>
                            </div>
                            <div class="card-footer">
                                <button type="button" class="btn btn-info" id="btnAgregarCarga">Nueva Carga Académica
                                </button>
                            </div>
                        </div>
                    </div><!-- end col-6 -->
                </div> <!-- End row -->
            </div> <!-- end container-fluid-->

        </div> <!-- end content -->

        <?php include_once("../components/footer.php"); ?>
    </div>
</div>

<!-- modal: AgregarActualizarCargaAcademica -->
<div class="modal fade" id="mdlAgregarActualizarCargaAcademica" tabindex="-1" role="dialog" aria-labelledby="mdlAgregarActualizarCargaAcademicaLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="AgregarNuevoPlanLabel"><span id="modal_title"></span></h5>
                <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="frmAgregaPlan" method="post" action="">
                    <input type="hidden" id="hdnIdPlan" name="id_plan" value="-1">
                    <input type="hidden" id="hdnIdCarga" name="id_carga" value="-1">
                    <input type="hidden" id="hdnTipoEnvio" value="update">
                    <div class="form-group row">
                        <label for="sctPeriodoNuevaCarga" class="control-label">Periodo</label>
                        <select id="sctPeriodoNuevaCarga" name="periodo" class="form-control selectpicker" data-style="btn btn-link">
                            <option value="1">Enero - Abril</option>
                            <option value="2">Mayo - Agosto</option>
                            <option value="3">Septiembre - Diciembre</option>
                        </select>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-6">
                            <label for="dteFechaInicio" class="control-label">Fecha de Inicio</label>
                            <input type="text" id="dteFechaInicio" name="fecha_inicio">
                        </div>
                        <div class="col-sm-6">
                            <label for="dteFechaFinal" class="control-label">Fecha de Cierre</label>
                            <input type="text" id="dteFechaFinal" name="fecha_cierre">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label col-lg" for="sctAnioCargaAcademia">Año</label>
                        <div class="col-lg">
                            <select id="sctAnioCargaAcademia" class="form-control" name="anio" data-style="btn btn-link">
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

<!--   Core JS Files   -->
<?php include_once("../components/core_js.php"); ?>
<script src="../../static/js/director/lista_carga_academicas.js"></script>
<!-- End Core JS Files -->

<script>
    $(document).ready(function () {
        $().ready(function () {
            $sidebar = $('.sidebar');

            $sidebar_img_container = $sidebar.find('.sidebar-background');

            $full_page = $('.full-page');

            $sidebar_responsive = $('body > .navbar-collapse');

            window_width = $(window).width();
        });

        $("#dteFechaInicio").datepicker({format: "yyyy/mm/dd", footer: true, modal: true, header: true,  uiLibrary: 'materialdesign' });
      $("#dteFechaFinal").datepicker({format: "yyyy/mm/dd", footer: true, modal: true, header: true,  uiLibrary: 'materialdesign' });

        sss("id_carrera", <?php echo $carrera->get_data("id"); ?>, false);
        initd();
    });
</script>
</body>

</html>