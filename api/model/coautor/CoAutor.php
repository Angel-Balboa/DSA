<?php

namespace dsa\api\model\coautor;

use dsa\api\model\coautor\Exceptions\CoAutorException;
use dsa\api\model\coautor\Exceptions\ParametrosNoValidosException;
use dsa\api\model\coautor\Exceptions\ProductoNoAgregadoAProfesorException;
use dsa\api\model\coautor\Exceptions\ProfesorNoAgregadoComoAutorException;
use dsa\api\model\coautor\Exceptions\ProfesorExisteEnProductoException;
use dsa\api\model\producto_cientifico\ProductoCientifico;
use dsa\api\model\profesor\Exceptions\LlaveDeBusquedaIncorrectaException;
use dsa\api\model\profesor\Profesor;
use dsa\lib\conexionSQL\COperacionesSQL;
use dsa\lib\conexionSQL\Exceptions\CConnexionException;
use dsa\lib\conexionSQL\Exceptions\SQLTransactionException;

class CoAutor
{
    private ?array $data;
    private COperacionesSQL $SqlOp;

    private function __construct(?Profesor $profesor=null, ?ProductoCientifico $productoCientifico=null, ?COperacionesSQL &$cop=null) {
        $this->SqlOp = COperacionesSQL::getInstance($cop);
        $this->data = null;

        if (!is_null($profesor)) {
            $this->data["id_profesor"] = $profesor->get_data("id");
        }

        if (!is_null($productoCientifico)) {
            $this->data["id_productoCientifico"] = $productoCientifico->get_data("id");
        }
    }

    public static function CoAutor_by_profesor(Profesor $profesor, ?COperacionesSQL &$cop=null) : CoAutor {
        return new CoAutor($profesor, null, $cop);
    }

    public static function CoAutor_by_productoCientifico(ProductoCientifico $producto, ?COperacionesSQL &$cop=null) : CoAutor {
        return new CoAutor(null, $producto, $cop);
    }

    public static function analiza_autores(String $autor_list) : array {
        $autores = explode(";", $autor_list);
        $detected_authors = array();

        $count = 1;
        foreach ($autores as $autor) {
            foreach (Profesor::get_all(["bibtex_style_name" => trim($autor)]) as $detected) {
                $detected_authors[] = ["id_profesor" => $detected["id"], "posicion" => $count];
            }
            $count++;
        }

        return $detected_authors;
    }

    public function get_profesores() : array {
        if (!isset($this->data["id_productoCientifico"])) throw new ParametrosNoValidosException("Para agregar a un nuevo co-autor al Producto científico se debe crear una instancia basada en Producto Cientifico", 16043);

        return $this->_get_profesores_from_db();
    }

    public function get_productos() : array {
        if (!isset($this->data["id_profesor"])) throw new ParametrosNoValidosException("Para asignar un producto a un profesor, se debe crear una instancia basada en un CProfesor", 16049);

        return $this->_get_productos_de_profesor();
    }

    private function _get_productos_de_profesor() : array {
        $prods = array();
        $sqlquery = "SELECT coautor.producto, coautor.posicion FROM coautor WHERE profesor = ?";
        try {
            $res = $this->SqlOp->exec($sqlquery, "i", [$this->data["id_profesor"]]);

            foreach ($res as $r) {
                $prods[] = array("id_producto" => $r["producto"], "posicion" => $r["posicion"]);
            }
        } catch (CConnexionException | SQLTransactionException $e) {
            $prods = array();
        }
        return $prods;
    }

    public function get_total() : array {
        if (!isset($this->data["id_profesor"])) throw new ParametrosNoValidosException("Para asignar un producto a un profesor, se debe crear una instancia basada en un CProfesor", 16049);

        return $this->_get_total_productos_de_profesor();
    }

    private function _get_total_productos_de_profesor() : array {
        $sqlquery = "SELECT COUNT( *) as NumberofRows FROM coautor WHERE profesor=?";
        try {
            $res = $this->SqlOp->exec($sqlquery, "i", [$this->data["id_profesor"]]);
        } catch (CConnexionException | SQLTransactionException $e) {
            $res = 0;
        }
        return $res;
    }

    private function _get_profesores_from_db() : array {
        $profs = array();
        $sqlquery = "SELECT coautor.profesor, coautor.posicion FROM coautor WHERE producto = ?";

        try {
            $res = $this->SqlOp->exec($sqlquery, "i", [$this->data["id_productoCientifico"]]);

            foreach ($res as $r) {
                $profs[] = array("id_profesor" => $r["profesor"], "posicion" => $r["posicion"]);
            }
        } catch (CConnexionException | SQLTransactionException $e) {
            $profs = array();
        }

        return $profs;
    }

    /**
     * @param ProductoCientifico $producto
     * @param $posicion
     * @return bool
     * @throws ParametrosNoValidosException
     * @throws ProductoNoAgregadoAProfesorException
     * @throws ProfesorExisteEnProductoException
     * @throws LlaveDeBusquedaIncorrectaException
     */
    public function agrega_productoCientifico_a_profesor(ProductoCientifico $producto, $posicion) : bool {
        if (!isset($this->data["id_profesor"])) throw new ParametrosNoValidosException("Para asignar un producto a un profesor, se debe crear una instancia basada en un CProfesor", 16042);

        $id_producto = $producto->get_data("id");
        if ($this->_profesor_existe_en_producto($id_producto, $this->data["id_profesor"])) throw new ProfesorExisteEnProductoException("El profesor ya es co-autor en el producto cientifico", 16045);

        if (!$this->_agrega_producto_a_profesor($id_producto, $posicion)) {
            throw new ProductoNoAgregadoAProfesorException("No fue posible agregar el producto al profesor", 16049);
        }
        return true;
    }

    public function agrega_profesor_a_producto(Profesor $newProfesor, int $posicion) : bool {
        if (!isset($this->data["id_productoCientifico"])) throw new ParametrosNoValidosException("Para agregar a un nuevo co-autor al Producto científico se debe crear una instancia basada en Producto Cientifico", 16039);

        $id_profesor = $newProfesor->get_data("id");

        if ($this->_profesor_existe_en_producto($this->data["id_productoCientifico"], $id_profesor)) throw new ProfesorExisteEnProductoException("El profesor $newProfesor ya es co-autor en el producto científico", 16043);

        if (!$this->_agrega_profesor_a_producto($id_profesor, $posicion)) {
            throw new ProfesorNoAgregadoComoAutorException("No fue posible agregar al profesor como co-autor del producto cientifico", 16047);
        }
        return true;
    }

    public function quita_profesor_a_producto(Profesor $profesor) : bool {
        if (!isset($this->data["id_productoCientifico"])) throw new ParametrosNoValidosException("Para agregar a un nuevo co-autor al Producto científico se debe crear una instancia basada en Producto Cientifico", 16039);

        $id_profesor = $profesor->get_data("id");

        if (!$this->_profesor_existe_en_producto($this->data["id_productoCientifico"], $id_profesor)) {
            throw new ProfesorNoAgregadoComoAutorException("El profesor $profesor no es co-autor del producto", 16057);
        }

        if (!$this->_quita_profesor_a_producto($id_profesor, $this->data["id_productoCientifico"])) {
            throw new CoAutorException("No se logró quitar al profesor como co-autor del producto", 16062);
        }
        return true;
    }

    public function quita_producto_a_profesor(ProductoCientifico $producto) : bool {
        if (!isset($this->data["id_profesor"])) throw new ParametrosNoValidosException("Para asignar un producto a un profesor, se debe crear una instancia basada en un CProfesor", 16042);

        $id_producto = $producto->get_data("id");

        if (!$this->_profesor_existe_en_producto($id_producto, $this->data["id_profesor"])) {
            throw new ProfesorNoAgregadoComoAutorException("El producto no esta asignado al profesor", 16084);
        }

        if (!$this->_quita_profesor_a_producto($this->data["id_profesor"], $id_producto)) {
            throw new CoAutorException("No se logró quitar el producto al profesor", 16088);
        }
        return true;
    }

    private function _quita_profesor_a_producto(int $id_profesor, int $id_producto) : bool {
        $sqlquery = "DELETE FROM coautor WHERE profesor = ? and producto = ?";

        try {
            $ban = $this->SqlOp->exec($sqlquery, "ii", [$id_profesor, $id_producto]) == 1;
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }
        return $ban;
    }

    private function _agrega_producto_a_profesor(int $id_producto, int $posicion) : bool {
        $sqlquery = "INSERT INTO coautor(profesor, producto, posicion) VALUES (?, ?, ?)";
        try {
            $ban = $this->SqlOp->exec($sqlquery, "iii", [$this->data["id_profesor"], $id_producto, $posicion]) == 1;
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }
        return $ban;
    }

    private function _agrega_profesor_a_producto(int $id_profesor, int $posicion) : bool {
        $sqlquery = "INSERT INTO coautor (profesor, producto, posicion) VALUES (?, ?, ?)";

        try {
            $ban = $this->SqlOp->exec($sqlquery, "iii", [$id_profesor, $this->data["id_productoCientifico"], $posicion]) == 1;
        } catch (CConnexionException | SQLTransactionException $e) {
            $ban = false;
        }
        return $ban;
    }

    private function _profesor_existe_en_producto(int $id_productoCientifico, int $id_profesor) : bool {
        $sqlquery = "SELECT COUNT(*) as existe FROM coautor WHERE producto = ? AND profesor = ?";

        try {
            $ban = $this->SqlOp->exec($sqlquery, "ii", [$id_productoCientifico, $id_profesor])[0]["existe"] > 0;
        } catch (CConnexionException | SQLTransactionException $e) {
            die("Error inesperado");
        }
        return $ban;
    }


}