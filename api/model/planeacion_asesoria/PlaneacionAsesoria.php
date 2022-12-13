<?php

namespace dsa\api\model\planeacion_asesoria;

use dsa\api\model\planeacion_academica\Exceptions\PlaneacionAcademicaException;
use dsa\api\model\planeacion_academica\PlaneacionAcademica;
use dsa\api\model\planeacion_asesoria\Exceptions\PlaneacionAsesoriaException;
use dsa\api\model\planeacion_asesoria\Exceptions\PlaneacionAsesoriaNoExisteException;
use dsa\api\model\planeacion_asesoria\Exceptions\PlaneacionAsesoriaYaExistenteException;
use dsa\lib\conexionSQL\COperacionesSQL;
use dsa\lib\conexionSQL\Exceptions\CConnexionException;
use dsa\lib\conexionSQL\Exceptions\SQLTransactionException;

class PlaneacionAsesoria
{
    private ?array $data;

    private bool $isNew;
    private ?COperacionesSQL $SqlOp;
    private ?array $tmpData;

    private function __construct(?int $id=null, ?int $id_planeacion_academica=null, bool $isNew=false, ?array $newData=null, ?COperacionesSQL &$cop=null) {

        $this->SqlOp = COperacionesSQL::getInstance($cop);

        $this->data = null;
        $this->isNew = $isNew;
        $this->tmpData = null;

        if (!$isNew) {
            if (!is_null($id)) {
                if (!$this->_get_planeacionAsesoria_by_id($id)) {
                    throw new PlaneacionAsesoriaNoExisteException("La planeacion de asesorias con Id: $id, no existe", 14025);
                }
            }

            if (!is_null($id_planeacion_academica)) {
                if (!$this->_get_planeacionAsesoria_by_planeacionAcademica($id_planeacion_academica)) {
                    throw new PlaneacionAsesoriaNoExisteException("La planeación de asesorías no existe para la Planeacion con ID: $id_planeacion_academica", 13034);
                }
            }
        } else {
            $this->tmpData = $newData;
        }
    }

    /**
     * @param int $id
     * @param COperacionesSQL|null $cop
     * @return PlaneacionAsesoria
     * @throws PlaneacionAsesoriaNoExisteException
     */
    public static function get_planeacionAsesoria_by_id(int $id, ?COperacionesSQL &$cop=null) : PlaneacionAsesoria {
        return new PlaneacionAsesoria($id, null, false, null, $cop);
    }

    /**
     * @param PlaneacionAcademica $planeacionAcademica
     * @param COperacionesSQL|null $cop
     * @return PlaneacionAsesoria
     * @throws PlaneacionAcademicaException
     * @throws PlaneacionAsesoriaNoExisteException
     */
    public static function get_planeacionAsesoria_by_planeacionAcademica(PlaneacionAcademica $planeacionAcademica, ?COperacionesSQL &$cop=null) : PlaneacionAsesoria {
        return new PlaneacionAsesoria(null, $planeacionAcademica->get_data("id"), false, null, $cop);
    }

    /**
     * @param PlaneacionAcademica $planeacionAcademica
     * @param int $institucional_estancia
     * @param int $institucional_estadia
     * @param int $empresarial_estancia
     * @param int $empresarial_estadia
     * @param COperacionesSQL|null $cop
     * @return PlaneacionAsesoria|null
     * @throws PlaneacionAcademicaException
     * @throws PlaneacionAsesoriaException
     * @throws PlaneacionAsesoriaNoExisteException
     */
    public static function crea_planeacion_asesoria(PlaneacionAcademica $planeacionAcademica, int $institucional_estancia=0, int $institucional_estadia=0, int $empresarial_estancia=0, int $empresarial_estadia=0, ?COperacionesSQL &$cop=null) : ?PlaneacionAsesoria {
        $newData = array("institucional_estancia" => $institucional_estancia, "institucional_estadia" => $institucional_estadia, "empresarial_estancia" => $empresarial_estancia, "empresarial_estadia" => $empresarial_estadia, "planeacion_academica" => $planeacionAcademica->get_data("id"));
        $tmpPlaneacionAsesoria = new PlaneacionAsesoria(null, null,true, $newData, $cop);

        if ($tmpPlaneacionAsesoria->_save()) {
            return PlaneacionAsesoria::get_planeacionAsesoria_by_id($tmpPlaneacionAsesoria->get_data("id"));
        }
        return null;
    }

    /**
     * @param null $filtro
     * @return array|mixed
     * @throws PlaneacionAsesoriaException
     */
    public function get_data($filtro=null) {
        $allowed_keys = ["id", "institucional_estancia", "institucional_estadia", "empresarial_estancia", "empresarial_estadia", "id_planeacion_academica", "planeacion_academica"];

        if (!is_null($filtro)) {
            if (!is_array($filtro)) {
                if (!in_array($filtro, $allowed_keys)) throw new PlaneacionAsesoriaException("La llave $filtro no es válida, verifica la documentación", 13069);

                $dataReturn = $this->_data_return($filtro);
            } else {
                $dataReturn = array();
                foreach($filtro as $key) {
                    if (!in_array($key, $allowed_keys)) throw new PlaneacionAsesoriaException("La llave $key no es válida, verifica la documentación", 13076);

                    $dataReturn[$key] = $this->_data_return($key);
                }
            }
        } else {
            $dataReturn = $this->data;
            $dataReturn["planeacion_academica"] = $this->_data_return("planeacion_academica");
        }
        return $dataReturn;
    }

    public function actualiza_datos(array $newData) : bool {
        $allowed_keys = ["institucional_estancia", "institucional_estadia", "empresarial_estancia", "empresarial_estadia"];
        $tmpArray = array();

        foreach(array_keys($newData) as $key) {
            if (!in_array($key, $allowed_keys)) throw new PlaneacionAsesoriaException("La llave $key no es válida, verifica la documentación", 13108);

            $tmpArray[$key] = $newData[$key] ?? $this->data[$key];
            if (!is_numeric($tmpArray[$key]) || ($tmpArray[$key] < 0 || $tmpArray[$key] > 99)) {
                throw new PlaneacionAsesoriaException("El valor de $key no es válido, verifica la documentación", 13112);
            }
        }

        foreach ($allowed_keys as $key) {
            if (!isset($tmpArray[$key])) {
                $tmpArray[$key] = $this->data[$key];
            }
        }
        $this->tmpData = $tmpArray;
        return $this->_save();
    }

    /**
     * @return bool
     * @throws PlaneacionAsesoriaException
     */
    private function _save() : bool {
        if ($this->isNew) {
            $this->_valida_datos_para_guardar();
            if (!$this->_crea_nueva_planeacion_asesoria_db()) {
                throw new PlaneacionAsesoriaException("No se ha podido guardar la Planeación de Asesorias", 13106);
            }
        } else {
            if (!$this->_actualiza_datos_de_planeacionAsesoria()) {
                throw new PlaneacionAsesoriaException("No se ha podido actualizar la Planeación de Asesorias", 13131);
            }
        }
        return true;
    }

    private function _actualiza_datos_de_planeacionAsesoria() : bool {
        $sqlquery = "UPDATE planeacion_asesoria SET institucional_estancia = ?, institucional_estadia =?, empresarial_estancia = ?, empresarial_estadia = ? WHERE planeacion_asesoria.id = ?";

        try {
            $ban = $this->SqlOp->exec($sqlquery, "iiiii", [$this->tmpData["institucional_estancia"], $this->tmpData["institucional_estadia"], $this->tmpData["empresarial_estancia"], $this->tmpData["empresarial_estadia"], $this->data["id"]]) == 1;
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }
        return $ban;
    }

    private function _crea_nueva_planeacion_asesoria_db() : bool {
        $sqlquery = "INSERT INTO planeacion_asesoria(institucional_estancia, institucional_estadia, empresarial_estancia, empresarial_estadia, planeacion_academica) VALUES (?, ?, ?, ?, ?)";
        try {
            $ban = $this->SqlOp->exec($sqlquery, "iiiii", [$this->tmpData["institucional_estancia"], $this->tmpData["institucional_estadia"], $this->tmpData["empresarial_estancia"], $this->tmpData["empresarial_estadia"], $this->tmpData["planeacion_academica"]]) == 1;

            if ($ban) $this->data["id"] = $this->SqlOp->obtenerUltimoIdInsertado();
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }

        return $ban;
    }

    private function _valida_datos_para_guardar() : bool {
        $keys = ["institucional_estancia", "institucional_estadia", "empresarial_estancia", "empresarial_estadia"];

        if ($this->_check_planeacionAsesoria_existe($this->tmpData["planeacion_academica"])) {
            throw new PlaneacionAsesoriaYaExistenteException("Ya existe una planeación de asesorias para la Planeación académica", 13134);
        }

        foreach ($keys as $key) {
            if ($this->tmpData[$key] < 0 || $this->tmpData[$key] > 99) {
                throw new PlaneacionAsesoriaException("El valor de $key debe ser mayor a 0 y menor a 99", 13117);
            }
        }
        return true;
    }

    private function _check_planeacionAsesoria_existe(int $id_planeacion) : bool {
        $sqlquery = "SELECT COUNT(*) as existe FROM planeacion_asesoria WHERE planeacion_academica = ?";

        try {
            $ban = $this->SqlOp->exec($sqlquery, "i", [$id_planeacion])[0]["existe"] > 0;
        } catch (CConnexionException | SQLTransactionException $e) {
            die("Error inesperado...XD");
        }

        return $ban;
    }

    private function _data_return($key) {
        if ($key == "id_planeacion_academica" || $key == "planeacion_academica") {
            $dataReturn = $this->data["id_planeacion_academica"];
        } else {
            $dataReturn = $this->data[$key];
        }

        return $dataReturn;
    }

    private function _get_planeacionAsesoria_by_planeacionAcademica(int $id_planeacion) : bool {
        $ban = false;
        $sqlquery = "SELECT planeacion_asesoria.id as id, planeacion_asesoria.institucional_estancia as institucional_estancia, planeacion_asesoria.institucional_estadia as institucional_estadia, planeacion_asesoria.empresarial_estancia as empresarial_estancia, planeacion_asesoria.empresarial_estadia as empresarial_estadia FROM planeacion_asesoria WHERE planeacion_asesoria.planeacion_academica = ?";
        try {
            $res = $this->SqlOp->exec($sqlquery, "i", [$id_planeacion]);

            if (count($res) == 1) {
                $this->_asigna_campos_privados($res[0]["id"], $res[0]["institucional_estancia"], $res[0]["institucional_estadia"], $res[0]["empresarial_estancia"], $res[0]["empresarial_estadia"], $id_planeacion);
                $ban = true;
            }
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }
        return $ban;
    }

    private function _get_planeacionAsesoria_by_id(int $id) : bool {
        $ban = false;
        $sqlquery = "SELECT planeacion_asesoria.institucional_estancia as institucional_estancia, planeacion_asesoria.institucional_estadia as institucional_estadia, planeacion_asesoria.empresarial_estancia as empresarial_estancia, planeacion_asesoria.empresarial_estadia as empresarial_estadia, planeacion_asesoria.planeacion_academica as planeacion_academica FROM planeacion_asesoria WHERE planeacion_asesoria.id = ?";
        try {
            $res = $this->SqlOp->exec($sqlquery, "i", [$id]);

            if (count($res) == 1) {
                $this->_asigna_campos_privados($id, $res[0]["institucional_estancia"], $res[0]["institucional_estadia"], $res[0]["empresarial_estancia"], $res[0]["empresarial_estadia"], $res[0]["planeacion_academica"]);
                $ban = true;
            }
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }
        return $ban;
    }

    private function _asigna_campos_privados(int $id, int $institucional_estancia, int $institucional_estadia, int $empresarial_estancia, int $empresarial_estadia, int $planeacion_academica) {
        $this->data["id"] = $id;
        $this->data["institucional_estancia"] = $institucional_estancia;
        $this->data["institucional_estadia"] = $institucional_estadia;
        $this->data["empresarial_estancia"] = $empresarial_estancia;
        $this->data["empresarial_estadia"] = $empresarial_estadia;
        $this->data["id_planeacion_academica"] = $planeacion_academica;
    }
}