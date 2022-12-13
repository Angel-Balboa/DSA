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
<html lang="en">

<head>
    <?php
    include_once ("../components/metas.php");
    include_once ("../components/links.php");
    ?>
    <title>
        Vista Cargas Academicas
    </title>

    <style>

        table#tblHorarioQuickView tbody tr {
            width: 1.2em;
        }

        .topics tr {
            line-height: 0.2em;
        }

        tr.separator > td {
            height: 0.4em;
        }
        tr.non_separator > td {
            height: 1em;
        }

        .ocupado {
            background-color: #551713;
        }
    </style>
</head>

<body class="">
  <div class="wrapper ">
      <?php include_once("../components/sidebar.php"); ?>

      <div class="main-panel">
        <?php include_once("../components/navbar.php"); ?>
        <!-- End Navbar -->
      <div class="content">
        <div class="container-fluid">

          <div class="row">
            <div class="col-lg-12 col-md-12">
              <div class="card">
                <div class="card-header card-header-primary">
                  <h4 class="card-title">Vista Cargas Académicas</h4>
                  <!--
                  <div class="row">
                    <div class="col-md-6">
                      <h4 class="card-title">Ingeniería en Tecnologías de la Información</h4>
                    </div>
                    <div class="col-md-3">
                      ITI-2018
                    </div>
                    <div class="col-md-3">
                      Nivel: Ingeniería
                    </div>
                  </div>
                -->
                </div> <!-- end card-header -->
              </div> <!-- end card-->
            </div> <!-- end col -->
          </div> <!-- End row -->
        </div> <!-- end container-fluid-->
      </div> <!-- end content -->

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
  <?php include_once("../components/core_js.php"); ?>

  <!-- Script para obtener las materias -->
  <script src="../../static/js/director/vistacargasacademicas.js"></script>
  <script>
    $(document).ready(function() {
      $().ready(function() {
        $sidebar = $('.sidebar');

        $sidebar_img_container = $sidebar.find('.sidebar-background');

        $full_page = $('.full-page');

        $sidebar_responsive = $('body > .navbar-collapse');

        window_width = $(window).width();
      });
        sss("id_carrera", <?php echo $carrera->get_data("id"); ?>, false);
        initd();
    });
  </script>
</body>

</html>