<?php
namespace zil\core\interfaces;

interface Config{

    public function getAppName():string;
    
    public function getDatabaseParams():array;

    public function getRoutes(): array;

    public function options(): array;
}

?>