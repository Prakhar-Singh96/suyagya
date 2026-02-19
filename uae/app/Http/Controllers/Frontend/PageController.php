<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function about()
    {
        return view('frontend.pages.about');
    }

    public function contact()
    {
        return view('frontend.pages.contact');
    }

    public function terms()
    {
        return view('frontend.pages.terms_conditions');
    }

    public function refund_policy()
    {
        return view('frontend.pages.refund_policy');
    }

    public function privacy_policy()
    {
        return view('frontend.pages.privacy_policy');
    }

    public function support_policy()
    {
        return view('frontend.pages.support_policy');
    }

}
