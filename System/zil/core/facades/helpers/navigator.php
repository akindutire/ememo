<?php 
/**
 * Author: Akindutire Ayomide Samuel
 */
namespace zil\core\facades\helpers;

use zil\factory\Redirect;
use zil\core\tracer\ErrorTracer;

trait Navigator{

    /**
     *  Navigate one step backward
     */
    private function goBack(){
        try{
 
            if(isset($_SERVER['HTTP_REFERER']))
                new Redirect($_SERVER['HTTP_REFERER']);
            else
                throw new \DomainException("Couldn't found previous URL on server domain");

        }catch(\DomainException $t){
            new ErrorTracer($t);
        }catch(\Throwable $t){
            new ErrorTracer($t);
        }
    }

    /**
     * @param string $url
     */
    private function goTo(string $url) : void{
        try{
                        
            new Redirect($url);
            
        }catch(\InvalidArgumentException $t){
            new ErrorTracer($t);
        }catch(\TypeError $t){
            new ErrorTracer($t);
        }catch(\Throwable $t){
            new ErrorTracer($t);
        }
    }

}
?>