var initd = function (id_profesor) {

    sss("id_profesor", id_profesor, false);
    llena_accordion();
}

var llena_accordion = function () {
    $.get("../components/html/profesor/collapse_planeacion_academica.php?id_profesor=" + ssg("id_profesor", false), function (data){
        $("#accordion").append(data);
        console.log("xd");
    }).fail(function() {
        console.log("ERROR AAAAAAAA0");
        rep("Error", "No se ha podido obtener los datos de las Planeaciones académicas");
    });
}