<?php
use App\Helpers\QrHelper;
?>
@include('../layouts.header')
<div class="container">
  <div class="card m-4">
    <div class="card-header text-center font-weight-bold">
        <h4>{{ __('easy-upsell.addon_title') }} - {{ __('general.settings') }}</h4>
    </div>
    <div class="card-body">
        @include('flashMessage')
        <form method="POST" action="{{ route('client.saveSettings', ['country' => $country, 'serviceUrlPath' => $service->getUrlPath(), 'language' => $language, 'eshopId' => $client->getEshopId()]) }}">
        @csrf
        @foreach ($settings_service as $setting)
            <div class="form-group row mt-4">
                <label for="settings_{{ $setting->name }}" class="col-md-6 col-md-form-label">{{ __($setting->name) }}:</label>
                <div class="col-md-6">
                    @if ($setting->type == 'value')
                        <input type="text" class="form-control" name="{{ $setting->id }}_value" id="{{ $setting->id }}_value" value="{{ $setting->getValue($client) }}" placeholder="{{ __( $service->getName() . '.' . $setting->id . '_placeholder')}}">
                    @elseif ($setting->type == 'select')
                        <select class="form-control" name="{{ $setting->id }}" id="{{ $setting->id }}">
                            @foreach ($setting->settingsServicesOptions as $option)
                                <option
                                    @if ($option->isSelected($client))
                                        selected
                                    @endif
                                    value="{{ $option->id }}"
                                >{{ __($option->name) }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
            </div>
        @endforeach
        </div>
        <div class="row mt-4">
            <div class="col-md-12 text-center">
                <input type="hidden" name="eshop_id" value="{{$client->eshop_id}}">
                <button type="submit" class="btn btn-primary m-3">{{ __('general.save') }}</button>
            </div>
        </div>
      </form>
      <div class="card-body">
            <h4>{{ __('easy-upsell.licence')}}</h4>
            <div class="form-group row mt-4">
                <div class="col-md-12 mb-4">
                    @if (count($licences) > 0)
                        @foreach ($licences as $licence)
                            <label>{{ __('easy-upsell.valid')}}: {{ $licence->valid_to }}</label>
                        @endforeach
                    @else
                        <label>{{ __('easy-upsell.no_licence')}}.</label>
                    @endif
                </div>
            </div>
            <div class="form-group row mt-4">
                <div class="col-md-12 mb-4">
                    <label>{{ __('easy-upsell.licence_info')}}</label>
                </div>
            </div>
            <div class="form-group row mt-4">
                <div class="col-md-12 mb-4">
                    <label>{!! __('easy-upsell.licence_monthly_payment', ['variable' => $variable_symbol]) !!}</label>
                </div>
                <div class="col-md-12 mb-4">
                    @if (Config::get('app.locale') == 'sk') 
                        <img src="{{ QrHelper::requestPayment(200, 'CZ3520100000002302969385', 19.90, $variable_symbol, 'EUR') }}"/>
                    @else
                        <img src="{{ QrHelper::requestPayment(200, 'CZ2206000000000258219536', 490, $variable_symbol, 'CZK') }}"/>
                    @endif
                </div>
            </div>
            <div class="form-group row mt-4">
                <div class="col-md-12 mb-4">
                    <label>{!! __('easy-upsell.licence_yearly_payment', ['variable' => $variable_symbol]) !!}</label>
                </div>
                <div class="col-md-12 mb-4">
                    <div class="text-start">
                        @if (Config::get('app.locale') == 'sk') 
                            <img src="{{ QrHelper::requestPayment(200, 'CZ3520100000002302969385', 199, $variable_symbol, 'EUR') }}"/>
                        @else
                            <img src="{{ QrHelper::requestPayment(200, 'CZ2206000000000258219536', 4990, $variable_symbol, 'CZK') }}"/>
                        @endif
                    </div>
                </div>
            </div>
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('recommendation.add', ['country' => $country, 'serviceUrlPath' => $service->getUrlPath(), 'language' => $language, 'eshopId' => $client->getEshopId()]) }}">
            @csrf
            <h4>{{ __('easy-upsell.category_recommendation_title')}}</h4>
            <div class="form-group row mt-4">
                <div class="col-md-12 mb-4">
                    <label>{{ __('easy-upsell.category_recommendation_info')}}</label>
                </div>
            </div>
            <div class="form-group row mt-4">
                <div class="col-md-4">
                    <label>{{ __('easy-upsell.category')}}</label>
                </div>
                <div class="col-md-6">
                    <select class="form-control" name="category" id="category">
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ __($category->name) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group row mt-4">
                <div class="col-md-4">
                    <label>{{ __('easy-upsell.product')}}</label>
                </div>
                <div class="col-md-6">
                    <input type="text" class="form-control" name="product_autocomplete" id="product_autocomplete" placeholder="{{ __('general.product_search') }}">
                    <div id="autocomplete_results" class="list-group mt-2"></div>
                    <input type="hidden" name="product" required>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-12 text-center">
                    <input type="hidden" name="eshop_id" value="{{$client->getEshopId()}}">
                    <button type="submit" class="btn btn-primary m-3">{{ __('general.insert') }}</button>
                </div>
            </div>
        </form>
      </div>
      <div class="card-body">
        @if (count($product_category_recommendations) > 0)
            <table class="table">
                <thead>
                    <tr>
                        <th>{{ __('easy-upsell.category')}}</th>
                        <th>{{ __('easy-upsell.product')}}</th>
                        <th>{{ __('general.delete')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($product_category_recommendations as $productCategoryRecommendation)
                        <tr>
                            <td>{{ $productCategoryRecommendation->category->name }}</td>
                            <td>{{ $productCategoryRecommendation->product->name }}</td>
                            <td>
                                <form method="POST" action="{{ route('recommendation.delete',
                                        [
                                            'country' => $country,
                                            'serviceUrlPath' => $service->getUrlPath(),
                                            'language' => $language,
                                            'eshopId' => $client->eshop_id
                                        ]
                                    ) }}">
                                    @csrf
                                    @method('DELETE')
                                    <input type="hidden" name="id" value="{{ $productCategoryRecommendation->id }}">
                                    <button type="submit" class="btn btn-danger">{{ __('general.delete') }}</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <p>{{ __('easy-upsell.no_recommendations')}}</p>
        @endif
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('client.sync', ['country' => $country, 'serviceUrlPath' => $service->getUrlPath(), 'language' => $language, 'eshopId' => $client->getEshopId()]) }}">
            @csrf
            <div class="form-group row mt-4">
                <div class="col-md-12 mb-4">
                    <label>{{ __('general.sync_info')}}</label>
                </div>
                <div class="col-md-6">
                    <label>{{ __('general.last_synced_at')}}:</label>
                </div>
                <div class="col-md-6">
                    @if ($update_in_process === 1)
                        <label>{{ __('general.sync_in_progress') }}</label>
                    @elseif ($last_synced === null)
                        <label>{{ __('general.not_synced_yet') }}</label>
                    @else
                        <label>{{ $last_synced->format('d.m.Y') }}</label>
                    @endif
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-12 text-center">
                    <input type="hidden" name="eshop_id" value="{{$client->getEshopId()}}">
                    <button type="submit" class="btn btn-secondary m-3">{{ __('general.sync_now') }}</button>
                </div>
            </div>
        </form>
      </div>
    </div>
  </div>
</div>
@include('../script.product_autocomplete')
@include('../layouts.footer')
