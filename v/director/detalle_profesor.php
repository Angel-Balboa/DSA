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
    <link rel="stylesheet" href="../../static/css/productos-academicos.css">
    <title>
        Admin Detalles de Usuario
    </title>
  <style>

    table#tblHorarioQuickView tbody tr td{
      width: 6em;
    }

    table#tblHorarioQuickView thead tr th{
      text-align: center;
    }

    .topics tr {
      line-height: 0.2em;
    }

    tr.separator > td {
      height: 0.4em;
    }
    tr.non_separator > td {
      height: 1.6em;
    }

    .disponible {
      background-color: #00bfa5;
    }

    .ocupado {
      background-color: #551713;
    }
  </style>

</head>

<body class="">
<div class="wrapper ">
    <?php include_once ("../components/sidebar.php"); ?>
    <div class="main-panel">
        <!-- Navbar -->
        <?php include_once ("../components/navbar.php"); ?>
        <!-- End Navbar -->
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-lg-12 col-md-12">
                        <div class="card">
                            <div class="card-header card-header-primary">
                                <h4 class="card-title"><span id="nombre_completo_profesor"></span></h4>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-lg-4 col-md-4">Email: <span id="email_profesor"></span></div>
                                    <div class="col-lg-4 col-md-4">Teléfono: <span id="telefono_profesor"></span></div>
                                    <div class="col-lg-4 col-md-4">Extensión: <span id="ext_profesor"></span></div>
                                </div><!-- end row -->
                                <div class="row">
                                  <div class="col-lg-4 col-md-4"><span id="tipo_contrato_profesor"></span></div>
                                    <div class="col-lg-4 col-md-4">Inicio de contrato: <span id="inicio_contrato_profesor"></span></div>
                                    <div class="col-lg-4 col-md-4">Fin de contrato: <span id="fin_contrato_profesor"></span></div>
                                </div>
                            </div>
                        </div><!-- end card -->
                    </div><!-- end col-12 -->
                </div><!-- end row -->
                <div class="row" id="rowProductosCientificos">
                    <div class="col-lg-12 col-md-12">
                        <div class="card">
                            <div class="card-header card-header-tabs card-header-primary">
                                <div class="nav-tabs-navigation">
                                    <div class="nav-tabs-wrapper">
                                        <span class="nav-tabs-title"></span>
                                        <ul class="nav nav-tabs" data-tabs="tabs">
                                            <li class="nav-item">
                                                <a class="nav-link active" href="#todas" data-toggle="tab">
                                                    <i class="material-icons">bug_report</i>Todas
                                                    <div class="ripple-container"></div>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="#journals" data-toggle="tab">
                                                    <i class="material-icons">code</i>Journals
                                                    <div class="ripple-container"></div>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="#indexados" data-toggle="tab">
                                                    <i class="material-icons">task</i>Indexados
                                                    <div class="ripple-container"></div>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="#congresos" data-toggle="tab">
                                                    <i class="material-icons">cloud</i>Congresos
                                                    <div class="ripple-container"></div>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div> <!-- end card-header-->

                            <div class="card-body">
                                <div class="tab-content">
                                    <div class="tab-pane active" id="todas">
                                    </div>

                                    <div class="tab-pane" id="journals">
                                    </div>

                                    <div class="tab-pane" id="indexados">
                                        <div>
                                            <h6 class="text-warning">No tienes ningún producto en esta sección</h6>
                                        </div>
                                    </div>

                                    <div class="tab-pane" id="congresos">
                                        <div>
                                            <h6 class="text-danger">No tienes ningún producto en esta sección</h6>
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- end card body-->
                        </div>
                    </div>
                </div>
                <div id="perfil_profesor" class="row">
                    <div class="col-lg-6 col-md-6">
                        <div class="card">
                            <div class="card-header card-header-primary">
                                <h4 class="card-title">Profesor / Carreras donde imparte Materias</h4>
                            </div> <!-- end card-header -->
                            <div class="card-body table-responsive">
                                <table class="table table-hover">
                                    <tbody id="tblProfesorImparteEn">
                                    </tbody>
                                </table>
                            </div> <!-- end card-body -->
                        </div> <!-- end card-->
                    </div> <!-- end col -->
                    <div class="col-lg-6 col-md-6">
                        <div class="card">
                            <div class="card-header card-header-primary">
                                <h4 class="card-title">Disponibilidad / Carga Académica</h4>
                            </div><!-- end card-header -->
                            <div class="card-body">
                              <div class="row">
                                <div class="d-flex justify-content-center table-responsive">
                                  <table id="tblHorarioQuickView" class="table-bordered">
                                    <thead>
                                    <tr class="small">
                                      <th>hora</th>
                                      <th>Lunes</th>
                                      <th>Martes</th>
                                      <th>Miércoles</th>
                                      <th>Jueves</th>
                                      <th>Viernes</th>
                                    </tr>
                                    </thead>
                                    <tbody id="view_disponibilidad">
                                    <tr class="non_separator">
                                      <td style="font-size: .75em; padding: 0; margin: 0; text-align: center">07:00-07:55</td>
                                      <td class="qv-disp" data-day="0" data-hour="0"></td>
                                      <td class="qv-disp" data-day="1" data-hour="0"></td>
                                      <td class="qv-disp" data-day="2" data-hour="0"></td>
                                      <td class="qv-disp" data-day="3" data-hour="0"></td>
                                      <td class="qv-disp" data-day="4" data-hour="0"></td>
                                    </tr>
                                    <tr class="non_separator">
                                      <td style="font-size: .75em; padding: 0; margin: 0; text-align: center">07:55-08:50</td>
                                      <td class="qv-disp" data-day="0" data-hour="1"></td>
                                      <td class="qv-disp" data-day="1" data-hour="1"></td>
                                      <td class="qv-disp" data-day="2" data-hour="1"></td>
                                      <td class="qv-disp" data-day="3" data-hour="1"></td>
                                      <td class="qv-disp" data-day="4" data-hour="1"></td>
                                    </tr>
                                    <tr class="non_separator">
                                      <td style="font-size: .75em; padding: 0; margin: 0; text-align: center">08:50-09:45</td>
                                      <td class="qv-disp" data-day="0" data-hour="2"></td>
                                      <td class="qv-disp" data-day="1" data-hour="2"></td>
                                      <td class="qv-disp" data-day="2" data-hour="2"></td>
                                      <td class="qv-disp" data-day="3" data-hour="2"></td>
                                      <td class="qv-disp" data-day="4" data-hour="2"></td>
                                    </tr>
                                    <tr class="non_separator">
                                      <td style="font-size: .75em; padding: 0; margin: 0; text-align: center">09:45-10:40</td>
                                      <td class="qv-disp" data-day="0" data-hour="3"></td>
                                      <td class="qv-disp" data-day="1" data-hour="3"></td>
                                      <td class="qv-disp" data-day="2" data-hour="3"></td>
                                      <td class="qv-disp" data-day="3" data-hour="3"></td>
                                      <td class="qv-disp" data-day="4" data-hour="3"></td>
                                    </tr>
                                    <tr class="separator">
                                      <td></td>
                                      <td></td>
                                      <td></td>
                                      <td></td>
                                      <td></td>
                                      <td></td>
                                    </tr>
                                    <tr class="non_separator">
                                      <td style="font-size: .75em; padding: 0; margin: 0; text-align: center">11:10-12:05</td>
                                      <td class="qv-disp" data-day="0" data-hour="4"></td>
                                      <td class="qv-disp" data-day="1" data-hour="4"></td>
                                      <td class="qv-disp" data-day="2" data-hour="4"></td>
                                      <td class="qv-disp" data-day="3" data-hour="4"></td>
                                      <td class="qv-disp" data-day="4" data-hour="4"></td>
                                    </tr>
                                    <tr class="non_separator">
                                      <td style="font-size: .75em; padding: 0; margin: 0; text-align: center">12:05-13:00</td>
                                      <td class="qv-disp" data-day="0" data-hour="5"></td>
                                      <td class="qv-disp" data-day="1" data-hour="5"></td>
                                      <td class="qv-disp" data-day="2" data-hour="5"></td>
                                      <td class="qv-disp" data-day="3" data-hour="5"></td>
                                      <td class="qv-disp" data-day="4" data-hour="5"></td>
                                    </tr>
                                    <tr class="non_separator">
                                      <td style="font-size: .75em; padding: 0; margin: 0; text-align: center">13:00-13:55</td>
                                      <td class="qv-disp" data-day="0" data-hour="6"></td>
                                      <td class="qv-disp" data-day="1" data-hour="6"></td>
                                      <td class="qv-disp" data-day="2" data-hour="6"></td>
                                      <td class="qv-disp" data-day="3" data-hour="6"></td>
                                      <td class="qv-disp" data-day="4" data-hour="6"></td>
                                    </tr>
                                    <tr class="separator">
                                      <td></td>
                                      <td></td>
                                      <td></td>
                                      <td></td>
                                      <td></td>
                                      <td></td>
                                    </tr>
                                    <tr class="non_separator">
                                      <td style="font-size: .75em; padding: 0; margin: 0; text-align: center">14:00-14:55</td>
                                      <td class="qv-disp" data-day="0" data-hour="7"></td>
                                      <td class="qv-disp" data-day="1" data-hour="7"></td>
                                      <td class="qv-disp" data-day="2" data-hour="7"></td>
                                      <td class="qv-disp" data-day="3" data-hour="7"></td>
                                      <td class="qv-disp" data-day="4" data-hour="7"></td>
                                    </tr>
                                    <tr class="non_separator">
                                      <td style="font-size: .75em; padding: 0; margin: 0; text-align: center">14:55-15:50</td>
                                      <td class="qv-disp" data-day="0" data-hour="8"></td>
                                      <td class="qv-disp" data-day="1" data-hour="8"></td>
                                      <td class="qv-disp" data-day="2" data-hour="8"></td>
                                      <td class="qv-disp" data-day="3" data-hour="8"></td>
                                      <td class="qv-disp" data-day="4" data-hour="8"></td>
                                    </tr>
                                    <tr class="non_separator">
                                      <td style="font-size: .75em; padding: 0; margin: 0; text-align: center">15:50-16:45</td>
                                      <td class="qv-disp" data-day="0" data-hour="9"></td>
                                      <td class="qv-disp" data-day="1" data-hour="9"></td>
                                      <td class="qv-disp" data-day="2" data-hour="9"></td>
                                      <td class="qv-disp" data-day="3" data-hour="9"></td>
                                      <td class="qv-disp" data-day="4" data-hour="9"></td>
                                    </tr>
                                    <tr class="non_separator">
                                      <td style="font-size: .75em; padding: 0; margin: 0; text-align: center">16:45-17:40</td>
                                      <td class="qv-disp" data-day="0" data-hour="10"></td>
                                      <td class="qv-disp" data-day="1" data-hour="10"></td>
                                      <td class="qv-disp" data-day="2" data-hour="10"></td>
                                      <td class="qv-disp" data-day="3" data-hour="10"></td>
                                      <td class="qv-disp" data-day="4" data-hour="10"></td>
                                    </tr>
                                    <tr class="separator">
                                      <td></td>
                                      <td></td>
                                      <td></td>
                                      <td></td>
                                      <td></td>
                                      <td></td>
                                    </tr>
                                    <tr class="non_separator">
                                      <td style="font-size: .75em; padding: 0; margin: 0; text-align: center">18:00-18:55</td>
                                      <td class="qv-disp" data-day="0" data-hour="11"></td>
                                      <td class="qv-disp" data-day="1" data-hour="11"></td>
                                      <td class="qv-disp" data-day="2" data-hour="11"></td>
                                      <td class="qv-disp" data-day="3" data-hour="11"></td>
                                      <td class="qv-disp" data-day="4" data-hour="11"></td>
                                    </tr>
                                    <tr class="non_separator">
                                      <td style="font-size: .75em; padding: 0; margin: 0; text-align: center">18:55-19:50</td>
                                      <td class="qv-disp" data-day="0" data-hour="12"></td>
                                      <td class="qv-disp" data-day="1" data-hour="12"></td>
                                      <td class="qv-disp" data-day="2" data-hour="12"></td>
                                      <td class="qv-disp" data-day="3" data-hour="12"></td>
                                      <td class="qv-disp" data-day="4" data-hour="12"></td>
                                    </tr>
                                    <tr class="non_separator">
                                      <td style="font-size: .75em; padding: 0; margin: 0; text-align: center">19:50-20:45</td>
                                      <td class="qv-disp" data-day="0" data-hour="13"></td>
                                      <td class="qv-disp" data-day="1" data-hour="13"></td>
                                      <td class="qv-disp" data-day="2" data-hour="13"></td>
                                      <td class="qv-disp" data-day="3" data-hour="13"></td>
                                      <td class="qv-disp" data-day="4" data-hour="13"></td>
                                    </tr>
                                    </tbody>
                                  </table>
                                </div>
                              </div>
                            </div>
                        </div>
                    </div>
                </div> <!-- End row -->
            </div> <!-- end container-fluid-->
        </div> <!-- end content -->

        <?php include_once ("../components/footer.php"); ?>
    </div>
</div>
<!--   Core JS Files   -->
<?php
include_once ("../components/core_js.php");
?>
<script src="../../static/assets/js/plugins/citation-0.4.0-9.min.js" type="text/javascript"></script>
<script src="../../static/js/director/detalle_profesor.js"></script>
<script>
    $(document).ready(function () {
        $().ready(function () {
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
