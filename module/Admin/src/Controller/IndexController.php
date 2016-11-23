<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Admin\Controller;

use Zf3ServiceBase\Controller\GenericController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

use Admin\Service\UploadService;
use RdnUpload\Adapter\Local;


class IndexController extends GenericController
{
    
    public function indexAction(){
        $srv = $this->app()->getEntity('Application','Depoimentos');
        $result = $srv->getAll();
        return new ViewModel(['depoimentos'=>$result]);
    }
    
    public function postarFotosAction(){
        $request = $this->getRequest();
        if( ! $request->isPost()){ $this->redirectAdmin(); }
        $dados = $request->getPost()->toArray();
        
        $categoria = implode('_', explode(' ', strtolower(trim($dados['categoria']))));
        $album_nome_atual = implode('_', explode(' ', strtolower(trim($dados['album_nome']))));
        $titulo = implode('_', explode(' ', strtolower(trim($dados['titulo']))));
        
        if($dados['album_nome']!='0'){
            rename("data/albuns/$categoria/$album_nome_atual", "data/albuns/$categoria/$titulo");
            $titulo = $album_nome_atual;
        }
        
        $uploads = new UploadService(new Local('data/albuns', '/files'));
        $uploads->substituir_se_existir = false;        
        $uploads->projeto_subpasta = "$categoria/$titulo";
        
        $files = $request->getFiles()->toArray();
        $id = [];
        foreach ($files['fotos'] as $key => $cada) {
            if($cada['size']<10){continue;}
            
            $cada['name'] = implode('_', explode(' ', strtolower(trim(strtr($cada['name'], "áàãâéêíóôõúüçÁÀÃÂÉÊÍÓÔÕÚÜÇ", "aaaaeeiooouucAAAAEEIOOOUUC")))));
            $id[] = $uploads->upload($cada);
        }
//        echo '<pre>';
//        print_r($id);
//        print_r($dados);
//        print_r($files);
//        die();
        $this->redirect()->toUrl('/admin#fotos');
    }
    
    public function buscarAlbunsAction(){
        $request = $this->getRequest();
        if( ! $request->isPost()){return false;}
        $dados = $request->getPost()->toArray();
        
        $categoria = $dados['categoria'] = implode('_', explode(' ', strtolower(trim($dados['categoria']))));
        $albuns = [];
        $path = getcwd()."/data/albuns/$categoria";         
        if(is_dir($path)){
            $albuns = scandir($path);
            unset($albuns[0]);
            unset($albuns[1]);
        } 

        return new JsonModel($albuns);
    }
    
    public function buscarFotosAction(){
        $request = $this->getRequest();
        if( ! $request->isPost()){return false;}
        $dados = $request->getPost()->toArray();
        
        $categoria = implode('_', explode(' ', strtolower(trim($dados['categoria']))));
        $album = implode('_', explode(' ', strtolower(trim($dados['titulo']))));
        
        $path = getcwd()."/data/albuns/$categoria/$album";         
        
        $files = preg_grep('~\.(jpeg|jpg|png)$~', scandir($path));
        
        return new JsonModel([
            'categoria'=>$categoria,
            'album'=>$album,
            'files'=>$files
        ]);
        
    }
    
    public function apagarFotoAction(){
        $request = $this->getRequest();
        if( ! $request->isPost()){return false;}
        $dados = $request->getPost()->toArray();
        
        $categoria = implode('_', explode(' ', strtolower(trim($dados['categoria']))));
        $album = implode('_', explode(' ', strtolower(trim($dados['titulo']))));
        $file = $dados['file'];
        
        $path = "$categoria/$album/$file";         
        
        $result = true;
        try {
            $uploads = new UploadService(new Local('data/albuns', '/files'));
            $uploads->delete($path);
        } catch (\Exception $exc) {
            $result = false;
        }

        return new JsonModel(['result'=>$result]);
        
    }
    
    public function buscarDepoimentoAction() {
        
        $request = $this->getRequest();
        if( ! $request->isPost()){ $this->redirectAdmin(); }
        $dados = $request->getPost()->toArray();
        $srv = $this->app()->getEntity('Application','Depoimentos');
        $result = $srv->getAllById($dados['id']);
        return new JsonModel($result);
        
    }
    
    public function postarDepoimentoAction() {
        
        $request = $this->getRequest();
        if( ! $request->isPost()){ $this->redirectAdmin(); }
        $dados = $request->getPost()->toArray();
        $file = $request->getFiles()->toArray()['foto'];
        
        if($file['size']>10){
            $uploads = new UploadService(new Local('data/depoimentos/fotos', '/files'));
            $uploads->substituir_se_existir = false;        
            $id = $uploads->upload($file);
            
            if($id){ $dados['imagem'] = $id; }
        }
        
        $srv = $this->app()->getEntity('Application','Depoimentos');
        $entity = $srv->create($dados);
        $result = $srv->save($entity);
        
        $this->redirect()->toUrl('/admin#depoimento');
        
    }
    
    public function excluirDepoimentoAction(){
        $request = $this->getRequest();
        if( ! $request->isPost()){return false;}
        $dados = $request->getPost()->toArray();
        
        $srv = $this->app()->getEntity('Application','Depoimentos');
        $result = $srv->removeById($dados['id']);
        return new JsonModel(['result'=>$result]);
    }

    private function redirectAdmin() {
        $this->redirect()->toUrl('/admin#fotos');
    }
    
    
    
}
