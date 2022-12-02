@extends(Theme::getThemeNamespace() . '::views.ecommerce.customers.master')
@section('content')
     <h2 class="customer-page-title">{{ __('Add a new address') }}</h2>
    <br>
    <div class="profile-content">

        {!! Form::open(['route' => 'customer.address.create']) !!}
            <div class="form-group">
                 <label>{{ __('Full Name') }}:</label>
                <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}">
                {!! Form::error('name', $errors) !!}
            </div>

            <div class="form-group">
                <label>{{ __('Email') }}:</label>
                <input id="email" type="text" class="form-control" name="email" value="{{ old('email') }}">
                {!! Form::error('email', $errors) !!}
            </div>

            <div class="form-group">
                 <label>{{ __('Phone') }}:</label>
                <input id="phone" type="text" class="form-control" name="phone" value="{{ old('phone') }}">
                {!! Form::error('phone', $errors) !!}
            </div>

            @if (EcommerceHelper::isUsingInMultipleCountries())
                <div class="form-group @if ($errors->has('country')) has-error @endif">
                    <label for="country">{{ __('Country') }}:</label>
                    <select name="country" class="form-control" id="country" data-type="country">
                        @foreach(EcommerceHelper::getAvailableCountries() as $countryCode => $countryName)
                            <option value="{{ $countryCode }}" @if (old('country') == $countryCode) selected @endif>{{ $countryName }}</option>
                        @endforeach
                    </select>
                </div>
                {!! Form::error('country', $errors) !!}
            @else
                <input type="hidden" name="country" value="{{ EcommerceHelper::getFirstCountryId() }}">
            @endif

            <div class="form-group @if ($errors->has('state')) has-error @endif">
                <label>{{ __('State') }}:</label>
                @if (EcommerceHelper::loadCountriesStatesCitiesFromPluginLocation())
                    <select name="state" class="form-control" id="state" data-type="state" data-url="{{ route('ajax.states-by-country') }}">
                        <option value="">{{ __('Select state...') }}</option>
                        @if (old('country') || !EcommerceHelper::isUsingInMultipleCountries())
                            @foreach(EcommerceHelper::getAvailableStatesByCountry(old('country')) as $stateId => $stateName)
                                <option value="{{ $stateId }}" @if (old('state') == $stateId) selected @endif>{{ $stateName }}</option>
                            @endforeach
                        @endif
                    </select>
                @else
                    <input id="state" type="text" class="form-control" name="state" value="{{ old('state') }}">
                @endif
                {!! Form::error('state', $errors) !!}
            </div>

            <div class="form-group @if ($errors->has('city')) has-error @endif">
                <label>{{ __('City') }}:</label>
                @if (EcommerceHelper::loadCountriesStatesCitiesFromPluginLocation())
                    <select name="city" class="form-control" id="city" data-type="city" data-url="{{ route('ajax.cities-by-state') }}">
                        <option value="">{{ __('Select city...') }}</option>
                        @if (old('state'))
                            @foreach(EcommerceHelper::getAvailableCitiesByState(old('state')) as $cityId => $cityName)
                                <option value="{{ $cityId }}" @if (old('city') == $cityId) selected @endif>{{ $cityName }}</option>
                            @endforeach
                        @endif
                    </select>
                @else
                    <input id="city" type="text" class="form-control" name="city" value="{{ old('city') }}">
                @endif

                {!! Form::error('city', $errors) !!}
            </div>

            <div class="form-group">
                <label>{{ __('Address') }}:</label>
                <input id="address" type="text" class="form-control" name="address" value="{{ old('address') }}">

                {!! Form::error('address', $errors) !!}
            </div>

            @if (EcommerceHelper::isZipCodeEnabled())
                <div class="form-group">
                    <label>{{ __('Zip code') }}:</label>
                    <input id="zip_code" type="text" class="form-control" name="zip_code" value="{{ old('zip_code') }}">
                    {!! Form::error('zip_code', $errors) !!}
                </div>
            @endif

            <div class="form-group">
                <label for="is_default">
                    <input type="checkbox" name="is_default" value="1" id="is_default">
                    {{ __('Use this address as default.') }}

                </label>
                {!! Form::error('is_default', $errors) !!}
            </div>

            <div class="form-group text-center">
                <button class="btn--custom btn--rounded btn--outline btn--sm" type="submit">{{ __('Add a new address') }}</button>
            </div>
        {!! Form::close() !!}
    </div>
@endsection
