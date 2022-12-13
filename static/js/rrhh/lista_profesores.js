var initd = function initd() {
    obten_profesores();
    modal_agregar_profesor();
    agregar_profesor();
};

var agregar_profesor = function agregar_profesor() {
    $("#frmNuevoProfesor").submit(function (event) {
        event.preventDefault();

        var dataSubmit = {
            "usuario_email": $(this).find("#txtEmailNuevoUsuario").val(),
            "perfil_nombre": $(this).find("#txtNombreNuevoUsuario").val(),
            "perfil_apellidos": $(this).find("#txtApellidosNuevoUsuario").val(),
            "perfil_telefono": $(this).find("#txtTelefonoNuevoUsuario").val(),
            "perfil_extension": $(this).find("#txtExtencionNuevoUsuario").val(),
            "perfil_profesor_nivel_adscripcion": $(this).find("#sctNivelAdscNuevoProfesor").val(),
            "perfil_profesor_tipo_contrato": $(this).find("#sctTipoContratoNuevoProfesor").val(),
            "perfil_profesor_categoria": $(this).find("#sctCategoriaNuevoProfesor").val(),
            "perfil_profesor_inicio_contrato": $(this).find("#dteInicioContratoNuevoProfesor").val(),
            "perfil_profesor_contrato_indefinido": $(this).find("#dteFinContratoNuevoProfesor").val(),
            "perfil_profesor_carrera_adscripcion": $(this).find("#sctCarreraAdscNuevoProfesor").selectpicker("val"),
        };

        var result = post_data("../../api/requests/rrhh/creates/usuario/crear_usuario_profesor.php", dataSubmit, true);

        if (result.done) {
            rep("Éxito", result.message +"\nLa contraseña del usuario es: " + result.data.pasw, 's');
            obten_usuarios();
            $("#mdlAgregarProfesor").modal("hide");
        }
    })
}

var modal_agregar_profesor = function modal_agregar_profesor() {
    $("#btnAgregarProfesor").click(function() {
        var result = get_data("../../api/requests/common/getters/carrera/get_all.php", true);
        var sctCarreraAdscNuevoProfesor = $("#sctCarreraAdscNuevoProfesor");
        sctCarreraAdscNuevoProfesor.find('option').remove().end();
        if (result.done) {
            result.data.forEach(carrera => sctCarreraAdscNuevoProfesor.append('<option value="' + carrera.id + '">' + carrera.nombre + '</option>'));
            sctCarreraAdscNuevoProfesor.selectpicker("refresh");
            $("#mdlAgregarProfesor").modal("show");
        }
    });
};

var obten_profesores = function obten_profesores() {

    var target_url = crea_query();

    var result = get_data(target_url, false);

    llena_tarjeta_profesores(result.data);
};

var llena_tarjeta_profesores = function llena_tarjeta_profesores(lista_profesores) {
    var tbListadoUsuarios = $("#tbListadoProfesores");
    tbListadoUsuarios.empty();
    lista_profesores.forEach(function (profesor) {
        var txtActivo = profesor.activo ? "Si" : "No";
        tbListadoUsuarios.append('<tr>\n' +
            '                      <td id="usuario_id">' + profesor.id + '</td>\n' +
            '                      <td>' + profesor.nombre + ' ' + profesor.apellidos + '</td>\n' +
            '                      <td>' + profesor.profesor.tipo_contrato + '-' + profesor.profesor.categoria + '</td>\n' +
            '                      <td>' + profesor.profesor.carrera_adscripcion.clave + '</td>\n' +
            '                      <td>' + txtActivo + '</td>\n' +
            '                      <td>\n' +
            '                        <a href="detalle_profesor.php?id_usuario=' + profesor.id + '" title="Editar" class="btn btn-primary btn-link">\n' +
            '                          <i class="material-icons">edit</i>\n' +
            '                        </a>\n' +
            '                        <button type="button" rel="tooltip" title="Vista Rápida" class="btn btn-primary btn-link quick-view-user">\n' +
            '                          <i class="material-icons">preview</i>\n' +
            '                        </button>\n' +
            '                      </td>\n' +
            '                    </tr>');
    });

    $("button.quick-view-user").click(function() {
        var item = $(this).closest('tr'); // fila
        modal_vista_rapida_profesor(item.find("#usuario_id").text());
    });
};

var modal_vista_rapida_profesor = function modal_vista_rapida_profesor(id_usuario) {
    var result = post_data("../../api/requests/common/getters/usuario/obten_datos_usuario.php", {"id_usuario": id_usuario}, true);

    if (result.done) {
        $("#qv-email-usuario").text(result.data.email);
        $("#qv-nombre_usuario").text(result.data.nombre + ' ' + result.data.apellidos);
        $("#qv-telefono-usuario").text(result.data.telefono);
        $("#qv-extension-usuario").text(result.data.extension);
        $("#qv-tipo-usuario").text(result.data.tipo);
        var txtActivo = result.data.activo ? "Si":"No";
        $("#qv-activo-usuario").text(txtActivo);
        $("#qv-nivel-adscripcion").text(text_nivel_adscripcion(result.data.profesor.nivel_adscripcion));
        $("#qv-tipo-contrato").text(text_tipo_contrato(result.data.profesor.tipo_contrato));
        $("#qv-categoria-profesor").text(result.data.profesor.categoria);
        $("#qv-inicio-contrato").text(result.data.profesor.inicio_contrato);
        var txtFinContrato = (result.data.profesor.fin_contrato !== "") ? result.data.profesor.fin_contrato : "Indefinido";
        $("#qv-fin-contrato").text(txtFinContrato);
        $("#crdPerfilProfesor").show();
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
    return "../../api/requests/rrhh/getters/profesor/get_all.php";
};