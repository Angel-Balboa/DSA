var initd = function () {
    if (ssg("id_carrera") !== null) {
        oculta_tarjetas();
        llena_tarjeta_planesEstudio();
        modal_agregar_carga_academica();
        modal_agregar_materia();
        submit_nueva_materia();
        submit_agrega_plan();
        submit_actualizar_plan();
        submit_nueva_carga_academica();
        submit_actualizar_materia();
    } else {
        rep("Error", "No se ha podido obtener información sobre la carrera", 'e');
    }
};

var modal_agregar_materia = function () {
    $("#btnAgregarMaterialAlPlan").click(function () {
        $("#frmAgregarMateria").trigger("reset");
        $("#mdlAgregarMateria").modal("show");
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

var submit_nueva_carga_academica = function () {
    $("#frmAgregarCargaAcademica").submit(function (e){
        e.preventDefault();

        var result = post_data("../../api/requests/director/creates/carga_academica/crea_nueva_carga_academica.php", $(this).serialize(), true);

        if (result.done) {
            rep("Éxito", result.message, 's');
            $("#mdlAgregarCargaAcademica").modal("hide");
            carga_cargas_academicas($("#hdnClavePlan").val());
        }
    });
};

var modal_agregar_carga_academica = function () {
    $("#btnAgregarCargaAcademica").click(function() {
        $("#frmAgregarCargaAcademica").trigger("reset");
        $("#mdlAgregarCargaAcademica").modal("show");
    });
};

var oculta_tarjetas = function () {
    sessionStorage.removeItem("tarjetas_are_shown");
    $("#divForCargasAcademicas").hide();
    $("#divForMateriasDePlan").hide();
};

var muestra_tarjetas = function () {
    if (ssg("tarjetas_are_shown", false) === null) {
        $("#divForCargasAcademicas").show();
        $("#divForMateriasDePlan").show();
        sss("tarjetas_are_shown", 1, false);
    }
};

var llena_tarjeta_planesEstudio = function() {
    var $tbListaPlanes = $("#tbody_lista_planes");
    $tbListaPlanes.empty();
    var result = get_data("../../api/requests/common/getters/plan_de_estudio/get_all.php?id_carrera=" + ssg("id_carrera", false), true);

    if (result.done) {
        var lista_planes = result.data;
        lista_planes.forEach(plan => $tbListaPlanes.append('<tr>\n' +
            '                          <td id="clave_plan_estudios">' + plan.clave + '</td>\n' +
            '                          <td id="nombre_plan_estudios">' + plan.nombre + '</td>\n' +
            '                          <td>\n' +
            '                            <button type="button" title="Actualizar" class="btn btn-link btn_editar_plan" data-clave-plan="'+ plan.clave +'">\n' +
            '                              <i class="material-icons">\n' +
            '                                edit\n' +
            '                              </i>\n' +
            '                            </button>\n' +
            '                            <button type="button" title="Vista a Detalle" class="btn btn-primary btn-link btn-show-info-plan" data-clave-plan="'+ plan.clave +'">\n' +
            '                              <i class="material-icons">\n' +
            '                                display_settings\n' +
            '                              </i>\n' +
            '                            </button>' +
            '                          </td>\n' +
            '                        </tr>'));

        $(".btn_editar_plan").click(function() {
            var clavePlan = $(this).data("clavePlan");
            var result = get_data("../../api/requests/common/getters/plan_de_estudio/get_one.php?clv_plan=" + clavePlan, true);

            if (result.done) {
                var $frmEditarPlanEstudios = $("#frmEditarPlanEstudios");
                $frmEditarPlanEstudios.find("#hdnClavePlanEstudios").val(clavePlan);
                $frmEditarPlanEstudios.find('#txtEditarNombrePlan').val(result.data.nombre);
                $frmEditarPlanEstudios.find('#sctEditAnioPlan option[value="'+ result.data.anio +'"]').attr("selected", true);
                $frmEditarPlanEstudios.find('#sctEditNivelPlan option:selected').attr('selected', false);
                $frmEditarPlanEstudios.find('#sctEditNivelPlan option[value="'+ result.data.nivel +'"]').attr("selected", true);
                $("#mdlActualizarPlanEstudios").modal("show");
            }
        });

        $(".btn-show-info-plan").click(function () {
            var $clavePlan = $(this).data("clavePlan");
            sss("clave_plan", $clavePlan, false);
            $("#clvPlanEstudio").text($clavePlan);
            $("#hdnClavePlan").val($clavePlan);
            carga_cargas_academicas();
            carga_materias_plan();
            muestra_tarjetas();
        });
    }
};

var carga_materias_plan = function() {
    var clvPlan = ssg("clave_plan", false);
    var result = get_data("../../api/requests/common/getters/materia/get_all.php?clv_plan=" + clvPlan, true);

    if (result.done) {
        llena_tarjeta_materias_plan(result.data);
        modal_actualizar_materia();
        modal_vista_rapida_materia();
    }
};

var llena_tarjeta_materias_plan = function (materias) {
    var $tbListaMateriasPlan = $("#tbListaMateriasPlan");
    $tbListaMateriasPlan.empty();

    materias.forEach(function (materia) {
        var temp = '<tr>\n' +
            '         <td>'+ materia.clave + '</td>\n' +
            '         <td>' + materia.nombre + '</td>\n' +
            '         <td>' + materia.cuatrimestre + '</td>\n' +
            '         <td>' + materia.tipo + '</td>\n' +
            '         <td>' + materia.creditos + '</td>' +
            '         <td>' + materia.horas_totales + '</td>' +
            '         <td class="text-center">\n' +
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
}

var modal_actualizar_materia = function () {
    $(".btn-editar-materia").click(function () {

        var result = get_data("../../api/requests/common/getters/materia/get_one.php?id_materia=" + $(this).data("idMateria"), true);

        if (result.done) {
            var datosMateria = result.data;
            var $frmActualizarDatosMateria = $("#frmActualizarDatosMateria");

            $frmActualizarDatosMateria.find("#hdnIdMateria").val(datosMateria.id);

            $frmActualizarDatosMateria.find("#txtNuevaClaveMateria").val(datosMateria.clave);
            $frmActualizarDatosMateria.find("#txtNuevoNombreMateria").val(datosMateria.nombre);

            // sss("clave_materia", datosMateria.clave, false);

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

var submit_actualizar_materia = function () {
    $("#frmActualizarDatosMateria").submit(function (e) {
        e.preventDefault();
        var result = post_data("../../api/requests/director/updates/materia/actualiza_datos.php", $(this).serialize(), true);

        if (result.done) {
            carga_materias_plan();
            rep("Éxito", result.message, 's');
            $("#mdlActualizarDatosMateria").modal("hide");
        }
    });
};

var carga_cargas_academicas = function () {
    var clvPlan = ssg("clave_plan", false);
    var $tbCargasAcademicasDePlan = $("#tbCargasAcademicasDePlan");
    $tbCargasAcademicasDePlan.empty();

    var result = get_data("../../api/requests/common/getters/carga_academica/get_all.php?clv_plan=" + clvPlan, true);
    if (result.done) {
        var template = '';
        for (i=0; i < result.data.length; i++){
            var carga = result.data[i];
            template = '<tr>\n' +
                '         <td>'+ carga.id +'</td>\n' +
                '         <td>'+ get_txt_periodo(carga.periodo) +'</td>\n' +
                '         <td>'+ carga.anio +'</td>\n' +
                '         <td>'+ carga.fecha_inicio +'</td>\n' +
                '         <td>'+ carga.fecha_final+'</td>\n' +
                '         <td>\n' +
                '           <a href="#" class="btn btn-primary btn-link" title="Actualizar" data-toggle="modal" data-target="#mdlAgregarActualizarCargaAcademica">\n' +
                '             <i class="material-icons">\n' +
                '               edit\n' +
                '             </i>\n' +
                '           </a>\n' +
                '           <a href="#" class="btn btn-primary btn-link" title="Visitar">\n' +
                '             <i class="material-icons">\n' +
                '               launch\n' +
                '             </i>\n' +
                '           </a>\n' +
                '         </td>\n' +
                '       </tr>';
            $tbCargasAcademicasDePlan.append(template);
        }
    }
};

var submit_agrega_plan = function () {
    $("#frmAgregaPlan").submit(function (e) {
        e.preventDefault();

        var result = post_data("../../api/requests/director/creates/plan_de_estudio/crea_plan_de_estudio.php", $(this).serialize(), true);
        if (result.done) {
            llena_tarjeta_planesEstudio();
            $("#AgregarNuevoPlan").modal("hide");
        }
    });
};

var submit_actualizar_plan = function () {
    $("#frmEditarPlanEstudios").submit(function (e) {
        e.preventDefault();

        var result = post_data("../../api/requests/director/updates/plan_de_estudio/actualiza_datos.php", $(this).serialize(), true);

        if (result.done) {
            llena_tarjeta_planesEstudio();
            $("#mdlActualizarPlanEstudios").modal("hide");
        }
    });
};