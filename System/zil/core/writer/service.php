<?php
/**
 * Author: Akindutire Ayomide Samuel
 */
namespace zil\core\writer;

use zil\core\interfaces\Writer;
use zil\core\TextProcessor;
use zil\core\scrapper\Info;

use zil\factory\Filehandler;

    class Service  implements Writer
    {

        public function __construct(){}

        public function create(Info $Info, string $service) {
            
            try{
                
                if( ($Info->getAppBase() !== null) && ($Info->getAppName() !== null) ){
                    $app_base = $Info->getAppBase();
                    $app_name = $Info->getAppName();
                }else{
                    throw new \Exception("Error: Couldn't resolve app directories");
                }

                $tree = $Info->getTree();
                $app_name = $Info->getAppName();
                
                if(is_null(@$tree->apps->{$app_name})){
                    
                    echo "Error: {$app_name} not recognised";
                    return false;
                }
                
                if(!empty($app_base) ){

                    $service = ucfirst($service);

                    if($service == null){
                        $service = "Home";

                    }else{
                        if( file_exists("{$app_base}service/{$service}.php") )
                            return false;
                    }        

                    if(!file_exists("{$app_base}service/{$service}.php")) {
                        
                        $Info->getProgressMessage("createService");

                        $application_buffering_point = $Info->getReadPoint();
                        
                        if( !file_exists("{$application_buffering_point}service/Service.php"))
                            die("Error: couldn't load template service");

                        $context_handle = fopen("{$application_buffering_point}service/Service.php", 'rb');
                        $context = null;

                        $arr_name = explode('/', rtrim(str_replace("\\", "/", $service), '/') );
                        $class_name = ucfirst(end($arr_name));
                        unset($arr_name[sizeof($arr_name) -1]);

                        while ($blueprint = fgets($context_handle) ) {

                            if(TextProcessor::IfExact("namespace src\\service;", $blueprint)){
                                
                                $service_namespace = null;
                                if(sizeof($arr_name) > 0)
                                    $service_namespace = '\\'.implode('/', $arr_name);

                                $blueprint = "namespace src\\$app_name\service{$service_namespace};\n";
                            }

                            if(TextProcessor::IfExact("use src\\Config;", $blueprint))
                                $blueprint = "\tuse src\\$app_name\config\Config;\n";
                            
                            if(TextProcessor::IfExact("class Home{", $blueprint))
                                $blueprint = "\tclass {$class_name}{";
                    
                            $context .= $blueprint;
                        }

                        (new Filehandler())->createFile("{$app_base}service/{$service}.php", $context);

                        unset( $context, $blueprint);
                        fclose($context_handle);
                        
                    }else{
                        echo "Error: Couldn't create {$service} service, {$service} exists\n";
                    }
                }
            }catch(\Exception $e){

                print($e->getMessage().' on line '.$e->getLine().' ('.$e->getFile().")\n");
            }catch(\Throwable $e){
                print($e->getMessage().' on line '.$e->getLine().' ('.$e->getFile().")\n");
            }finally{
                print "Service creation closed\n";
            }
        } 

        public function destroy(Info $Info, string $serviceName){

            try{
                
                if( ($Info->getAppBase() !== null) && ($Info->getAppName() !== null) ){
                    $app_base = $Info->getAppBase();
                    $app_name = $Info->getAppName();

                    $serviceName = ucfirst($serviceName);
                    
                }else{
                    throw new \Exception("Error: Couldn't resolve app directories");
                }
                print("Destroying {$serviceName} service\n");
                @unlink("{$Info->getAppDir()}src/{$app_name}/service/{$serviceName}.php");

                
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