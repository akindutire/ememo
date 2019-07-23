<?php
namespace src\memoboard\controller;

	use src\memoboard\model\Memo;
    use src\memoboard\model\Memotemplate;
    use src\memoboard\model\Users;
    use src\memoboard\service\UserInfoScrapper;
    use \zil\core\server\Param;
	use \zil\core\server\Response;
    use zil\factory\Redirect;
    use \zil\factory\View;
	use \zil\core\facades\helpers\Notifier;
	use \zil\core\facades\helpers\Navigator;

	use src\memoboard\config\Config;
 
	

	class Dashboard{

		use Notifier, Navigator;

		
		public function UseTemplate(Param $param){

            if ($param->url()->template_id > 0){

                if(Memotemplate::find($param->url()->template_id)->count() == 1) {

                    $MemoTemplate = Memotemplate::find($param->url()->template_id)->get();

                }else {
                    new Redirect('memo-template');
                }
            }
            $OutputData = [
                'MemoTemplate' => $MemoTemplate,
                'AllUsers' => (new Users())->getAllUsersNameWithId(),
                'MyInfo' => (new Users())->getDetails(),
            ];

            #render the desired interface inside the view folder

			View::render("Dashboard/UseTemplate.php", $OutputData);
		}

		public function CreateTemplate(Param $param){

			$OutputData = [];

			#render the desired interface inside the view folder

			View::render("Dashboard/CreateTemplate.php", $OutputData);
		}

		public function ManageTemplate(Param $param){

            if ($param->url()->template_id > 0){

                if(Memotemplate::find($param->url()->template_id)->count() == 1) {

                    $MemoTemplate = Memotemplate::find($param->url()->template_id)->get();

                }else {
                    new Redirect('memo-template');
                }
            }
            $OutputData = [
                'MemoTemplate' => $MemoTemplate
            ];


			#render the desired interface inside the view folder

			View::render("Dashboard/ManageTemplate.php", $OutputData);
		}

		public function MemoTemplate(Param $param){

			$OutputData = [
			    'Templates' => (new Memotemplate())->listTemplates()
            ];

			#render the desired interface inside the view folder

			View::render("Dashboard/MemoTemplate.php", $OutputData);
		}

		public function ChangePass(Param $param){

			$OutputData = [];

			#render the desired interface inside the view folder

			View::render("Dashboard/ChangePass.php", $OutputData);
		}

		public function CreateUser(Param $param){

			$OutputData = [];

			#render the desired interface inside the view folder

			View::render("Dashboard/CreateUser.php", $OutputData);
		}

		public function CreateMemo(Param $param){

			$OutputData = [
			    'AllUsers' => (new Users())->getAllUsersNameWithId(),
                'MyInfo' => (new Users())->getDetails(),
            ];

			#render the desired interface inside the view folder

			View::render("Dashboard/CreateMemo.php", $OutputData);
		}

		public function ReadMemo(Param $param){

		    if ($param->url()->memo_id > 0){

		        if(Memo::find($param->url()->memo_id)->count() == 1) {
//                    Sense memo acknowledgement

                    (new Memo())->MarkAsRead($param->url()->memo_id);

                    $Memo = Memo::find($param->url()->memo_id)->get();

		        }else {
                    new Redirect('dashboard');
                }
            }
			$OutputData = [
			    'Memo' => $Memo
            ];

			#render the desired interface inside the view folder

			View::render("Dashboard/ReadMemo.php", $OutputData);
		}

		public function Index(Param $param){


			$OutputData = [
                'MemoInfo' => (new Memo())->myInfo(),
                'MemoAssociatedToMe' => (new Memo())->getAllMyMemo(),
            ];

			#render the desired interface inside the view folder

//            View::raw("Dashboard/Index.php");
			View::render("Dashboard/Index.php", $OutputData);
		}

		public function __construct(){
			
		}

		
				
	}

?>
