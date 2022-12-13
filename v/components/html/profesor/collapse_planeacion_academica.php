<?php
include_once ("../../../../init.php");

use dsa\api\model\planeacion_academica\PlaneacionAcademica;
use dsa\api\model\profesor\Exceptions\ProfesorException;
use dsa\api\model\profesor\Profesor;
use dsa\lib\ValidadorDeEntradas\CValidadorDeEntradas;
use dsa\lib\Exceptions\GeneralException;



$filtroAnio = array();
$filtro = array();

function txtPeriodo($periodo) {
  switch ($periodo) {
    case 1:
      echo "Enero - Abril";
      break;
    case 2:
      echo "Mayo - Agosto";
      break;
    case 3:
      echo "Septiembre - Diciembre";
      break;
  }
}

function printYear($year) {
  echo $year;
}

function print_collapse($year, $arrayPlaneaciones) {
  ?>
  <div class="card">
    <div class="card-header" role="tab" id="heading<?php printYear($year); ?>">
      <h6 class="mb-0">
        <a data-toggle="collapse" href="#collapse<?php printYear($year); ?>" aria-expanded="true" aria-controls="collapse<?php printYear($year); ?>">
          Periodos del año <?php printYear($year); ?>
        </a>
      </h6>
    </div>
    <a id="collapse<?php printYear($year); ?>" class="collapse show" role="tabpanel" aria-labelledby="heading<?php printYear($year); ?>"
       data-parent="#accordion">
      <div class="card-body">
        <div class="table-responsive">
          <table class="table" id="tbl<?php printYear($year); ?>">
            <thead class="text-warning">
            <tr>
              <th>#</th>
              <th>Periodo</th>
              <th></th>
            </tr>
            </thead>
            <tbody id="tb<?php printYear($year); ?>">

            <?php
            $simpleCount = 1;
            foreach ($arrayPlaneaciones as $planeacion) {
              // Array ( [id] => 12 [periodo] => 1 [anio] => 2022 [estado] => iniciada [profesor] => 1 [id_profesor] => 1 )
              ?>
              <tr>
                <td><?php echo $simpleCount++; ?></td>
                <td><?php txtPeriodo($planeacion["periodo"]); ?></td>
                <td class="td-actions text-right">
                  <?php
                  if ($planeacion["estado"] == "edicion" || $planeacion["estado"] == "iniciada") {
                    ?>
                      <a href="edit_planeacion_academica.php?id_planeacion=<?php echo $planeacion["id"]; ?>&estado=<?php echo $planeacion["estado"]; ?>" role="tooltip" title="Editar Planeación" class="text-primary">
                        <i class="material-icons">edit</i>
                      </a>
                  <?php
                  } elseif ($planeacion["estado"] == "aceptada") {
                    ?>
                    <a href="detalle_planeacion_academica.php?id_planeacion=<?php echo $planeacion["id"]; ?>" role="tooltip" title="Ver Planeación" class="text-primary">
                      <i class="material-icons">visibility</i>
                    </a>
                  <?php
                  }
                  ?>
                </td>
              </tr>
            <?php
            }
            ?>
            </tbody>
          </table>
        </div>
      </div>
    </a>
  </div> <!-- end card collapseLastYear-->
<?php
}

try {
  if (isset($_GET["id_profesor"])) {
    $profesor = Profesor::get_profesor_by_id(CValidadorDeEntradas::validarEnteros($_GET["id_profesor"], "Id del profesor", false, false, false,false));
  } elseif (isset($_GET["email_profesor"])) {
    $profesor = Profesor::get_profesor_by_email(CValidadorDeEntradas::validarEmail($_GET["email_profesor"], false));
  } else {
    throw new ProfesorException("Se esperaba el Id o el Email del profesor", -150);
  }

  if (isset($_GET["anio"])) {
    $filtroAnio["anio"] = intval(CValidadorDeEntradas::validarEnteros($_GET["anio"], "Año de la Planeación académica", false, false, false, false));
  } else { // si no se define un año en específico se solicitan los últimos 3 años
    $filtroAnio["anio"] = array();
    $actualYear = intval(date("Y"));
    for($i=$actualYear ; $i > ($actualYear - 3); $i--) {
      $filtroAnio["anio"][] = $i;
    }
  }

  if (isset($_GET["estado"])) {
    $filtro["estado"] = CValidadorDeEntradas::validarOpciones($_GET["estado"], ["iniciada", "edicion", "enviada", "retornada", "finalizada"]);
  }

  if (isset($_GET["periodo"])) {
    $filtro["periodo"] = CValidadorDeEntradas::validarEnteros($_GET["periodo"], "Periodo de la Planeación Académica", false, false, false, false);
  }

  $filtro["profesor"] = $profesor;

  $planeaciones = array();
  foreach ($filtroAnio["anio"] as $anio) {
    $filtro["anio"] = $anio;
    $planXAnio = PlaneacionAcademica::get_all($filtro);

    foreach ($planXAnio as $planeacion) {
      $planeaciones[$anio][] = PlaneacionAcademica::get_PlaneacionAcademica_by_id($planeacion)->get_data();
    }
  }

  $years  = array_keys($planeaciones);

  foreach ($years as $year) {
    print_collapse($year, $planeaciones[$year]);
  }

} catch (GeneralException $e) {
  http_response_code(500);
  exit;
}
?>
