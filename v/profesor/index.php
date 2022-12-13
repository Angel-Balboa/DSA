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
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="card card-stats">
                  <div class="card-header card-header-info card-header-icon">
                    <div class="card-icon">
                      <i class="material-icons">library_books</i>
                    </div>
                    <p class="card-category">Productos</p>
                    <h3 id="txttotal" class="card-title"></h3>
                  </div>
                  <div class="card-footer">
                    <div class="stats">
                      <i class="material-icons text-info">library_add</i>
                      <a href="#newProductModal" data-toggle="modal" data-target="#newProductModal" onclick="cambiarTexto('Agregar nuevo producto académico');">Agregar Nuevo</a>
                    </div>
                  </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 col-sm-6">
                <div class="card card-stats">
                  <div class="card-header card-header-success card-header-icon">
                    <div class="card-icon">
                      <i class="material-icons">assignment</i>
                    </div>
                    <p class="card-category">Planeaciones</p>
                    <h3 class="card-title"></h3>
                  </div>
                  <div class="card-footer">
                    <div class="stats">
                      <i class="material-icons">date_range</i> Actualizado el 24/03/21
                    </div>
                  </div>
                </div>
            </div>
          </div>
          <!-- End row -->

          <div class="row">
              <div class="col-md-8">
                <div class="card">
                  <div class="card-header card-header-tabs card-header-primary">
                    <div class="nav-tabs-navigation">
                      <div class="nav-tabs-wrapper">
                        <span class="nav-tabs-title"></span>
                        <ul class="nav nav-tabs" data-tabs="tabs">
                          <li class="nav-item">
                            <a class="nav-link active" href="#others" data-toggle="tab">
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
                          <table class="table">
                              <thead class="text-primary">
                              <th>#</th>
                              <th>Título</th>
                              <th>Journal</th>
                              <th>año</th>
                              <th>Acciones</th>
                              </thead>
                              <tbody id="tbOthers">
                              </tbody>
                          </table>
                      </div>

                      <div class="tab-pane" id="journals">
                          <table class="table">
                              <thead class="text-primary">
                              <th>#</th>
                              <th>Título</th>
                              <th>Journal</th>
                              <th>año</th>
                              <th>Acciones</th>
                              </thead>
                              <tbody id="tbJournals">
                              </tbody>
                          </table>
                      </div>

                        <div class="tab-pane" id="libros">
                            <table class="table">
                                <thead class="text-primary">
                                <th>#</th>
                                <th>Título</th>
                                <th>Editorial</th>
                                <th>año</th>
                                <th>Acciones</th>
                                </thead>
                                <tbody id="tbLibros">
                                </tbody>
                            </table>
                        </div>

                        <div class="tab-pane" id="indexados">
                            <table class="table">
                                <thead class="text-primary">
                                <th>#</th>
                                <th>Título</th>
                                <th>Actas</th>
                                <th>año</th>
                                <th>Acciones</th>
                                </thead>
                                <tbody id="tbIndexados">
                                </tbody>
                            </table>

                      </div>

                      <div class="tab-pane" id="congresos">
                          <table class="table">
                              <thead class="text-primary">
                              <th>#</th>
                              <th>Título</th>
                              <th>Congreso</th>
                              <th>año</th>
                              <th>Acciones</th>
                              </thead>
                              <tbody id="tbCongresos">
                              </tbody>
                          </table>

                      </div>
                    </div>
                  </div> <!-- end card body-->
                </div>
              </div>

              <div class="col-md-4">
                <div class="card">
                  <div class="card-header card-header-success">
                    <h4>Publicaciones recientes</h4>
                  </div>
                    <div class="card-body">

                      <div class="comment-widgets scrollable">
                        <div class="d-flex flex-row">
                            <div class="p-2">
                              <img src="../../static/assets/img/faces/marc.jpg" class="rounded-circle" style="width: 2.5rem; height: 2.5rem;">
                            </div>
                            <div class="comment-text w-100">
                                <h6 class="font-meddium">Said polanco <span class="text-muted float-end">April 14, 2021</span></h6>
                                <span class="m-b-15 d-block">
                                  Herramienta para simular la marcha de una persona con diplejía espástica
                                </span>
                                <div class="comment-footer">
                                    <span class="text-muted float-end">in 2009 16th IEEE International Conference on Electronics, Circuits and Systems-(ICECS 2009)</span>
                                </div>
                            </div>
                        </div>
                        <hr style="border: 0; 
                        height: 1px; 
                        background-image: -webkit-linear-gradient(left, #f0f0f0, #8c8b8b, #f0f0f0);
                        background-image: -moz-linear-gradient(left, #f0f0f0, #8c8b8b, #f0f0f0);
                        background-image: -ms-linear-gradient(left, #f0f0f0, #8c8b8b, #f0f0f0);
                        background-image: -o-linear-gradient(left, #f0f0f0, #8c8b8b, #f0f0f0);">
                        <div class="d-flex flex-row">
                          <div class="p-2">
                            <img src="../../static/assets/img/faces/marc.jpg" class="rounded-circle" style="width: 2.5rem; height: 2.5rem;">
                          </div>
                          <div class="comment-text w-100">
                              <h6 class="font-meddium">Said polanco <span class="text-muted float-end">April 14, 2021</span></h6>
                              <span class="m-b-15 d-block">
                                Herramienta para simular la marcha de una persona con diplejía espástica
                              </span>
                              <div class="comment-footer">
                                  <span class="text-muted float-end">in 2009 16th IEEE International Conference on Electronics, Circuits and Systems-(ICECS 2009)</span>
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

        <!-- Inicia formulario de productos académicos -->
        <div class="modal fade" id="newProductModal" tabindex="-1" role="dialog" aria-labelledby="NewProduct" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Agregar nuevo producto académico</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="productForm" action="" method="post">

                            <input type="hidden" id="hdnIdProfesor" name="id_profesor" value="<?php echo $profesor->get_data("id"); ?>" />
                            <!-- Tipo de registro -->
                            <div class="form-row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="sctTipoProducto" class="control-label">Tipo</label>
                                        <select name="type_product" id="sctTipoProducto" class="form-control selectpicker" data-style="btn btn-link" data-width="75%" required>
                                            ["article", "book", "booklet", "conference", "in_book", "in_collection", "in_proceedings", "manual", "master_tesis", "misc", "phd_tesis", "proceedings", "tech_report", "unpublished"]
                                            <option value="">Tipo de producto Académico</option>
                                            <option value="Article">Artículo</option>
                                            <option value="Book">Libro</option>
                                            <option value="Booklet">Folleto</option>
                                            <option value="Conference">Conferencia</option>
                                            <option value="InBook">Dentro de un libro</option>
                                            <option value="InCollection">En una colección</option>
                                            <option value="InProceedings">En las actas</option>
                                            <option value="Manual">Manual</option>
                                            <option value="MasterThesis">Tesis de Maestría</option>
                                            <option value="Misc">Miscelánea</option>
                                            <option value="PhdThesis">Tesis doctoral</option>
                                            <option value="Proceedings">Libro de actas</option>
                                            <option value="TechReport">Informe técnico</option>
                                            <option value="UnPublished">No publicado</option>
                                        </select>
                                    </div>
                                </div>
                            </div><!-- end form-row -->
                            <div id="form_content1"></div>
                            <div class="row">
                                <div class="col-lg col-md d-flex justify-content-end">
                                    <input type="submit" class="btn btn-success invisible" id="btnGuardarNuevoProducto" value="Guardar" />
                                    <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                                </div>
                            </div>
                        </form>
                    </div><!-- end modal-body -->
                </div>
            </div>
        </div>
        <!-- Termina formulario -->
        <!-- footer -->
        <?php include_once ("../components/footer.php"); ?>
    </div>
  </div>
<!-- Visualizar producto -->
<div class="modal fade" id="visualizarProducto" tabindex="-1" role="dialog" aria-labelledby="NewProduct" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Producto académico</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12" id="apaFormat"></div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-12" id="bibtexFormat"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<!-- Termina visualizar producto -->
<!--   Core JS Files   -->
<?php include_once ("../components/core_js.php"); ?>
<script src="../../static/assets/js/plugins/citation-0.4.0-9.min.js" type="text/javascript"></script>
<script src="../../static/js/profesor/productos_academicos.js"></script>
<!-- end Core JS Files -->
<!--  <script src="../../static/js/productos-academicos.js"></script>-->
<script>
    $(document).ready(function() {
        $().ready(function() {
            $sidebar = $('.sidebar');

            $sidebar_img_container = $sidebar.find('.sidebar-background');

            $full_page = $('.full-page');

            $sidebar_responsive = $('body > .navbar-collapse');

            window_width = $(window).width();
        });
        sss("id_profesor", <?php echo $profesor->get_data("id"); ?>, false);
        initd();
    });
</script>
</body>

</html>