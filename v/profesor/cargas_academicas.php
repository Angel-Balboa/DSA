<?php
$tipo = "profesor";
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <?php
  include_once ("../components/metas.php");
  include_once ("../components/links.php");
  ?>
  <title>
    Cargas Académicas
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
              <!-- Cargas Académicas -->
              <div class="col-md-4">
                  <div class="card">
                      <div class="card-header card-header-primary">
                          <h4 class="card-title">Cargas académicas</h4>
                      </div>
                      <div class="card-body">
                          <div id="accordion" role="tablist">
                              <div class="card">
                                  <div class="card-header" role="tab" id="headingOne">
                                      <h6 class="mb-0">
                                          <a data-toggle="collapse" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                              Periodos del año 2021
                                          </a>
                                      </h6>
                                  </div>
                                  <a id="collapseOne" class="collapse show" role="tabpanel" aria-labelledby="headingOne" data-parent="#accordion">
                                      <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead class="text-warning">
                                                    <th>#</th>
                                                    <th>Periodo</th>
                                                    <th></th>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>1</td>
                                                        <td>Enero - Abril</td>
                                                        <td class="td-actions text-right">
                                                          <button type="button" role="tooltip" title="Edit Task" class="btn btn-primary btn-link btn-sm" id="primero21" onclick="editarPeriodo(this);">
                                                            <i class="material-icons">visibility</i>
                                                          </button>                                                          
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>2</td>
                                                        <td>Mayo - Agosto</td>
                                                        <td class="td-actions text-right">
                                                          <button type="button" role="tooltip" title="Edit Task" class="btn btn-primary btn-link btn-sm" id="segundo21" onclick="editarPeriodo(this);">
                                                            <i class="material-icons">visibility</i>
                                                          </button>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>3</td>
                                                        <td>Septiembre - Diciembre </td>
                                                        <td class="td-actions text-right">
                                                          <button type="button" role="tooltip" title="Edit Task" class="btn btn-primary btn-link btn-sm" id="tercero21" onclick="editarPeriodo(this);">
                                                            <i class="material-icons">visibility</i>
                                                          </button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                      </div>
                                  </a>
                              </div> <!-- end card collapseOne-->

                              <div class="card">
                                <div class="card-header" role="tab" id="headingTwo">
                                    <h6 class="mb-0">
                                        <a data-toggle="collapse" href="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo">
                                            Periodos del año 2020
                                        </a>
                                    </h6>
                                </div>
                                <a id="collapseTwo" class="collapse" role="tabpanel" aria-labelledby="headingTwo" data-parent="#accordion">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead class="text-warning">
                                                    <th>#</th>
                                                    <th>Periodo</th>
                                                    <th></th>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>1</td>
                                                        <td>Enero - Abril</td>
                                                        <td class="td-actions text-right">
                                                          <button type="button" role="tooltip" title="Edit Task" class="btn btn-primary btn-link btn-sm" id="primero20" onclick="editarPeriodo(this);">
                                                            <i class="material-icons">visibility</i>
                                                          </button>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>2</td>
                                                        <td>Mayo - Agosto</td>
                                                        <td class="td-actions text-right">
                                                          <button type="button" role="tooltip" title="Edit Task" class="btn btn-primary btn-link btn-sm" id="segundo20" onclick="editarPeriodo(this);">
                                                            <i class="material-icons">visibility</i>
                                                          </button>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>3</td>
                                                        <td>Septiembre - Diciembre </td>
                                                        <td class="td-actions text-right">
                                                          <button type="button" role="tooltip" title="Edit Task" class="btn btn-primary btn-link btn-sm" id="tercero20" onclick="editarPeriodo(this);">
                                                            <i class="material-icons">visibility</i>
                                                          </button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </a>
                            </div> <!-- end card collapseTwo-->

                            <div class="card">
                                <div class="card-header" role="tab" id="headingThree">
                                    <h6 class="mb-0">
                                        <a data-toggle="collapse" href="#collapseThree" aria-expanded="true" aria-controls="collapseThree">
                                            Periodos del año 2019
                                        </a>
                                    </h6>
                                </div>
                                <a id="collapseThree" class="collapse" role="tabpanel" aria-labelledby="headingTwo" data-parent="#accordion">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            <table class="table">
                                                <thead class="text-warning">
                                                    <th>#</th>
                                                    <th>Periodo</th>
                                                    <th></th>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>1</td>
                                                        <td>Enero - Abril</td>
                                                        <td class="td-actions text-right">
                                                          <button type="button" role="tooltip" title="Edit Task" class="btn btn-primary btn-link btn-sm" id="primero19" onclick="editarPeriodo(this);">
                                                            <i class="material-icons">visibility</i>
                                                          </button>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>2</td>
                                                        <td>Mayo - Agosto</td>
                                                        <td class="td-actions text-right">
                                                          <button type="button" role="tooltip" title="Edit Task" class="btn btn-primary btn-link btn-sm" id="segundo19" onclick="editarPeriodo(this);">
                                                            <i class="material-icons">visibility</i>
                                                          </button>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>3</td>
                                                        <td>Septiembre - Diciembre </td>
                                                        <td class="td-actions text-right">
                                                          <button type="button" role="tooltip" title="Edit Task" class="btn btn-primary btn-link btn-sm" id="tercero19" onclick="editarPeriodo(this);">
                                                            <i class="material-icons">visibility</i>
                                                          </button>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </a>
                            </div> <!-- end card collapseTwo-->

                          </div>
                      </div>
                  </div>
              </div>
              <!-- Cargas Académicas por periodo -->
              <div class=" col-md-8">
                <div class="card">
                    <div class="card-header card-header-primary">
                        <h4 class="card-title" id="periodo-dinamico">Septiembre - Diciembre 2021 / Grupos</h4>
                    </div>                    
                    <div class="card-body">
                        <div id="accordion2" role="tablist">
                            <div class="total-horas">
                              <h5>Total de horas: 18</h5>
                            </div>
                            <div class="card">
                                <div class="card-header" role="tab" id="headingOne2">
                                    <h6 class="mb-0">
                                        <a data-toggle="collapse" href="#collapseOne2" aria-expanded="true" aria-controls="collapseOne2">
                                            Ingeniería en Tecnologías de la Información
                                        </a>
                                    </h6>
                                </div>
                                <a id="collapseOne2" class="collapse show" role="tabpanel" aria-labelledby="headingOne2" data-parent="#accordion2">
                                    <div class="card-body">
                                      <div class="table-responsive">
                                        <table class="table">
                                          <thead class="text-primary">
                                              <th>Grupo</th>
                                              <th>Turno</th>
                                              <th>Materia</th>
                                              <th>Horas</th>
                                              <th>Manual</th>
                                          </thead>
                                          <tbody>
                                            <tr>
                                              <td>ITI 3-1</td>
                                              <td>Mat</td>
                                              <td>Introducción a la Programación</td>
                                              <td>6</td>
                                              <td class="td-actions text-right">
                                                <a class="btn btn-primary btn-link btn-sm" href="#">
                                                  <i class="material-icons">picture_as_pdf</i>
                                                </a>
                                              </td>
                                            </tr>
                                          </tbody>
                                        </table>
                                      </div>
                                    </div>
                                </a>
                            </div> <!-- end card collapseOne-->

                            <div class="card">
                              <div class="card-header" role="tab" id="headingTwo2">
                                  <h6 class="mb-0">
                                      <a data-toggle="collapse" href="#collapseTwo2" aria-expanded="true" aria-controls="collapseTwo2">
                                          Ingeniería en Mecatrónica
                                      </a>
                                  </h6>
                              </div>
                              <a id="collapseTwo2" class="collapse" role="tabpanel" aria-labelledby="headingTwo2" data-parent="#accordion2">
                                  <div class="card-body">
                                      <div class="table-responsive">
                                        <table class="table">
                                          <thead class="text-primary">
                                              <th>Grupo</th>
                                              <th>Turno</th>
                                              <th>Materia</th>
                                              <th>Horas</th>
                                              <th>Manual</th>
                                          </thead>
                                          <tbody>
                                            <tr>
                                              <td>ITI 3-1</td>
                                              <td>Mat</td>
                                              <td>Introducción a la Programación</td>
                                              <td>6</td>
                                              <td class="td-actions text-right">
                                                <a class="btn btn-primary btn-link btn-sm" href="#">
                                                  <i class="material-icons">picture_as_pdf</i>
                                                </a>
                                              </td>
                                            </tr>
                                          </tbody>
                                        </table>
                                      </div>
                                  </div>
                              </a>
                          </div> <!-- end card collapseTwo-->

                          <div class="card">
                              <div class="card-header" role="tab" id="headingThree2">
                                  <h6 class="mb-0">
                                      <a data-toggle="collapse" href="#collapseThree2" aria-expanded="true" aria-controls="collapseThree2">
                                          Maestría en Ingeniería
                                      </a>
                                  </h6>
                              </div>
                              <a id="collapseThree2" class="collapse" role="tabpanel" aria-labelledby="headingTwo2" data-parent="#accordion2">
                                  <div class="card-body">
                                      <div class="table-responsive">
                                        <table class="table">
                                          <thead class="text-primary">
                                              <th>Grupo</th>
                                              <th>Turno</th>
                                              <th>Materia</th>
                                              <th>Horas</th>
                                              <th>Manual</th>
                                          </thead>
                                          <tbody>
                                            <tr>
                                              <td>ITI 3-1</td>
                                              <td>Mat</td>
                                              <td>Introducción a la Programación</td>
                                              <td>6</td>
                                              <td class="td-actions text-right">
                                                <a class="btn btn-primary btn-link btn-sm" href="#">
                                                  <i class="material-icons">picture_as_pdf</i>
                                                </a>
                                              </td>
                                            </tr>
                                          </tbody>
                                        </table>
                                      </div>
                                  </div>
                              </a>
                          </div> <!-- end card collapseTwo-->

                        </div>
                    </div>
                </div>
            </div>
          </div>
        </div>
      </div>
      <!-- footer -->
      <?php include_once ("../components/footer.php"); ?>
      <!-- end footer-->
    </div>
  </div>
  
  <!--   Core JS Files   -->
  <?php include_once ("../components/core_js.php"); ?>
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
    });
  </script>
</body>

</html>