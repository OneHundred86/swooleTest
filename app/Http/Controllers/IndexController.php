<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IndexController extends Controller
{
  public function indexPage(){
    return redirect('/chat');
  }

  public function chatPage(){
    return view('chat');
  }

  public function chat1Page(){
    return view('chat1');
  }
}



