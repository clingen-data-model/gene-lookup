<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    public function phpinfo()
    {
        phpinfo();
    }

    public function testRouteHelper()
    {
        return ['route 1' => route('login')];
    }
}
