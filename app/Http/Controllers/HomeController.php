<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Show the home page
     */
    public function index()
    {
        return view('welcome');
    }

    /**
     * Show the about page
     */
    public function about()
    {
        return redirect()->to(route('home') . '#tentang-kami');
    }

    /**
     * Show the contact page
     */
    public function contact()
    {
        return redirect()->to(route('home') . '#kontak');
    }
}