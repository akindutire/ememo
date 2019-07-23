<?php
/**
 * Author: Akindutire Ayomide Samuel
 */
namespace zil\core\writer;

use zil\core\interfaces\Writer;
use zil\core\scrapper\Info;
use zil\core\TextProcessor;

use zil\factory\Filehandler;


    class Config  implements Writer
    {

        public function __construct(Info $Info){

            try{

                if( ($Info->getAppBase() !== null) && ($Info->getAppName() !== null) ){
                    $app_base = $Info->getAppBase();
                    $app_name = $Info->getAppName();
                    $AppDir = $Info->getAppDir();
                
                }else{
                    throw new \Exception("Error: Couldn't resolve app directories");
                }

                if(!file_exists("{$app_base}config/Config.php")) {

                    $Info->getprogressMessage("createConfiguration");

                    $application_buffering_point = $Info->getReadPoint();
                    $config_file = "{$application_buffering_point}config/config.php";          
                    
                    if(!file_exists($config_file))
                        die("Error: couldn't load configuration file");

                    $context_handle = fopen($config_file, 'rb');
                    $context = null;

                    while ($blueprint = fgets($context_handle) ) {

                        if(TextProcessor::IfExact("namespace src\config;", $blueprint))
                            $blueprint = "namespace src\\$app_name\config;\n";

                        if(TextProcessor::IfExact("use src\\route\Web;", $blueprint))
                            $blueprint = "use src\\$app_name\\route\Web;\n";
                            
                        if(TextProcessor::IfExact("use src\\route\Api;", $blueprint))
                            $blueprint = "use src\\$app_name\\route\Api;\n";

                        if(TextProcessor::IfExact("private const APP_NAME	= '';", $blueprint))
                            $blueprint = "\tprivate const APP_NAME  = '{$app_name}';\n";
                      
                        $context .= $blueprint;
                    }

                    
                    (new Filehandler())->createFile("{$app_base}config/config.php", $context);

                    unset($base_path, $context, $blueprint);
                    fclose($context_handle);
                }

                return "{$app_base}config/config.php";
           
            }catch(\Exception $e){
                
                print($e->getMessage().' on line '.$e->getLine().' ('.$e->getFile().")\n");
            }catch(\Throwable $e){
                print($e->getMessage().' on line '.$e->getLine().' ('.$e->getFile().")\n");
            }finally{
                
            }
        }  
        
        public function destroy(Info $Info, string $name){
          try{

          }catch(\Exception $e){
                
            print($e->getMessage().' on line '.$e->getLine().' ('.$e->getFile().")\n");
          }catch(\Throwable $e){

          }finally{
            print "destruction closed";
          }

        }

    }
?>