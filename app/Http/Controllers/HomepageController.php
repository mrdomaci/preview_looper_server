<?php

namespace App\Http\Controllers;

use App\Helpers\LocaleHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Session;

class HomepageController extends Controller
{
    public function setLocale(string $locale): RedirectResponse
    {
        Session::put('locale', $locale);
        return redirect()->back();
    }
}
