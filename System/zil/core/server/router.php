<?php
namespace zil\core\server;

use zil\core\tracer\ErrorTracer;
use zil\core\facades\helpers\Reporter;

class Router{
    use Reporter;

    
    public function __construct(){ }

    public static function Route(string $urlkey){
      
        try{
            $req = (new Request())->Uri($urlkey)->getFrame();

            if( count($req) > 0){
                Response::fromHttp( $req );
            }else{
                http_response_code(404);
                self::report(404);
            }

            unset($req);
            
        }catch(\InvalidArgumentException $t){
            new ErrorTracer($t);
        }catch(\TypeError $t){
            new ErrorTracer($t);
        }catch(\BadMethodCallException $t){
            new ErrorTracer($t);
        }catch(\Throwable $t){
            new ErrorTracer($t);
        }
    }
}
?>