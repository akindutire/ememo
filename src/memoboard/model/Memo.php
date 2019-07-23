<?php
namespace src\memoboard\model;
    use src\config\Config;
    use zil\core\tracer\ErrorTracer;
    use \zil\factory\Model;

	class Memo {
	    use Model;

		public $id = null;
		public $ifrom_ID = null;
		public $ito_ID = null;
		public $subject = null;
		public $message = null;
		public $ref = null;
		public $user_is_read_receipt = null;
		public $created_at = null;


		public static $table = 'memo';
		public static $key = 'id';

        /** Sense memo delivery
         *
         * @param int $memo_id
         * @return bool
         */
        public function MarkAsRead(int $memo_id): bool{
            try{
                $activeUserId = (new Users())->getUserId();
                $memo_receipient_id = $this->find($memo_id)->filter('ito_ID')->get()->ito_ID;

                /**
                * Is receiver of the mail on this procedure, then proceed
                 */

                if($memo_receipient_id == $activeUserId) {
                    $this->user_is_read_receipt = true;
                    $AffectedRows = $this->where(['id', $memo_id])->update();

                    if ($AffectedRows == 1)
                        return true;
                    else
                        return false;
                }

                return false;

            }catch (\Throwable $t){
                new ErrorTracer($t);
            }
        }

        public function messageNotExist() {

        }

        /**
         * @return array
         */
        public function myInfo() : array {
           try {
               $id = (new Users())->getUserId();

               $all = $this->all()->where(['ifrom_ID', $id, 'OR'], ['ito_ID', $id])->count();
               $read = $this->all()->where(['ito_ID', $id], ['user_is_read_receipt', true])->count();
               $unread = $this->all()->where(['ito_ID', $id], ['user_is_read_receipt', '<>', true])->count();

               return [
                   'all' => $all,
                   'read' => $read,
                   'unread' => $unread
               ];

           }catch (\Throwable $t){
               new ErrorTracer($t);
           }
        }

        public function getAllMyMemo() : array {

            try{

                $my_id = (new Users())->getUserId();
                if( $this->all()->where( ['ito_ID', $my_id, 'OR'], ['ifrom_ID', $my_id] )->count())
                   return $this->all()->where( ['ito_ID', $my_id, 'OR'], ['ifrom_ID', $my_id] )->desc()->get('VERBOSE');
                else
                    return [];

            }catch (\Throwable $t){

                new ErrorTracer($t);
            }
        }
	}
?>
