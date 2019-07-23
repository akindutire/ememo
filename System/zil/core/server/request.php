<?php
namespace zil\core\server;

use zil\core\config\Config;
use zil\core\exception\UnexpectedRouteException;
use zil\core\exception\UnexpectedCaseException;
use zil\core\scrapper\Info;
use zil\core\tracer\ErrorTracer;
use zil\core\facades\helpers\Reporter;
use zil\factory\Session;
use zil\factory\Logger;

class Request extends Config{

    use Reporter;

    private $cfg = null;
    private $uri    = '';
    private $method = null;

    private const GET       =   'GET';
    private const POST      =   'POST';
    private const PUT       =   'PUT';
    private const DELETE    =   'DELETE';

    private const VERB_RANGE = [ 'GET', 'POST', 'PUT', 'DELETE'];

    public $data = [];

    public function __construct(){
        $this->cfg = new Config;
    }

    public function Uri(string $urlrequestkey){

        try{

            $cfg = $this->cfg;

            if(isset($_REQUEST[$urlrequestkey])){
                unset($_REQUEST[$urlrequestkey]);
            }

            #this mimics the htacess incase it's not enabled or you use the php built-in webserver
            list($REQUEST_BASE,  $REQUEST_URI) = [ trim($cfg->requestBase, '/'), trim($_SERVER['REQUEST_URI'], '/') ];


            /** @var * $REQUEST_BASE : Remove redundant url seperator */
            $REQUEST_BASE = preg_replace('/\/+/', '/', $REQUEST_BASE);
            $uri = $REQUEST_BASE != '/' || !empty($REQUEST_BASE) ? str_replace($REQUEST_BASE, '',  $REQUEST_URI ) : $REQUEST_URI;

            $this->uri = trim($uri, '/');

            unset($uri);


            return $this;

        }catch(\Throwable $t){
            new ErrorTracer($t);
        }

    }

    public function inBuiltSetUriAndMethod(string $uri, string $method){
        try{

            $this->uri = trim($uri, '/');

            $this->method = $method;

            return $this;

        }catch(\TypeError $t){
            new ErrorTracer($t);
        }catch(\Throwable $t){
            new ErrorTracer($t);
        }
    }

    public function getFrame(?array $formData=[]) : array{

        try{


            $cfg = $this->cfg;

            /**
             * Grab Normalized Request Uri
             */
            $uri = $this->uri;


            /**
             * Grab Request method such as http verbs(POST, GET, PATCH, ..)
             */
            $method = !is_null($this->method) ? $this->method : $_SERVER['REQUEST_METHOD'];

            /**
             * Forbids verbs not expected from routes
             */
            if( !in_array( strtoupper($method), self::VERB_RANGE) )
                throw new \DomainException(strtoupper($method)." method of exploring {$uri} is a Forbidden Http Verb");

            /**
             * PECL Regex expects to match all non spaced character
             */
            $pattern = '/^(\S+\/?)+$/';
            if (preg_match($pattern, $uri ) === 1 || empty($uri) ) {

                /**
                 * Good Uri Parsed, load All Routes from App Config.
                 */

                $appRoutes = $cfg->appRoutes;


                if(!is_array($appRoutes)){
                    throw new \RangeException("Routes expected an array, ".gettype($appRoutes)." given");
                }

                /**
                 * Initialize Controller-View(cv) Combination to null
                 * Uri Params to empty
                 * Form Params (if sent) to empty
                 */
                $Resource = null;
                $params = [];
                $form_params = $formData;
                $sapi = 'web';


                /**
                 * Split Request Uri for SAPI Extraction
                 */
                $requestArr = explode('/', $uri);

                if( $requestArr[0] == 'web' ||  $requestArr[0] == 'api' || !($requestArr[0] == 'web' ||  $requestArr[0] == 'api') ){

                    /**
                     * For Non-Singleton, Verbose and Dynamic Routing
                     */

                    /**
                     * Extract SAPI Used and Normalize Request Uri
                     */
                    if($requestArr[0] == 'web' ||  $requestArr[0] == 'api'){
                        $sapi = $requestArr[0];
                        unset($requestArr[0]);

                        /** @var Reset var $requestArr */
                        $requestArr = array_values($requestArr);
                    }else{
                        $sapi = 'web';
                    }

                    /**
                     * Simplify and Stringify Request Uri Segments and Masks
                     */
                    $r_uri = implode('/', $requestArr);

                    /**
                     * Load Associate Route-Table for the SAPI Extracted
                     */
                    $lookUpRoutes = $appRoutes[$sapi][strtolower($method)];

                    if(!is_array($lookUpRoutes)){
                        throw new \RangeException("Routes expected an array, ".gettype($lookUpRoutes)." given");
                    }

                    /**
                     * Proceed for Response on empty Route-Table
                     */

                    if(sizeof($lookUpRoutes) == 0){
                        $Resource = null;
                        goto pushRequest;
                    }

                    /**
                     * Indirect Route
                     */

                    $Resource = null;

                    foreach($lookUpRoutes as $verbose_route => $resource_with_or_without_headers){

                            if(empty($verbose_route))
                                continue;

                            if( !($resource_with_or_without_headers instanceof Resource) )
                                throw new UnexpectedRouteException("{$resource_with_or_without_headers} not of type zil\core\server\Resource : Route resource must be an instance of Resource");

                            $regex_route = null;
                            $route = null;
                            $verbose_route_is_valid = false;
                            /**
                             * Routes on Route-Table could be grouped or have alias seperated by |
                             */

                            foreach( explode('|',$verbose_route) as $extract){


                                $regex = preg_replace(
                                    [ '/\//','/\?+/', '/=+/', '/&+/', '/:[a-zA-Z_-]+/', '/[\s]+/' ],
                                    [ '\/','\?', '[\W]+', '[\W]+', '[^=|?|&|/|:]+', '[\s]' ],
                                    trim($extract) );

                                /**
                                 * Find Match for each alias,
                                 * consider the verbose route if at least on alias match the uri
                                 * */


                                if(preg_match_all( "(^$regex$)", trim($r_uri) ) === 1){
                                    $route = trim($extract);
                                    $verbose_route_is_valid = true;
                                    break;
                                }

                            }


                            if($verbose_route_is_valid){

                                /**
                                 * Valid Route and its segments
                                 */
                                $r_arr = explode('/', $route);
                                $u_arr = explode('/', $r_uri);
                                $params = [];

                                foreach($r_arr as $index => $segment){

                                    if($r_arr[$index] != $u_arr[$index]){

                                        if(preg_match("/\?/", $segment)){

                                            /**
                                             * Query Part
                                             */

                                            /**Route Query on Route-Table*/
                                            $r_query_segment = explode('?', trim($segment, '?') );

                                            /**Uri Query */
                                            $u_query_segment = explode('?', trim($u_arr[$index], '?') );

                                            /**Only a Single Query Part Can Exist on Uri and Route, 2 items on the segment array
                                             * 1. main part
                                             * 2. query part
                                             */


                                            if(count($r_query_segment) != 2 || count($u_query_segment) != 2)
                                                continue;

                                            /**Route and Uri Query Parameters */
                                            $r_queries = explode('&', trim($r_query_segment[1], '&') );
                                            $u_queries = explode('&', trim($u_query_segment[1], '&') );


                                            /**Skip when no query params. exist */
                                            if( count($r_queries) < 1 || count($u_queries) < 1 )
                                                continue;



                                            foreach($r_queries as $q_index => $query){

                                                /**
                                                 * Extract Queries and its corespponding keys
                                                 */
                                                list($q_key, $q_val) = explode('=', trim($query, '=') );
                                                list($u_key, $u_val) = explode('=', trim($u_queries[$q_index], '=') );


                                                $params[str_replace( ':', null, $q_key )] = $u_key;
                                                $params[str_replace( ':', null, $q_val )] = $u_val;


                                            }

                                        }else{
                                            $params[str_replace( ':', null, $r_arr[$index] )] = $u_arr[$index];
                                        }

                                    }
                                }

                                $Resource = $resource_with_or_without_headers;

                                unset($r_arr, $u_arr, $r_query_segment, $u_query_segment, $r_queries, $u_queries, $q_key, $q_val, $u_key, $u_val);


                                break;

                            }else{

                                if(isset($lookUpRoutes['404']))
                                    $Resource = $lookUpRoutes['404'];

                            }
                        }

                }



                pushRequest:

                if( !is_null($Resource) ){

                    list($guard, $allow, $deny) = [null, null, null];

                    if( sizeof($Resource->allowed) > 0  || sizeof($Resource->denials) > 0 || $Resource->guardTouched) {

                            /**
                             * Get Guard-Class
                             * Get List of ips to allow or deny
                             */
                            if( $Resource->guardTouched ){
                                if(count($Resource->guards) == 0){
                                    $guard = isset($cfg->appGuardClass) ? [$cfg->appGuardClass] : null;
                                }else{
                                    $guard = $Resource->guards;
                                }
                            }else{
                                $guard = null;
                            }


                            if(count($Resource->allowed) > 0)
                                $allow = $Resource->allowed;
                            else
                                $allow = null;


                            if(count($Resource->denials) > 0)
                                $deny = $Resource->denials;
                            else
                                $deny = null;

                    }

                    /** Treat View and Controller-View Resource differently */
                    if($Resource->as_view) {
                        $c = null;
                        $v = $Resource->context;
                    }else{
                            list($c, $v) = explode('@', $Resource->context);
                        }

                    if(!isset($c) || !isset($v))
                        throw new UnexpectedRouteException("Unexpected route, couldn't find a match key on $sapi route table through $method method");

                    /**
                     * Convert all Request Uri Params from Web and Api to Object
                     */

                     $params = (object)$params;

                    /**
                     * Convert all Request Body from Web and Api to Object
                     */
                    if($method != 'GET'){

                        if($sapi == 'api')
                            $form_params = sizeof($formData) == 0 ? json_decode(file_get_contents('php://input')) : $formData ;
                        else
                            $form_params = sizeof($formData) == 0 ? $_REQUEST : $formData;

                    }
                    $form_params = (object)$form_params;

                    /**
                     * Reckon SAPI Used
                     */
                    Info::$_dataLounge['SAPI_USED'] = $sapi;

                    /**
                     * Construct and Deliver Request Frame
                     */

                     return [
                         'sapi' => $sapi,
                         'controller' => $c,
                         'view' => $v,
                         'url_params' => $params,
                         'form_params' => $form_params,
                         'verb' => strtolower($method),
                         'guard' => $guard,
                         'allow' => $allow,
                         'deny' => $deny,
                         'as-view' => $Resource->as_view,
                         'data' => $Resource->data
                     ];

                }else{
                    /**
                     * Route not found
                     */
                    return [];
                }

            }else{
                badRequest:
                // Bad requeest
                return [];
            }

        }catch(\Throwable | UnexpectedRouteException | UnexpectedCaseException $t){
            new ErrorTracer($t);
        }
    }


}

?>