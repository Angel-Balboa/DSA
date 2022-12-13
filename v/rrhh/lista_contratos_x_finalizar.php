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
                                                            multiple data-selected-text-format="count" title="Tipo Contrato">
                                                        <option value="P.A">PTC</option>
                                                        <option value="P.T.C">PA</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg">
                                                <div class="form-group">
                                                    <select class="form-control selectpicker" data-style="btn btn-link" id="sctFiltroCategoria"
                                                            multiple data-selected-text-format="count" title="Categoria">
                                                        <option value="A">A</option>
                                                        <option value="B">B</option>
                                                        <option value="C">C</option>
                                                        <option value="D">D</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-lg">
                                                <div class="form-group">
                                                    <select class="form-control selectpicker" data-style="btn btn-link" id="sctFiltroCarrera"
                                                            multiple data-selected-text-format="count" title="Carrera Adscripción">
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
                                            <th class="text-center">Status</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                        </thead>
                                        <tbody id="tbContratoPorFinalizar">
                                        <tr>
                                          <td>1</td>
                                          <td>Said Polanco Martagón</td>
                                          <td>PTC</td>
                                          <td>Ingeniería en Tecnologías de la Información</td>
                                          <td>
                                            <a href="" class="text-danger">
                                              <i class="material-icons">directions_car</i>
                                            </a>
                                            <a href="" class="text-danger">
                                              <i class="material-icons">computer</i>
                                            </a>
                                            <a href="" class="text-danger">
                                              <i class="material-icons">precision_manufacturing</i>
                                            </a>
                                            <a href="" class="text-danger">
                                              <i class="material-icons">factory</i>
                                            </a>
                                            <a href="" class="text-danger">
                                              <i class="material-icons">supervised_user_circle</i>
                                            </a>
                                          </td>
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

<!-- modal: VerEstadoCargaFinalizada -->
<div aria-hidden="true" aria-labelledby="mdlVerEstadoCargaFinalizadaLabel" class="modal fade" id="mdlVerEstadoCargaFinalizada" role="dialog"
     tabindex="-1">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Carga Académica del Profesor</h5>
        <button aria-label="Close" class="close" data-dismiss="modal" type="button">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-6 col-sm-6 text-center font-weight-bold">
            Dr. Said Polanco Martagón
          </div>
          <div class="col-md-6 col-sm-6 text-center font-weight-bold">
            Ingeniería en Tecnologías de la Información
          </div>
        </div>
        <hr class="text-dark">
        <div class="row">
          <div class="col-md-2 col-sm-2">
            <span class="material-icons text-success">thumb_up_alt</span>
          </div>
          <div class="col-md-10 col-sm-10">
            Ingeniería en Tecnologías de la Información
          </div>
        </div>
        <hr>
        <div class="row table-responsive">
          <table class="table table-hover">
            <tbody>
            <tr>
              <td>ITI 1-3</td>
              <td>Algebra 1</td>
              <td>5 horas</td>
              <td>del <span id="fechaInicio">06/01/2022</span> al <span id="fechaCierre">21/04/2022</span></td>
            </tr>
            <tr>
              <td>ITI 7-1</td>
              <td>Programación Web</td>
              <td>6 horas</td>
              <td>del <span id="fechaInicio">06/01/2022</span> al <span id="fechaCierre">13/03/2022</span></td>
            </tr>
            </tbody>
          </table>
        </div>
        <hr class="text-dark">
        <div class="row">
          <div class="col-md-2 col-sm-2">
            <span class="material-icons text-danger">thumb_down_alt</span>
          </div>
          <div class="col-md-10 col-sm-10">
            Ingeniería en Tecnologías de Manufactura
          </div>
        </div>
        <hr>
        <div class="row table-responsive">
          <table class="table table-hover">
            <tbody>
            <tr>
              <td>ITM 3-2</td>
              <td>Algebra 1</td>
              <td>5 horas</td>
              <td>del <span id="fechaInicio">06/01/2022</span> al <span id="fechaCierre">21/04/2022</span></td>
            </tr>
            </tbody>
          </table>
        </div>
        <hr class="text-dark">
        <div class="row">
          <div class="col-md-2 col-sm-2">
            <span class="material-icons text-danger">thumb_down_alt</span>
          </div>
          <div class="col-md-10 col-sm-10">
            Ingeniería en Sistemas Automotrices
          </div>
        </div>
        <hr>
        <div class="row table-responsive">
          <table class="table table-hover">
            <tbody>
            <tr>
              <td>ISA 3-1</td>
              <td>Algebra 1</td>
              <td>5 horas</td>
              <td>del <span id="fechaInicio">06/01/2022</span> al <span id="fechaCierre">21/04/2022</span></td>
            </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
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
