<?php

include_once("../../init.php");

use dsa\api\model\profesor\Profesor;
use dsa\api\model\carrera\Carrera;

?>
<div id="perfil_profesor" class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header card-header-primary">
                <h4>Profesor</h4>
                <p class="card-category">Perfil de profesor</p>
            </div>
            <div class="card-body">

                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="bmd-label-floating">Nivel de Adscripción</label>
                            <select id="perfil_profesor_nivel_adscripcion" name="perfil_profesor_nivel_adscripcion" class="form-control">
                                <?php
                                foreach (Profesor::obten_niveles_de_ascripcion() as $key => $value) {
                                    echo "<option value=\"$key\">$value</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div><hr>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="bmd-label-floating">Tipo de Contrato</label>
                            <select id="perfil_profesor_tipo_contrato" name="perfil_profesor_tipo_contrato" class="form-control">
                                <?php
                                    foreach (Profesor::obten_tipos_contrato() as $key => $value) {
                                        echo "<option value=\"$key\">$value</option>";
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                </div><hr>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="bmd-label-floating">Categoría</label>
                            <select id="perfil_profesor_categoria" name="perfil_profesor_categoria" class="form-group">
                                <?php
                                    foreach (Profesor::obten_categorias() as $key => $value) {
                                        echo "<option value=\"$key\">$value</option>";
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                </div><hr>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-check-label">Fecha de inicio de Contrato</label>
                            <input type="date" class="form-control" id="perfil_profesor_inicio_contrato" name="perfil_profesor_inicio_contrato" required="true">
                        </div>
                    </div>
                </div><hr>
                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label class="bmd-label" for="perfil_profesor_fin_contrato">Fecha de fin de Contrato</label>
                            <input type="date" class="form-control" id="perfil_profesor_fin_contrato" name="perfil_profesor_fin_contrato" required>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="bmd-label" for="perfil_profesor_contrato_indefinido">Contrato Indefinido</label>
                            <input type="checkbox" class="form-check-input" id="perfil_profesor_contrato_indefinido" name="perfil_profesor_contrato_indefinido" data-toggle="toggle">
                        </div>
                    </div>
                </div><hr>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="bmd-label" for="perfil_profesor_carrera_adscripcion">Carrera de Adscripción</label>
                            <select class="form-control" id="perfil_profesor_carrera_adscripcion" name="perfil_profesor_carrera_adscripcion">
                                <?php
                                    $tmpCarrera = null;
                                    foreach (Carrera::get_all_carreras() as $carrera) {
                                        $tmpCarrera = $carrera->get_data();
                                        echo "<option value=\"" . $tmpCarrera["id"] . "\">" . $tmpCarrera["nombre"] . "</option>";
                                    }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>

            </div> <!-- end card body -->
        </div> <!-- end card-->
    </div> <!-- end col -->
</div> <!-- end row -->
