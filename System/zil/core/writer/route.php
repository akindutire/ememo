<?php
/**
 * Author: Akindutire Ayomide Samuel
 */
namespace zil\core\writer;

use zil\core\interfaces\Writer;
use zil\core\TextProcessor;
use zil\core\scrapper\Info;

use zil\factory\Filehandler;


    class Route  implements Writer
    {

        public function __construct(Info $Info){

            try{

                $AppDir = $Info->getAppDir();
                $app_name = $Info->getAppName();           
                $app_base = $Info->getAppBase();

                if(!file_exists("{$app_base}route/Api.php")) {

                    echo "Preparing Routes...\n";

                    $application_buffering_point = $Info->getReadPoint();
                    $api_route_file = "{$application_buffering_point}route/Api.php";
                    
                    if(!file_exists($api_route_file))
                        die("Error: couldn't load api route file");

                    $context_handle = fopen($api_route_file, 'rb');
                    $context = null;

                    while ($blueprint = fgets($context_handle) ) {

                        if(TextProcessor::IfExact("namespace src\\route;", $blueprint))
                            $blueprint = "namespace src\\$app_name\\route;\n";

                        $context .= $blueprint;
                    }

                    (new Filehandler())->createFile("{$app_base}route/Api.php", $context);
                }


                if(!file_exists("{$app_base}route/Web.php")) {

                    $application_buffering_point = $Info->getReadPoint();
                    $web_route_file = "{$application_buffering_point}route/Web.php";
                    
                    if(!file_exists($web_route_file))
                        die("Error: couldn't load web route file");

                    $context_handle = fopen($web_route_file, 'rb');
                    $context = null;

                    while ($blueprint = fgets($context_handle) ) {

                        if(TextProcessor::IfExact("namespace src\\route;", $blueprint))
                            $blueprint = "namespace src\\$app_name\\route;\n";

                        $context .= $blueprint;
                    }

                    (new Filehandler())->createFile("{$app_base}route/Web.php", $context);

                    unset( $context, $blueprint);
                    fclose($context_handle);
                }


               
            }catch(\Exception $e){
                
                print($e->getMessage().' on line '.$e->getLine().' ('.$e->getFile().")\n");
            }catch(\THrowable $e){
                
                print($e->getMessage().' on line '.$e->getLine().' ('.$e->getFile().")\n");
            }

        }    

        public function destroy(Info $Info, string $name){

            try{

            }catch(\Throwable $e){
            
            }finally{
                print "destruction closed";
            }


        }
    }
?>