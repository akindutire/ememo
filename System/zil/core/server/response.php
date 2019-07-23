<?php
namespace zil\core\server;

use zil\core\config\Config;

use zil\core\exception\CorsException;
use zil\core\tracer\ErrorTracer;
use zil\core\middleware\Csrf;
use zil\factory\View;


class Response extends Config{

    use \zil\core\facades\helpers\Reporter;
    use \zil\core\facades\decorators\Route_D1;

    private static $instance = null;

    public function __construct(){
        
    }

    /**
     * Singleton
     *
     * @return Response
     */
    public function getInstance() : Response{
        
        if(is_null(self::$instance)){
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Request from Browser
     *
     * @param array $requestFrame
     * @return void
     */
    public static function fromHttp(array $requestFrame){

        
        try{


            /**
             * Extract App Path
             */
            $AppPath = (new Config())->curAppPath;


            /**
             * Get SAPI
             */
            $sapi = $requestFrame['sapi'] == 'web' ? '' : $requestFrame['sapi'];


            /** Format Request Data */
            $param = new Param($requestFrame['url_params'], $requestFrame['form_params'] );


            /**
             * Check All Guards for validation
             */

            if(!is_null($requestFrame['guard']) ){

                foreach ($requestFrame['guard'] as $guard_class) {


                    if ( class_exists($guard_class) ) {

                        // Exceute Guard Closure
                           (new $guard_class())->validate($param)();

                    } else {
                        throw new \DomainException("Couldn't found " . $requestFrame['guard'] . " guard class");
                    }
                }
            }

            /**
             * Allowed IPs
             */
            $ResponseClone = new self;
            if(!is_null($requestFrame['allow']) ){
                    
                if( !in_array('*', $requestFrame['allow']) || !in_array($ResponseClone->ipDetect(), $requestFrame['allow'])){

                    if( empty($sapi) )
                        self::report(403);
                    else
                        self::fromApi(['403 page forbidden'], 403);

                    exit();
                }
            }

            /**
             * Denied IPs
             */
            if(!is_null($requestFrame['deny']) ) {
                if (in_array('*', $requestFrame['deny'])) {

                    if (empty($sapi))
                        self::report(403);
                    else
                        self::fromApi(['403 page forbidden'], 403);

                    exit();
                }
            }
            
            /**
             * CSRF Middleware
             */
             if( empty($sapi) && sizeof( (array)$requestFrame['form_params']) > 0 ){
                
                if(!isset($requestFrame['form_params']->CSRF_FLAG))
                    throw new CorsException('Data transfer request a valid CSRF token, NULL given');

                if ( new Csrf($requestFrame['form_params']->CSRF_FLAG) == false )
                    throw new CorsException('Insecure form data transfer, CSRF token missing');
             }

             if($requestFrame['as-view']){
                 /**
                  * Verify View File existence
                  */
                 if( !file_exists($AppPath."/view/{$requestFrame['view']}.php") )
                     throw new \DomainException("Couldn't found file of <b>{$requestFrame['view']} View</b>");

                 View::render($requestFrame['view'], $requestFrame['view_data']);

             }else{
                 /**
                  * Verify Controller File existence
                  */
                 if( !file_exists($AppPath."/controller/{$sapi}/{$requestFrame['controller']}.php") )
                     throw new \DomainException("Couldn't found file of <b>{$requestFrame['controller']} Controller</b>");

                 /**
                  * Proceed to Engage Controller
                  */
                 $controller = (new self())->get_controller_classname($AppPath."/controller/{$sapi}/{$requestFrame['controller']}.php");

                 /**
                  * Verify Class Exist in Controller File
                  */
                 if(!class_exists($controller))
                     throw new \DomainException("Couldn't found class of {$requestFrame['controller']} controller");

                 /**
                  * Instantiate Controller
                  */
                 $controller = new $controller;

                 /**
                  * Verify View Conveyor of Interest Exist in Controller Class
                  */
                 if(method_exists($controller, $requestFrame['view']) ) {

                     /**
                      * Initialize View Conveyor Request.
                      */
                     $controller->{$requestFrame['view']}( $param );
                     unset($requestFrame);

                 }else{
                     throw new \DomainException("Couldn't found <b>{$requestFrame['view']}</b> View Conveyor in <b>{$requestFrame['controller']}</b> {$requestFrame['sapi']} Controller");
                 }

             }


        }catch(\BadMethodCallException $t){
            new ErrorTracer($t);
        }catch(\InvalidArgumentException $t){
            new ErrorTracer($t);
        }catch(\RangeException $t){
            new ErrorTracer($t);
        }catch(\DomainException $t){
            new ErrorTracer($t);
        }catch(\Throwable | CorsException $t){
            var_dump($t);
            new ErrorTracer($t);
        }

    }    

    /**
     * Request from Api
     *
     * @param array $data
     * @param integer $status
     * @return string
     */
    public static function fromApi(array $data, int $status) : string {

        /** Clean all existing output including errors and non-json output */
        ob_end_clean();

        http_response_code($status);
        return json_encode($data);
    }

    /**
     * Normalized  Controller name
     *
     * @param string $controllerFile
     * @return void
     */
    private function get_controller_classname( string $controllerFile ) : string {

        try{
            $handle = fopen($controllerFile, 'r');

            $arr_name = explode('/', rtrim( str_replace("\\", "/", $controllerFile), '/') );
            $class_name = ucfirst( str_replace( '.php', null, end($arr_name) ) );

            unset($arr_name);

            while($line = fgets($handle)){

                if( preg_match("/[\s]*namespace[\s]+[\S]+/", $line, $match) ){
                    $namespace = preg_replace('/[\s]*namespace[\s]+/', null, $match[0]);
                    break;
                }
            }

            return str_replace(';', null, $namespace)."\\".$class_name;
        }catch(\Throwable $t){
            new ErrorTracer($t);
        } 
    }

   
    
}
?>
