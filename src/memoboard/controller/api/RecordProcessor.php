<?php
namespace src\memoboard\controller\api;

    use src\memoboard\model\Users;

    use \zil\core\server\Param;
	use \zil\core\server\Response;
	use \zil\core\facades\helpers\Notifier;
	use \zil\core\facades\helpers\Navigator;


    use zil\security\Validation;


    class RecordProcessor{

		use Notifier, Navigator;

		
		public function __construct(){
			
		}

		public function GetRecipientDetails(Param $param){
             try {

                 $Validation = new Validation(['recipient_id', 'int']);
                 if ($Validation->isPassed()) {

                     $Recipient = (new Users())->getAnyUser(  intval($param->form()->recipient_id) );

                    $data = ['msg' => $Recipient, 'status' => true];

                 } else {
                    throw new \Exception("Recipient Id expects an integer behind the scence");
                 }

             }catch (\Throwable $t){
                 $data = [ 'msg' => $t->getMessage(), 'status' => false ];
             }  finally {
                 echo Response::fromApi($data, 200);
             }
        }
				
	}

?>