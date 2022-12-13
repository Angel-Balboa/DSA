<?php
include_once ("../../init.php");

use dsa\api\controller\sesion\CSesion;

$sesion = CSesion::inits();
$tipo = $sesion->tipo_usuario;
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <?php
  include_once("../components/metas.php");
  include_once("../components/links.php");
  ?>
  <title>
    Perfil del Profesor
  </title>
</head>

<body class="">
<div class="wrapper ">
  <!-- sidebar -->
  <?php
  include_once("../components/sidebar.php");
  ?>
  <!-- end sidebar -->
  <div class="main-panel">
    <!-- Navbar -->
    <?php
    include_once("../components/navbar.php");
    ?>
    <!-- End Navbar -->
    <div class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-header card-header-primary">
                <h4 class="card-title">Planeación Académica: Enero - Abril 2021</h4>
              </div>
              <div class="card-body">

                <div id="accordion" role="tablist">
                  <div class="card">
                    <div class="card-header" role="tab" id="gestionAcademica">
                      <h6 class="mb-0">
                        <a data-toggle="collapse" href="#collapseGestionAcademica" aria-expanded="true"
                           aria-controls="collapseGestionAcademica">
                          <div class="row">
                            <div class="col-md-10">
                              Gestión Académica
                            </div>
                            <div class="col-md-2">
                              Total: <span id="totHorasGestion"></span>
                            </div>
                          </div>
                        </a>
                      </h6>
                    </div>
                    <a id="collapseGestionAcademica" class="collapse show" role="tabpanel"
                       aria-labelledby="gestionAcademica" data-parent="#accordion">
                      <div class="card-body" id="gestion-academica">
                        <form id="frmGestionAcademica" method="post" action="#">
                          <input type="hidden" class="id-planeacion" name="id_planeacion" value="-1">
                          <input type="hidden" name="tipo_actividad_academica" value="GESTION">
                          <div class="row">
                            <div class="col-lg-4">
                              <div class="text-center text-primary">Actividad</div>
                            </div>
                            <div class="col-lg-3">
                              <div class="text-center text-primary">Horas</div>
                            </div>
                            <div class="col-lg-4">
                              <div class="text-center text-primary">Evidencia al término del periodo</div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12" id="content-gestion-academica">
                            </div>
                          </div>
                          <div id="help-block"></div>
                          <hr>
                          <div class="row justify-content-end">
                            <div class="col-sm-1">
                              <button type="submit" href="" class="btn btn-success btn-round btn-sm" title="Guardar"
                                     id="btnSubmitFrmGestion">
                                <i class="material-icons">
                                  save
                                </i>
                              </button>
                            </div>
                          </div>
                        </form>
                      </div>
                    </a>
                  </div> <!-- end card -->

                  <div class="card">
                    <div class="card-header" role="tab" id="capacitacionYDesarrollo">
                      <h6 class="mb-0">
                        <a data-toggle="collapse" href="#collapseCapacitacionYDesarrollo" aria-expanded="true"
                           aria-controls="collapseCapacitacionYDesarrollo">
                          <div class="row">
                            <div class="col-md-10">
                              Capacitación y Desarrollo Profesional
                            </div>
                            <div class="col-md-2">
                              Total: <span id="totHorasCapacitacion"></span>
                            </div>
                          </div>
                        </a>
                      </h6>
                    </div>
                    <a id="collapseCapacitacionYDesarrollo" class="collapse show" role="tabpanel"
                       aria-labelledby="capacitacionYDesarrollo" data-parent="#accordion">
                      <div class="card-body" id="capacitacion">
                        <form id="frmCapacitacionAcademica" method="post" action="">
                          <input type="hidden" class="id-planeacion" name="id_planeacion" value="-1">
                          <input type="hidden" name="tipo_actividad_academica" value="CAPACITACION">
                          <div class="row">
                            <div class="col-lg-4">
                              <div class="text-center text-primary">Curso</div>
                            </div>
                            <div class="col-lg-3">
                              <div class="text-center text-primary">Horas</div>
                            </div>
                            <div class="col-lg-4">
                              <div class="text-center text-primary">Evidencia al término del periodo</div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12" id="content-capacitacion">
                            </div>
                          </div>
                          <div id="help-block-capacitacion"></div>
                          <hr>
                          <div class="row justify-content-end">
                            <div class="col-sm-1">
                              <button type="submit" href="" class="btn btn-success btn-round btn-sm" title="Guardar" id="btnSubmitFrmCapacitacion">
                                <i class="material-icons">
                                  save
                                </i>
                              </button>
                            </div>
                          </div>
                        </form>
                      </div>
                    </a>
                  </div> <!-- end card -->

                  <div class="card">
                    <div class="card-header" role="tab" id="asesorias">
                      <h6 class="mb-0">
                        <a data-toggle="collapse" href="#collapseAsesorias" aria-expanded="true"
                           aria-controls="collapseAsesorias">
                          <div class="row">
                            <div class="col-md-10">
                              Asesorías
                            </div>
                            <div class="col-md-2">
                              Total: <span id="totHorasAsesoria"></span>
                            </div>
                          </div>
                        </a>
                      </h6>
                    </div>
                    <a id="collapseAsesorias" class="collapse show" role="tabpanel" aria-labelledby="asesorias"
                       data-parent="#accordion">
                      <div class="card-body">
                        <form id="frmPlaneacionAsesorias" method="" action="">
                          <input type="hidden" class="id-planeacion" name="id_planeacion" value="-1">
                          <input type="hidden" id="hdnIdPlenacionAsesoria" name="id_planeacion_asesoria" value="-1">
                          <div class="row">
                            <div class="col-lg-4">
                              Evidencia por medio de:
                              <div class="font-weight-bold">Reporte de estancia</div>
                            </div>

                            <div class="col-lg-2">
                              <div class="font-weight-bold">Asesor Institucional Estancia</div>
                            </div>
                            <div class="col-lg-2">
                              <div class="font-weight-bold">Asesor Institucional de Estadía</div>
                            </div>
                            <div class="col-lg-2">
                              <div class="font-weight-bold">No. Proyectos Asesor Empresarial de Estancia</div>
                            </div>
                            <div class="col-lg-2">
                              <div class="font-weight-bold">No. Proyectos Asesor Empresarial de Estadía</div>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-lg-4">
                              No. Alumnos / Proyectos
                            </div>

                            <div class="col-lg-2">
                              <select class="custom-select select-horas-asesorias" id="sctInstitucionalEstancia" name="institucional_estancia">
                                <?php for ($i=0; $i <= 10; $i++) echo '<option value="' . $i . '">' . $i . '</option>'; ?>
                              </select>
                            </div>
                            <div class="col-lg-2">
                              <select class="custom-select select-horas-asesorias" id="sctInstitucionalEstadia" name="institucional_estadia">
                                <?php for ($i=0; $i <= 10; $i++) echo '<option value="' . $i . '">' . $i . '</option>'; ?>
                              </select>
                            </div>
                            <div class="col-lg-2">
                              <select class="custom-select select-horas-asesorias" id="sctEmpresarialEstancia" name="empresarial_estancia">
                                <?php for ($i=0; $i <= 4; $i++) echo '<option value="' . $i . '">' . $i . '</option>'; ?>
                              </select>
                            </div>
                            <div class="col-lg-2">
                              <select class="custom-select select-horas-asesorias" id="sctEmpresarialEstadia" name="empresarial_estadia">
                                <?php for ($i=0; $i <= 2; $i++) echo '<option value="' . $i . '">' . $i . '</option>'; ?>
                              </select>
                            </div>
                          </div>
                          <hr>
                          <div class="row">
                            <div class="col-lg-4">
                              <ul class="text-danger">
                                <li>3 HRAS - ASESOR INSTITUCIONAL POR CADA AESORADO DE ESTANCIA I Y II</li>
                                <li>3 HRAS - ASESOR INSTITUCIONAL POR CADA AESORADO DE ESTANCIA I Y II</li>
                                <li>5 HRAS - ASESOR INSTITUCIONAL POR CADA ASESORADO DE ESTADÍA</li>
                                <li>5 HRAS - PROYECTO COMO ASESOR EMPRESARIAL DE ESTANCIA</li>
                                <li>20 HRAS - PROYECTO COMO ASESOR EMPRESARIAL DE ESTADÍA</li>

                              </ul>
                              <small class="text-muted"> 2 ALUMNOS COMO ASESOR EMPRESARIAL DE ESTADÍA X PROFESOR; MAX. 4
                                ASESORADOS POR PROFESOR ENTRE ESTANCIA Y ESTADÍA, LLEVAR REGISTRO DE ASESORÍAS EN EL
                                SITA)</small>
                            </div>

                            <div class="col-lg-2 text-center">
                              <span id="horas_institucional_estancia"></span>
                            </div>
                            <div class="col-lg-2 text-center">
                              <span id="horas_institucional_estadia"></span>
                            </div>
                            <div class="col-lg-2 text-center">
                              <span id="horas_empresarial_estancia"></span>
                            </div>
                            <div class="col-lg-2 text-center">
                              <span id="horas_empresarial_estadia"></span>
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-lg-10 text-center text-danger">
                              Asesorías académicas sobre asignaturas afines
                            </div>
                            <div class="col-lg-2 text-center">
                              <span id="horasAsignaturasAfines">???</span>
                            </div>
                          </div>
                          <div class="row justify-content-end">
                            <div class="col-sm-1">
                              <button type="submit" class="btn btn-success btn-round btn-sm" title="Guardar" id="btnSubmitFrmAsesorias">
                                <i class="material-icons">
                                  save
                                </i>
                              </button>
                            </div>
                          </div>
                        </form>
                      </div>
                    </a>
                  </div> <!-- end card -->

                  <div class="card">
                    <div class="card-header" role="tab" id="promocion">
                      <h6 class="mb-0">
                        <a data-toggle="collapse" href="#collapsePromocion" aria-expanded="true"
                           aria-controls="collapsePromocion">
                          <div class="row">
                            <div class="col-md-10">
                              Promoción
                            </div>
                            <div class="col-md-2">
                              Total: <span id="totHorasPromocion"></span>
                            </div>
                          </div>
                        </a>
                      </h6>
                    </div>
                    <a id="collapsePromocion" class="collapse show" role="tabpanel" aria-labelledby="promocion"
                       data-parent="#accordion">
                      <div class="card-body">
                        <form id="frmPromocionAcademica" method="post" action="">
                          <input type="hidden" class="id-planeacion" name="id_planeacion" value="-1">
                          <input type="hidden" id="hdnIdActividadPromocion" name="id_actividad_promocion" value="-1">
                          <div class="row">
                            <div class="col-lg-2">
                              <select class="custom-select" id="sctHorasPromocion" name="horasPromocion">
                                <option value="0">0</option>
                                <option value="5">5</option>
                                <option value="10">10</option>
                                <option value="15">15</option>
                                <option value="20">20</option>
                              </select>
                            </div>

                            <div class="col-lg-10">
                              <ul class="text-danger">
                                <li>3 EVENTOS POR PROFESOR DE TIEMPO COMPLETO ORIENTADO A LA DOCENCIA</li>
                                <li>1 EVENTO POR PROFESOR DE TIEMPO COMPLETO ORIENTADO A INVESTIGACIÓN</li>
                                <li>EVIDENCIA POR MEDIO DE:</li>
                                <ul>
                                  <li>Oficio de comisión para cada actividad promoción</li>
                                </ul>
                              </ul>

                              <small class="text-muted">Se desarrollarán sesiones de promoción a través de
                                videoconferencias o difusión de material multimedia desarrollado o propuesto por
                                profesores.</small>
                            </div>
                          </div>
                          <hr>
                          <div class="row justify-content-end">
                            <div class="col-sm-1">
                              <button type="submit" class="btn btn-success btn-round btn-sm" title="Guardar" id="btnSubmitPromocion">
                                <i class="material-icons">
                                  save
                                </i>
                              </button>
                            </div>
                          </div>
                        </form>
                      </div>
                    </a>
                  </div> <!-- end card -->

                  <div class="card">
                    <div class="card-header" role="tab" id="vinculacion">
                      <h6 class="mb-0">
                        <a data-toggle="collapse" href="#collapseVinculacion" aria-expanded="true"
                           aria-controls="collapseVinculacion">
                          <div class="row">
                            <div class="col-md-10">
                              Vinculación
                            </div>
                            <div class="col-md-2">
                              Total: <span id="totHorasVinculacion"></span>
                            </div>
                          </div>
                        </a>
                      </h6>
                    </div>
                    <a id="collapseVinculacion" class="collapse show" role="tabpanel" aria-labelledby="vinculacion"
                       data-parent="#accordion">
                      <div class="card-body">
                        <form  id="frmVinculacionAcademica" action="" method="post">
                          <input type="hidden" class="id-planeacion" name="id_planeacion" value="-1">
                          <input type="hidden" name="tipo_actividad_academica" value="VINCULACION">
                          <div class="row">
                            <div class="col-lg-3 font-weight-bold text-center">
                              Actividad
                            </div>
                            <div class="col-lg-3 font-weight-bold text-center">
                              Empresa/Institución
                            </div>
                            <div class="col-lg-2 font-weight-bold text-center">
                              Horas
                            </div>
                            <div class="col-lg-3 font-weight-bold text-center">
                              Evidencia al término del periodo
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12" id="content-vinculacion">
                            </div>
                          </div>
                          <div id="help-block-vinculacion"></div>
                          <hr>
                          <div class="row justify-content-end">
                            <div class="col-sm-1">
                              <button type="submit" href="" class="btn btn-success btn-round btn-sm" title="Guardar" id="btnSubmitFrmCapacitacion">
                                <i class="material-icons">
                                  save
                                </i>
                              </button>
                            </div>
                          </div>
                        </form>
                      </div>
                    </a>
                  </div> <!-- end card -->

                  <div class="card">
                    <div class="card-header" role="tab" id="investigacion">
                      <h6 class="mb-0">
                        <a data-toggle="collapse" href="#collapseInvestigacion" aria-expanded="true"
                           aria-controls="collapseInvestigacion">
                          <div class="row">
                            <div class="col-md-10">
                              Investigación
                            </div>
                            <div class="col-md-2">
                              Total: <span id="totHorasInvestigacion"></span>
                            </div>
                          </div>
                        </a>
                      </h6>
                    </div>
                    <a id="collapseInvestigacion" class="collapse show" role="tabpanel" aria-labelledby="investigacion"
                       data-parent="#accordion">
                      <div class="card-body" id="investigation">
                        <form id="frmPlaneacionInvestigacion" method="post" action="">
                          <input type="hidden" class="id-planeacion" name="id_planeacion" value="-1">
  <!--                        <div class="row justify-content-end">-->
  <!--                          <div class="col-md-2 align-self-end">-->
  <!--                            Total de horas: <input type="number" class="form-control" max="300" min="10" value="100">-->
  <!--                          </div>-->
  <!--                        </div>-->
  <!--                        <hr>-->
                          <div class="row">
                            <div class="col-lg-3 font-weight-bold text-center">
                              Nombre del proyecto/Actividad
                            </div>
                            <div class="col-lg-2 font-weight-bold text-center">
                              Tipo de producto
                            </div>
                            <div class="col-lg-2 font-weight-bold text-center">
                              % de avance
                            </div>
                            <div class="col-lg-2 font-weight-bold text-center">
                              % esperado
                            </div>
                            <div class="col-lg-2 font-weight-bold text-center">
                              Fecha tentativa
                            </div>
                          </div>
                          <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12" id="content-investigacion">
                            </div>
                          </div>
                          <div id="help-block-investigacion"></div>
                          <hr>
                          <div class="row justify-content-end">
                            <div class="col-sm-1">
                              <button type="submit" class="btn btn-success btn-round btn-sm" title="Guardar" id="btnSubmitPlaneacionInvestigacion">
                                <i class="material-icons">
                                  save
                                </i>
                              </button>
                            </div>
                          </div>
                        </form>
                      </div>
                    </a>
                  </div> <!-- end card -->

                </div>

              </div> <!-- end Card body-->
              <div class="card-footer justify-content-end">
                <div class="col-lg-4">
                  <div class="font-weight-bold">Total de horas de actividades académico-administrativas</div>
                </div>
                <div class="col-lg-2 text-left" id="txtHorasAcademicoAdministrativas">
                  ???
                </div>
                <div class="col-lg-4">
                  <div class="font-weight-bold">Total de horas al cuatrimestre</div>
                </div>
                <div class="col-lg-1">
                  600
                </div>
                <div class="col-lg-1">
                  <button class="btn btn-info" id="btnFinalizarPlaneacion">Finalizar</button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <?php
    include_once("../components/footer.php");
    ?>
  </div>
</div>

<!--   Core JS Files   -->
<?php include_once("../components/core_js.php"); ?>
<!--end Core JS Files -->
<script src="../../static/js/profesor/edit_planeacion.js"></script>
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