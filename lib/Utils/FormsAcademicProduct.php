<?php

namespace dsa\lib\Utils;

use dsa\api\model\carga_academica\Exceptions\CargaAcademicaYaExistenteException;
use dsa\lib\Exceptions\GeneralException;

class FormsAcademicProduct
{
    private array $tipos;
    private String $tipo;

    public function __construct(String $tipo="misc") {
        $this->tipos = ["Article", "Book", "Booklet", "Conference", "InBook", "InCollection", "InProceedings", "Manual", "MasterThesis", "Misc", "PhdThesis", "Proceedings", "TechReport", "Unpublished"];

        if (!in_array($tipo, $this->tipos)) {
            throw new GeneralException("El tipo: $tipo no es válido", -90);
        }

        $this->tipo = $tipo;
    }

    public function get_form(?array $dataProduct=null) {

      if (isset($dataProduct["id"])) {
        $this->_id_product($dataProduct["id"]);
      }
      $this->_citation_key($dataProduct["entries"]["citation-key"] ?? null);
        switch ($this->tipo) {
          case "Article":
            $this->_author($dataProduct["entries"]["author"] ?? null, true);
            $this->_title($dataProduct["entries"]["title"] ?? null,true);
            $this->_journal($dataProduct["entries"]["journal"] ?? null, true);
            $this->_year($dataProduct["entries"]["year"] ?? null, true);
            $this->_volume($dataProduct["entries"]["volume"] ?? null);
            $this->_number($dataProduct["entries"]["number"] ?? null);
            $this->_pages($dataProduct["entries"]["pages"] ?? null);
            $this->_month($dataProduct["entries"]["month"] ?? null);
            break;
          case "Book":
            $this->_editor($dataProduct["entries"]["editor"] ?? null, true);
            $this->_title($dataProduct["entries"]["title"] ?? null, true);
            $this->_publisher($dataProduct["entries"]["publisher"] ?? null, true);
            $this->_year($dataProduct["entries"]["year"] ?? null, true);
            $this->_volume($dataProduct["entries"]["volume"] ?? null);
            $this->_serie($dataProduct["entries"]["serie"] ?? null);
            $this->_address($dataProduct["entries"]["address"] ?? null);
            $this->_edition($dataProduct["entries"]["edition"] ?? null);
            $this->_month($dataProduct["entries"]["month"] ?? null);
            $this->_year($dataProduct["entries"]["year"] ?? null);
            break;
          case "Booklet":
            $this->_title(true);
            $this->_author($dataProduct["entries"]["author"] ?? null);
            $this->_how_published($dataProduct["entries"]["howpublished"] ?? null);
            $this->_address($dataProduct["entries"]["address"] ?? null);
            $this->_month($dataProduct["entries"]["month"] ?? null);
            $this->_year($dataProduct["entries"]["year"] ?? null);
            break;
          case "Conference":
          case "InProceedings":
            $this->_author($dataProduct["entries"]["author"] ?? null, true);
            $this->_title($dataProduct["entries"]["title"] ?? null, true);
            $this->_book_title($dataProduct["entries"]["booktitle"] ?? null, true);
            $this->_year($dataProduct["entries"]["year"] ?? null, true);
            $this->_editor($dataProduct["entries"]["editor"] ?? null);
            $this->_volume($dataProduct["entries"]["volume"] ?? null);
            $this->_number($dataProduct["entries"]["number"] ?? null);
            $this->_serie($dataProduct["entries"]["serie"] ?? null);
            $this->_pages($dataProduct["entries"]["pages"] ?? null);
            $this->_address($dataProduct["entries"]["address"] ?? null);
            $this->_month($dataProduct["entries"]["month"] ?? null);
            $this->_organization($dataProduct["entries"]["organization"] ?? null);
            $this->_publisher($dataProduct["entries"]["publisher"] ?? null);
            break;
          case "InBook":
            $this->_author($dataProduct["entries"]["author"] ?? null, true);
            $this->_title($dataProduct["entries"]["title"] ?? null, true);
            $this->_chapter($dataProduct["entries"]["chapter"] ?? null, true);
            $this->_publisher($dataProduct["entries"]["publisher"] ?? null, true);
            $this->_year($dataProduct["entries"]["year"] ?? null, true);
            $this->_volume($dataProduct["entries"]["volume"] ?? null);
            $this->_number($dataProduct["entries"]["number"] ?? null);
            $this->_serie($dataProduct["entries"]["serie"] ?? null);
            $this->_type($dataProduct["entries"]["type"] ?? null);
            $this->_address($dataProduct["entries"]["address"] ?? null);
            $this->_edition($dataProduct["entries"]["edition"] ?? null);
            $this->_month($dataProduct["entries"]["month"] ?? null);
            break;
          case "InCollection":
            $this->_author($dataProduct["entries"]["author"] ?? null, true);
            $this->_title($dataProduct["entries"]["title"] ?? null, true);
            $this->_book_title($dataProduct["entries"]["booktitle"] ?? null, true);
            $this->_publisher($dataProduct["entries"]["publisher"] ?? null, true);
            $this->_year($dataProduct["entries"]["year"] ?? null, true);
            $this->_editor($dataProduct["entries"]["editor"] ?? null);
            $this->_volume($dataProduct["entries"]["volume"] ?? null);
            $this->_number($dataProduct["entries"]["number"] ?? null);
            $this->_serie($dataProduct["entries"]["serie"] ?? null);
            $this->_type($dataProduct["entries"]["type"] ?? null);
            $this->_chapter($dataProduct["entries"]["chapter"] ?? null);
            $this->_pages($dataProduct["entries"]["pages"] ?? null);
            $this->_address($dataProduct["entries"]["address"] ?? null);
            $this->_edition($dataProduct["entries"]["edition"] ?? null);
            $this->_month($dataProduct["entries"]["month"] ?? null);
            break;
          case "Manual":
            $this->_title($dataProduct["entries"]["title"] ?? null, true);
            $this->_author($dataProduct["entries"]["author"] ?? null);
            $this->_organization($dataProduct["entries"]["organization"] ?? null);
            $this->_address($dataProduct["entries"]["address"] ?? null);
            $this->_edition($dataProduct["entries"]["edition"] ?? null);
            $this->_month($dataProduct["entries"]["month"] ?? null);
            $this->_year($dataProduct["entries"]["year"] ?? null);
            break;
          case "PhdTesis":
          case "MasterTesis":
            $this->_author($dataProduct["entries"]["author"] ?? null, true);
            $this->_title($dataProduct["entries"]["title"] ?? null, true);
            $this->_organization($dataProduct["entries"]["organization"] ?? null, true);
            $this->_year($dataProduct["entries"]["year"] ?? null, true);
            $this->_type($dataProduct["entries"]["type"] ?? null);
            $this->_address($dataProduct["entries"]["address"] ?? null);
            $this->_month($dataProduct["entries"]["month"] ?? null);
            break;
          case "Proceedings":
            $this->_title($dataProduct["entries"]["title"] ?? null, true);
            $this->_year($dataProduct["entries"]["year"] ?? null, true);
            $this->_book_title($dataProduct["entries"]["booktitle"] ?? null);
            $this->_editor($dataProduct["entries"]["editor"] ?? null);
            $this->_volume($dataProduct["entries"]["volume"] ?? null);
            $this->_serie($dataProduct["entries"]["serie"] ?? null);
            $this->_address($dataProduct["entries"]["address"] ?? null);
            $this->_month($dataProduct["entries"]["month"] ?? null);
            $this->_organization($dataProduct["entries"]["organization"] ?? null);
            $this->_publisher($dataProduct["entries"]["publisher"] ?? null);
            break;
          case "TechReport":
            $this->_author($dataProduct["entries"]["author"] ?? null, true);
            $this->_title($dataProduct["entries"]["title"] ?? null, true);
            $this->_organization($dataProduct["entries"]["organization"] ?? null, true);
            $this->_year($dataProduct["entries"]["year"] ?? null, true);
            $this->_type($dataProduct["entries"]["type"] ?? null);
            $this->_number($dataProduct["entries"]["number"] ?? null);
            $this->_address($dataProduct["entries"]["address"] ?? null);
            $this->_month($dataProduct["entries"]["month"] ?? null);
            break;
          case "Unpublished":
            $this->_author($dataProduct["entries"]["author"] ?? null, true);
            $this->_title($dataProduct["entries"]["title"] ?? null, true);
            $this->_note($dataProduct["entries"]["note"] ?? null, true);
            $this->_month($dataProduct["entries"]["month"] ?? null);
            $this->_year($dataProduct["entries"]["year"] ?? null, true);
            break;
          default:
            $this->_author($dataProduct["entries"]["author"] ?? null);
            $this->_title($dataProduct["entries"]["title"] ?? null, true);
            $this->_how_published($dataProduct["entries"]["howpublished"] ?? null);
            $this->_month($dataProduct["entries"]["month"] ?? null);
            $this->_year($dataProduct["entries"]["year"] ?? null);
            break;
        }
    }

    private function _id_product(int $id) {
      ?>
        <input type="hidden" id="id_producto" name="id_producto" value="<?php echo $id; ?>" />
      <?php
    }

    private function _citation_key(?String $citationKey=null) {
      ?>
        <div class="row" id="citation-key-product">
          <div class="col-md-12">
            <div class="form-group">
              <label for="txtCitationKey" class="bmd-label-floating">Clave de Citación</label>
              <input class="form-control" type="text" id="txtCitationKey" name="citation-key" value="<?php echo (!is_null($citationKey) ? $citationKey : ""); ?>" required />
            </div>
          </div>
        </div>
      <?php
    }

    private function _title(?String $title=null, bool $required=false) {
        ?>
        <div class="row" id="titulo-producto">
          <div class="col-md-12">
            <div class="form-group">
              <label for="txtTitle" class="bmd-label-floating">Título</label>
              <input class="form-control" type="text" id="txtTitle" name="title" value="<?php echo (!is_null($title) ? $title:""); ?>" <?php $this->_print_required($required); ?> />
            </div>
          </div>
        </div>
        <?php
    }

  private function _author(?String $author=null, bool $required=false) {
    ?>
    <div class="row" id="autor-producto">
      <div class="col-md-12">
        <div class="form-group">
          <label for="txtAuthor" class="bmd-label-floating">Autores</label>
          <input id="txtAuthor" class="form-control" type="text" name="author" value="<?php echo (!is_null($author)?$author:""); ?>" <?php $this->_print_required($required); ?> />
        </div>
      </div>
    </div>
    <?php
  }

  private function _journal(?String $journal=null, bool $required=false) {
    ?>
    <div class="row" id="journal-producto">
      <div class="col-md-12">
        <div class="form-group">
          <label for="txtJournal" class="bmd-label-floating">Journal</label>
          <input id="txtJournal" class="form-control" type="text" name="journal" value="<?php echo (!is_null($journal)?$journal:""); ?>" <?php $this->_print_required($required); ?> />
        </div>
      </div>
    </div>
    <?php
  }

  private function _year(?String $year=null, bool $required=false) {
    ?>
    <!-- Año de publicación -->
    <div class="row" id="anio-producto">
      <div class="col-md-12">
        <div class="form-group">
          <label for="nbrYear" class="bmd-label-floating">Año</label>
          <input id="nbrYear" class="form-control" type="number" name="year" maxlength="4" <?php $this->_print_required($required); ?> value="<?php echo !is_null($year)?$year:date("Y"); ?>" />
        </div>
      </div>
    </div>
    <?php
  }

  private function _volume(?String $volume=null, bool $required=false) {
      ?>
    <!-- Volumen de publicación -->
    <div id="volumen-producto" class="row">
      <div class="col-md-12">
        <div class="form-group">
          <label for="nbrVolume" class="bmd-label-floating">Volumen</label>
          <input id="bnrVolume" class="form-control" type="number" name="volume" value="<?php echo !is_null($volume)?$volume:"" ; ?>" <?php $this->_print_required($required); ?> />
        </div>
      </div>
    </div>
      <?php
  }

  private function _number(?String $number=null, bool $required=false) {
      ?>
    <!-- Número de publicación -->
    <div class="row" id="numero-producto">
      <div class="col-md-12">
        <div class="form-group">
          <label for="nbrNumber" class="bmd-label-floating">Número</label>
          <input id="nbrNumber" class="form-control" type="number" name="number" value="<?php echo !is_null($number)?$number:""; ?>" <?php $this->_print_required($required); ?> />
        </div>
      </div>
    </div>
      <?php
  }

  private function _pages(?String $pages=null, bool $required=false) {
      ?>
    <!-- Páginas de publicación -->
    <div class="row" id="paginas-producto">
      <div class="col-md-12">
        <div class="form-group">
          <label for="txtPages" class="bmd-label-floating">Páginas</label>
          <input id="txtPages" class="form-control" type="text" name="pages" value="<?php echo !is_null($pages)?$pages:""; ?>" <?php $this->_print_required($required); ?> />
        </div>
      </div>
    </div>
      <?php
  }

  private function _month(?int $month=null, bool $required=false) {
      ?>
    <!-- Mes de publicación -->
    <div class="row" id="mes-producto">
      <div class="col-md-12">
        <div class="form-group">
          <label for="sctMonth" class="control-label">Mes</label>
          <select id="sctMonth" class="form-control" name="month" <?php $this->_print_required($required); ?>>
            <?php
            $arrayMonths = ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"];
            $i = 1;
            foreach ($arrayMonths as $strMonth) {
              if ($month == $i) {
                echo "<option value=\"$i\" selected>$strMonth</option>";
              } else {
                echo "<option value=\"$i\">$strMonth</option>";
              }
              $i++;
            }
            ?>
          </select>
        </div>
      </div>
    </div>
      <?php
  }

  private function _serie(?String $serie=null, bool $required=false) {
      ?>
      <div class="row" id="serie-producto">
        <div class="col-md-12">
          <div class="form-group">
            <label for="txtSerie" class="bmd-label-floating">Serie</label>
            <input id="txtSerie" class="form-control" type="text" name="serie" value="<?php echo !is_null($serie)?$serie:""; ?>" <?php $this->_print_required($required); ?> />
          </div>
        </div>
      </div>
      <?php
  }

  private function _publisher(?String $publisher=null, bool $required=false) {
      ?>
    <!-- Editorial -->
    <div class="row" id="editorial-producto">
      <div class="col-md-12">
        <div class="form-group">
          <label for="txtPublisher" class="bmd-label-floating">Editorial</label>
          <input id="txtPublisher" class="form-control" type="text" name="publisher" value="<?php echo !is_null($publisher)?$publisher:"" ?>" <?php $this->_print_required($required); ?>>
        </div>
      </div>
    </div>
      <?php
  }

  private function _edition(?String $edition=null, bool $required=false) {
      ?>
      <!-- Edición -->
      <div class="row" id="edicion-producto">
        <div class="col-md-12">
          <div class="form-group">
            <label for="nbrEdition" class="bmd-label-floating">Edición</label>
            <input id="nbrEdition" class="form-control" type="number" name="edition" value="<?php echo !is_null($edition)?$edition:""; ?>" <?php $this->_print_required($required); ?> />
          </div>
        </div>
      </div>
      <?php
  }

  private function _type(?String $type=null, bool $required=false) {
      ?>
      <!-- Tipo -->
      <div class="row" id="tipo-producto">
        <div class="col-md-12">
          <div class="form-group">
            <label for="txtType" class="bmd-label-floating">Tipo</label>
            <input id="txtType" class="form-control" type="text" name="type" value="<?php echo !is_null($type)?$type:""; ?>" <?php $this->_print_required($required); ?> />
          </div>
        </div>
      </div>
      <?php
  }

  private function _chapter(?String $chapter=null, bool $required=false) {
      ?>
      <!-- Capítulo -->
      <div class="row" id="capitulo-producto">
        <div class="col-md-12">
          <div class="form-group">
            <label for="nbrChapter" class="bmd-label-floating">Capítulo</label>
            <input id="nbrChapter" class="form-control" type="number" name="chapter" value="<?php echo !is_null($chapter)?$chapter:"" ?>" <?php $this->_print_required($required); ?> />
          </div>
        </div>
      </div>
      <?php
  }

  private function _book_title(?String $booktitle=null, bool $required=false) {
      ?>
      <!-- Título del libro -->
      <div class="row" id="titulo-libro">
        <div class="col-md-12">
          <div class="form-group">
            <label for="txtBookTitle" class="bmd-label-floating">Título del libro</label>
            <input id="txtBookTitle" class="form-control" type="text" name="booktitle" value="<?php echo !is_null($booktitle)?$booktitle:""; ?>" <?php $this->_print_required($required); ?> />
          </div>
        </div>
      </div>
      <?php
  }

  private function _organization(?String $organization=null, bool $required=false) {
      ?>
      <!-- Institución, escuela u organización -->
      <div class="row" id="institucion-producto">
        <div class="col-md-12">
          <div class="form-group">
            <label for="txtOrganization" class="bmd-label-floating">Institucion/escuela/organización</label>
            <input id="txtOrganization" class="form-control" type="text" name="organization" value="<?php echo !is_null($organization)?$organization:""; ?>" <?php $this->_print_required($required); ?> />
          </div>
        </div>
      </div>
      <?php
  }

  private function _address(?String $address=null, bool $required=false) {
      ?>
      <!-- Dirección -->
      <div class="row" id="direccion-producto">
        <div class="col-md-12">
          <div class="form-group">
            <label for="txtAddress" class="bmd-label-floating">Dirección</label>
            <input id="txtAddress" class="form-control" type="text" name="address" value="<?php echo !is_null($address)?$address:""; ?>" <?php $this->_print_required($required); ?> />
          </div>
        </div>
      </div>
      <?php
  }

  private function _editor(?String $editor=null, bool $required=false) {
      ?>
      <!-- Editor -->
      <div class="row" id="editor-producto">
        <div class="col-md-12">
          <div class="form-group">
            <label for="txtEditor" class="bmd-label-floating">Editor</label>
            <input id="txtEditor" class="form-control" type="text" name="editor" value="<?php echo !is_null($editor)?$editor:""; ?>" <?php $this->_print_required($required); ?> />
          </div>
        </div>
      </div>
      <?php
  }

  private function _how_published(?String $howPublished=null, bool $required=false) {
      ?>
      <!-- Cómo fue publicado -->
      <div class="row" id="how-published-producto">
        <div class="col-md-12">
          <div class="form-group">
            <label for="txtHowPublished" class="bmd-label-floating">¿Cómo fue publicado?</label>
            <input id="txtHowPublished" class="form-control" type="text" name="howpublished" value="<?php echo !is_null($howPublished)?$howPublished:""; ?>" <?php $this->_print_required($required); ?> />
          </div>
        </div>
      </div>
      <?php
  }

  private function _note(?String $note=null, bool $required=false) {
      ?>
      <!-- Nota -->
      <div class="row" id="nota-producto">
        <div class="form-group">
          <label for="txtNote" class="bmd-label-floating">Nota</label>
          <input id="txtNote" class="form-control" type="text" name="note" value="<?php echo !is_null($note)?$note:""; ?>" <?php $this->_print_required($required); ?> />
        </div>
      </div>
      <?php
  }

  private function _doi(?String $doi=null, bool $required=false) {
      ?>
      <!-- doi -->
      <div class="row">
        <div class="form-group">
          <label for="txtDoi" class="bmd-label-floating">Nota</label>
          <input id="txtDoi" class="form-control" type="text" name="doi" value="<?php echo !is_null($doi)?$doi:""; ?>" <?php $this->_print_required($required); ?> />
        </div>
      </div>
      <?php
  }

  private function _url(?String $url=null, bool $required=false) {
      ?>
      <!-- doi -->
      <div class="row">
        <div class="form-group">
          <label for="txtUrl" class="bmd-label-floating">URL</label>
          <input id="txtUrl" class="form-control" type="url" name="url" value="<?php echo !is_null($url)?$url:""; ?>" <?php $this->_print_required($required); ?> />
        </div>
      </div>
      <?php
  }

  private function _isbn(?String $isbn=null, bool $required=false) {
      ?>
      <!-- doi -->
      <div class="row">
        <div class="form-group">
          <label for="txtISBN" class="bmd-label-floating">URL</label>
          <input id="txtISBN" class="form-control" type="text" name="isbn" value="<?php echo !is_null($isbn)?$isbn:""; ?>" <?php $this->_print_required($required); ?> />
        </div>
      </div>
      <?php
  }

  private function _print_required(bool $required=false) {
    $txtRequired = $required ? "required=\"required\"" : "";
    echo $txtRequired;
  }
}
?>