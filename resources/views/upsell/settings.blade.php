@include('../layouts.header')
<div class="container">
  <div class="card m-4">
    <div class="card-header text-center font-weight-bold">
        <h4>{{ __('upsell.addon_title') }} - {{ __('general.settings') }}</h4>
    </div>
    <div class="card-body">
        @include('flashMessage')
        <form method="POST" action="{{ route('client.saveSettings', ['country' => $country, 'serviceUrlPath' => $service_url_path, 'language' => $language, 'eshopId' => $client->eshop_id]) }}">
        @csrf
        @foreach ($settings_service as $setting)
            <div class="form-group row mt-4">
                <label for="settings_{{ $setting->name }}" class="col-md-6 col-md-form-label">{{ __($setting->name) }}:</label>
                <div class="col-md-6">
                    @if ($setting->type == 'value')
                        <input type="text" class="form-control" name="{{ $setting->id }}_value" id="{{ $setting->id }}_value" value="{{ $setting->getValue($client) }}" placeholder="{{ __( $title . '.' . $setting->id . '_placeholder')}}">
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
        <form method="POST" action="{{ route('recommendation.add', ['country' => $country, 'serviceUrlPath' => $service_url_path, 'language' => $language, 'eshopId' => $client->eshop_id]) }}">
            @csrf
            <h4>{{ __('upsell.category_recommendation_title')}}</h4>
            <div class="form-group row mt-4">
                <div class="col-md-12 mb-4">
                    <label>{{ __('upsell.category_recommendation_info')}}</label>
                </div>
            </div>
            <div class="form-group row mt-4">
                <div class="col-md-4">
                    <label>{{ __('upsell.category')}}</label>
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
                    <label>{{ __('upsell.product')}}</label>
                </div>
                <div class="col-md-6">
                    <input type="text" class="form-control" name="product_autocomplete" id="product_autocomplete" placeholder="{{ __('general.product_search') }}">
                    <div id="autocomplete_results" class="list-group mt-2"></div>
                    <input type="hidden" name="product" required>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-12 text-center">
                    <input type="hidden" name="eshop_id" value="{{$client->eshop_id}}">
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
                        <th>{{ __('upsell.category')}}</th>
                        <th>{{ __('upsell.product')}}</th>
                        <th>{{ __('general.delete')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($product_category_recommendations as $productCategoryRecommendation)
                        <tr>
                            <td>{{ __($productCategoryRecommendation->category->name) }}</td>
                            <td>{{ __($productCategoryRecommendation->product->name) }}</td>
                            <td>
                                <form method="POST" action="{{ route('recommendation.delete', 
                                        [
                                            'country' => $country,
                                            'serviceUrlPath' => $service_url_path,
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
            <p>{{ __('upsell.no_recommendations')}}</p>
        @endif
      </div>
      <div class="card-body">
        <form method="POST" action="{{ route('client.sync', ['country' => $country, 'serviceUrlPath' => $service_url_path, 'language' => $language, 'eshopId' => $client->eshop_id]) }}">
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
                    @else
                        <label>{{ \Carbon\Carbon::parse($last_synced)->format('d.m.Y') }}</label>
                    @endif
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-12 text-center">
                    <input type="hidden" name="eshop_id" value="{{$client->eshop_id}}">
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