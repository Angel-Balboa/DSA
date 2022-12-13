<?php

include_once ("../../init.php");

use dsa\api\controller\sesion\CSesion;

$sesion = CSesion::inits();
$tipo = $sesion->tipo_usuario;
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <?php
  include_once ("../components/metas.php");
  include_once ("../components/links.php");
  ?>
    <title>
        Admin Detalle de Carrera
    </title>
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
                    <div class="col-lg-4 col-md-4">
                        <div class="card">
                            <div class="card-header card-header-primary">
                                <h4 class="card-title">Datos de la Carrera</h4>
                            </div> <!-- end card-header -->
                            <div class="card-body table-responsive">
                                <table class="table table-hover" id="tblDetallesCarrera">
                                    <tbody style="text-align: center;">
                                    <tr>
                                        <td>Clave</td>
                                        <td id="clave_carrera"></td>
                                    </tr>
                                    <tr>
                                        <td>Nombre</td>
                                        <td id="nombre_carrera"></td>
                                    </tr>
                                    <tr>
                                        <td>Nivel</td>
                                        <td id="nivel_carrera"></td>
                                    </tr>
                                    <tr>
                                        <td>Director<div id="id_director" hidden>-1</div></td>
                                        <td id="director_carrera"></td>

                                    </tr>
                                    </tbody>
                                </table>
                            </div> <!-- end card-body -->
                            <div class="card-footer">
                                <button type="button" class="btn btn-primary" id="btnOpenUpdCarrera">Actualizar
                                </button>
                            </div>
                        </div> <!-- end card-->
                    </div> <!-- end col -->
                    <div class="col-lg-8 col-md-8">
                        <div class="card">
                            <div class="card-header card-header-primary">
                                <h4 class="card-title">Planes de Estudio</h4>
                            </div> <!-- end card-header -->
                            <div class="card-body table-responsive">
                                <table class="table table-hover">
                                    <thead class="text-primary" style="text-align: center;">
                                    <tr>
                                        <th>Clave</th>
                                        <th>Nivel</th>
                                        <th>Nombre</th>
                                        <th>Año</th>
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
                    </div>
                </div> <!-- End row -->
                <div class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="card">
                            <div class="card-header card-header-primary">
                                <h4 class="card-title">Profesores Adscritos</h4>
                            </div> <!-- end card-header -->
                            <div class="card-body table-responsive">
                                <table class="table table-hover">
                                    <thead class="text-primary" style="text-align: center;">
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Tipo</th>
                                        <th>Acciones</th>
                                    </tr>
                                    </thead>
                                    <tbody style="text-align: center;" id="tbody_profesores_adscritos">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- end container-fluid-->

        </div> <!-- end content -->
        <?php include_once ("../components/footer.php"); ?>
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
                    <input type="hidden" id="hdnClaveCarrera" name="clv_carrera" value="-1">
                    <div class="form-group row">
                        <label class="col-form-label col-lg" for="txtClaveNuevoPlan">Clave</label>
                        <div class="col-lg">
                            <input type="text" class="form-control" id="txtClaveNuevoPlan" name="clv_plan" maxlength="50" minlength="5" required>
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
<div class="modal fade" id="ActualizarPlanEstudios" tabindex="-1" role="dialog"
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
                <input type="hidden" id="hdnClavePlanEstudios" name="clv_plan_estudios" value="-1">
                <div class="modal-body">
                    <div class="form-group row">
                        <label for="txtEditarNombrePlan" class="col-lg col-form-label">Nombre Plan de Estudios</label>
                        <div class="col-lg">
                            <input type="text" class="form-control" id="txtEditarNombrePlan" name="nombre_plan_estudios" required maxlength="250" minlength="10">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="sctEditAnioPlan" class="col-lg col-form-label">Año de registro</label>
                        <div class="col-lg">
                            <select id="sctEditAnioPlan" name="anio_plan_estudios" class="form-control">
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
                            <select id="sctEditNivelPlan" name="nivel_plan_estudios" class="form-control">
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
                        <input type="submit" class="btn btn-primary" value="Guardar" />
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            Cancelar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div> <!-- end modal ActualizarPlanEstudios -->

<!-- modal: EditarCarrera-->
<div class="modal fade" id="EditarCarrera" tabindex="-1" role="dialog" aria-labelledby="EditarCarreraLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="EditarCarreraLabel">Actualizar Carrera</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="frmEditarCarrera" action="" method="post">
                    <input type="hidden" id="hdnEditClaveCarrera" name="clv_carrera" value="-1">
                    <div class="form-group row">
                        <label for="txtEditNombreCarrera" class="col-sm-3 col-form-label">Nombre</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="txtEditNombreCarrera" name="nuevo_nombre" required />
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="sctEditNivelCarrera" class="col-sm-3 col-form-label">Nivel</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="sctEditNivelCarrera" name="nuevo_nivel" required>
                                <option value="Ing">Ingeniería</option>
                                <option value="Lic">Licenciatura</option>
                                <option value="M.I.">Maestría en Ingeniería</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="sctEditDirectorCarrera" class="col-sm-3 col-form-label">Director</label>
                        <div class="col-sm-9">
                            <select class="form-control" id="sctEditDirectorCarrera" name="nuevo_director">
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <input type="submit" class="btn btn-primary" data-toggle="modal" value="Actualizar" />
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!--   Core JS Files   -->
<?php include_once ("../components/core_js.php"); ?>
<!-- End Core JS Files -->
<script src="../../static/js/admin/detalle_carrera.js"></script>
<script>
    $(document).ready(function () {
        $().ready(function () {
            $sidebar = $('.sidebar');

            $sidebar_img_container = $sidebar.find('.sidebar-background');

            $full_page = $('.full-page');

            $sidebar_responsive = $('body > .navbar-collapse');

            window_width = $(window).width();
        });
        initd();
    });
</script>
</body>

</html>