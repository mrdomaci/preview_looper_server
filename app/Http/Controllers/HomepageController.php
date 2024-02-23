<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\LocaleHelper;
use App\Repositories\ServiceRepository;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Throwable;

class HomepageController extends Controller
{
    public function __construct(
        public readonly ServiceRepository $serviceRepository
    ) {
    }
    public function setLocale(string $locale): RedirectResponse
    {
        Session::put('locale', $locale);
        $originalRequestUrl = redirect()->back()->getTargetUrl();
        $requestUrlWithoutParams = explode('?', $originalRequestUrl)[0];
        return redirect($requestUrlWithoutParams);
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
        
        return View('welcome');
    }

    public function plugin(string $serviceUrlPath, Request $request): View
    {
        $locale = $request->input('locale');
        if ($locale === null && Session::has('locale') === false) {
            $locale = 'cs';
        }
        if ($locale !== null) {
            LocaleHelper::setLocale($locale);
        }

        try {
            $service = $this->serviceRepository->getByUrlPath($serviceUrlPath);
        } catch (Throwable) {
            abort(404);
        }
        return View($service->getViewName() . '.index', ['service_url_path' => $serviceUrlPath, 'title' => $service->getName()]);
    }

    public function terms(string $serviceUrlPath, Request $request): View
    {
        $locale = $request->input('locale');
        if ($locale === null && Session::has('locale') === false) {
            $locale = 'cs';
        }
        if ($locale !== null) {
            LocaleHelper::setLocale($locale);
        }

        try {
            $service = $this->serviceRepository->getByUrlPath($serviceUrlPath);
        } catch (Throwable) {
            abort(404);
        }
        
        return View($service->getViewName() . '.terms', ['service_url_path' => $serviceUrlPath, 'title' => $service->getName()]);
    }
}
