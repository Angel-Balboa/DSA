<?php

include_once ("../../init.php");

use dsa\api\controller\sesion\CSesion;
use dsa\api\model\usuario\Usuario;
use dsa\api\model\profesor\Profesor;

$sesion = CSesion::inits();

$tipo = $sesion->tipo_usuario;

$usuario = Usuario::get_usuario_by_id($sesion->id_usuario);
$profesor = Profesor::get_profesor_by_usuario($usuario);
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <?php
    include_once ("../components/metas.php");
    include_once ("../components/links.php");
    ?>
<!--    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" onclick="disponibilidad(this, 'transparent');" />-->
    <title>Perfil del Profesor</title>
  </head>

  <body>
    <div class="wrapper">
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
              <div class="col-sm-12">
                <div class="card">

                  <!-- CARD HEADER -->
                  <div class="card-header card-header-primary">
                    <h4 class="card-title">Disponibilidad</h4>
                  </div>

                  <!-- CARD BODY -->
                  <div class="card-body">
                    <div class="container">
                      <div class="text-center">
                        <div class="row mt-2">
                          <div class="col-md-2" >
                            <span class="text-primary">Hora/Día</span>
                          </div>
                          <div class="col-md-2">
                            <span class="text-primary">Lunes</span>
                          </div>
                          <div class="col-md-2">
                            <span class="text-primary">Martes</span>
                          </div>
                          <div class="col-md-2">
                            <span class="text-primary">Miercoles</span>
                          </div>
                          <div class="col-md-2">
                            <span class="text-primary">Jueves</span>
                          </div>
                          <div class="col-md-2">
                            <span class="text-primary">Viernes</span>
                          </div>
                        </div>

                        <!-- BLOQUE 1 (07:00 - 10:40) -->

                        <div class="row mt-2">
                          <div class="col-md-2" >
                            <span class="text-primary mt-0">07:00 - 07:54</span>
                          </div>
                          <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="0" data-hour="0">&#32</button>
                          </div>
                          <div class="col-md-2 ">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="1" data-hour="0">&#32</button>
                          </div>
                          <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="2" data-hour="0">&#32</button>
                          </div>
                          <div class="col-md-2 ">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="3" data-hour="0">&#32</button>
                          </div>
                          <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="4" data-hour="0">&#32</button>
                          </div>
                        </div>
                        <div class="row mt-2">
                          <div class="col-md-2" >
                            <span class="text-primary mt-0">07:55 - 08:49</span>
                          </div>
                          <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="0" data-hour="1" >&#32</button>
                          </div>
                          <div class="col-md-2 ">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="1" data-hour="1" >&#32</button>
                          </div>
                          <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="2" data-hour="1" >&#32</button>
                          </div>
                          <div class="col-md-2 ">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="3" data-hour="1" >&#32</button>
                          </div>
                          <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="4" data-hour="1" >&#32</button>
                          </div>
                        </div>

                        <div class="row mt-2">
                          <div class="col-md-2" >
                            <span class="text-primary mt-0">08:50 - 09:44</span>
                          </div>
                          <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="0" data-hour="2" >&#32</button>
                          </div>
                          <div class="col-md-2 ">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="1" data-hour="2" >&#32</button>
                          </div>
                          <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="2" data-hour="2" >&#32</button>
                          </div>
                          <div class="col-md-2 ">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="3" data-hour="2" >&#32</button>
                          </div>
                          <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="4" data-hour="2" >&#32</button>
                          </div>
                        </div>

                        <div class="row mt-2">
                          <div class="col-md-2" >
                            <span class="text-primary mt-0">09:45 - 10:39</span>
                          </div>
                          <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="0" data-hour="3" >&#32</button>
                          </div>
                          <div class="col-md-2 ">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="1" data-hour="3" >&#32</button>
                          </div>
                          <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="2" data-hour="3" >&#32</button>
                          </div>
                          <div class="col-md-2 ">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="3" data-hour="3" >&#32</button>
                          </div>
                          <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="4" data-hour="3" >&#32</button>
                          </div>
                        </div>

                        <!-- BLOQUE 2 (11:10 - 13:54) -->

                        <div class="row mt-5" >
                          <div class="col-md-2" >
                            <span class="text-primary mt-0">11:10 - 12:04</span>
                          </div>
                          <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="0" data-hour="4" >&#32</button>
                          </div>
                          <div class="col-md-2 ">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="1" data-hour="4" >&#32</button>
                          </div>
                          <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="2" data-hour="4" >&#32</button>
                          </div>
                          <div class="col-md-2 ">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="3" data-hour="4" >&#32</button>
                          </div>
                          <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="4" data-hour="4" >&#32</button>
                          </div>
                        </div>

                        <div class="row mt-2">
                          <div class="col-md-2" >
                            <span class="text-primary mt-0">12:05 - 12:59</span>
                          </div>
                          <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="0" data-hour="5" >&#32</button>
                          </div>
                          <div class="col-md-2 ">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="1" data-hour="5" >&#32</button>
                          </div>
                          <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="2" data-hour="5" >&#32</button>
                          </div>
                          <div class="col-md-2 ">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="3" data-hour="5" >&#32</button>
                          </div>
                          <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="4" data-hour="5" >&#32</button>
                          </div>
                        </div>

                        <div class="row mt-2">
                          <div class="col-md-2" >
                            <span class="text-primary mt-0">13:00 - 13:54</span>
                          </div>
                          <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="0" data-hour="6" >&#32</button>
                          </div>
                          <div class="col-md-2 ">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="1" data-hour="6" >&#32</button>
                          </div>
                          <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="2" data-hour="6" >&#32</button>
                          </div>
                          <div class="col-md-2 ">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="3" data-hour="6" >&#32</button>
                          </div>
                          <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="4" data-hour="6" >&#32</button>
                          </div>
                        </div>
                    
                        <!-- BLOQUE 3 (14:00 - 17:39) -->

                        <div class="row mt-5">
                          <div class="col-md-2" >
                            <span class="text-primary mt-0">14:00 - 14:54</span>
                          </div>
                          <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="0" data-hour="7" >&#32</button>
                          </div>
                          <div class="col-md-2 ">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="1" data-hour="7" >&#32</button>
                          </div>
                          <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="2" data-hour="7" >&#32</button>
                          </div>
                          <div class="col-md-2 ">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="3" data-hour="7" >&#32</button>
                          </div>
                          <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="4" data-hour="7" >&#32</button>
                          </div>
                        </div>
                        
                        <div class="row mt-2">
                          <div class="col-md-2" >
                            <span class="text-primary mt-0">14:55 - 15:49</span>
                          </div>
                          <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="0" data-hour="8" >&#32</button>
                          </div>
                          <div class="col-md-2 ">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="1" data-hour="8" >&#32</button>
                          </div>
                          <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="2" data-hour="8" >&#32</button>
                          </div>
                          <div class="col-md-2 ">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="3" data-hour="8" >&#32</button>
                          </div>
                          <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="4" data-hour="8" >&#32</button>
                          </div>
                        </div>

                        <div class="row mt-2">
                          <div class="col-md-2" >
                            <span class="text-primary mt-0">15:50 - 16:44</span>
                          </div>
                          <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="0" data-hour="9" >&#32</button>
                          </div>
                          <div class="col-md-2 ">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="1" data-hour="9" >&#32</button>
                          </div>
                          <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="2" data-hour="9" >&#32</button>
                          </div>
                          <div class="col-md-2 ">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="3" data-hour="9" >&#32</button>
                          </div>
                          <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="4" data-hour="9" >&#32</button>
                          </div>
                        </div>

                        <div class="row mt-2">
                          <div class="col-md-2" >
                            <span class="text-primary mt-0">16:45 - 17:39</span>
                          </div>
                          <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="0" data-hour="10" >&#32</button>
                          </div>
                          <div class="col-md-2 ">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="1" data-hour="10" >&#32</button>
                          </div>
                          <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="2" data-hour="10" >&#32</button>
                          </div>
                          <div class="col-md-2 ">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="3" data-hour="10" >&#32</button>
                          </div>
                          <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="4" data-hour="10" >&#32</button>
                          </div>
                        </div>
                        
                        <!-- BLOQUE 4 (18:00 - 20:44) -->

                        <div class="row mt-5" >
                          <div class="col-md-2" >
                            <span class="text-primary mt-0">18:00 - 18:54</span>
                          </div>
                          <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="0" data-hour="11" >&#32</button>
                          </div>
                          <div class="col-md-2 ">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="1" data-hour="11">&#32</button>
                          </div>
                          <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="2" data-hour="11">&#32</button>
                          </div>
                          <div class="col-md-2 ">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="3" data-hour="11">&#32</button>
                          </div>
                          <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="4" data-hour="11">&#32</button>
                          </div>
                        </div>

                        <div class="row mt-2">
                          <div class="col-md-2" >
                            <span class="text-primary mt-0">18:55 - 19:49</span>
                          </div>
                          <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="0" data-hour="12">&#32</button>
                          </div>
                          <div class="col-md-2 ">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="1" data-hour="12">&#32</button>
                          </div>
                          <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="2" data-hour="12">&#32</button>
                          </div>
                          <div class="col-md-2 ">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="3" data-hour="12">&#32</button>
                          </div>
                          <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="4" data-hour="12">&#32</button>
                          </div>
                        </div>

                        <div class="row mt-2">
                          <div class="col-md-2" >
                            <span class="text-primary mt-0">19:50 - 20:44</span>
                          </div>
                          <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="0" data-hour="13">&#32</button>
                          </div>
                          <div class="col-md-2 ">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="1" data-hour="13">&#32</button>
                          </div>
                          <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="2" data-hour="13">&#32</button>
                          </div>
                          <div class="col-md-2 ">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="3" data-hour="13">&#32</button>
                          </div>
                          <div class="col-md-2">
                            <button type="button" class="btn btn-sm btn-block h-100 btn-outline-primary mt-0 p-0 btn-disponibilidad" data-day="4" data-hour="13">&#32</button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- FOOTER -->
        <?php include_once ("../components/footer.php"); ?>
        <!-- end footer -->
      </div>
    </div>

    <!--   Core JS Files   -->
    <?php include_once ("../components/core_js.php"); ?>
    <script src="../../static/js/profesor/disponibilidad.js"></script>
    <!-- end Core JS Files -->
    <!-- Script para el manejo de los botones -->
    <script>
      $(document).ready(function () {
        $().ready(function () {
          $sidebar = $(".sidebar");

          $sidebar_img_container = $sidebar.find(".sidebar-background");

          $full_page = $(".full-page");

          $sidebar_responsive = $("body > .navbar-collapse");

          window_width = $(window).width();
        });
        initd('<?php echo $profesor->get_data("id"); ?>');
      });
    </script>
  </body>
</html>
