<?php
include_once("../../init.php");

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
  include_once("../components/metas.php");
  include_once("../components/links.php");
  ?>
  <title>
    Planeaciones académicas
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
          <div class="col-lg-4 col-md-4">
            <div class="card">
              <div class="card-header card-header-primary">
                <h4 class="card-title">Planeaciones Académicas</h4>
              </div>
              <div class="card-body" id="aniosPeriodos">
              </div><!-- end card-body -->
            </div>
          </div> <!-- end col-4 -->
          <div class="col-lg-8 col-md-8" id="crdPlaneacionesPTC">
            <div class="card">
              <div class="card-header card-header-info">
                <h4 class="card-title">Planeaciones de PTC / <span id="chPeriodo"></span> <span id="chAnio"></span></h4>
              </div>
              <div class="card-body table-responsive" id="cbPlaneacionesPTCs">
                <table class="table table-hover">
                  <thead class="text-primary">
                  <tr>
                    <th>Nombre</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                    <th>Aceptar</th>
                  </tr>
                  </thead>
                  <tbody id="tbPlaneacionesPTCs">

                  <tr>
                    <td>Dr. Hiram Herrera Rivas</td>
                    <th><span class="material-icons text-danger">thumb_down</span></th>
                    <th>
                      <!-- <a href=""><i class="material-icons">print</i></a> -->
                      <a href=""><i class="material-icons">visibility</i></a>
                      <!-- <a href="" title="Regresar"><i class="material-icons">reply_all</i></a> -->
                    </th>
                    <th>
                      <!-- <a href="" class="text-success" title="Aceptar"><i class="material-icons">done_all</i> </a> -->
                    </th>
                  </tr>
                  </tbody>
                </table>
              </div>
              <div class="card-footer" id="cfSolicitarPlaneaciones">
                <div class="row">
                  <div class="col-md-2">
                    <button type="button" class="btn btn-warning" id="btnSolicitarPlaneaciones">Solicitar Planeaciones</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div> <!-- end container-fluid-->
    </div> <!-- end content -->

    <?php include_once("../components/footer.php"); ?>
  </div>
</div>

<!--   Core JS Files   -->
<?php include_once("../components/core_js.php"); ?>
<script src="../../static/js/director/planeaciones_academicas.js"></script>
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
  });
  sss("id_carrera", <?php echo $carrera->get_data("id"); ?>, false);

  initd();

</script>
</body>

</html>
