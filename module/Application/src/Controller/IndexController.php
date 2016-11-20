<?php

/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zf3ServiceBase\Controller\GenericController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

class IndexController extends GenericController {

    public function indexAction() {
//        $portifolio = $this->buscaPortifolio();
        $menu_portfolio = $this->buscaItensMenuPortfolio();
        $depoimentos = $this->buscaDepoimentos();
//        echo "<pre>";print_r($portifolio);die;
        return new ViewModel([
            "menu_portfolio" => $menu_portfolio,
            "depoimentos" => $depoimentos,
        ]);
    }

    public function buscaItensMenuPortfolioAction(){
        $request = $this->getRequest();
        if( ! $request->isPost()){ return false; }
        
        $dados = $request->getPost()->toArray();
        
        $categoria = implode('_', explode(' ', strtolower(trim($dados['categoria']))));
        $album = implode('_', explode(' ', strtolower(trim($dados['album']))));
        
        $menu = $this->getDirectory("data/albuns/$categoria/$album");

        return new JsonModel(['fotos'=>$menu]); 
        
    }

    private function buscaItensMenuPortfolio() {
        $menu = array(
            'casamento' => [],
            'namoro' => [],
            'books' => [],
            'aniversarios' => [],
        );
        
        foreach ($menu as $key => $cada) {
            $menu[$key] = $this->getDirectory("data/albuns/$key");
            if( ! $menu[$key]){
                $menu[$key] = [];
            }
        }
        
        return $menu;
    }

    private function getDirectory($path = '.', $level = 0, $dir_aux = false) {
        
        if( ! is_dir($path)){return false;}

        $files = [];
        $ignore = array('cgi-bin', '.', '..');
        // Directories to ignore when listing output. Many hosts 
        // will deny PHP access to the cgi-bin. 
        
        $dh = @opendir($path);
        // Open the directory to the handle $dh 

        while (false !== ( $file = readdir($dh) )) {
            // Loop through the directory 

            if (!in_array($file, $ignore)) {
                // Check that this file is not to be ignored 

                $spaces = str_repeat('&nbsp;', ( $level * 4));
                // Just to add spacing to the list, to better 
                // show the directory tree. 
                $dir = '';
//                if($level==1){
//                    echo '<pre>';
//                    print_r($file);
//                    die();
//                }
                if (is_dir("$path/$file")) {
                    // Its a directory, so we need to keep reading down... 
//                    $dir = $file;
//                    echo "<strong>$spaces $file</strong><br />";
//                    $files[$file]='';
                    $files[$file]=$this->getDirectory("$path/$file", ($level + 1), $file);
                    // Re-call this same function but on a new directory. 
                    // this is what makes function recursive. 
                } else {
                    $files[]=$file;
//                    echo "$spaces $file<br />";
                    // Just print out the filename 
                }
            }
        }

        closedir($dh);
        
        // Close the directory handle 
        return $files;
    }

    private function buscaDepoimentos() {

        $srv = $this->app()->getEntity('Application', 'Depoimentos');
        $depoimentos = $srv->getAll();
        return $depoimentos;
    }

}
