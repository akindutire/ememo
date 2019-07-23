<?php
namespace src\memoboard\migration;


    use zil\factory\Schema;
    use zil\core\interfaces\Migration;
	
	class Add_ref_to_memo implements Migration{
        /**
         * Attributes to be created
         *
         * @return void
         */
        public function set(){

            /**
             * New Schema or Connect to existing schema
             */
                
			$schema = new Schema('Memo');
            $schema->build('ref')->String()->After('message');
            
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