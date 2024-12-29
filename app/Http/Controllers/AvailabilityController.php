<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\CountryEnum;
use App\Repositories\AvailabilityRepository;
use App\Repositories\ClientRepository;
use Illuminate\Http\Request;
use Throwable;

class AvailabilityController extends Controller
{
    public function __construct(
        private readonly ClientRepository $clientRepository,
        private readonly AvailabilityRepository $availabilityRepository
    ) {
    }
    public function add(string $countryCode, string $serviceUrlPath, string $language, string $eshopId, Request $request): \Illuminate\Http\RedirectResponse
    {
        $country = CountryEnum::getByValue($countryCode);
        $client = $this->clientRepository->getByEshopId((int) $eshopId);
        if ($request->input('availability') === null) {
            return redirect()->route('client.settings', ['country' => $country->value, 'serviceUrlPath' => $serviceUrlPath, 'language' => $language, 'eshop_id' => $eshopId])->with('error', __('general.error'));
        } else {
            $availability = $this->availabilityRepository->getForClient($client, $request->input('availability'));
            $availability->setIsForbidden(true)->save();
        }
        return redirect()->route('client.settings', ['country' => $country->value, 'serviceUrlPath' => $serviceUrlPath, 'language' => $language, 'eshop_id' => $eshopId])->with('success', __('general.success'));
    }

    public function delete(string $countryCode, string $serviceUrlPath, string $language, string $eshopId, Request $request): \Illuminate\Http\RedirectResponse
    {
        $country = CountryEnum::getByValue($countryCode);
        try {
            $client = $this->clientRepository->getByEshopId((int) $eshopId);
            $availability = $this->availabilityRepository->getForClient($client, $request->input('foreign_id'));
            $availability->setIsForbidden(false)->save();
        } catch (Throwable $t) {
            return redirect()->route('client.settings', ['country' => $country->value, 'serviceUrlPath' => $serviceUrlPath, 'language' => $language, 'eshop_id' => $eshopId])->with('error', __('general.error'));
        }
        return redirect()->route('client.settings', ['country' => $country->value, 'serviceUrlPath' => $serviceUrlPath, 'language' => $language, 'eshop_id' => $eshopId])->with('success', __('general.success'));
    }
}
