<?php 
/**
 * Author: Akindutire Ayomide Samuel
 */

namespace zil\core\server;

use zil\core\config\Config;
use zil\core\exception\UnexpectedRouteException;
use zil\core\exception\UnexpectedCaseException;
use zil\core\tracer\ErrorTracer;

class Http extends Config{

    private $uri = '';


    public function __construct(){ }

    public function create(string $uri){

        try{
            $cfg = new Config;
            
            $this->uri =  trim($uri, '/');

            return $this;

        }catch(\Throwablen $t){
            new ErrorTracer($t);
        }

    }

    public function get(){
        return (new Request())->inBuiltSetUriAndMethod($this->uri, 'GET')->getFrame();
    }

    public function post(?array $formData = []){
        return (new Request())->inBuiltSetUriAndMethod($this->uri, 'POST')->getFrame($formData);
    }

    public function put(?array $formData = []){
        return (new Request())->inBuiltSetUriAndMethod($this->uri, 'PUT')->getFrame($formData);
    }
    
    public function delete(){
        return (new Request())->inBuiltSetUriAndMethod($this->uri, 'DELETE')->getFrame();
    }   
}

?>