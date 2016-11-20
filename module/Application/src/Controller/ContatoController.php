<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\View\Model\ViewModel;
use Zf3ServiceBase\Controller\GenericController;

class ContatoController extends GenericController
{
    public function enviaMensagemAction() {
        
        $request = $this->getRequest();
        
        if( !$request->isPost() ) {
            return false;
        }
        
        $dados = $request->getPost()->toArray();
        echo "<pre>";
        print_r($dados);
        die('teste');
    }
}
