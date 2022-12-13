<?php
include_once ("../../init.php");

use dsa\api\controller\sesion\CSesion;

$sesion = CSesion::inits();
$tipo = $sesion->tipo_usuario;
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
                <h4 class="card-title">Lista de Profesores</h4>
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
                  <table class="table table-hover" id="tblProfesores">
                    <thead class="text-primary">
                    <tr>
                      <th>Id</th>
                      <th>Nombre</th>
                      <th>Categoría</th>
                      <th>Carrera</th>
                      <th>Activo</th>
                      <th class="text-center">Acciones</th>
                    </tr>
                    </thead>
                    <tbody id="tbListadoProfesores">
                    </tbody>
                  </table>
                </div>
              </div> <!-- end card-body -->
              <div class="card-footer">
                <button class="btn btn-primary" id="btnAgregarProfesor" type="button">Agregar Profesor</button>
              </div>
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

<div class="modal fade" id="VistaRapidaUsuario" tabindex="-1" role="dialog" aria-labelledby="VistaRapidaUsuarioLabel"
     aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Vista Rápida</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="card">
            <div class="card-header-info">
              <h6 class="card-title">Datos de usuario</h6>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-sm-2">email</div>
                <div class="col-sm" id="qv-email-usuario"></div>
              </div>
              <div class="row">
                <div class="col-sm-2">Nombre</div>
                <div class="col-sm" id="qv-nombre_usuario"></div>
              </div>
              <div class="row">
                <div class="col-sm-3">Teléfono</div>
                <div class="col-sm-4" id="qv-telefono-usuario"></div>
                <div class="col-sm-2">Ext.</div>
                <div class="col-sm-2" id="qv-extension-usuario"></div>
              </div>
              <div class="row">
                <div class="col-sm-3">Tipo</div>
                <div class="col-sm-4" id="qv-tipo-usuario"></div>
                <div class="col-sm-2">Activo</div>
                <div class="col-sm-2" id="qv-activo-usuario"></div>
              </div>
            </div>
          </div>
        </div>

        <div class="row">
          <div class="card" id="crdPerfilProfesor">
            <div class="card-header-warning">
              <h6 class="card-title">Datos de Profesor</h6>
            </div>
            <div class="card-body">
              <div class="row">
                <div class="col-sm-4" id="qv-nivel-adscripcion"></div>
                <div class="col-sm-4" id="qv-tipo-contrato"></div>
                <div class="col-sm-3">Categoria -
                  <div id="qv-categoria-profesor"></div>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-3">Inicio de Contrato</div>
                <div class="col-sm-3" id="qv-inicio-contrato"></div>
                <div class="col-sm-3">Fin de Contrato</div>
                <div class="col-sm-3" id="qv-fin-contrato"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="mdlAgregarProfesor" tabindex="-1" role="dialog" aria-labelledby="mdlAgregarProfesorLabel"
     aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Agregar Nuevo Profesor</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form id="frmNuevoProfesor" action="" method="post">
          <div class="card">
            <div class="card-header-primary">
              <h6 class="card-title">Datos de Usuario</h6>
            </div>
            <div class="card-body">
              <div class="row form-group">
                <label class="col-md-2" for="txtEmailNuevoUsuario">Email</label>
                <div class="col-md">
                  <input class="form-control" type="email" id="txtEmailNuevoUsuario" maxlength="150" required>
                </div>
              </div>
              <div class="row form-group">
                <div class="col-md-6">
                  <label for="txtNombreNuevoUsuario">Nombre</label>
                  <input type="text" class="form-control" id="txtNombreNuevoUsuario" maxlength="150" required>
                </div>
                <div class="col-md-6">
                  <label for="txtApellidosNuevoUsuario">Apellidos</label>
                  <input type="text" class="form-control" id="txtApellidosNuevoUsuario" maxlength="150" required>
                </div>
              </div>
              <div class="row form-group">
                <div class="col-md-6">
                  <label for="txtTelefonoNuevoUsuario">Teléfono</label>
                  <input type="text" class="form-control" id="txtTelefonoNuevoUsuario" maxlength="10" minlength="10">
                </div>
                <div class="col-md-6">
                  <label for="txtExtencionNuevoUsuario">Extension</label>
                  <input type="text" class="form-control" id="txtExtencionNuevoUsuario" maxlength="4" minlength="4">
                </div>
              </div>
            </div>
          </div>
          <div class="card" id="crdNuevoUsuarioProfesor">
            <div class="card-header-warning">
              <h6 class="card-title">Datos del Profesor</h6>
            </div>
            <div class="card-body">
              <div class="row form-group">
                <div class="col-md-6">
                  <label for="sctNivelAdscNuevoProfesor">Nivel de Adscripción</label>
                  <select class="form-control selectpicker" id="sctNivelAdscNuevoProfesor" data-style="btn btn-link">
                    <option value="Dr.">Doctor en Ciencias</option>
                    <option value="M.C.">Maestro en Ciencias</option>
                    <option value="M.A.">Maestro en Administración</option>
                    <option value="Ing.">Ingeniero</option>
                    <option value="Lic.">Licenciado</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label for="sctTipoContratoNuevoProfesor">Tipo Contrato</label>
                  <select class="form-control selectpicker" id="sctTipoContratoNuevoProfesor" data-style="btn btn-link">
                    <option value="P.A">Profesor de Asignatura</option>
                    <option value="P.T.C">Profesor Tiempo Completo</option>
                  </select>
                </div>
              </div>
              <div class="row form-group">
                <div class="col-md-8">
                  <label for="sctCarreraAdscNuevoProfesor">Carrera adscripción</label>
                  <select class="form-control selectpicker" id="sctCarreraAdscNuevoProfesor" data-style="btn btn-link"
                          required>
                  </select>
                </div>
                <div class="col-md-4">
                  <label for="sctCategoriaNuevoProfesor">Categoria</label>
                  <select class="form-control selectpicker" id="sctCategoriaNuevoProfesor" data-style="btn btn-link">
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                  </select>
                </div>
              </div>
              <div class="row form-group">
                <div class="col-md-6">
                  <label for="dteInicioContratoNuevoProfesor">Inicio de Contrato</label>
                  <input type="date" id="dteInicioContratoNuevoProfesor" class="form-control">
                </div>
                <div class="col-md-6">
                  <label for="dteFinContratoNuevoProfesor">Fin de Contrato</label>
                  <input type="date" id="dteFinContratoNuevoProfesor" class="form-control">
                </div>
              </div>
            </div>
          </div>
          <div class="row form-group">
            <div class="col-md float-right">
              <input type="submit" class="btn btn-primary" value="Guardar"/>
              <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<?php
include_once("../components/core_js.php");
?>
<!-- Script específico para cada página -->
<!-- <script src="../../static/js/productos-academicos.js"></script> -->
<script src="../../static/js/rrhh/lista_profesores.js"></script>
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