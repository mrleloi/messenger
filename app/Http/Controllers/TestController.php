<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index(Request $request) {
        if (auth('web')->check()) echo 'Logged web!';
        if (auth('employee')->check()) echo 'Logged employee!';
        if (auth('admin')->check()) echo 'Logged admin!';
        $user = messenger()->getProvider();
        echo 'provider - '. $user;
        $emp = $request->user();
        echo 'emp - '. $emp;
        die();
    }
}
