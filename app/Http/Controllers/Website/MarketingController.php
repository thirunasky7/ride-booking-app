<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\Subscription;

class MarketingController extends Controller
{
    public function home()
    {
        $plans = Subscription::where('status', true)->orderBy('price')->take(3)->get();

        return view('website.marketing.home', compact('plans'));
    }

    public function about()
    {
        return view('website.marketing.about');
    }

    public function services()
    {
        return view('website.marketing.services');
    }

    public function pricing()
    {
        $plans = Subscription::where('status', true)->orderBy('price')->get();

        return view('website.marketing.pricing', compact('plans'));
    }

    public function contact()
    {
        return view('website.marketing.contact');
    }

    public function driverRegister()
    {
        return view('website.marketing.driver-register');
    }

    public function privacy()
    {
        return view('website.marketing.privacy');
    }

    public function terms()
    {
        return view('website.marketing.terms');
    }

    public function accountDeletion()
    {
        return view('website.marketing.account-deletion');
    }
}
