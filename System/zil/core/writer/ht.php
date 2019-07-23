<?php
/**
 * Author: Akindutire Ayomide Samuel
 */
namespace zil\core\writer;

use zil\core\interfaces\Writer;
use zil\core\TextProcessor;
use zil\core\scrapper\Info;

use zil\factory\Filehandler;

    class Ht  implements Writer
    {

        public function __construct(){}

        public function create(Info $Info, bool $isRoot){

            try{
                if( ($Info->getAppBase() !== null) && ($Info->getAppName() !== null) ){
                    $app_base = $Info->getAppBase();
                    $app_name = $Info->getAppName();
                    $AppDir = $Info->getAppDir();
                }else{
                    throw new \Exception("Error: Couldn't resolve app directories");
                }
                
                $application_buffering_point = $Info->getReadPoint();
                
                if (!file_exists("{$application_buffering_point}/.htaccess"))
                    die("Error: couldn't load template initialisation file");


                $context_handle = fopen("{$application_buffering_point}/.htaccess", 'rb');
                
                $context    = null;
                
                while ($blueprint = fgets($context_handle) ) {
                    
                    if(TextProcessor::IfExact("RewriteBase /", $blueprint)){
                        $blueprint = "\tRewriteBase /{$app_name}\n";
                        if($isRoot)
                            $blueprint = "\tRewriteBase /\n";
                    
                    }                        
                                        
                    $context .= $blueprint;
                }
                    
                $app_init_point  =  "{$AppDir}{$app_name}/";
                
                if( $isRoot ) 
                    $app_init_point     =   $AppDir;  
                

                (new Filehandler())->createFile("{$app_init_point}.htaccess", $context);
            
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
                unlink("{$Info->getAppDir()}.htaccess");
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