<?php

namespace App\Http\Controllers;

use App\Helpers\LocaleHelper;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class HomepageController extends Controller
{
    public function setLocale(string $locale): RedirectResponse
    {
        Session::put('locale', $locale);
        return redirect()->back();
    }

    public function index(Request $request): View
    {
        $locale = $request->input('locale');
        if ($locale === null && Session::has('locale') === false) {
            $locale = 'cs';
        }
        if ($locale !== null) {
            LocaleHelper::setLocale($locale);
        }
        
        return View('welcome', ['footer_link' => 'layouts.terms_link']);
    }

    public function terms(Request $request): View
    {
        $locale = $request->input('locale');
        if ($locale === null && Session::has('locale') === false) {
            $locale = 'cs';
        }
        if ($locale !== null) {
            LocaleHelper::setLocale($locale);
        }
        
        return View('terms', ['footer_link' => 'layouts.homepage_link']);
    }
}
