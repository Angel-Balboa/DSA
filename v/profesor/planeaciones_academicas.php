<?php
include_once ("../../init.php");

use dsa\api\controller\sesion\CSesion;
use dsa\api\model\usuario\Usuario;
use dsa\api\model\profesor\Profesor;

$sesion = CSesion::inits();
$usuario = Usuario::get_usuario_by_id($sesion->id_usuario);
$profesor = Profesor::get_profesor_by_usuario($usuario);

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

<body>
<div class="wrapper ">
  <!-- sidebar -->
  <?php
  include_once("../components/sidebar.php");
  ?>
  <!-- end sidebar -->
  <div class="main-panel">
    <!-- Navbar -->
    <?php
    include_once("../components/navbar.php")
    ?>
    <!-- End Navbar -->
    <div class="content">
      <div class="container-fluid">
        <div class="row">
          <div class=" col-lg-10 col-md-10">
            <div class="card">
              <div class="card-header card-header-primary">
                <h4 class="card-title">Planeaciones académicas</h4>
              </div><!--end card-header-->
              <div class="card-body">
                <div id="accordion" role="tablist">

                </div><!--end accordion -->
              </div><!--end card-body -->
            </div><!-- end card -->
          </div><!--End col-10 -->
        </div><!--end row-->
      </div><!--end container-fluid-->
    </div><!--End content -->

    <?php
    include_once("../components/footer.php")
    ?>
  </div>
</div>

<!--   Core JS Files   -->
<?php
include_once("../components/core_js.php");
?>
<!-- end Core JS Files -->
<script src="../../static/js/profesor/planeaciones_academicas.js"></script>
<script>
  $(document).ready(function () {
    $().ready(function () {
      $sidebar = $('.sidebar');
      $sidebar_img_container = $sidebar.find('.sidebar-background');
      $full_page = $('.full-page');
      $sidebar_responsive = $('body > .navbar-collapse');
      window_width = $(window).width();
    });
      sss("id_profesor", <?php echo $profesor->get_data("id"); ?>, false);
    initd(<?php echo $profesor->get_data("id") ?>);
  });
</script>
</body>

</html>