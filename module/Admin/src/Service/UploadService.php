<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Admin\Service;

use RdnUpload\Container;

use RdnUpload\Adapter\AdapterInterface;
use RdnUpload\File\FileInterface;
use RdnUpload\File\Input;

/**
 * Description of UploadService
 *
 * @author dpc-bi
 */
class UploadService extends Container {
    
    public $substituir_se_existir = true;
    public $projeto_subpasta = false;
    
    public function __construct(AdapterInterface $adapter = null, $tempDir = null) {
        parent::__construct($adapter, $tempDir);
    }
    
    public function upload($input) {
        
        if (is_array($input)) {
            $input = new Input($input);
        }

        if (!$input instanceof FileInterface) {
                throw new \InvalidArgumentException(sprintf(
                        "Input must be an object implementing %s"
                        , __NAMESPACE__ .'\File\FileInterface'
                ));
        }

        $id = $this->generateSequence($input->getBasename());
        
        if ($this->has($id)) {
            return $this->upload($input);
        }

        $this->adapter->upload($id, $input);

        return $id;
    }
    
    /**
    * Generate a unique/random sequence.
    *
    * @param string $basename
    *
    * @return string
    */
    
    protected function generateSequence($basename,$novo_nome = false) {
        
        $basename = $this->sanitize($basename);
        
        if ($novo_nome) {
            $timestamps = date('d-m-Y H-i-s');
            $basename = $timestamps." - ".$basename;
        }
        
        if ($this->projeto_subpasta != false) {
            $basename = $this->projeto_subpasta."/".$basename;
        }
        
        return $basename;
        
    }
    
    public function has($id) {
        
        if (empty($id)) {
            return false;
        }

        $has = $this->adapter->has($id);
        
        if ($has) {
            if ($this->substituir_se_existir) {
                $this->delete($id);
                return true;
            }           
        }
        
    }
    
    public function __call($method_name, $arguments) {
        
        return call_user_func_array([$this->adapter, $method_name], $arguments);
        
    }
    
}
