<?php
namespace zil\core\interfaces;

interface Guard{

    public function validate(Param $param): \Closure;

}

?>