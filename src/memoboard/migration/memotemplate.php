<?php
namespace src\memoboard\migration;


    use zil\factory\Schema;
    use zil\core\interfaces\Migration;
	
	class Memotemplate implements Migration{
        /**
         * Attributes to be created
         *
         * @return void
         */
        public function set(){

            /**
             * New Schema or Connect to existing schema
             */
                
			$schema = new Schema('Memotemplate');
            $schema->build('id')->Primary()->Integer()->AutoIncrement();
            $schema->build('template')->String();
            $schema->build('created_at')->Timestamp();

            
        }

        /**
         * Attributes to be dropped
         *
         * @return void
         */
        public function drop(){
          

        }

      

    }
   

?>