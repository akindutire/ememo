<?php 
namespace zil\core\server;

use zil\core\config\Config;
use zil\core\interfaces\Param as PIf;

class Param implements PIf{

    private $u = null;
    private $f = null;


    /**
     * Param constructor.
     * @param object $urlParameters
     * @param object $formParameters
     */
    public function __construct(object $urlParameters, object $formParameters){

        $this->u = $urlParameters;
        $this->f = $formParameters;
     }

    /**
     * @return object
     */
    public function url() : object{
    
        return $this->u;
    }

    /**
     * @return object
     */
    public function form() : object{
        return $this->f;
    }

    
 
}

?>