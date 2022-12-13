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
        Admin Listado de Carreras
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
                    <div class="col-lg-12 col-md-12">
                        <div class="card">
                            <div class="card-header card-header-primary">
                                <h4 class="card-title">Lista de Carreras</h4>
                            </div> <!-- end card-header -->
                            <div class="card-body table-responsive">
                                <table class="table table-hover" id="lstCarreras">
                                    <thead class="text-primary">
                                    <tr>
                                        <th>Clave</th>
                                        <th>Nivel</th>
                                        <th>Nombre</th>
                                        <th>Director</th>
                                        <th>Acciones</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>

                                <!-- modal: AgregarNuevaCarrera-->
                                <div aria-hidden="true" aria-labelledby="AgregarNuevaCarreraLabel" class="modal fade" id="AgregarNuevaCarrera"
                                     role="dialog" tabindex="-1">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="AgregarNuevaCarreraLabel">Agregar Nueva
                                                    Carrera</h5>
                                                <button aria-label="Close" class="close" data-dismiss="modal"
                                                        type="button">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <form action="#" id="frmAgregarCarrera" method="post">
                                                    <div class="form-group row">
                                                        <label class="col-sm-3 col-form-label"
                                                               for="txtAgregarClaveCarrera">Clave</label>
                                                        <div class="col-sm-9">
                                                            <input class="form-control" id="txtAgregarClaveCarrera"
                                                                   maxlength="10" minlength="3" name="txtAgregarClaveCarrera"
                                                                   required
                                                                   style="text-transform: uppercase" type="text"/>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-sm-3 col-form-label"
                                                               for="txtAgregarNombreCarrera">Nombre</label>
                                                        <div class="col-sm-9">
                                                            <input class="form-control" id="txtAgregarNombreCarrera"
                                                                   maxlength="250"
                                                                   minlength="10" name="txtAgregarNombreCarrera"
                                                                   required type="text"/>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-sm-3 col-form-label"
                                                               for="sctAgregarNivelCarrera">Nivel</label>
                                                        <div class="col-sm-9">
                                                            <select class="form-control" id="sctAgregarNivelCarrera"
                                                                    name="sctAgregarNivelCarrera" required>
                                                                <option selected value="Ing">Ingeniería</option>
                                                                <option value="Lic">Licenciatura</option>
                                                                <option value="M.I.">Maestría en Ingeniería</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <label class="col-sm-3 col-form-label"
                                                               for="sctAgregarDirectorCarrera">Director</label>
                                                        <div class="col-sm-9">
                                                            <select class="form-control" id="sctAgregarDirectorCarrera"
                                                                    name="sctAgregarDirectorCarrera">
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="form-group row">
                                                        <input class="btn btn-primary" id="btnGuardarNuevaCarrera"
                                                               type="submit"/>
                                                        <button class="btn btn-secondary" data-dismiss="modal"
                                                                type="button">Cancelar
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- end card-body -->
                            <div class="card-footer">
                                <button class="btn btn-primary" id="btnAgregarCarrera" type="button">Agregar Carrera
                                </button>
                            </div>
                        </div> <!-- end card-->
                    </div> <!-- end col -->
                </div> <!-- End row -->
            </div> <!-- end container-fluid-->
        </div> <!-- end content -->
      <!-- footer -->
      <?php include_once ("../components/footer.php"); ?>
      <!-- end footer -->
    </div>
</div>

<!-- modal: EditarCarrera-->
<div class="modal fade" id="EditarCarrera" tabindex="-1" role="dialog" aria-labelledby="EditarCarreraLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="EditarCarreraLabel">Actualizar Carrera</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="frmEditarCarrera" action="" method="post">
          <input type="hidden" id="hdnEditClaveCarrera" name="clv_carrera" value="-1">
          <div class="form-group row">
            <label for="txtEditNombreCarrera" class="col-sm-3 col-form-label">Nombre</label>
            <div class="col-sm-9">
              <input type="text" class="form-control" id="txtEditNombreCarrera" name="nuevo_nombre" required />
            </div>
          </div>
          <div class="form-group row">
            <label for="sctEditNivelCarrera" class="col-sm-3 col-form-label">Nivel</label>
            <div class="col-sm-9">
              <select class="form-control" id="sctEditNivelCarrera" name="nuevo_nivel" required>
                <option value="Ing">Ingeniería</option>
                <option value="Lic">Licenciatura</option>
                <option value="M.I.">Maestría en Ingeniería</option>
              </select>
            </div>
          </div>
          <div class="form-group row">
            <label for="sctEditDirectorCarrera" class="col-sm-3 col-form-label">Director</label>
            <div class="col-sm-9">
              <select class="form-control" id="sctEditDirectorCarrera" name="nuevo_director">
              </select>
            </div>
          </div>
          <div class="form-group row">
            <input type="submit" class="btn btn-primary" data-toggle="modal" value="Actualizar" />
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<!--   Core JS Files   -->
<?php include_once ("../components/core_js.php"); ?>
<!-- End Core JS Files -->
<script src="../../static/js/admin/lista_carreras_funcs.js"></script>
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