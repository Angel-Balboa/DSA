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
                    <div class="col-lg-4 col-md-4">
                        <div class="card">
                            <div class="card-header card-header-primary">
                                <h4 class="card-title">Datos de Usuarios </h4>
                            </div> <!-- end card-header -->
                            <div class="card-body table-responsive">
                                <table id="tblDatosUsuario" class="table table-hover">
                                    <tbody>
                                    <tr>
                                        <td>Email</td>
                                        <td id="email_usuario"></td>
                                    </tr>
                                    <tr>
                                        <td>Activo</td>
                                        <td id="activo_usuario"></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div> <!-- end card-body -->
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col text-right">
                                        <button class="btn btn-primary" type="button" id="btnEditarDatosUsuario">Editar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- end card-->
                    </div> <!-- end col -->
                    <div class="col-lg-8 col-md-8">
                        <div class="card">
                            <div class="card-header card-header-primary">
                                <h4 class="card-title">Datos de Perfil</h4>
                            </div> <!-- end card-header -->
                            <div class="card-body table-responsive">
                                <table id="tblDatosPerfil" class="table table-hover">
                                    <tbody>
                                    <tr>
                                        <td>Nombre</td>
                                        <td><span id="perfil_nombre"></span> <span id="perfil_apellidos"></span></td>
                                    </tr>
                                    <tr>
                                        <td>Teléfono</td>
                                        <td id="perfil_telefono"></td>
                                    </tr>
                                    <tr>
                                        <td>Extensión</td>
                                        <td id="perfil_extension"></td>
                                    </tr>
                                    <tr style="text-align:center;">
                                        <td colspan="2">
                                <span class="material-icons" style="font-size: 128px;">
                                    account_circle
                                </span>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div> <!-- end card-body -->
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-md-auto">
                                        <button id="btnEditarPerfilUsuario" class="btn btn-primary" type="button">Editar</button>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- end card-->
                    </div> <!-- end col -->
                </div> <!-- End row -->
                <div id="perfil_profesor" class="row">
                    <div class="col-lg-4 col-md-4">
                        <div class="card">
                            <div class="card-header card-header-primary">
                                <h4 class="card-title">Profesor / Perfil de Profesor</h4>
                            </div> <!-- end card-header -->
                            <div class="card-body table-responsive">
                                <table id="tblDatosPerfilProfesor" class="table table-hover">
                                    <tbody>
                                    <tr>
                                        <td>Nivel de Adscripción</td>
                                        <td id="profesor_nivel_adscripcion"></td>
                                    </tr>
                                    <tr>
                                        <td>Tipo Contrato</td>
                                        <td id="profesor_tipo_contrato"></td>
                                    <tr>
                                        <td>Categoría</td>
                                        <td id="profesor_categoria"></td>
                                    </tr>
                                    <tr>
                                        <td>Inicio de contrato</td>
                                        <td id="profesor_inicio_contrato"></td>
                                    </tr>
                                    <tr>
                                        <td>Fin de Contrato</td>
                                        <td id="profesor_fin_contrato"></td>
                                    <tr>
                                        <td>Carrera</td>
                                        <td id="profesor_carrera_adscripcion"></td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div> <!-- end card-body -->
                            <div class="card-footer">
                                <div class="row">
                                    <div class="col-md">
                                        <button class="btn btn-primary" id="btnEditarPerfilProfesor">
                                            Editar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div> <!-- end card-->
                    </div> <!-- end col -->
                    <div class="col-lg-8 col-md-8">
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
                </div> <!-- End row -->
            </div> <!-- end container-fluid-->
        </div> <!-- end content -->

        <?php include_once ("../components/footer.php"); ?>
    </div>
</div>

<!-- modal: EditarDatosDeUsuario -->
<div aria-hidden="true" aria-labelledby="EditarDatosUsuarioLabel" class="modal fade" id="EditarDatosUsuario" role="dialog"
     tabindex="-1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Actualizar Datos de Usuario</h5>
                <button aria-label="Close" class="close" data-dismiss="modal" type="button">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" id="frmEditarDatosUsuario" method="post">
                    <input id="hdnDatosUsuario" name="id_usuario" type="hidden" value="-1">
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label" for="txtEmailUsuario">Email</label>
                        <div class="col-sm-9">
                            <input class="form-control" id="txtEmailUsuario" name="email_usuario" required
                                   type="email"/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-12 text-right">
                            <input id="chkAvilitadoUsuario" name="usuario_activo" data-off="Deshabilitado" data-offstyle="danger" data-on="Habilitado" data-onstyle="primary" data-toggle="toggle" data-width="120" type="checkbox">
                        </div>
                    </div>
                    <div class="form-group row">
                        <input class="btn btn-primary" data-toggle="modal" type="submit" value="Guardar"/>
                        <button class="btn btn-secondary" data-dismiss="modal" type="button">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- modal: EditarDatosPerfil -->
<div aria-hidden="true" aria-labelledby="EditarDatosPerfilLabel" class="modal fade" id="EditarDatosPerfil" role="dialog"
     tabindex="-1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Actualizar Perfil</h5>
                <button aria-label="Close" class="close" data-dismiss="modal" type="button">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" id="frmEditarDatosPerfil" method="post">
                    <input id="hdnIdUsuario" name="id_usuario" type="hidden" value="-1">
                    <div class="form-group row">
                        <div class="col-sm-6">
                            <label for="txtNombrePefil">Nombre(s)</label>
                            <input class="form-control" id="txtNombrePefil" name="nombre_usuario" required type="text"/>
                        </div>
                        <div class="col-sm-6">
                            <label for="txtApellidosPerfil">Apellidos</label>
                            <input class="form-control" id="txtApellidosPerfil" name="apellidos_perfil" required type="text">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label" for="txtTelefonoPefil">Teléfono</label>
                        <div class="col-sm-9">
                            <input class="form-control" id="txtTelefonoPefil" maxlength="10" minlength="10"
                                   name="Telefono_usuario" type="text"/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label" for="txtExtensionPerfil">Extension</label>
                        <div class="col-sm-9">
                            <input class="form-control" id="txtExtensionPerfil" maxlength="4" minlength="4"
                                   name="extension_usuario" type="text"/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-md-auto">
                            <label class="form-label" for="txtFotoPerfil">Foto de perfil</label>
                            <input accept="image/png, image/jpg, image/jpeg" class="form-control" id="txtFotoPerfil" name="foto_perfil"
                                   type="file"/>
                        </div>
                    </div>
                    <div class="form-group row">
                        <input class="btn btn-primary" type="submit" value="Guardar"/>
                        <button class="btn btn-secondary" data-dismiss="modal" type="button">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- modal: EditarPerfilProfesor -->
<div  id="EditarPerfilProfesor" aria-hidden="true" aria-labelledby="EditarPerfilProfesorLabel" class="modal fade" role="dialog" tabindex="-1">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Actualizar Perfil de Profesor</h5>
                <button aria-label="Close" class="close" data-dismiss="modal" type="button">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="" id="frmEditarPerfilProfesor" method="post">
                    <input id="hdnIdUsuarioProfesor" name="idUsuarioProfesor" type="hidden" value="-1" />
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label" for="sctNivelAdscripcion">Nivel de Adscripción</label>
                        <div class="col-sm-9">
                            <select id="sctNivelAdscripcion" class="form-control">
                                <option value="Dr.">Doctor en Ciencias</option>
                                <option value="M.C.">Maestro en Ciencias</option>
                                <option value="M.A.">Maestro en Administración</option>
                                <option value="Ing.">Ingeniero</option>
                                <option value="Lic.">Licenciado</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label" for="sctTipoContrato">Tipo de Contrato</label>
                        <div class="col-sm-9">
                            <select id="sctTipoContrato" class="form-control">
                                <option value="P.A">Profesor por Asignatura</option>
                                <option value="P.T.C">Profesor Tiempo Completo</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-3 col-form-label" for="sctCategoriaProfesor">Categoria</label>
                        <div class="col-sm-9">
                            <select id="sctCategoriaProfesor" class="form-control">
                                <option value="A">A</option>
                                <option value="B">B</option>
                                <option value="C">C</option>
                                <option value="D">D</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label" for="dteInicioContrato">Inicio de contrato</label>
                        <div class="col-sm-auto">
                            <input type="date" id="dteInicioContrato" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-form-label" for="dteFinContrato">Fin de contrato</label>
                        <div class="col-sm-auto">
                            <input type="date" id="dteFinContrato" class="form-control">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-sm-6 col-form-label" for="sctCarreraAdscripcion">Carrera de Adscripción</label>
                        <div class="col-sm-6">
                            <select id="sctCarreraAdscripcion" class="form-control">
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <input id="btnSavePerfilProfesor" class="btn btn-primary" data-toggle="modal" type="submit" value="Actualizar"/>
                        <button class="btn btn-secondary" data-dismiss="modal" type="button">Cancelar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!--   Core JS Files   -->
<?php
include_once ("../components/core_js.php");
?>
<script src="../../static/js/rrhh/detalle_profesor.js"></script>
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
<?php echo $tipo;?>
</html>