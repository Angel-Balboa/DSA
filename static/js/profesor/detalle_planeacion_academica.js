var initd = function () {
    let $id_planeacion = getUrlParameter("id_planeacion");

    if ($id_planeacion !== false) {
        obten_datos_planeacion($id_planeacion);
    } else {
        rep("Error", "Se esperaba el Id de la planeación académica a consultar", 'e');
    }
};

var obten_datos_planeacion = function (id_planeacion) {

    var result = get_data("../../api/requests/common/getters/planeacion_academica/obten_datos_completos_planeacion.php?id_profesor=" + ssg("id_profesor", false) + "&id_planeacion=" + id_planeacion, true);

    if (result.done) {

        $("#periodo_anio").text(get_txt_periodo(result.data.planeacion.periodo) + ' ' + result.data.planeacion.anio);

        llena_tarjeta_gestion(result.data.gestion);
        llena_tarjeta_capacitacion(result.data.capacitacion);
        llena_tarjeta_asesorias(result.data.asesorias);
        llena_card_promocion(result.data.promocion);
        llena_tarjeta_vinculacion(result.data.vinculacion);
        llena_tarjeta_investigacion(result.data.investigacion);
    }
};

var llena_tarjeta_investigacion = function (actividadesInvestigacion) {
    $("#total_h_investigacion").text("NaN");
    $("#total_h_investigacion_2").text("NaN");

    actividadesInvestigacion.actividades.forEach(function(actividad) {
        var template = '<div class="row text-center">\n' +
            '             <div class="col-lg-3">\n' +
            '               <p>' + actividad.actividad + '</p>\n' +
            '             </div>\n' +
            '             <div class="col-lg-2">\n' +
            '               <p>' + actividad.tipo + '</p>\n' +
            '             </div>\n' +
            '             <div class="col-lg-2">\n' +
            '               <p>'+ actividad.avance_actual +'</p>\n' +
            '             </div>\n' +
            '             <div class="col-lg-2">\n' +
            '               <p>' + actividad.avance_esperado + '</p>\n' +
            '             </div>\n' +
            '             <div class="col-lg-2">\n' +
            '               <p>' + actividad.fecha_termino + '</p>\n' +
            '             </div>\n' +
            '           </div><!-- end row -->';
        $("#cardBody-investigation").append(template);
    });
}

var llena_tarjeta_vinculacion = function(actividadesVinculacion) {
    $("#total_h_vinculacion").text(actividadesVinculacion.horas_totales);

    actividadesVinculacion.actividades.forEach(function (actividad) {
        var template = '<div class="row text-center" data-id-actividad-vinculacion="' + actividad.id + '">\n' +
            '             <div class="col-lg-3">\n' +
            '               <p>' + actividad.descripcion + '</p>\n' +
            '             </div>\n' +
            '             <div class="col-lg-3">\n' +
            '               <p>' + actividad.empresa_receptora + '</p>\n' +
            '             </div>\n' +
            '             <div class="col-lg-2">\n' +
            '               <p>' + actividad.horas + '</p>\n' +
            '             </div>\n' +
            '             <div class="col-lg-3">\n' +
            '               <p>' + actividad.evidencia + '</p>\n' +
            '             </div>\n' +
            '           </div><!-- end row -->';
        $("#cardBody-vinculacion").append(template);
    });
};

var llena_card_promocion = function(actividadPromocion) {
    $("#total_h_promocion").text(actividadPromocion.actividades[0].horas);
    $("#horas_promocion").text(actividadPromocion.actividades[0].horas);
};

var llena_tarjeta_asesorias = function (actividesAsesorias) {

    $("#alumnos_institucional_estancia").text(actividesAsesorias.institucional_estancia);
    $("#alumnos_institucional_estadia").text(actividesAsesorias.institucional_estadia);
    $("#alumnos_empresarial_estancia").text(actividesAsesorias.empresarial_estancia);
    $("#alumnos_empresarial_estadia").text(actividesAsesorias.empresarial_estadia);

    var horas_institucional_estancia = (3 * actividesAsesorias.institucional_estancia);
    $("#horas_institucional_estancia").text(horas_institucional_estancia);

    var horas_institucional_estadia = (5 * actividesAsesorias.institucional_estadia);
    $("#horas_institucional_estadia").text(horas_institucional_estadia);

    var horas_empresarial_estancia = (5 * actividesAsesorias.empresarial_estancia);
    $("#horas_empresarial_estancia").text(horas_empresarial_estancia);

    var horas_empresarial_estadia = (20 * actividesAsesorias.empresarial_estadia);
    $("#horas_empresarial_estadia").text(horas_empresarial_estadia);

    var horas_totales = horas_institucional_estancia + horas_institucional_estadia + horas_empresarial_estancia + horas_empresarial_estadia;

    $("#total_h_asesorias").text(horas_totales);
};

var llena_tarjeta_gestion = function (actividadesGestion) {
  $("#total_h_gestion").text(actividadesGestion.horas_totales);
  actividadesGestion.actividades.forEach(function (actividad) {
      var template = '<div class="row text-center" data-id-actividad-gestion="' + actividad.id + '">\n' +
          '             <div class="col-lg-4">\n' +
          '               <p>' + actividad.descripcion + '</p>\n' +
          '             </div>\n' +
          '             <div class="col-lg-3">\n' +
          '               <p>' + actividad.horas + '</p>\n' +
          '             </div>\n' +
          '             <div class="col-lg-4">\n' +
          '               <p>' + actividad.evidencia + '</p>\n' +
          '             </div>\n' +
          '           </div><!-- end row -->';
      $("#cardBody-gestion-academica").append(template);
  });
};

var llena_tarjeta_capacitacion = function (actividadesCapacitacion) {
    $("#total_h_capacitacion").text(actividadesCapacitacion.horas_totales);
    actividadesCapacitacion.actividades.forEach(function (actividad) {
        var template = '<div class="row text-center" data-id-actividad-capacitacion="' + actividad.id + '">\n' +
            '             <div class="col-lg-4">\n' +
            '               <p>'+ actividad.descripcion + '</p>\n' +
            '             </div>\n' +
            '             <div class="col-lg-3">\n' +
            '               <p>' + actividad.horas + '</p>\n' +
            '             </div>\n' +
            '             <div class="col-lg-4">\n' +
            '               <p>'+ actividad.evidencia +'</p>\n' +
            '             </div>\n' +
            '           </div><!-- end row -->';
        $("#cardBody-capacitacion").append(template);
    });
};