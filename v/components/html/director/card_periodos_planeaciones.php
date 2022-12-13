<?php

include_once "../../../../init.php";

use dsa\lib\Exceptions\GeneralException;
use dsa\lib\Utils\DateUtils;
use dsa\api\controller\sesion\CSesion;

try {
    $sesion = CSesion::inits();

    $currentYear = DateUtils::current_year();
    $startYear = $currentYear - 2;
    $endYear = (DateUtils::current_month() > 10) ? ($currentYear+1) : $currentYear;

    for($i=$endYear; $i>= $startYear; $i--){
        ?>
        <!-- begin accordion -->
        <div id="accordion<?php echo $i; ?>" role="tablist">
            <div class="card">
                <div class="card-header" role="tab" id="headingPlaneaciones<?php echo $i; ?>">
                    <h6 class="mb-0">
                        <a data-toggle="collapse" href="#collapsePlaneaciones<?php echo $i; ?>" aria-expanded="true"
                           aria-controls="collapsePlaneaciones<?php echo $i; ?>">
                            Periodos del año <span id="anio_<?php echo $i; ?>"><?php echo $i; ?></span>
                        </a>
                    </h6>
                </div><!-- end card-header -->
                <a id="collapsePlaneaciones<?php echo $i; ?>" class="collapse <?php if ($i == $currentYear) echo "show"; else "hide"; ?>" role="tabpanel"
                   aria-labelledby="headingPlaneaciones<?php echo $i; ?>" data-parent="#accordion<?php echo $i; ?>">
                    <div class="card-body table-responsive">
                        <table class="table table-hover">
                            <thead class="text-primary">
                            <tr>
                                <th>#</th>
                                <th>Periodo</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>1</td>
                                <td>Enero-Abril</td>
                                <td>
                                    <button type="button" class="btn btn-primary btn-link btn-get-planeaciones" title="Ver Planeaciones" data-periodo="1" data-anio="<?php echo $i; ?>">
                                        <i class="material-icons">
                                            <span class="material-icons">fact_check</span>
                                        </i>
                                    </button>
                                </td>
                            </tr>
                            <?php
                            if ($i < $currentYear || DateUtils::current_month() > 3 ) {
                                ?>
                                <tr>
                                    <td>2</td>
                                    <td>Mayo-Agosto</td>
                                    <td>
                                        <button type="button" class="btn btn-primary btn-link btn-get-planeaciones" title="Ver Planeaciones" data-periodo="2" data-anio="<?php echo $i; ?>">
                                            <i class="material-icons">
                                                <span class="material-icons">fact_check</span>
                                            </i>
                                        </button>
                                    </td>
                                </tr>
                                <?php
                            }

                            if ($i < $currentYear || DateUtils::current_month() > 6) {
                                ?>
                                <tr>
                                    <td>3</td>
                                    <td>Septiembre-Diciembre</td>
                                    <td>
                                        <button type="button" class="btn btn-primary btn-link btn-get-planeaciones" title="Ver Planeaciones" data-periodo="3" data-anio="<?php echo $i; ?>">
                                            <i class="material-icons">
                                                <span class="material-icons">fact_check</span>
                                            </i>
                                        </button>
                                    </td>
                                </tr>
                                <?php
                            }
                            ?>
                            </tbody>
                        </table>
                    </div><!-- end interal card-body -->
                </a>
            </div><!-- end card -->
        </div><!-- end accordion -->
        <?php
    }

} catch (GeneralException $e) {
    ?>
    <div>Error</div>
    <?php
}
