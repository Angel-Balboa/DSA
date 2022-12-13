<?php
include_once("../../init.php");

use dsa\api\model\carrera\Carrera;
?>
<div class="col-md-4">
    <div class="card">
        <div class="card-header card-header-primary">
            <h4>Profesor</h4>
            <p class="card-category">Carreras en donde imparte materias</p>
        </div>
        <div id="profesor_imparteen" class="card-body">
                <?php

                foreach (Carrera::get_all_carreras() as $carrera) {
                    $data_carrera = $carrera->get_data();

                    echo "<div class=\"row\"><div class=\"col-md-12\"><div class=\"form-group\"><input type=\"checkbox\" name=\"carrera_" . $data_carrera["id"] . "\" value=\"" . $data_carrera["id"] . "\"> " . $data_carrera["nombre"] . "</div></div></div><hr>";
                }
                ?>
        </div> <!-- end card body -->
    </div> <!-- end card-->
</div> <!-- end col -->


