<?php
#error_reporting(E_ALL);
#ini_set("display_errors", 1);
/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *     <ctima@itajai.sc.gov.br>
 *
 * Este programa é software livre; você pode redistribuí-lo e/ou modificá-lo
 * sob os termos da Licença Pública Geral GNU conforme publicada pela Free
 * Software Foundation; tanto a versão 2 da Licença, como (a seu critério)
 * qualquer versão posterior.
 *
 * Este programa é distribuí­do na expectativa de que seja útil, porém, SEM
 * NENHUMA GARANTIA; nem mesmo a garantia implí­cita de COMERCIABILIDADE OU
 * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral
 * do GNU para mais detalhes.
 *
 * Você deve ter recebido uma cópia da Licença Pública Geral do GNU junto
 * com este programa; se não, escreva para a Free Software Foundation, Inc., no
 * endereço 59 Temple Street, Suite 330, Boston, MA 02111-1307 USA.
 *
 * @author    Lucas Schmoeller da Silva <lucas@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Api
 * @subpackage  Modules
 * @since   Arquivo disponível desde a versão ?
 * @version   $Id$
 */

require_once 'Portabilis/Controller/ApiCoreController.php';
require_once 'Portabilis/Array/Utils.php';
require_once 'Portabilis/String/Utils.php';
require_once 'Portabilis/Array/Utils.php';
require_once 'Portabilis/Date/Utils.php';

class CursoController extends ApiCoreController
{

  protected function canGetCursos(){
    return $this->validatesPresenceOf('instituicao_id');
  }

  protected function getCursos(){
    if ($this->canGetCursos()){
      $instituicaoId = $this->getRequest()->instituicao_id;
      $escolaId = $this->getRequest()->escola_id;
      $getSeries = (bool)$this->getRequest()->get_series;

      if($escolaId){
        if(is_array($escolaId))
          $escolaId = implode(",", $escolaId);

        $sql = "SELECT DISTINCT c.cod_curso, c.nm_curso
                  FROM pmieducar.curso c
                  INNER JOIN pmieducar.escola_curso ec ON ec.ref_cod_curso = c.cod_curso
                  WHERE c.ativo = 1
                  AND ec.ativo = 1
                  AND c.ref_cod_instituicao = $1
                  AND ec.ref_cod_escola IN ($escolaId)
                  ORDER BY c.nm_curso ASC ";
      }else{
        $sql = "SELECT cod_curso, nm_curso
                  FROM pmieducar.curso 
                    WHERE ref_cod_instituicao = $1 
                    AND ativo = 1
                    ORDER BY nm_curso ASC ";        
      }
      $params     = array($this->getRequest()->instituicao_id);

      $cursos = $this->fetchPreparedQuery($sql, $params);

      $sqlSerie = "SELECT DISTINCT s.cod_serie, s.nm_serie                  
                    FROM pmieducar.serie s
                    INNER JOIN pmieducar.escola_serie es ON es.ref_cod_serie = s.cod_serie
                    WHERE es.ativo = 1
                    AND s.ativo = 1";
      if($escolaId)
        $sqlSerie .= " AND es.ref_cod_escola IN ({$escolaId}) ";

      foreach ($cursos as &$curso) {
        $curso['nm_curso'] = Portabilis_String_Utils::toUtf8($curso['nm_curso']);
        if($getSeries){
          $series = $this->fetchPreparedQuery($sqlSerie . " AND s.ref_cod_curso = {$curso['cod_curso']} ORDER BY s.nm_serie ASC");
          foreach ($series as &$serie) {
            $serie['nm_serie'] = Portabilis_String_Utils::toUtf8($serie['nm_serie']);
          }
          $curso['series'] = Portabilis_Array_Utils::filterSet($series, array('cod_serie' => 'id', 'nm_serie' => 'nome'));
        }
      }

      $attrs = array(
        'cod_curso'       => 'id',
        'nm_curso'        => 'nome'
      );

      if ($getSeries)
        $attrs['series'] = 'series';

      $cursos = Portabilis_Array_Utils::filterSet($cursos, $attrs);

      return array('cursos' => $cursos );
    }
  }

  public function Gerar() {
    if ($this->isRequestFor('get', 'cursos'))
      $this->appendResponse($this->getCursos());
    else
      $this->notImplementedOperationError();
  }
}
