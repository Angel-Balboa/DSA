var initd = function() {
    obten_clave_plan();
    if (ssg("clave_plan", false) !== null) {
        carga_detalles_plan();
        carga_cargas_academicas();
        carga_materias_plan();
        modal_agregar_carga_academica();
        submit_nueva_carga_academica();
        modal_agregar_materia();
        submit_nueva_materia();
        modal_actualizar_datos_plan();
        submit_actualizar_datos_plan();
        submit_edit_carga();
        submit_actualizar_materia();
    }
};

var modal_agregar_carga_academica = function () {
    $("#btnAgregarCargaAcademica").click(function() {
        $("#frmAgregarCargaAcademica").trigger("reset");
        $("#mdlAgregarCargaAcademica").modal("show");
    });
};

var modal_agregar_materia = function () {
    $("#btnAgregarMaterialAlPlan").click(function () {
        $("#frmAgregarMateria").trigger("reset");
        $("#mdlAgregarMateria").modal("show");
    });
}

var modal_actualizar_datos_plan = function () {
    $("#btnActualizarDatosPlan").click(function () {

        var datosPlan = ssg("data_plan");

        if (datosPlan !== null) {
            var $frmDatosPlan = $("#frmDatosPlanEstudio");
            var $sctNuevoAnioPlan = $frmDatosPlan.find("#sctNuevoAnioPlan");
            var $sctNuevoNivelPlan = $frmDatosPlan.find("#sctNuevoNivelPlan");

            $frmDatosPlan.find("#txtNuevaClavePlan").val(datosPlan.clave);
            $frmDatosPlan.find("#txtNuevoNombrePlan").val(datosPlan.nombre);

            $sctNuevoAnioPlan.val(datosPlan.anio);
            $sctNuevoAnioPlan.selectpicker("refresh");

            $sctNuevoNivelPlan.val(datosPlan.nivel);
            $sctNuevoNivelPlan.selectpicker("refresh");

            $("#mdlDatosPlanEstudio").modal("show");
        } else {
            rep("Error", "No se han podido extraer los datos del plan de estudios", 'w');
        }
    });
};

var modal_edit_carga = function () {
    $(".btn-edit-carga").click(function () {
        var $row = $(this).closest("tr");
        var $frmActualizarDatosCarga = $("#frmActualizarDatosCarga");
        var sctPeriodo = $frmActualizarDatosCarga.find("#sctNuevoPeriodoCarga");

        $frmActualizarDatosCarga.find("#id_carga").val($(this).data("idCarga"));

        sctPeriodo.val($row.find(".periodo_carga").data("intPeriodo"));
        sctPeriodo.selectpicker("refresh");

        var sctNuevoAnioCarga = $frmActualizarDatosCarga.find("#sctNuevoAnioCarga");
        sctNuevoAnioCarga.val($row.find(".anio_carga").text());
        sctNuevoAnioCarga.selectpicker("refresh");

        $("#dteNuevaFechaInicioCarga").datepicker().val($row.find(".fecha_inicio_carga").text());
        $("#dteNuevaFechaFinCarga").datepicker().val($row.find(".fecha_fin_carga").text())

        $("#mdlActualizarDatosCarga").modal("show");

    });
};

var modal_actualizar_materia = function () {
    $(".btn-editar-materia").click(function () {

        var result = get_data("../../api/requests/common/getters/materia/get_one.php?id_materia=" + $(this).data("idMateria"), true);

        if (result.done) {
            var datosMateria = result.data;
            var $frmActualizarDatosMateria = $("#frmActualizarDatosMateria");
            $frmActualizarDatosMateria.find("#txtNuevaClaveMateria").val(datosMateria.clave);
            $frmActualizarDatosMateria.find("#txtNuevoNombreMateria").val(datosMateria.nombre);

            sss("clave_materia", datosMateria.clave, false);

            var $sctNuevoCreditosMateria = $frmActualizarDatosMateria.find("#sctNuevoCreditosMateria");
            $sctNuevoCreditosMateria.val(datosMateria.creditos);
            $sctNuevoCreditosMateria.selectpicker("refresh");

            var $sctNuevoTipoMateria = $frmActualizarDatosMateria.find("#sctNuevoTipoMateria");
            $sctNuevoTipoMateria.val(datosMateria.tipo);
            $sctNuevoTipoMateria.selectpicker("refresh");

            var $sctNuevasHorasMateria = $frmActualizarDatosMateria.find("#sctNuevasHorasMateria");
            $sctNuevasHorasMateria.val(datosMateria.horas_totales);
            $sctNuevasHorasMateria.selectpicker("refresh");

            var $sctNuevoCuatrimestreMateria = $frmActualizarDatosMateria.find("#sctNuevoCuatrimestreMateria");
            $sctNuevoCuatrimestreMateria.val(datosMateria.cuatrimestre);
            $sctNuevoCuatrimestreMateria.selectpicker("refresh");

            var $sctNuevaPosicionHMateria = $frmActualizarDatosMateria.find("#sctNuevaPosicionHMateria");
            $sctNuevaPosicionHMateria.val(datosMateria.posicion_h);
            $sctNuevaPosicionHMateria.selectpicker("refresh");

            $("#mdlActualizarDatosMateria").modal("show");
        }
    });
};

var modal_vista_rapida_materia = function () {
    $(".btn-quickview-materia").click(function () {

        var result = get_data("../../api/requests/common/getters/materia/get_one.php?id_materia=" + $(this).data("idMateria"), true);

        if (result.done) {
            $("#qvClaveMateria").text(result.data.clave);
            $("#qvNombreMateria").text(result.data.nombre);
            $("#qvTipoMateria").text(result.data.tipo);
            $("#qvCreditosMateria").text(result.data.creditos);
            $("#qvCuatrimestreMateria").text(result.data.cuatrimestre);
            $("#qvHorasTotalesMateria").text(result.data.horas_totales);
            $("#qvPosicionHMateria").text(result.data.posicion_h);
            $("#mdlVistaRapidaMateria").modal("show");
        }
    });
};

var submit_actualizar_datos_plan = function () {
    $("#frmDatosPlanEstudio").submit(function (e) {
        e.preventDefault();
        var dataSubmit = get_object_from_serializedArray($(this).serializeArray());
        var clv_plan = ssg("clave_plan", false);
        var ban_reload = false;


        dataSubmit["clv_plan_de_estudios"] = clv_plan;

        // verificamos si la clave anterior es igual a la nueva
        if (dataSubmit.clv_plan_de_estudios === dataSubmit.nueva_clave_plan.trim()) {
            delete dataSubmit["nueva_clave_plan"]; // si es así, evitamos el envío de la nueva clave
        } else {
            ban_reload = true;
            var url = window.location.href.split('?')[0] + '?clv_plan=' + dataSubmit.nueva_clave_plan.trim();
        }

        var result = post_data("../../api/requests/director/updates/plan_de_estudio/actualiza_datos.php", dataSubmit, true);

        if (result.done) {
            if (ban_reload) {
                window.location.href = url;
            } else {
                rep("Éxito", "Se han actualizado los datos del plan de estudios", 's');
                $("#mdlDatosPlanEstudio").modal("hide");
                carga_detalles_plan();
            }
        }
    });
};

var submit_nueva_carga_academica = function () {
    $("#frmAgregarCargaAcademica").submit(function (e){
        e.preventDefault();
        var dataSubmit = {}
        $(this).serializeArray().map(function(x) {dataSubmit[x.name] = x.value; });
        dataSubmit["clave_plan_de_estudio"] = ssg("clave_plan", false);

        var result = post_data("../../api/requests/director/creates/carga_academica/crea_nueva_carga_academica.php", dataSubmit, true);

        if (result.done) {
            rep("Éxito", result.message, 's');
            $("#mdlAgregarCargaAcademica").modal("hide");
            carga_cargas_academicas();
        }
    });
};

var submit_nueva_materia = function () {
    $("#frmAgregarMateria").submit(function (e) {
        e.preventDefault();
        var dataSubmit = get_object_from_serializedArray($(this).serializeArray());
        dataSubmit["clv_plan"] = ssg("clave_plan", false);

        var result = post_data("../../api/requests/director/creates/materia/crea_nueva_materia_en_plan.php", dataSubmit, true);

        if (result.done) {
            rep("Éxito", "Se ha generado una nueva materia", 's');
            carga_materias_plan();
            $("#mdlAgregarMateria").modal("hide");
        }
    });
};

var submit_edit_carga = function () {
    $("#frmActualizarDatosCarga").submit(function(e) {
        e.preventDefault();

        var dataSubmit = get_object_from_serializedArray($(this).serializeArray());

        var result = post_data("../../api/requests/director/updates/carga_academica/actualiza_datos.php", dataSubmit, true);

        if (result.done) {
            carga_cargas_academicas();
            rep("Éxito", "Se han actualizado los datos de la Carga Académica", 's');
            $("#mdlActualizarDatosCarga").modal("hide");
        }
    });
};

var submit_actualizar_materia = function () {
    $("#frmActualizarDatosMateria").submit(function (e) {
        e.preventDefault();

        var $clave_materia = ssg("clave_materia", false);

        if ($clave_materia !== null) {
            var dataSubmit = get_object_from_serializedArray($(this).serializeArray());
            dataSubmit["clv_materia"] = $clave_materia;
            dataSubmit["clv_plan"] = ssg("clave_plan", false);

            var result = post_data("../../api/requests/director/updates/materia/actualiza_datos.php", dataSubmit, true);

            if (result.done) {
                carga_materias_plan();
                rep("Éxito", result.message, 's');
                $("#mdlActualizarDatosMateria").modal("hide");
            }

        } else {
            rep("Error - Clave Materia", "Ha ocurrido un error al obtener la clave de la materia", 'e', 'Cerrar');
        }


    });
};

var carga_materias_plan = function() {
    var result = get_data("../../api/requests/common/getters/materia/get_all.php?clv_plan=" + ssg("clave_plan", false), true);

    if (result.done) {
        llena_card_materias_plan(result.data);
        modal_actualizar_materia();
        modal_vista_rapida_materia();
    }
};

var carga_cargas_academicas = function() {
    var result = get_data("../../api/requests/common/getters/carga_academica/get_all.php?clv_plan=" + ssg("clave_plan", false), true);

    if (result.done) {
        llena_card_cargas_academicas(result.data);
        modal_edit_carga();
    }
};

var carga_detalles_plan = function() {
    var result = get_data("../../api/requests/common/getters/plan_de_estudio/get_one.php?clv_plan=" + ssg("clave_plan", false), true);
    if (result.done) {
        var datos_plan = result.data;

        sss("data_plan", datos_plan);
        llena_card_datos_plan(datos_plan.id, datos_plan.clave, datos_plan.nombre, datos_plan.anio, datos_plan.nivel, datos_plan.carrera);
    }
};

var llena_card_datos_plan = function (id_plan, clave_plan, nombre_plan, anio_plan, nivel_plan, carrera) {
    $("#clave_plan").text(clave_plan);
    $("#nombre_plan").text(nombre_plan);
    $("#anio_plan").text(anio_plan);
    $("#nivel_plan").text(nivel_plan);

    var tdCarrera = $("#nombre_carrera_plan");
    tdCarrera.text(carrera.nombre);
    tdCarrera.data("idCarreraPlan", carrera.id);
};

var llena_card_cargas_academicas = function (cargas_academicas) {
    var $tbListaCargaAcademicas = $("#tbListaCargasAcademicas")
    $tbListaCargaAcademicas.empty();
    cargas_academicas.forEach(function (carga_academica) {
        var temp = '<tr>\n' +
            '         <td class="periodo_carga" data-int-periodo="' + carga_academica.periodo + '">' + get_txt_periodo(carga_academica.periodo) + '</td>\n' +
            '         <td class="anio_carga">' + carga_academica.anio + '</td>\n' +
            '         <td class="fecha_inicio_carga">' + carga_academica.fecha_inicio + '</td>\n' +
            '         <td class="fecha_fin_carga">' + carga_academica.fecha_final + '</td>\n' +
            '         <td>\n' +
            '           <button type="button" rel="tooltip" title="Editar" class="btn btn-primary btn-link btn-edit-carga" data-id-carga="' + carga_academica.id + '" > <i class="material-icons">edit</i></button>\n' +
            '         </td>\n' +
            '       </tr>';
        $tbListaCargaAcademicas.append(temp);
    });
};

var llena_card_materias_plan = function (materias) {
    var $tbListaMateriasPlan = $("#tbListaMateriasPlan");
    $tbListaMateriasPlan.empty();

    materias.forEach(function (materia) {
        var temp = '<tr>\n' +
            '         <td>'+ materia.clave + '</td>\n' +
            '         <td>' + materia.nombre + '</td>\n' +
            '         <td>' + materia.cuatrimestre + '</td>\n' +
            '         <td>' + materia.tipo + '</td>\n' +
            '         <td>\n' +
            '           <button type="button" rel="tooltip" title="Editar" class="btn btn-primary btn-link btn-editar-materia" data-id-materia="' + materia.id + '">\n' +
            '             <i class="material-icons">edit</i>\n' +
            '           </button>\n' +
            '           <button type="button" rel="tooltip" title="Vista Rápida" class="btn btn-primary btn-link btn-quickview-materia" data-id-materia="' + materia.id + '">\n' +
            '             <i class="material-icons">preview</i>\n' +
            '           </button>\n' +
            '         </td>\n' +
            '       </tr>';
        $tbListaMateriasPlan.append(temp);
    });
};

var obten_clave_plan = function() {
    var $clave_plan = getUrlParameter("clv_plan");

    if (!$clave_plan) {
        rep("Error", "Se debe proporcionar la clave del Plan de Estudios", "e", "Aceptar");
    } else {
        sss("clave_plan", $clave_plan, false);
    }

    return $clave_plan;
};