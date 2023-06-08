<?php

namespace App\Traits;


use Exception;
trait Template
{
    public function getTwig()
    {
        try {
            return 'teste';
        } catch (Exception $e) {
            var_dump($e->getMessage());
        }
    }

    public function setView($name)
    {
        return $name;
    }
}
