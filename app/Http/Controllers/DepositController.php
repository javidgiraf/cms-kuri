<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService;

class DepositController extends Controller
{
    
    public function index(UserService $userService)
    {
        $users = $userService->getUsersWithSchemes();

        return view('deposits.index', compact('users'));
    }
}
