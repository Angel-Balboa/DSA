<?php

include_once ("../../init.php");

use dsa\api\controller\sesion\CSesion;
use dsa\api\model\usuario\Usuario;
use dsa\api\model\profesor\Profesor;

$sesion = CSesion::inits();
$tipo = $sesion->tipo_usuario;
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <?php
  include_once ("../components/metas.php");
  include_once ("../components/links.php");
  ?>
  <title>
    Planeacion Académica
  </title>
</head>

<body class="">
<div class="wrapper ">
  <!-- sidebar -->
  <?php
  include_once ("../components/sidebar.php");
  ?>
  <!-- end sidebar -->
  <div class="main-panel">
    <!-- Navbar -->
    <?php include_once ("../components/navbar.php"); ?>
    <!-- End Navbar -->
    <div class="content">
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-12">
            <div class="card">
              <div class="card-header card-header-primary">
                <h4 class="card-title">Planeación Académica: <span id="periodo_anio" class="font-weight-bold"></span> - <span id="profesor">
<!--                    --><?php //echo $sesion->nivel_adscripcion . " " . $sesion->nombre_usuario["nombre"] . " " . $sesion->nombre_usuario["apellidos"] ?>
                  </span> </h4>
              </div>
              <div class="card-body">
                <div id="accordion" role="tablist">
                  <div class="card">
                    <div class="card-header" role="tab" id="gestionAcademica">
                      <h6 class="mb-0">
                        <a data-toggle="collapse" href="#collapseGestionAcademica" aria-expanded="true" aria-controls="collapseGestionAcademica">
                          <div class="row">
                            <div class="col-md-10">
                              Gestión Académica
                            </div>
                            <div class="col-md-2">
                              Total: <span id="total_h_gestion"></span>
                            </div>
                          </div><!-- end row -->
                        </a>
                      </h6>
                    </div><!-- end card-header -->
                    <a id="collapseGestionAcademica" class="collapse hide" role="tabpanel" aria-labelledby="gestionAcademica" data-parent="#accordion">
                      <div class="card-body" id="cardBody-gestion-academica">
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
                        </div><!-- end header row -->
                      </div><!-- end car-body -->
                    </a>
                  </div><!-- end card -->
                  <div class="card">
                    <div class="card-header" role="tab" id="capacitacionYDesarrollo">
                      <h6 class="mb-0">
                        <a data-toggle="collapse" href="#collapseCapacitacionYDesarrollo" aria-expanded="true" aria-controls="collapseCapacitacionYDesarrollo">
                          <div class="row">
                            <div class="col-md-10">
                              Capacitación y Desarrollo Profesional
                            </div>
                            <div class="col-md-2">
                              Total: <span id="total_h_capacitacion"></span>
                            </div>
                          </div><!-- end row -->
                        </a>
                      </h6>
                    </div><!-- end card-header -->
                    <a id="collapseCapacitacionYDesarrollo" class="collapse hide" role="tabpanel" aria-labelledby="capacitacionYDesarrollo" data-parent="#accordion">
                      <div class="card-body" id="cardBody-capacitacion">
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
                        </div><!-- end row encabezados -->
                      </div><!-- end card body -->
                    </a>
                  </div> <!-- end card -->
                  <div class="card">
                    <div class="card-header" role="tab" id="asesorias">
                      <h6 class="mb-0">
                        <a data-toggle="collapse" href="#collapseAsesorias" aria-expanded="true" aria-controls="collapseAsesorias">
                          <div class="row">
                            <div class="col-md-10">
                              Asesorías
                            </div>
                            <div class="col-md-2">
                              Total: <span id="total_h_asesorias"></span>
                            </div>
                          </div><!-- end row -->
                        </a>
                      </h6>
                    </div><!-- end card-header -->
                    <a id="collapseAsesorias" class="collapse hide" role="tabpanel" aria-labelledby="asesorias" data-parent="#accordion">
                      <div class="card-body">
                        <div class="row">
                          <div class="col-lg-4">
                            Evidencia por medio de: <p class="font-weight-bold">Reporte de estancia</p>
                          </div>
                          <div class="col-lg-2">
                            <div class="font-weight-bold">Asesor Institucional Estancia</div>
                          </div>
                          <div class="col-lg-2">
                            <div class="font-weight-bold">Asesor Institucional Estadía</div>
                          </div>
                          <div class="col-lg-2">
                            <div class="font-weight-bold">No. Proyectos Asesor Empresarial de Estancia</div>
                          </div>
                          <div class="col-lg-2">
                            <div class="font-weight-bold">No. Proyectos Asesor Empresarial de Estadía</div>
                          </div>
                        </div><!-- end row -->
                        <div class="row">
                          <div class="col-lg-4">
                            No. Alumnos / Proyectos
                          </div>
                          <div class="col-lg-2 text-center">
                            <p id="alumnos_institucional_estancia"></p>
                          </div>
                          <div class="col-lg-2 text-center">
                            <p id="alumnos_institucional_estadia"></p>
                          </div>
                          <div class="col-lg-2">
                            <p id="alumnos_empresarial_estancia"></p>
                          </div>
                          <div class="col-lg-2">
                            <p id="alumnos_empresarial_estadia"></p>
                          </div>
                        </div><!-- end row -->
                        <hr>
                        <div class="row">
                          <div class="col-lg-4">
                            <ul class="text-danger">
                              <li>3 HRAS - ASESOR INSTITUCIONAL POR CADA AESORADO DE ESTANCIA I Y II</li>
                              <li>5 HRAS - ASESOR INSTITUCIONAL POR CADA ASESORADO DE ESTADÍA</li>
                              <li>5 HRAS - PROYECTO COMO ASESOR EMPRESARIAL DE ESTANCIA</li>
                              <li>20 HRAS - PROYECTO COMO ASESOR EMPRESARIAL DE ESTADÍA</li>
                              <li>(MAX. 2 ALUMNOS COMO ASESOR EMPRESARIAL DE ESTADÍA X PROFESOR;  MAX. 4 ASESORADOS POR PROFESOR ENTRE ESTANCIA Y ESTADÍA, LLEVAR REGISTRO DE ASESORÍAS EN EL SITA)</li>
                            </ul>
                            <small class="text-muted"> 2 ALUMNOS COMO ASESOR EMPRESARIAL DE ESTADÍA X PROFESOR;  MAX. 4 ASESORADOS POR PROFESOR ENTRE ESTANCIA Y ESTADÍA, LLEVAR REGISTRO DE ASESORÍAS EN EL SITA)</small>
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
                            ??
                          </div>
                        </div>
                      </div><!-- end card-body -->
                    </a>
                  </div> <!-- end card -->
                  <div class="card">
                    <div class="card-header" role="tab" id="promocion">
                      <h6 class="mb-0">
                        <a data-toggle="collapse" href="#collapsePromocion" aria-expanded="true" aria-controls="collapsePromocion">
                          <div class="row">
                            <div class="col-md-10">
                              Promoción
                            </div>
                            <div class="col-md-2">
                              Total: <span id="total_h_promocion"></span>
                            </div>
                          </div>
                        </a>
                      </h6>
                    </div><!-- end card-header -->
                    <a id="collapsePromocion" class="collapse hide" role="tabpanel" aria-labelledby="promocion" data-parent="#accordion">
                      <div class="card-body">
                        <div class="row">
                          <div class="col-lg-2">
                            <p id="horas_promocion"></p>
                          </div>
                          <div class="col-lg-10">
                            <ul class="text-danger">
                              <li>3 EVENTOS POR PROFESOR DE TIEMPO COMPLETO ORIENTADO A LA DOCENCIA</li>
                              <li>1 EVENTO POR PROFESOR DE TIEMPO COMPLETO ORIENTADO A INVESTIGACIÓN</li>
                              <li>EVIDENCIA POR MEDIO DE:</li>
                              <ul>
                                <li>Oficio de comisión para cada actividad promoción </li>
                              </ul>
                            </ul>
                            <small class="text-muted">Se desarrollarán sesiones de promoción a través de videoconferencias o difusión de material multimedia desarrollado o propuesto por profesores.</small>
                          </div>
                        </div><!-- end row -->
                      </div><<!-- end card-body -->
                    </a>
                  </div> <!-- end card -->

                  <div class="card">
                    <div class="card-header" role="tab" id="vinculacion">
                      <h6 class="mb-0">
                        <a data-toggle="collapse" href="#collapseVinculacion" aria-expanded="true" aria-controls="collapseVinculacion">
                          <div class="row">
                            <div class="col-md-10">
                              Vinculación
                            </div>
                            <div class="col-md-2">
                              Total: <span id="total_h_vinculacion"></span>
                            </div>
                          </div>
                        </a>
                      </h6>
                    </div>
                    <a id="collapseVinculacion" class="collapse hide" role="tabpanel" aria-labelledby="vinculacion" data-parent="#accordion">
                      <div class="card-body" id="cardBody-vinculacion">
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
                        </div><!-- end row encabezados -->
                      </div>
                    </a>
                  </div> <!-- end card -->

                  <div class="card">
                    <div class="card-header" role="tab" id="investigacion">
                      <h6 class="mb-0">
                        <a data-toggle="collapse" href="#collapseInvestigacion" aria-expanded="true" aria-controls="collapseInvestigacion">
                          <div class="row">
                            <div class="col-md-10">
                              Investigación
                            </div>
                            <div class="col-md-2">
                              Total: <span id="total_h_investigacion"></span>
                            </div>
                          </div>
                        </a>
                      </h6>
                    </div>
                    <a id="collapseInvestigacion" class="collapse hide" role="tabpanel" aria-labelledby="investigacion" data-parent="#accordion">
                      <div class="card-body" id="cardBody-investigation">
                        <div class="row justify-content-end">
                          <div class="col-md-2 align-self-end">
                            Total de horas: <span id="total_h_investigacion_2" class="font-weight-bold">120</span>
                          </div>
                        </div>
                        <hr>
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
                        </div><!-- end row encabezados -->
                      </div>
                    </a>
                  </div> <!-- end card -->
                </div><!-- end accordion -->
              </div> <!-- end Card body-->
              <div class="card-footer">
                <div class="row">
                  <div class="col-md-4 font-weight-bold">
                    Total de horas de actividades académico-administrativas
                  </div>
                  <div class="col-md-1">
                    <span id="horas-academico-administrativas">???</span>
                  </div>
                  <div class="col-md-4 font-weight-bold">
                    Total de horas al cuatrimestre
                  </div>
                  <div class="col-md-1">
                    600
                  </div>
                  <div class="col-md-1">
                    <button type="button" id="btnAceptarPlaneacion" class="btn btn-warning btn-sm" title="Aceptar Planeacion Académica">Aceptar Planeación</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <?php include_once ("../components/footer.php"); ?>
  </div>
</div>

<!--   Core JS Files   -->
<?php include_once ("../components/core_js.php"); ?>
<!--   end Core JS Files   -->
<script src="../../static/js/director/detalle_planeacion_academica.js"></script>
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
  });
</script>
</body>
</html>
