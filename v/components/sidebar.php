<?php
function print_sidebar(String $tipo="RRHH") {
    switch ($tipo) {
        case "director":
            readfile("../../v/components/html/director/sidebar.html");
            break;
        case "admin":
            readfile("../../v/components/html/admin/sidebar.html");
            break;
        case "RRHH":
            readfile("../../v/components/html/rrhh/sidebar.html");
            break;
        case "profesor":
            readfile("../../v/components/html/profesor/sidebar.html");
            break;
        default:
            break;
    }
}

print_sidebar($tipo);
