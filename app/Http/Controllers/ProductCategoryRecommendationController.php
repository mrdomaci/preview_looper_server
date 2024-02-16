<?php

namespace App\Http\Controllers;

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
    ) {}
    public function add(string $country, string $serviceUrlPath, string $language, string $eshopId, Request $request): \Illuminate\Http\RedirectResponse
    {
        $client = $this->clientRepository->getByEshopId((int) $eshopId);
        $category = $this->categoryRepository->getForClient($client, (int) $request->input('category'));
        $product = $this->productRepository->getForClient($client, (int) $request->input('product'));
        try {
            $this->productCategoryRecommendationRepository->create($client, $product, $category);
        }
        catch (Throwable) {
            return redirect()->route('client.settings', ['country' => $country, 'serviceUrlPath' => $serviceUrlPath, 'language' => $language, 'eshop_id' => $eshopId])->with('error', __('general.error'));
        }
        return redirect()->route('client.settings', ['country' => $country, 'serviceUrlPath' => $serviceUrlPath, 'language' => $language, 'eshop_id' => $eshopId])->with('success', __('general.success'));
    }

    public function delete(string $country, string $serviceUrlPath, string $language, string $eshopId, Request $request): \Illuminate\Http\RedirectResponse
    {
        try {
            $productCategoryRecommendationRepository = $this->productCategoryRecommendationRepository->get((int) $request->input('id'));
            $this->productCategoryRecommendationRepository->delete($productCategoryRecommendationRepository);
        }
        catch (Throwable) {
            return redirect()->route('client.settings', ['country' => $country, 'serviceUrlPath' => $serviceUrlPath, 'language' => $language, 'eshop_id' => $eshopId])->with('error', __('general.error'));
        }
        return redirect()->route('client.settings', ['country' => $country, 'serviceUrlPath' => $serviceUrlPath, 'language' => $language, 'eshop_id' => $eshopId])->with('success', __('general.success'));
    }
}
