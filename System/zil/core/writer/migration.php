<?php
/**
 * Author: Akindutire Ayomide Samuel
 */
namespace zil\core\writer;

use zil\core\interfaces\Writer;
use zil\core\TextProcessor;
use zil\core\scrapper\Info;

use zil\factory\Filehandler;

    class Migration implements Writer
    {

        public function __construct(){}

        public function create(Info $Info, string $migration_name, string $model_name = null) {

            try{
                if( ($Info->getAppBase() !== null) && ($Info->getAppName() !== null) ){

                    $app_base = $Info->getAppBase();
                    $app_name = $Info->getAppName();
            
                }else{
                    throw new \Exception("Error: Couldn't resolve app directories");
                }

                if(!empty($app_base) ){

                  
                    if(!file_exists("{$app_base}migration/{$migration_name}.php")) {
                        
                        print "Creating {$migration_name} migration...\n";

                        $application_buffering_point = $Info->getReadPoint();
                        
                        if( !file_exists("{$application_buffering_point}migration/Migration.php"))
                        throw new \Exception("Error: couldn't load template migration");

                        $context_handle = fopen("{$application_buffering_point}migration/migration.php", 'rb');
                        $context = null;
                        

                        while ($blueprint = fgets($context_handle) ) {

                            if(TextProcessor::IfExact("namespace src\\migration;", $blueprint))
                                $blueprint = "namespace src\\$app_name\migration;\n";
                            

                            if(TextProcessor::IfExact("use src\\config;", $blueprint))
                                $blueprint = "use src\\$app_name\config\config;\n";
                            
                            if(TextProcessor::IfExact("class User implements Migration{", $blueprint))
                                $blueprint =  "\tclass ".ucwords($migration_name)." implements Migration{";

                            if(TextProcessor::IfExact("\$schema = new Schema('user');", $blueprint)){
                                $m = is_null($model_name) ? $migration_name : $model_name;
                                $blueprint = "\t\t\t\$schema = new Schema('".ucfirst($m)."');";
                            }
                            $context .= $blueprint;
                        }
                        

                        (new Filehandler())->createFile("{$app_base}migration/{$migration_name}.php", $context);
                        
                        
                        unset( $context, $blueprint);
                        fclose($context_handle);
                    }
                }
            }catch(\Throwable $e){
                print($e->getMessage().' on line '.$e->getLine().' ('.$e->getFile().")\n");
            }finally{

                print "Migration creation  closed\n";
            }
        } 
        
        public function createORM(Info $Info, string $model, string $context, array $modelAttribs){

            try{
                if( ($Info->getAppBase() !== null) && ($Info->getAppName() !== null) ){

                    $app_base = $Info->getAppBase();
                    $app_name = $Info->getAppName();
            
                }else{
                    throw new \Exception("Error: Couldn't resolve app directories");
                }

                if(!empty($app_base) ){

                        $Info->getProgressMessage("createModel");

                       
                        $context_arr = explode("\n", $context);
                        $context = null;
                        $class_found = false;
                        $model_name = ucfirst($model);

                        foreach ($context_arr as $blueprint ) {

                            if(TextProcessor::IfExact("namespace src\\model;", $blueprint))
                                $blueprint = "namespace src\\$app_name\model;\n";
                            
                            if(TextProcessor::IfExact("use src\\config;", $blueprint))
                                $blueprint = "use src\\$app_name\config\config;\n";
                            
                            if(preg_match("/class[\s]+(User|{$model_name})[\s]+extends[\s]+Model\{?/", $blueprint, $m)){
                                $blueprint = "\n\n\tclass {$model} extends Model{\n\n";
                                $class_found = true;
                            }
                            $context .= $blueprint;

                            if($class_found){

                                foreach($modelAttribs as $attrib){
                                    $context .= "\n\t\tpublic \${$attrib} = null;";
                                }
                                $context .= "\n\n";
                                $class_found = false;
                            }
                        }

                        unset($context_arr);

                        (new Filehandler())->createFile("{$app_base}model/".strtolower($model).".php", $context);
                    
                }

            }catch(Throwable $e){
                print($e->getMessage().' on line '.$e->getLine().' ('.$e->getFile().")\n");
            }finally{
                print "Model creation  closed\n";
            }

        }

        public function destroy(Info $Info, string $name){

        }
        
    }
?>