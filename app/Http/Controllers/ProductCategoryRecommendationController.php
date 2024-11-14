<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\CountryEnum;
use App\Helpers\StringHelper;
use App\Repositories\CategoryRepository;
use App\Repositories\ClientRepository;
use App\Repositories\ProductCategoryRecommendationRepository;
use App\Repositories\ProductRepository;
use Illuminate\Http\Request;
use Throwable;

class ProductCategoryRecommendationController extends Controller
{
    public function __construct(
        private readonly ClientRepository $clientRepository,
        private readonly CategoryRepository $categoryRepository,
        private readonly ProductRepository $productRepository,
        private readonly ProductCategoryRecommendationRepository $productCategoryRecommendationRepository
    ) {
    }
    public function add(string $countryCode, string $serviceUrlPath, string $language, string $eshopId, Request $request): \Illuminate\Http\RedirectResponse
    {
        $country = CountryEnum::getByValue($countryCode);
        $client = $this->clientRepository->getByEshopId((int) $eshopId);
        if ($request->input('category') === null) {
            $category = null;
        } else {
            $category = $this->categoryRepository->getForClient($client, $request->input('category'));
        }
        if ($request->input('product') === null) {
            return redirect()->route('client.settings', ['country' => $country->value, 'serviceUrlPath' => $serviceUrlPath, 'language' => $language, 'eshop_id' => $eshopId])->with('error', __('general.product_empty_not_allowed'));
        }
        $product = $this->productRepository->getForClient($client, $request->input('product'));
        if ($request->input('is_forbidden') === null) {
            $isForbidden = false;
        } else {
            $isForbidden = StringHelper::getBool($request->input('is_forbidden'));
        }
        try {
            $this->productCategoryRecommendationRepository->create($client, $product, $category, $isForbidden);
        } catch (Throwable) {
            return redirect()->route('client.settings', ['country' => $country->value, 'serviceUrlPath' => $serviceUrlPath, 'language' => $language, 'eshop_id' => $eshopId])->with('error', __('general.error'));
        }
        return redirect()->route('client.settings', ['country' => $country->value, 'serviceUrlPath' => $serviceUrlPath, 'language' => $language, 'eshop_id' => $eshopId])->with('success', __('general.success'));
    }

    public function delete(string $countryCode, string $serviceUrlPath, string $language, string $eshopId, Request $request): \Illuminate\Http\RedirectResponse
    {
        $country = CountryEnum::getByValue($countryCode);
        try {
            $productCategoryRecommendationRepository = $this->productCategoryRecommendationRepository->get((int) $request->input('id'));
            $this->productCategoryRecommendationRepository->delete($productCategoryRecommendationRepository);
        } catch (Throwable) {
            return redirect()->route('client.settings', ['country' => $country->value, 'serviceUrlPath' => $serviceUrlPath, 'language' => $language, 'eshop_id' => $eshopId])->with('error', __('general.error'));
        }
        return redirect()->route('client.settings', ['country' => $country->value, 'serviceUrlPath' => $serviceUrlPath, 'language' => $language, 'eshop_id' => $eshopId])->with('success', __('general.success'));
    }
}
