<?php
namespace src\memoboard\model;
    use src\config\Config;
    use zil\core\tracer\ErrorTracer;
    use \zil\factory\Model;

	class Memotemplate {

	    use Model;

		public $id = null;
		public $name = null;
		public $template = null;
		public $created_at = null;


		public static $table = 'memotemplate';
		public static $key = 'id';

		public function createTemplate(string  $name, string $templateString) : bool {

		    try{
                $this->name = $name;
                $this->template = $templateString;

                if($this->create() == 1)
                    return true;
                else
                    return false;

            }catch (\Throwable $t){
		        new ErrorTracer($t);
            }
        }

        public function templateNameExists(string $templateName) : bool {
		    try{
		        if($this->where(['name', $templateName])->filter('id')->count() == 1)
		            return true;
		        else
		            return false;
            }catch (\Throwable $t){
		        new ErrorTracer($t);
            }
        }

        public function listTemplates() : array {
		    try{
		        return $this->all()->get('VERBOSE');
            }catch (\Throwable $t){
		        new ErrorTracer($t);
            }
        }
	}
?>