<?php
namespace src\memoboard\controller;

	use \zil\core\server\Param;
	use \zil\core\server\Response;
    use zil\factory\Redirect;
    use zil\factory\Session;
    use \zil\factory\View;
	use \zil\core\facades\helpers\Notifier;
	use \zil\core\facades\helpers\Navigator;

	use src\memoboard\config\Config;
    use zil\security\Authentication;


    class Home{

		use Notifier, Navigator;

		public function Logout(Param $param){
		    Authentication::Destroy();
		    Session::delete('username');
		    $this->goTo('');
        }

		public function Login(Param $param){

			$OutputData = [];

			#render the desired interface inside the view folder

			View::render("Home/Login.php", $OutputData);
		}

		public function Index(Param $param){

			$OutputData = [];

			#render the desired interface inside the view folder

			View::render("Home/Index.php", $OutputData);
		}

		public function __construct(){
			
		}

		
				
	}

?>