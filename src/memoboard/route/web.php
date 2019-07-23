<?php
namespace src\memoboard\route;

use \zil\core\interfaces\Route;
use \zil\core\server\Resource;

/**
 *   Web Routes
 */

class Web implements Route{

	use \zil\core\facades\decorators\Route_D1;

	    private const GUARD = '\src\memoboard\service\GuardL';

		/**
		 * Web routes, routes from browser
		 *
		 * @return array
		 */
		public function route(): array{

			return [
				'get' 	=> 	$this->get(),
				'post'	=> 	$this->post()
			];

		}


		private function get(): array{

			return [

			    '|' => new Resource('Home@Index'),

                'login' => new Resource('Home@Login'),

                'dashboard' => (new Resource('Dashboard@Index'))->guard(),

                'create-memo' => (new Resource('Dashboard@CreateMemo'))->guard(),

                'create-user' => (new Resource('Dashboard@CreateUser'))->guard(),

                'change-pass' => (new Resource('Dashboard@ChangePass'))->guard(),

                'read-memo/:memo_id' => (new Resource('Dashboard@ReadMemo'))->guard(),

                'memo-template' => (new Resource('Dashboard@MemoTemplate'))->guard(),

                'manage-template/:template_id' => (new Resource('Dashboard@ManageTemplate'))->guard(),

                'create-template' => (new Resource('Dashboard@CreateTemplate'))->guard(),

                'use-template/:template_id' => (new Resource('Dashboard@UseTemplate'))->guard(),

                'logout' => new Resource('Home@Logout')

            ];
		}

		private function post(): array{

			return [

			    'change-pass' => new Resource('api/FormProcessor@ChangePassword'),

            ];
		}
	
}
	
?>
