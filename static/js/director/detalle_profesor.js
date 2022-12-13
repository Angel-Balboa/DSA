var initd = function () {
    obten_id_profesor();

    if (ssg("id_profesor") !== null) {
        llena_tarjeta_profesor();
        llena_tarjeta_productosCientificos();
        llena_tarjeta_carreras_donde_imparte();
        llena_tarjeta_disponibilidad();
    }
}

var llena_tarjeta_disponibilidad = function() {
    var result = get_data("../../api/requests/common/getters/disponibilidad/disponibilidad_profesor.php?id_profesor=" + ssg("id_profesor", false), true);

    if (result.data) {
        var disponibilidad = result.data;

        for(let $dia=0; $dia<5;$dia++) {
            for (let $hora=0; $hora<14; $hora++) {
                if (disponibilidad[$dia][$hora] === 1) {
                    $('.qv-disp[data-day="' + $dia + '"][data-hour="' + $hora + '"]').css("background-color", "green");
                } else {
                    $('.qv-disp[data-day="' + $dia + '"][data-hour="' + $hora + '"]').css("background-color", "white");
                }
            }
        }
    }
};

var llena_tarjeta_carreras_donde_imparte = function () {
    var result = get_data("../../api/requests/common/getters/carrera/imparte_por_profesor.php?id_profesor=" + ssg("id_profesor", false), true);

    if (result.done) {
        var $tblProfesorImparteEn = $("#tblProfesorImparteEn");
        result.data.forEach(carrera => {
            $tblProfesorImparteEn.append('<tr><td>'+ carrera.nombre +'</td></tr>')
        });
    }
};

var llena_tarjeta_productosCientificos = function () {
    $("#rowProductosCientificos").hide();

    if (ssg("isPTC") === true) {

        var result = get_data("../../api/requests/common/getters/producto_cientifico/get_productos_del_profesor.php?id_profesor=" + ssg("id_profesor", false), true);
        if (result.done) {

            agrega_articulos(result.data.Article, $("#journals"));
            agrega_articulos(result.data.InCollection.concat(result.data.InProceedings).concat(result.data.Proceedings), $("#indexados"));
            agrega_articulos(result.data.Conference, $("#congresos"));

            var otrosProductos = ["Book", "Booklet", "InBook", "InCollection", "Manual", "MasterThesis", "Misc", "PhdThesis", "TechReport", "Unpublished"];
            var concOtrosProd = []

            otrosProductos.forEach(producto => {
                concOtrosProd = concOtrosProd.concat(result.data[producto]);
            });

            agrega_articulos(concOtrosProd, $("#todas"));

            $("#rowProductosCientificos").show();
        }
    }
};

var agrega_articulos = function (listaArticulos, targetDiv) {
    var template = '';

    targetDiv.empty();

    if (listaArticulos.length < 1) {
        template = '<div><h6 class="text-warning">Actualmente el profesor no ha publicado ningún producto</h6></div>';
        targetDiv.append(template);
    } else {
        listaArticulos.forEach(producto => {
            const Cite = require('citation-js');
            var apaCite = new Cite(producto.entries._original);
            let output = apaCite.format('bibliography', {
                format: 'html',
                template: 'apa',
                lang: 'en-US'
            });
            template = '<div>' + output + '</div><hr>';
            targetDiv.append(template);
        });
    }
}

var llena_tarjeta_profesor = function () {
    var result = get_data("../../api/requests/common/getters/profesor/get_one_profesor.php?id_profesor=" + ssg("id_profesor", false), true);

    if (result.done) {
        $("#nombre_completo_profesor").text(result.data.nivel_adscripcion + " " + result.data.usuario.nombre + " " + result.data.usuario.apellidos);
        $("#email_profesor").text(result.data.usuario.email);
        $("#telefono_profesor").text(result.data.telefono);
        $("#ext_profesor").text(result.data.extension);
        var txtTipoContrato = (result.data.tipo_contrato === "P.T.C") ? "Profesor de Tiempo Completo" : "Profesor por Asignatura";
        $("#tipo_contrato_profesor").text(txtTipoContrato);
        $("#inicio_contrato_profesor").text(result.data.inicio_contrato);
        $("#fin_contrato_profesor").text(result.data.fin_contrato);

        sss("isPTC", result.data.tipo_contrato === "P.T.C", false);
    }
}

var obten_id_profesor = function () {
   var $id_profesor = getUrlParameter("id_profesor");

   if (!$id_profesor) {
       rep("Error", "Se debe proporcionar el Id del Profesor a consultar", 'e');
   } else {
       sss("id_profesor", $id_profesor, false);
   }
};