<?php
$tipo = "RRHH";
?>
<!DOCTYPE html>
<html lang="es">

<head>
  <?php
  include_once("../components/metas.php");
  include_once("../components/links.php");
  ?>
  <title>
    Profesores
  </title>
</head>
<body>
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
          <div class="col-lg col-md">
            <div class="card">
              <div class="card-header card-header-primary">
                <h4 class="card-title">Contratos por finalizar</h4>
              </div> <!-- end card-header -->
              <div class="card-body">
                <div class="row float-right">
                  <form class="form-inline">
                    <div class="col-lg form-group">
                      <div class="input-group no-border">
                        <div class="input-group-prepend">
                        <span class="input-group-text">
                          <i class="material-icons">search</i>
                        </span>
                        </div>
                        <input type="text" class="form-control" placeholder="Buscar..." size="25">
                      </div>
                      <div class="col-lg">
                        <div class="form-group">
                          <select class="form-control selectpicker" data-style="btn btn-link" id="sctFiltroContrato"
                                  multiple data-selected-text-format="count" title="Tipo">
                            <option value="P.A">PTC</option>
                            <option value="P.T.C">PA</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-lg">
                        <div class="form-group">
                          <select class="form-control selectpicker" data-style="btn btn-link" id="sctFiltroAnio"
                                  multiple data-selected-text-format="count" title="Año">
                            <option value="2021">2021</option>
                            <option value="2022">2022</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-lg">
                        <div class="form-group">
                          <select class="form-control selectpicker" data-style="btn btn-link" id="sctFiltroPeriodo" multiple data-selected-text-format="count" title="Periodo" data-width="70%">
                            <option value="1">Enero-Abril</option>
                            <option value="2">Mayo-Junio</option>
                            <option value="3">Septiembre-Diciembre</option>
                          </select>
                        </div>
                      </div>
                      <div class="col-lg">
                        <div class="form-group">
                          <select class="form-control selectpicker" data-style="btn btn-link" id="sctFiltroCarrera" multiple data-selected-text-format="count" title="Carrera" data-width="50%">
                            <option value="1">Ingeniería en Tecnologías de Manufactura</option>
                            <option value="2">Ingeniería en Tecnologías de la Información</option>
                          </select>
                        </div>
                      </div>
                    </div>
                  </form>
                </div>
                <div class="row table-responsive">
                  <table class="table table-hover" id="tblContratosPorFinalizar">
                    <thead class="text-primary">
                    <tr>
                      <th>Id</th>
                      <th>Profesor</th>
                      <th>Tipo</th>
                      <th>Carrera de Adscripción</th>
                      <th>Periodo</th>
                      <th class="text-center">Acciones</th>
                    </tr>
                    </thead>
                    <tbody id="tbContratoPorFinalizar">
                    <tr>
                      <td>1</td>
                      <td>Said Polanco Martagón</td>
                      <td>PTC</td>
                      <td>Ingeniería en Tecnologías de la Información</td>
                      <td>Enero - Abril 2021</td>
                      <td>
                        <a href="" class="text-dark" data-toggle="modal" data-target="#mdlVerEstadoCargaFinalizada">
                          <i class="material-icons">visibility</i>
                        </a>
                        <a href="" class="text-dark">
                          <i class="material-icons">print</i>
                        </a>
                      </td>
                    </tr>
                    <tr>
                      <td>2</td>
                      <td>María Raquel Ortiz Alvarez</td>
                      <td>PA</td>
                      <td>Ingeniería en Tecnologías de la Información</td>
                      <td>Enero - Abril 2021</td>
                      <td>
                        <a href="" class="text-dark" data-toggle="modal" data-target="#mdlVerEstadoCargaFinalizada">
                          <i class="material-icons">visibility</i>
                        </a>
                        <a href="" class="text-dark">
                          <i class="material-icons">print</i>
                        </a>
                      </td>
                    </tr>
                    </tbody>
                  </table>
                </div>
              </div> <!-- end card-body -->
            </div> <!-- end card-->
          </div> <!-- end col -->
        </div> <!-- End row -->
      </div> <!-- end container-fluid-->
    </div> <!-- end content -->
    <!-- footer -->
    <?php include_once("../components/footer.php"); ?>
    <!-- end footer -->
  </div>
</div>

<?php
include_once("../components/core_js.php");
?>
<!-- Script específico para cada página -->
<!-- <script src="../../static/js/productos-academicos.js"></script> -->
<!-- <script src="../../static/js/rrhh/lista_profesores.js"></script> -->
<script>
  $(document).ready(function () {
    $().ready(function () {
      $sidebar = $('.sidebar');

      $sidebar_img_container = $sidebar.find('.sidebar-background');

      $full_page = $('.full-page');

      $sidebar_responsive = $('body > .navbar-collapse');

      window_width = $(window).width();
    });
    // initd();
  });
</script>
</body>

</html>
