<?php
/**
 * Author: Akindutire Ayomide Samuel
 */
namespace zil\core\writer;

use zil\core\interfaces\Writer;
use zil\core\TextProcessor;
use zil\core\scrapper\Info;

use zil\factory\Filehandler;


    class Init  implements Writer
    {

        public $_isRoot = false;

        public function __construct(){}
            

        public function create(Info $Info){

            try{

                if( ($Info->getAppBase() !== null) && ($Info->getAppName() !== null) ){

                    $app_base = $Info->getAppBase();
                    $app_name = $Info->getAppName();
                    $AppDir = $Info->getAppDir();
        
                }else{
                    throw new \Exception("Error: Couldn't resolve app directories");
                }

                
                $application_buffering_point = $Info->getReadPoint();
                
                if (!file_exists("{$application_buffering_point}/index.php"))
                    die("Error: couldn't load template initialisation file");

                

                $context_handle = fopen("{$application_buffering_point}/index.php", 'rb');
                $context    = null;
                

                    while ($blueprint = fgets($context_handle) ) {
                    
                        if(TextProcessor::IfExact("include_once 'vendor/autoload.php';", $blueprint))
                            $blueprint = "\tinclude_once \"{$AppDir}System/vendor/autoload.php\";\n\tinclude_once \"{$AppDir}vendor/autoload.php\";\n";
                                            
                        if(TextProcessor::IfExact("include_once 'zil/main.php';", $blueprint))
                            $blueprint = "\tinclude_once \"{$AppDir}System/zil/main.php\";";
                    
                        if(TextProcessor::IfExact("use src\\Config;", $blueprint) )
                            $blueprint= "\tuse src\\$app_name\config\Config;";                    
                                            
                        $context .= $blueprint;
                    }

                
                    $app_init_point     =   "{$AppDir}{$app_name}/";
                    
                    if( !file_exists("{$AppDir}/index.php") ) {
                    
                        $app_init_point  =   $AppDir;  
                        $this->_isRoot   =   true;
                    }
                    
                    (new Filehandler())->createFile("{$app_init_point}index.php", $context);
                   
                    unset($context, $blueprint);
                    fclose($context_handle);

            }catch(\Exception $e){
                
                print($e->getMessage().' on line '.$e->getLine().' ('.$e->getFile().")\n");
            }catch(\Throwable $e){
                print($e->getMessage().' on line '.$e->getLine().' ('.$e->getFile().")\n");
            }finally{
                
            }
        }

        public function destroy(Info $Info, ?string $name = null){
            try{
                unlink("{$Info->getAppDir()}index.php");
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