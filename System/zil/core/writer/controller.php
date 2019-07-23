<?php
/**
 * Author: Akindutire Ayomide Samuel
 */
namespace zil\core\writer;

use zil\core\interfaces\Writer;
use zil\core\TextProcessor;
use zil\core\scrapper\Info;

use zil\factory\Filehandler;

/**
 * Creates controller class and file in application
 */
    class Controller  implements Writer
    {

        /**
         * Constructor
         */
        public function __construct(){}

        /**
         * create the controller class
         *
         * @param Info $Info
         * @param string|null $controller
         * @param string|null $type
         * @return void
         */
        public function create(Info $Info, ?string $controller = null, ?string $type = null){

            try{

                if( ($Info->getAppBase() !== null) && ($Info->getAppName() !== null) ){
                    $app_base = $Info->getAppBase();
                    $app_name = $Info->getAppName();
                }else{
                    throw new \Exception("Error: Couldn't resolve app directories");
                }
                
                if( !empty($app_base) ){
                    
                    /*  Controller Buffering */
                    if($controller == null)
                        $controller = "Home";
                    else
                        ucfirst($controller);

                    if(!file_exists("{$app_base}controller/{$controller}.php")) {
                        
                        /**
                         * Highlight progress on controller creation
                         */
                        $Info->getprogressMessage("createController");

                        $application_buffering_point = $Info->getReadPoint();
                        
                        if(!file_exists("{$application_buffering_point}controller/Home.php"))
                            throw new \Exception("Error: couldn't load template controller");

                        /**
                         * Get Controller BluePrint
                         */
                        $context_handle = fopen("{$application_buffering_point}controller/Home.php", 'rb');
                        $context = null;

                        /**
                         * Get Classname
                         */
                        $arr_name = explode('/', rtrim(str_replace("\\", "/", $controller), '/') );
                        $class_name = ucfirst(end($arr_name));
                        unset($arr_name[sizeof($arr_name) -1]);
                                

                        /**
                         * Mutate Controller BluePrint
                         */
                        while ($blueprint = fgets($context_handle) ) {

                            if(TextProcessor::IfExact("namespace src\controller;", $blueprint)){
                                
                                $controller_namespace = null;
                                if(sizeof($arr_name) > 0)
                                    $controller_namespace = '\\'.implode('/', $arr_name);
                                    
                                $blueprint = "namespace src\\$app_name\controller{$controller_namespace};\n";
                            }

                            if(TextProcessor::IfExact("use src\Config;", $blueprint))
                                $blueprint = "\tuse src\\$app_name\config\Config;\n";

                            if(TextProcessor::IfExact("class Home{", $blueprint))    
                                $blueprint = "\tclass {$class_name}{\n";
                            
                            $context .= $blueprint;
                        }


                        (new Filehandler())->createFile("{$app_base}controller/{$controller}.php", $context);
                        
                        fclose($context_handle);
                        unset($arr_name, $context,  $blueprint);
                        
                    }else{
                        throw new \Exception("Error: Couldn't create {$controller} controller, {$controller} exists\n");
                    }
                }
                
            }catch(\Exception $e){
                
                print($e->getMessage().' on line '.$e->getLine().' ('.$e->getFile().")\n");
            }catch(\Throwable $e){
                print($e->getMessage().' on line '.$e->getLine().' ('.$e->getFile().")\n");
            }finally{

                if(empty($type))
                    print "Web controller creation closed\n";
                else
                    print "{$type} controller creation closed\n";
            }
        }

        /**
         * Create a controller and a view
         *
         * @param Info $Info
         * @param string $controller
         * @param string|null $view
         * @return void
         */
        public function createEx(Info $Info, string $controller, ?string $view = "index", bool $updateController = true){


            try{

                /**
                 * Details Gathering
                 */
                if( ($Info->getAppBase() !== null) && ($Info->getAppName() !== null) ){
                    $app_base = $Info->getAppBase();
                    $app_name = $Info->getAppName();
                }else{
                    throw new \Exception("Error: Couldn't resolve app directories");
                }


                if( !is_null($app_name) ){
    
                    if( !empty($controller) )
                        $this->create($Info, $controller);
                    
                    if( !empty($view) )
                        (new View())->create($Info, $view, $controller, $updateController);
                }else{
                    return false;
                }
            
            }catch(\Exception $e){
                
                print($e->getMessage().' on line '.$e->getLine().' ('.$e->getFile().")\n");
            }catch(\Throwable $e){

                print($e->getMessage().' on line '.$e->getLine().' ('.$e->getFile().")\n");
            }
        
        }

        /**
         * Destroy a controller
         *
         * @param Info $Info
         * @param string $controllerName
         * @return void
         */
        public function destroy(Info $Info, string $controllerName){
            try{


                if( ($Info->getAppBase() !== null) && ($Info->getAppName() !== null) ){
                    $app_base = $Info->getAppBase();
                    $app_name = $Info->getAppName();
                }else{
                    throw new \Exception("Error: Couldn't resolve app directories");
                }
                
                print "destroying {$controllerName}\n";
                
                @unlink("{$Info->getAppDir()}src/{$app_name}/controller/{$controllerName}.php");
                (new Filehandler())->removeDir("{$Info->getAppDir()}src/{$app_name}/view/{$controllerName}/");

            }catch(\Exception $e){
                
                print($e->getMessage().' on line '.$e->getLine().' ('.$e->getFile().")\n");
            }catch(\Throwable $e){

                print($e->getMessage().' on line '.$e->getLine().' ('.$e->getFile().")\n");
            }finally{
                print "destruction closed\n";
            }
        }
    }
?>