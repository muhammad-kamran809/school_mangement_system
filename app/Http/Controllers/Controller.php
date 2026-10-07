<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Api\Concerns\PaginatesApiResponse;

abstract class Controller
{
    use PaginatesApiResponse;
}
