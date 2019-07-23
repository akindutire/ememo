<?php
namespace src\memoboard\migration;


    use zil\factory\Schema;
    use zil\core\interfaces\Migration;
	
	class Add_name_to_users implements Migration{
        /**
         * Attributes to be created
         *
         * @return void
         */
        public function set(){

            /**
             * New Schema or Connect to existing schema
             */
                
			$schema = new Schema('Users');
            $schema->build('subject')->String()->After('ito_ID');


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