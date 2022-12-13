<?php

namespace dsa\api\model\producto_cientifico;

use dsa\api\model\producto_cientifico\Exceptions\ProductoCientificoException;
use dsa\api\model\producto_cientifico\Exceptions\ProductoCientificoNoExisteException;
use dsa\api\model\profesor\Exceptions\LlaveDeBusquedaIncorrectaException;
use dsa\lib\conexionSQL\COperacionesSQL;
use dsa\lib\conexionSQL\Exceptions\CConnexionException;
use dsa\lib\conexionSQL\Exceptions\SQLTransactionException;
use dsa\lib\Utils\BibtexUtils;
use dsa\lib\Utils\DataChecker;
use RenanBr\BibTexParser\Listener;
use RenanBr\BibTexParser\Parser;
use RenanBr\BibTexParser\Processor;


require_once $GLOBALS["dsa_root"] . '/vendor/autoload.php';

class ProductoCientifico
{
    private ?array $data;

    private bool $isNew;
    private ?COperacionesSQL $SqlOp;
    private ?array $tmpData;

    private function __construct(?int $id=null, bool $isNew=false, ?array $newData=null, ?COperacionesSQL &$cop=null) {
        $this->data = ["id" => null, "entries" => array()];
        $this->isNew = $isNew;
        $this->tmpData = null;
        $this->SqlOp = COperacionesSQL::getInstance($cop);

        if (!$isNew){
            if (!is_null($id)) {
                if (!$this->_get_productoCientifico_by_id($id)) {
                    throw new ProductoCientificoNoExisteException("El producto científico con Id: $id no existe", 15025);
                }
            }
        } else {
            $this->tmpData = $newData;
        }
    }

    public static function get_productoCientifico_by_id(int $id, ?COperacionesSQL &$cop=null) : ProductoCientifico {
        return new ProductoCientifico($id, false, null, $cop);
    }

    /***
     * @param String $citation_key
     * @param array $newData
     * @param COperacionesSQL|null $cop
     * @return ProductoCientifico|null
     * @throws ProductoCientificoException
     * @throws ProductoCientificoNoExisteException
     */
    public static function crea_productoCientifico(array $newData, ?COperacionesSQL &$cop=null) : ?ProductoCientifico {
        $tmpProductoCient = new ProductoCientifico(null, true, ["bibtex" => BibtexUtils::crea_bibtex($newData)], $cop);

        if ($tmpProductoCient->_save()) {
            return ProductoCientifico::get_productoCientifico_by_id($tmpProductoCient->get_data("id"));
        }
        return null;
    }

    /**
     * @param array|null $filtro
     * @return array
     * @throws ProductoCientificoException
     */
    public static function get_all(?array $filtro=null) : array {
        $tmpProductoCientifico = new ProductoCientifico(null, false, null);
        return $tmpProductoCientifico->_get_all_productoCientifico($tmpProductoCientifico->_create_sqlquery($filtro));
    }

    private function _get_all_productoCientifico(String $query) : array {
        $ids = array();
        try {
            $res = $this->SqlOp->exec($query);

            foreach ($res as $r) {
                $ids[] = $r["id"];
            }
        } catch (CConnexionException | SQLTransactionException $e) {
            $ids = array();
        }
        return $ids;
    }

    private function _create_sqlquery(?array $filtro) : String {
        $sqlquery = "SELECT producto_cientifico.id FROM producto_cientifico";

        if (!is_null($filtro)) {

            if (!DataChecker::isAssoc($filtro)) {
                throw new ProductoCientificoException("Se esperaba un array asociativo como filtro con llaves válidas", 15088);
            } else {
                $allowedKeys = ["profesor", "contains"];
                $filtroKeys = array_keys($filtro);

                $sqlquery .= " WHERE ";
                $tmpArray = array();
                foreach ($filtroKeys as $key) {
                    if (!in_array($key, $allowedKeys)) {
                        throw new ProductoCientificoException("La llave $key no es permitida, verifica la documentación", 15095);
                    } else {
                        switch ($key) {
                            case "profesor":
                                $sqlquery = str_replace("WHERE", ", coautor WHERE ", $sqlquery);
                                $tmpArray[] = $this->_block_query_profesor($filtro["profesor"]);
                                break;
                            case "contains":
                                $tmpArray[] = "bibtex LIKE '%" . $filtro["contains"] . "%'";
                                break;
                        }
                    }
                }
                $sqlquery .= implode(" AND ", $tmpArray);
            }
        }

        return $sqlquery;
    }

    private function _block_query_profesor($block) : String {
        $tmpStr = "";
        if (DataChecker::check_instance_of($block, "Profesor")) {
            $tmpStr = "producto_cientifico.id = coautor.producto and coautor.profesor = " . $block->get_data("id");
        } elseif (is_numeric($block)) {
            $tmpStr = "producto_cientifico.id = coautor.producto and coautor.profesor = " . $block;
        } else {
            throw new ProductoCientificoException("Se esperaba una instancia de tipo Profesor o el Id del profesor");
        }

        return $tmpStr;
    }

    public function actualiza_datos(array $newData) : bool {
        $this->tmpData = ["bibtex" => BibtexUtils::crea_bibtex($newData)];
        return $this->_save();
    }

    private function _save() : bool {
        if ($this->isNew) {
            if (!$this->_crea_productoCientifico_db()) {
                throw new ProductoCientificoException("No se ha podido crear el producto cientifico", 15072);
            }
        } else {
            if (!$this->_actualiza_datos_productoCientifico()) {
                throw new ProductoCientificoException("No se ha podido actualizar el producto cientifo", 15079);
            }
        }

        return true;
    }

    private function _actualiza_datos_productoCientifico() : bool {
        $sqlquery = "UPDATE producto_cientifico SET bibtex = ? WHERE producto_cientifico.id = ?";
        try {
            $ban = $this->SqlOp->exec($sqlquery, "si", [$this->tmpData["bibtex"], $this->data["id"]]) == 1;
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }
        return $ban;
    }

    /**
     * @return bool
     */
    private function _crea_productoCientifico_db() : bool {
        $sqlquery = "INSERT INTO producto_cientifico (bibtex) VALUES (?)";
        try {
            $ban = $this->SqlOp->exec($sqlquery, "s", [$this->tmpData["bibtex"]]);
            if ($ban) $this->data["id"] = $this->SqlOp->obtenerUltimoIdInsertado();
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }

        return $ban;
    }

    /**
     * @param null $filtro
     * @return array|mixed|null
     * @throws LlaveDeBusquedaIncorrectaException
     */
    public function get_data($filtro=null) {
        $data_keys = array_keys($this->data);
        $bibtex_keys = array_keys($this->data["entries"]);
        if (!is_null($filtro)) {
            if (!is_array($filtro)) {
                if (in_array($filtro, $data_keys)) {
                    $dataReturn = $this->data[$filtro];
                } elseif (in_array($filtro, $bibtex_keys)) {
                    $dataReturn = $this->data["entries"][$filtro];
                } else {
                    throw new LlaveDeBusquedaIncorrectaException("El filtro $filtro no existe en el bibtex, favor de verificar", 15056);
                }
            } else {
                $dataReturn = array();
                foreach ($filtro as $key) {
                    if (in_array($key, $data_keys)) {
                        $dataReturn[] = $this->data[$key];
                    } elseif (in_array($key, $bibtex_keys)) {
                        $dataReturn[] = $this->data["entries"][$key];
                    } else {
                        throw new LlaveDeBusquedaIncorrectaException("El filtro $key no existe en el bibtex, favor de verificar", 15066);
                    }
                }
            }
        } else {
            $dataReturn = $this->data;
        }

        return $dataReturn;
    }

    private function _get_productoCientifico_by_id(int $id) : bool {
        $ban = false;
        $sqlquery = "SELECT producto_cientifico.bibtex as bibtex FROM producto_cientifico WHERE producto_cientifico.id = ?";
        try {
            $res = $this->SqlOp->exec($sqlquery, "i", [$id]);

            if (count($res) == 1) {
                $this->data["id"] = $id;
                $listener = new Listener();
                $listener->addProcessor(new Processor\TagNameCaseProcessor(CASE_LOWER));
                $parser = new Parser();
                $parser->addListener($listener);
                $bibtex = $res[0]["bibtex"];
                $parser->parseString($bibtex); // or parseFile('/path/to/file.bib')
                $this->data["entries"] = $listener->export()[0];

                $ban = true;
            }
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }
        return $ban;
    }
}