var initd = function() {
    if (ssg("id_carrera", false !== null)) {
        llena_materias_carrera();
    } else {
        rep("Error", "No se ha podido obtener el Id de la carrera", 'e');
    }
};


var llena_materias_carrera = function() {
    var $tbListaPlanes = $("#tbody_lista_planes");
    $tbListaPlanes.empty();
    var result = get_data("../../api/requests/common/getters/InnerCarga/get_vista.php?id_carrera=" + ssg("id_carrera", false), true);
    console.log(result.data[1].clave);



    /*var result = get_data("../../api/requests/common/getters/plan_de_estudio/get_all.php?id_carrera=" + ssg("id_carrera", false), true);

    if (result.done) {
        var lista_planes = result.data;
        lista_planes.forEach(plan => $tbListaPlanes.append('<tr>\n' +
            '                          <td id="clave_plan_estudios">' + plan.clave + '</td>\n' +
            '                          <td id="nombre_plan_estudios">' + plan.nombre + '</td>\n' +
            '                          <td>\n' +
            '                            <button type="button" rel="tooltip" title="Editar" class="btn btn-primary btn-link btn_editar_plan" data-clave-plan="'+ plan.clave +'">\n' +
            '                              <i class="material-icons">edit</i>\n' +
            '                            </button>' +
            '                            <a href="detalle_plan_estudio.php?clv_plan=' + plan.clave + '" rel="tooltip" title="Detalle" class="btn btn-primary btn-link btn_detalle_plan">\n' +
            '                              <i class="material-icons">visibility</i>\n' +
            '                            </a>\n' +
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
    }*/

}