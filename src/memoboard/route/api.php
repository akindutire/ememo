<?php
namespace src\memoboard\route;

use \zil\core\interfaces\Route;
use \zil\core\server\Resource;

/**
 *   Api Routes
 */

class Api implements Route{

	use \zil\core\facades\decorators\Route_D1;

		/**
		 * Api routes
		 *
		 * @return array
		 */
		public function route(): array{
			
			return [
				'get' 	=> 	$this->get(),
				'post'	=> 	$this->post(),
				'put' 	=> 	$this->put(),
				'delete'=>	$this->delete()
			];
		}

		private function get() : array {

				return [];
		}

		private function post():array{

			return [
			    'auth' => new Resource('Auth@LoginUser'),
                'create-new-user' => new Resource('FormProcessor@CreateUser'),
                'send-memo' => new Resource('FormProcessor@CreateMemo'),
                'fetchRecipientDetails' => new Resource('RecordProcessor@GetRecipientDetails'),
                'create-memo-template' => new Resource('FormProcessor@CreateMemoTemplate'),
            ];
		}
		
		private function put():array{

			return [];
		}
    
		private function delete():array{

			return [];
		}
	
}
	
?>
