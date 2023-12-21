@include('../layouts.header')
<div class="container">
  <div class="card m-4">
    <div class="card-header text-center font-weight-bold">
        <h4>{{ __('order-status.addon_title') }} - {{ __('general.settings') }}</h4>
    </div>
    <div class="card-body">
        @include('flashMessage')
        <form method="POST" action="{{ route('client.saveSettings', ['country' => $country, 'serviceUrlPath' => $service_url_path, 'language' => $language, 'eshopId' => $client->eshop_id]) }}">
        @csrf
        @foreach ($settings_service as $setting)
            <div class="form-group row mt-4">
                <label for="settings_{{ $setting->name }}" class="col-md-6 col-md-form-label">{{ __($setting->name) }}:</label>
                <div class="col-md-6">
                    @if ($setting->type === 'select')
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
                    @elseif ($setting->type === 'value')
                        <select class="form-control" name="{{ $setting->id }}" id="{{ $setting->id }}">
                            <option>-</option>
                            @foreach ($order_statuses as $option)
                                <?php $selected = '' ?>
                                @foreach ($client_settings as $clientSetting)
                                    @if ($clientSetting->settings_service_option_id == $option->id && $clientSetting->settings_service_id == $setting->id)
                                        <?php $selected = 'selected' ?>
                                    @endif
                                @endforeach
                                <option value="{{ $option->id }}" {{ $selected }}
                                    >{{ __($option->name) }} {{ $setting->value }}</option>
                            @endforeach
                        </select>
                    @endif
                </div>
                @if ($setting->type === 'value')
                    <?php $value = '' ?>
                    @foreach ($client_settings as $clientSetting)
                        @if ($clientSetting->settings_service_id == $setting->id)
                            <?php $value = $clientSetting->value ?>
                        @endif
                    @endforeach
                    <div class="col-md-6">
                        <label for="{{ $setting->id }}_value" class="col-md-6 col-md-form-label">{{ __('order-status.custom_text') }}:</label>
                    </div>
                    <div class="col-md-6">
                        <input type="text" class="form-control" name="{{ $setting->id }}_value" id="{{ $setting->id }}_value" value="{{ $value }}">
                    </div>
                @endif
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
                        <label>{{ $last_synced }}</label>
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
@include('../layouts.footer')