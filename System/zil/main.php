<?php
declare(strict_types = 1);

namespace zil;

error_reporting(E_STRICT);

use zil\core\server\Router;
use zil\core\tracer\ErrorTracer;
use zil\core\directory\Tree;

use zil\core\exception\BadConfigurationException;

use zil\core\interfaces\Config;

use zil\core\middleware\Session as SessionMiddleware;

use zil\factory\Filehandler;



class App{

    use \zil\core\facades\helpers\Reporter;
    use \zil\core\facades\decorators\Route_D1;

    protected static $_curSysPath = null;
    protected static $_curAppPath = null;
    protected static $_databaseParams = [];
    protected static $_appRoutes = [];
    protected static $_eventLog = true;
    protected static $_requestBase = null;
    protected static $_configOptions = [ ];
    /**
     * @var Config
     */
    private $config;


    /**
     * Set up app
     *
     * @param Config $config
     * @param boolean $eventLog
     */
    public function __construct(Config $config, bool $eventLog = true ){

        try{
           
            if(in_array('zil\core\interfaces\Config', class_implements($config)) || in_array('\zil\core\interfaces\Config', class_implements($config))){

                self::$_curAppPath =  '/src/'.$config->getAppName().'/'; 
                self::$_databaseParams = $config->getDatabaseParams();
                self::$_appRoutes = $config->getRoutes();
                self::$_eventLog = (bool)$eventLog;
                self::$_curSysPath = __DIR__.'/';


                /**
                 * Get Request base
                 */
                self::$_requestBase = '/';
                if( !(new Tree())->getAppTree( $config->getAppName() )->root )
                    self::$_requestBase = "/".$config->getAppName().'/';

                /**
                 * Get Config Options
                 */
                if(sizeof($config->options()) > 0)
                    self::$_configOptions = $config->options();

                
                
                $this->SessionInit($config->getAppName());

               
                
            }else{
                throw new BadConfigurationException("Configuration class must implement zil\core\Config");
            }

         
        }catch(\InvalidArgumentException $t){
            new ErrorTracer($t);
        }catch(\BadMethodCallException $t){
            new ErrorTracer($t);
        }catch(\Throwable | BadConfigurationException $t){
            new ErrorTracer($t);
        }
        $this->config = $config;
    }

    /**
     * Initialize Session
     *
     * @return void
     */
    private function SessionInit(string $prefix): void{

        try{

            $projectBasePath = isset(self::$_configOptions['projectBasePath']) ? self::$_configOptions['projectBasePath'] : '/';
            $session_path = $_SERVER['DOCUMENT_ROOT'].'/'.$projectBasePath.str_replace("\\", "/", self::$_curAppPath)."/session/";

            if(!is_dir($session_path))
                    (new Filehandler())->createDir($session_path, 0775);
                
                SessionMiddleware::secureSession($session_path, $prefix);

        }catch(\Throwable $t){
            new ErrorTracer($t);
        }
    }

    /**
     * Bootstrap  the application
     *
     * 
     * @return void
     */
    public function start() {

        try{

            Router::Route('url_parameters');

            return null;
        }catch(\BadMethodCallException $t){
            
            new ErrorTracer($t);
        }
    }

    /**
     * Bootstrap app while on development mode
     *
     * @return void
     */
    public function dev(...$onlys){
        try{

            if(!in_array($this->ipDetect(), $onlys))
                self::report(503);
            else
                $this->start();
                        

        }catch(\TypeError $t){
            new ErrorTracer($t);
        }catch(\DomainException $t){
            new ErrorTracer($t);
        }catch(\Exception $t){
            new ErrorTracer($t); 
        }
    }

    public function stop(): void{
        try{

            list(self::$_appRoutes, self::$_curAppPath, self::$_databaseParams, self::$_curSysPath, self::$_eventLog) = [ [], null, [], null, null];

        }catch(\Throwable $t){
            new ErrorTracer($t);
        }
    
    }

}

?>