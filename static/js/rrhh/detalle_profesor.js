var initd = function () {
    var $id_usuario = obten_id_usuario();

    if ($id_usuario) {
        sss('id_usuario', $id_usuario, false);
        carga_datos();
        llena_tarjetas();
        modal_editar_datos_usuario();
        actualizar_datos_usuario();
        modal_editar_perfil_usuario();
        actualizar_datos_perfil();
        modal_editar_perfil_profesor();
        actualizar_perfil_profesor();
    }
}

var modal_editar_perfil_usuario = function () {
    $("#btnEditarPerfilUsuario").click(function() {
        var frmEditarPerfilUsuario = $("#frmEditarDatosPerfil");
        var id_usuario = ssg('id_usuario', false);
        var profData = ssg('profData');

        if (profData !== null) {
            frmEditarPerfilUsuario.find("#hdnIdUsuario").val(id_usuario);
            frmEditarPerfilUsuario.find("#txtNombrePefil").val(profData.nombre);
            frmEditarPerfilUsuario.find("#txtApellidosPerfil").val(profData.apellidos);
            frmEditarPerfilUsuario.find("#txtTelefonoPefil").val(profData.telefono);
            frmEditarPerfilUsuario.find("#txtExtensionPerfil").val(profData.extension);
            $("#EditarDatosPerfil").modal("show");
        } else {
            rep('Error', 'No se han podido cargar los datos del usuario');
        }
    });
};

var modal_editar_datos_usuario = function() {
    $("#btnEditarDatosUsuario").click(function () {
        var frmEditarDatosUsuario = $("#frmEditarDatosUsuario");
        var id_usuario = ssg('id_usuario', false);
        var profData = ssg('profData');

        if (profData !== null) {
            frmEditarDatosUsuario.find("#hdnDatosUsuario").val(id_usuario);

            frmEditarDatosUsuario.find("#txtEmailUsuario").val(profData.email);

            if (profData.activo) {
                frmEditarDatosUsuario.find("#chkAvilitadoUsuario").bootstrapToggle('on');
            } else {
                frmEditarDatosUsuario.find("#chkAvilitadoUsuario").bootstrapToggle('off');
            }

            $("#EditarDatosUsuario").modal("show");
        } else {
            Notiflix.Report.failure("Error", "No se han podido cargar los datos del usuario", "Cerrar");
        }
    });
};

var modal_editar_perfil_profesor = function() {
    $("#btnEditarPerfilProfesor").click(function() {

        var profData = ssg('profData');

        if (profData.profesor !== null) {
            var frmEditarPerfilProfesor = $("form#frmEditarPerfilProfesor");
            frmEditarPerfilProfesor.find("#hdnIdUsuarioProfesor").val(profData.profesor.id);
            frmEditarPerfilProfesor.find("#btnSavePerfilProfesor").val("Actualizar");

            frmEditarPerfilProfesor.find("#sctNivelAdscripcion option[value=\"" + profData.profesor.nivel_adscripcion + "\"]").attr("selected", true);
            frmEditarPerfilProfesor.find("#sctTipoContrato option[value=\"" + profData.profesor.tipo_contrato + "\"]").attr("selected", true);
            frmEditarPerfilProfesor.find("#sctCategoriaProfesor option[value=\"" + profData.profesor.categoria + "\"]").attr("selected", true);

            if (profData.profesor.inicio_contrato !== "") {
                frmEditarPerfilProfesor.find("#dteInicioContrato").val(moment(profData.profesor.inicio_contrato, "YYYY/MM/DD").format("YYYY-MM-DD"));
            }

            if (profData.profesor.fin_contrato !== "") {
                frmEditarPerfilProfesor.find("#dteFinContrato").val(moment(profData.profesor.fin_contrato, "YYYY/MM/DD").format("YYYY-MM-DD"));
            }
            llena_select_carreras();
            frmEditarPerfilProfesor.find("#sctCarreraAdscripcion option[value=\"" + profData.profesor.carrera_adscripcion.id + "\"]").attr("selected", true);
            $("#EditarPerfilProfesor").modal("show");

        } else {
            rep("Error", "No se han logrado obtener los datos del profesor, intenta mas tarde o contacta con el administrador del sistema", "Aceptar");
        }
    });
}

var actualizar_datos_usuario = function () {
    $("#frmEditarDatosUsuario").submit(function (event) {
        event.preventDefault();

        var dataSubmit = {'id_usuario': $("#hdnDatosUsuario").val(),
            'nuevo_status_usuario': $("#chkAvilitadoUsuario").prop('checked'),
            'nuevo_email_usuario': $("#txtEmailUsuario").val(),
        };
        var result = post_data("../../api/requests/rrhh/updates/usuario/actualiza_datos.php", dataSubmit,true);

        if (result.done) {
            Notiflix.Report.success("Éxito", result.message, "Aceptar");
            carga_tarjeta_usuario(dataSubmit.nuevo_email_usuario, dataSubmit.nuevo_tipo_usuario, dataSubmit.nuevo_status_usuario);
            $("#EditarDatosUsuario").modal("hide");
        }
    });
};

var actualizar_datos_perfil = function() {
    $("#frmEditarDatosPerfil").submit(function(event) {
        event.preventDefault();

        var rawTelefono = $('form#frmEditarDatosPerfil input#txtTelefonoPefil').val();
        var rawExtension = $('form#frmEditarDatosPerfil input#txtExtensionPerfil').val();
        var valTelefono = rawTelefono === "" ? 'NULL' : $('form#frmEditarDatosPerfil input#txtTelefonoPefil').val();
        var valExtension = rawExtension === "" ? 'NULL' : $('form#frmEditarDatosPerfil input#txtExtensionPerfil').val();

        var dataSubmit = {
            'id_usuario': $("form#frmEditarDatosPerfil input#hdnIdUsuario").val(),
            'nuevo_nombre_usuario': $("form#frmEditarDatosPerfil input#txtNombrePefil").val(),
            'nuevo_apellidos_usuario': $("form#frmEditarDatosPerfil input#txtApellidosPerfil").val(),
            'nuevo_telefono_usuario': valTelefono,
            'nueva_extension_usuario': valExtension
        }

        var result = post_data("../../api/requests/rrhh/updates/usuario/actualiza_datos.php", dataSubmit,true);

        if (result.done) {

            var profData = ssg('profData');
            profData.nombre = dataSubmit.nuevo_nombre_usuario;
            profData.apellidos = dataSubmit.nuevo_apellidos_usuario;
            profData.telefono = rawTelefono;
            profData.extension = rawExtension;
            sss('profData', profData);

            rep("Éxito", result.message,'s');

            $("#EditarDatosPerfil").modal("hide");
            carga_tarjeta_perfil(dataSubmit.nuevo_nombre_usuario,dataSubmit.nuevo_apellidos_usuario, rawTelefono, rawExtension);
        }
    });
};

var actualizar_perfil_profesor = function () {
    $("#frmEditarPerfilProfesor").submit(function(event) {
        event.preventDefault();

        var dataSubmit = {
            'id_profesor': $(this).find("#hdnIdUsuarioProfesor").val(),
            'nueva_carrera_adscripcion': $(this).find("#sctCarreraAdscripcion").val(),
            'nuevo_nivel_adscripcion': $(this).find("#sctNivelAdscripcion").val(),
            'nuevo_tipo_contrato': $(this).find("#sctTipoContrato").val(),
            'nueva_categoria': $(this).find("#sctCategoriaProfesor").val(),
            'nuevo_inicio_contrato': $(this).find("#dteInicioContrato").val(),
            'nuevo_fin_de_contrato': $(this).find("#dteFinContrato").val()
        };

        var result = post_data("../../api/requests/rrhh/updates/profesor/actualiza_datos.php", dataSubmit, true);

        console.log(result);

        if (result.done) {
            var profData = ssg('profData');
            profData.profesor.carrera_adscripcion = { "id": parseInt(dataSubmit.nueva_carrera_adscripcion), "nombre": $(this).find("#sctCarreraAdscripcion option:selected").text()};
            profData.profesor.nivel_adscripcion = dataSubmit.nuevo_nivel_adscripcion;
            profData.profesor.tipo_contrato = dataSubmit.nuevo_tipo_contrato;
            profData.profesor.categoria = dataSubmit.nueva_categoria;
            profData.profesor.inicio_contrato = dataSubmit.nuevo_inicio_contrato;
            profData.profesor.fin_contrato = dataSubmit.nuevo_fin_de_contrato;

            if (!profesor_imparte_en_carrera(profData.profesor.carrera_adscripcion.id, profData.profesor.imparteEn)) {
                profData.profesor.imparteEn.push(profData.profesor.carrera_adscripcion);
            }

            sss('profData', profData);

            rep("Éxito", result.message, 's');
            $("#EditarPerfilProfesor").modal("hide");
            carga_tarjeta_perfilProfesor(profData.profesor.nivel_adscripcion, profData.profesor.tipo_contrato, profData.profesor.categoria, profData.profesor.inicio_contrato, profData.profesor.fin_contrato, profData.profesor.carrera_adscripcion.nombre, profData.profesor.imparteEn);
        }

    });
};

var llena_tarjetas = function() {
    var profData = ssg('profData');

    if (profData !== null) {
        carga_tarjeta_usuario(profData.email, profData.tipo, profData.activo);
        carga_tarjeta_perfil(profData.nombre, profData.apellidos, profData.telefono, profData.extension);
        carga_tarjeta_perfilProfesor(profData.profesor.nivel_adscripcion, profData.profesor.tipo_contrato, profData.profesor.categoria, profData.profesor.inicio_contrato, profData.profesor.fin_contrato, profData.profesor.carrera_adscripcion.nombre, profData.profesor.imparteEn);
    }
};

var carga_tarjeta_usuario = function (email, tipo, activo) {
    var tblDatosUsuario = $("#tblDatosUsuario");
    tblDatosUsuario.find("#email_usuario").text(email);
    tblDatosUsuario.find("#tipo_usuario").text(tipo);
    tblDatosUsuario.find("#activo_usuario").text(activo === true ? "Si" : "No");
};

var carga_tarjeta_perfil = function (nombre, apellidos, telefono, extension) {
    var tblDatosPerfil = $("#tblDatosPerfil");
    tblDatosPerfil.find("#perfil_nombre").text(nombre);
    tblDatosPerfil.find("#perfil_apellidos").text(apellidos);
    tblDatosPerfil.find("#perfil_telefono").text(telefono);
    tblDatosPerfil.find("#perfil_extension").text(extension);
};

var carga_tarjeta_perfilProfesor = function (nivel_adscripcion, tipo_contrato, categoria, inicio_contrato, fin_contrato, carrera_adscripcion, carrerasDondeImparte) {
    var tblDatosPerfilProfesor = $("#tblDatosPerfilProfesor");
    tblDatosPerfilProfesor.find("#profesor_nivel_adscripcion").text(nivel_adscripcion);
    tblDatosPerfilProfesor.find("#profesor_tipo_contrato").text(tipo_contrato);
    tblDatosPerfilProfesor.find("#profesor_categoria").text(categoria);
    tblDatosPerfilProfesor.find("#profesor_inicio_contrato").text(inicio_contrato);
    txtFinContrato = fin_contrato.length != 10 ? "Indefinido" : fin_contrato;
    tblDatosPerfilProfesor.find("#profesor_fin_contrato").text(txtFinContrato);
    tblDatosPerfilProfesor.find("#profesor_carrera_adscripcion").text(carrera_adscripcion);

    var tblProfesorImparteEn = $("#tblProfesorImparteEn");
    tblProfesorImparteEn.empty();
    carrerasDondeImparte.forEach(carrera => tblProfesorImparteEn.append('<tr><td>' + carrera.nombre + '</td></tr>'));
};

var carga_datos = function () {
    if (ssg('id_usuario', false) === null) {
        Notiflix.Report.failure("Error", "No se ha podido obtener los datos del usuario. Verifique la configuración de su navegador", "Cerrar");
    } else {
        var id_usuario = ssg('id_usuario', false);
        var result = post_data("../../api/requests/common/getters/usuario/obten_datos_usuario.php", {id_usuario: id_usuario}, true);

        if (result.done) {
            sss('profData', result.data);
        } else {
            Notiflix.Report.failure("Error", result.message, "Aceptar");
        }
    }
};

var obten_id_usuario = function () {
    var id_usuario = getUrlParameter("id_usuario");

    if (!id_usuario) {
        Notiflix.Report.failure('Error', 'Se debe proporcionar el id del usuario a consultar', 'Aceptar');
    }

    return id_usuario;
};

var llena_select_carreras = function () {
    var result = get_data("../../api/requests/common/getters/carrera/get_all.php", false);

    $("#sctCarreraAdscripcion").find('option').remove().end();
    if (result.done) {
        result.data.forEach(carrera => $("#sctCarreraAdscripcion").append($("<option>", {value: carrera.id, text: carrera.nombre})));
    }
};

var profesor_imparte_en_carrera = function (id_carrera, carrerasDondeImparte) {
    var ban = false;
    for (var i=0; i < carrerasDondeImparte.length; i++) {
        if (id_carrera === carrerasDondeImparte[i].id) {
            ban = true;
            break;
        }
    }
    return ban;
};