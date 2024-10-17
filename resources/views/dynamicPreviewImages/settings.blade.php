@include('../layouts.header')
<div class="container">
  <div class="card m-4">
    <div class="card-header text-center font-weight-bold">
        <h4>{{ __('dynamic-preview-images.addon_title') }} - {{ __('general.settings') }}</h4>
    </div>
    <div class="card-body">
        @include('flashMessage')
        <form method="POST" action="{{ route('client.saveSettings', ['country' => $country, 'serviceUrlPath' => $service->getUrlPath(), 'language' => $language, 'eshopId' => $client->getEshopId()]) }}">
        @csrf
        @foreach ($settings_service as $setting)
            @php
                $modifiedName = str_replace('dynamic-preview-images-', 'dynamic-preview-images.', $setting->name);
            @endphp
            <div class="form-group row mt-4">
                <label for="settings_{{ $setting->name }}" class="col-md-6 col-md-form-label">{{ __($modifiedName) }}:</label>
                <div class="col-md-6">
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
        <h4>{{ __('general.data_sync')}}</h4>
        <div class="form-group row mt-4">
            <div class="col-md-12 mb-4">
                <label>{{ __('general.sync_info')}}</label>
            </div>
            <div class="col">
                @if ($update_in_process === 1)
                    <label>{{ __('general.last_synced_at')}}: {{ __('general.sync_in_progress') }}</label>
                @elseif ($last_synced === null)
                    <label>{{ __('general.last_synced_at')}}: {{ __('general.not_synced_yet') }}</label>
                @else
                    <label>{{ __('general.last_synced_at')}}: {{ $last_synced->format('d.m.Y') }}</label>
                @endif
            </div>
        </div>
      </div>
    </div>
  </div>
</div>
@include('../layouts.footer')