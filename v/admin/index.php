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
            Index
        </title>
        <link rel="stylesheet" href="../../static/css/productos-academicos.css">
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
                    <?php
                    echo $sesion->nombre_usuario["nombre"] . " " . $sesion->nombre_usuario["apellidos"];
                    ?>
                </div>
            </div>

            <!-- footer -->
            <?php include_once ("../components/footer.php"); ?>
            <!-- end footer -->
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
