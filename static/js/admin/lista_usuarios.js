var initd = function initd() {
    obten_usuarios();
    modal_agregar_usuario();
    agregar_usuario();
};

var agregar_usuario = function agregar_usuario() {
    $("#frmNuevoUsuario").submit(function (event) {
        event.preventDefault();

        var tipoUsuario = $(this).find("#sctTipoNuevoUsuario").val()

        var dataSubmit = {
            "usuario_email": $(this).find("#txtEmailNuevoUsuario").val(),
            "usuario_tipo": tipoUsuario,
            "perfil_nombre": $(this).find("#txtNombreNuevoUsuario").val(),
            "perfil_apellidos": $(this).find("#txtApellidosNuevoUsuario").val(),
            "perfil_telefono": $(this).find("#txtTelefonoNuevoUsuario").val() !== "" ? $(this).find("#txtTelefonoNuevoUsuario").val() : null,
            "perfil_extension": $(this).find("#txtExtencionNuevoUsuario").val()
        }

        if (tipoUsuario == "profesor") {
            dataSubmit["perfil_profesor_nivel_adscripcion"] = $(this).find("#sctNivelAdscNuevoProfesor").val();
            dataSubmit["perfil_profesor_tipo_contrato"] = $(this).find("#sctTipoContratoNuevoProfesor").val();
            dataSubmit["perfil_profesor_categoria"] = $(this).find("#sctCategoriaNuevoProfesor").val();
            dataSubmit["perfil_profesor_inicio_contrato"] = $(this).find("#dteInicioContratoNuevoProfesor").val();
            dataSubmit["perfil_profesor_contrato_indefinido"] = $(this).find("#dteFinContratoNuevoProfesor").val();
            dataSubmit["perfil_profesor_carrera_adscripcion"] = $(this).find("#sctCarreraAdscNuevoProfesor").selectpicker("val");
        }

        var result = post_data("../../api/requests/admin/creates/usuario/crear_usuario.php", dataSubmit, true);

        if (result.done) {
            rep("Éxito", result.message +"\nLa contraseña del usuario es: " + result.data.pasw, 's');
            obten_usuarios();
            $("#mdlAgregarUsuario").modal("hide");
        }
    })
}

var modal_agregar_usuario = function modal_agregar_usuario() {
    $("#btnAgregarUsuario").click(function() {
        var result = get_data("../../api/requests/common/getters/carrera/get_all.php", true);
        var sctCarreraAdscNuevoProfesor = $("#sctCarreraAdscNuevoProfesor");
        sctCarreraAdscNuevoProfesor.find('option').remove().end();
        if (result.done) {
            result.data.forEach(carrera => sctCarreraAdscNuevoProfesor.append('<option value="' + carrera.id + '">' + carrera.nombre + '</option>'));
            sctCarreraAdscNuevoProfesor.selectpicker("refresh");
            $("#mdlAgregarUsuario").modal("show");
        }

        $("#sctTipoNuevoUsuario").change(function (){
            var v = $(this).selectpicker("val");
            if (v === "profesor") {
                $("#crdNuevoUsuarioProfesor").show();
            } else {
                $("#crdNuevoUsuarioProfesor").hide();
            }
        })
    })
};

var obten_usuarios = function obten_usuarios() {

    var target_url = crea_query();

    var result = get_data(target_url, false);

    llena_tarjeta_usuarios(result.data);
};

var llena_tarjeta_usuarios = function llena_tarjeta_usuarios(lista_usuarios) {
    var tbListadoUsuarios = $("#tbListadoUsuarios");
    tbListadoUsuarios.empty();
    lista_usuarios.forEach(function (usuario) {
        var txtActivo = usuario.activo ? "Si" : "No";
        tbListadoUsuarios.append('<tr>\n' +
            '                      <td id="usuario_id">' + usuario.id + '</td>\n' +
            '                      <td>' + usuario.email + '</td>\n' +
            '                      <td>' + usuario.nombre + ' ' + usuario.apellidos + '</td>\n' +
            '                      <td>' + usuario.tipo + '</td>\n' +
            '                      <td>' + txtActivo + '</td>\n' +
            '                      <td>\n' +
            '                        <a href="../../v/admin/detalle_usuario.php?id_usuario=' + usuario.id +'" title="Editar" class="btn btn-primary btn-link">\n' +
            '                          <i class="material-icons">edit</i>\n' +
            '                        </a>\n' +
            '                        <button type="button" rel="tooltip" title="Vista Rápida" class="btn btn-primary btn-link quick-view-user">\n' +
            '                          <i class="material-icons">preview</i>\n' +
            '                        </button>\n' +
            '                      </td>\n' +
            '                    </tr>')
    });

    $("button.quick-view-user").click(function() {
        var item = $(this).closest('tr'); // fila
        modal_vista_rapida_usuario(item.find("#usuario_id").text());
    });
};

var modal_vista_rapida_usuario = function modal_vista_rapida_usuario(id_usuario) {
    var result = post_data("../../api/requests/common/getters/usuario/obten_datos_usuario.php", {"id_usuario": id_usuario}, true);

    if (result.done) {
        $("#qv-email-usuario").text(result.data.email);
        $("#qv-nombre_usuario").text(result.data.nombre + ' ' + result.data.apellidos);
        $("#qv-telefono-usuario").text(result.data.telefono);
        $("#qv-extension-usuario").text(result.data.extension);
        $("#qv-tipo-usuario").text(result.data.tipo);
        var txtActivo = result.data.activo ? "Si":"No";
        $("#qv-activo-usuario").text(txtActivo);

        if (!result.data.profesor) {
            $("#crdPerfilProfesor").hide();
        } else {
            $("#qv-nivel-adscripcion").text(text_nivel_adscripcion(result.data.profesor.nivel_adscripcion));
            $("#qv-tipo-contrato").text(text_tipo_contrato(result.data.profesor.tipo_contrato));
            $("#qv-categoria-profesor").text(result.data.profesor.categoria);
            $("#qv-inicio-contrato").text(result.data.profesor.inicio_contrato);
            var txtFinContrato = (result.data.profesor.fin_contrato !== "") ? result.data.profesor.fin_contrato : "Indefinido";
            $("#qv-fin-contrato").text(txtFinContrato);
            $("#crdPerfilProfesor").show();
        }
        $("#VistaRapidaUsuario").modal("show");
    }
};

var text_tipo_contrato = function text_tipo_contrato(tipo_contrato) {
  return (tipo_contrato === "P.A") ? "CProfesor de Asignatura" : "CProfesor de Tiempo Completo";
};

var text_nivel_adscripcion = function text_nivel_adscripcion(nivel_adscripcion) {
    var txtNivelAdscripcion = "";
    switch (nivel_adscripcion) {
        case "Dr.":
            txtNivelAdscripcion = "Doctor en Ciencias";
            break;
        case "M.C.":
            txtNivelAdscripcion = "Maestro en Ciencias";
            break;
        case "M.A.":
            txtNivelAdscripcion = "Maestro en Administración";
            break;
        case "Lic":
            txtNivelAdscripcion = "Licenciado";
            break;
        default:
            txtNivelAdscripcion = "Ingeniero";
            break;
    }
    return txtNivelAdscripcion;
};

var crea_query = function crea_query() {
  return "../../api/requests/admin/getters/usuario/get_all.php";
};