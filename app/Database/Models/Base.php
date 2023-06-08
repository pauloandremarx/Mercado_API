<?php

namespace App\Database\Models;

use App\traits\Read;
use App\Traits\Create;
use App\Traits\Delete;
use App\Traits\Update;
use App\Traits\Connection;

abstract class Base
{
    use Create,Read,Update,Delete, Connection;
}
