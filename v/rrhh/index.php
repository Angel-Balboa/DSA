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
    include_once ("../components/metas.php");
    include_once ("../components/links.php");
    ?>
    <title>
        Admin Detalles de Usuario
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

            <div class="col-lg-12 col-mg-12 col-sm-12">
                <div class="card">
                    <div class="card-header card-header-primary">
                        <h4 class="header-title">Contratos por finalizar</h4>
                    </div> <!-- end card-header -->
                    <div class="card-body table-responsive">
                        <table class="table table-hover">
                            <thead class="text-primary">
                                <tr>
                                    <th>ID</th>
                                    <th>Finalizar</th>
                                    <th>Nombre</th>
                                    <th>Adscripción</th>
                                    <th>Status</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="tlbprof">
                            </tbody>
                        </table>
                    </div> <!-- end card-body -->
                </div> <!-- end card-->
            </div> <!-- end col -->
          
          </div> <!-- End row -->
        </div> <!-- end container-fluid-->
      </div> <!-- end content -->

      <!-- Visualizar contrato -->
      <div class="modal fade" id="visualizarContrato" tabindex="-1" role="dialog" aria-labelledby="NewProduct" aria-hidden="true">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="exampleModalLabel">Contrato por finalizar</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <!-- cuerpo -->
              <div class="table-responsive">
                <table class="table">
                    <thead class="text-primary">
                        <tr>
                            <th colspan="3" id="profesorname"></th>
                            <th colspan="2" id="carreraAds"></th>
                        </tr>
                    </thead>
                    <tbody id="tlbmaterias">
                    </tbody>
                </table>
              </div> <!-- end card-body -->
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
            </div>
          </div>
        </div>
      </div>
      <!-- Termina visualizar contrato -->

      <footer class="footer">
        <div class="container-fluid">
          <nav class="float-left">
            <ul>
              <li>
                <a href="https://creative-tim.com/presentation">
                  About Us
                </a>
              </li>
            </ul>
          </nav>
          <div class="copyright float-right">
            &copy;
            <script>
              document.write(new Date().getFullYear())
            </script>, Martagón Systems & Technology
          </div>
        </div>
      </footer>
    </div>
  </div>
  
  <!--   Core JS Files   -->
  <?php
  include_once("../components/core_js.php");
  ?>
  <script src="../../static/js/rrhh/vistamaterias.js"></script>

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
  <script>
    $(document).ready(function() {
      // Javascript method's body can be found in assets/js/demos.js
      md.initDashboardPageCharts();
    });
  </script>
</body>

</html>