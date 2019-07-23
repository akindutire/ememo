<?php
/**
 * Author: Akindutire Ayomide Samuel
 */
namespace zil\core\writer;

use zil\core\interfaces\Writer;
use zil\core\TextProcessor;
use zil\core\scrapper\Info;

use zil\factory\Filehandler;

class Model implements Writer
{

    public function __construct(){}

    public function create(Info $Info) {


    }

    public function scaffold(Info $Info, string $table, string $context, array $modelAttribs){

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
                $class_passed = false;
                $method_write_init = false;
                $model_name = ucfirst($table);


                foreach ($context_arr as $key => $blueprint ) {

                    /** Skip empty lines */
                    if(strlen( trim($blueprint) ) === 0 && !$method_write_init)
                        continue;

                    if(TextProcessor::IfExact("namespace src\\model;", $blueprint))
                        $blueprint = "namespace src\\$app_name\model;\n\n";


                    if(preg_match("/class[\s]+(User|{$model_name})[\s]*\{?[\s]*/", $blueprint, $m)){
                        $blueprint = "\n\tclass {$model_name} {\n";

                        $class_found = true;
                    }

                    /**
                     * Write all existing lines before or on class line
                     */
                    if(!$class_passed)
                        $context .= "$blueprint\n";

                    /**
                     * Wrap and ReWrite lines before methods
                     * In between the class and methods
                     */

                    if($class_found){

                        if( (trim($blueprint) == "use Model;") && (strlen(trim($blueprint)) != 0) )
                            $context .= "\t\tuse Model;\n\n";

                        foreach($modelAttribs as $attrib){

                            $attrib_of_interest = "\t\tpublic \${$attrib} = null;\n";

                            if( ($attrib_of_interest != $blueprint) && (strlen($blueprint) != 0) )
                                $context .= $attrib_of_interest;
                            else
                                $context .= null;
                        }

                        $attrib_of_interest = "\n\n\t\tpublic static \$table = '{$table}';\n\t\tpublic static \$key = '';\n\n";
                        if( ($attrib_of_interest != $blueprint) && (strlen($blueprint) != 0) )
                            $context .= $attrib_of_interest;


                        $class_found = false;
                        $class_passed = true;
                    }


                    /**
                     * Method found by implication closes the class and file
                     */

                    if( preg_match("/(public|private|protected)([\s]+static)?[\s]+function[\s]+[\w]+[\s\S]+/", $blueprint, $m) )
                        $method_write_init = true;
                    /**
                     * Write all existing methods after the class line
                     */

                    if($class_passed && $method_write_init)
                        $context .= "$blueprint\n";

                    unset($context_arr[$key]);
                }

                /** Add closing tags manually if no methods were found */
                if($method_write_init === false) {
                    $context .= "\n\t}\n?>";
                    preg_replace("/(\?>)/", '?>', $context);
                }

                (new Filehandler())->createFile("{$app_base}model/".$model_name.".php", $context);


                unset( $context, $blueprint);


            }

        }catch(\Throwable $e){
            print($e->getMessage().' on line '.$e->getLine().' ('.$e->getFile().")\n");
        }finally{
            print "Model creation  closed\n";
        }

    }

    public function destroy(Info $Info, string $name){

    }

}
?>