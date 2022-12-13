var initd = function (id_profesor) {

    sss("id_profesor", id_profesor, false);

    change_color_on_click();
    get_disponibilidad_profesor();
};

var get_disponibilidad_profesor = function () {
    var $id_profesor = ssg("id_profesor", false);

    // $('.btn-disponibilidad[data-day="0"][data-hour="0"]').css("background-color", "green");

    var result = get_data("../../api/requests/common/getters/disponibilidad/disponibilidad_profesor.php?id_profesor=" + $id_profesor, true);

    if (result.done) {
        var disponibilidad = result.data;

        for(let $dia=0; $dia<5;$dia++) {
            for (let $hora=0; $hora<14; $hora++) {
                if (disponibilidad[$dia][$hora] === 1) {
                    $('.btn-disponibilidad[data-day="' + $dia + '"][data-hour="' + $hora + '"]').css("background-color", "green");
                }
            }
        }
    }
}

var change_color_on_click = function () {
    $(".btn-disponibilidad").click(function () {
        var $dia = parseInt($(this).data("day"));
        var $hora = parseInt($(this).data("hour"));

        var $url = "../../api/requests/profesor/updates/disponibilidad/cambia_disponibilidad.php";

        var dataSubmit = { 'id_profesor': ssg("id_profesor", false),
            'dia': $dia,
            'hora': $hora
        };

        console.log(dataSubmit);

        var result = post_data($url, dataSubmit, true);

        if (result.done) {
            if (this.style.backgroundColor === "green") {
                this.style.backgroundColor = "";
                nfy("Se ha quitado la disponibilidad", 'w');
            } else {
                this.style.backgroundColor = "green";
                nfy("Se ha agregado la disponibilidad", "i");
            }
        }
    });
};