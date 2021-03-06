<?php
#error_reporting(E_ALL);
#ini_set("display_errors", 1);
/**
 * i-Educar - Sistema de gestão escolar
 *
 * Copyright (C) 2006  Prefeitura Municipal de Itajaí
 *                     <ctima@itajai.sc.gov.br>
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
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Arquivo disponível desde a versão 1.1.0
 * @version   $Id$
 */

require_once 'lib/Portabilis/View/Helper/DynamicInput/CoreSelect.php';
require_once 'Portabilis/Business/Professor.php';

/**
 * Portabilis_View_Helper_DynamicInput_Serie class.
 *
 * @author    Lucas D'Avila <lucasdavila@portabilis.com.br>
 * @category  i-Educar
 * @license   @@license@@
 * @package   Portabilis
 * @since     Classe disponível desde a versão 1.1.0
 * @version   @@package_version@@
 */
class Portabilis_View_Helper_DynamicInput_Serie extends Portabilis_View_Helper_DynamicInput_CoreSelect {

  protected function inputName() {
    return 'ref_cod_serie';
  }

  protected function inputOptions($options) {
    $resources     = $options['resources'];
    $instituicaoId = $this->getInstituicaoId($options['instituicaoId']);
    $escolaId      = $this->getEscolaId($options['escolaId']);
    $cursoId       = $this->getCursoId($options['cursoId']);
    $userId        = $this->getCurrentUserId();
    $isProfessor   = Portabilis_Business_Professor::isProfessor($instituicaoId, $userId);

    if ($isProfessor && Portabilis_Business_Professor::canLoadSeriesAlocado($instituicaoId)){      
      $series = Portabilis_Business_Professor::seriesAlocado($instituicaoId, $escolaId, $serieId, $userId);
      //$resources = Portabilis_Array_Utils::setAsIdValue($series, 'id', 'nome');
    }
    elseif ($escolaId && $cursoId && empty($resources))
      $resources = App_Model_IedFinder::getSeries($instituicaoId = null, $escolaId, $cursoId);

    return $this->insertOption(null, "Selecione uma s&eacute;rie", $resources);
  }

  protected function defaultOptions(){
    return array('options' => array('label' => 'S&eacute;rie'));
  }  

  public function serie($options = array()) {
    parent::select($options);
  }
}