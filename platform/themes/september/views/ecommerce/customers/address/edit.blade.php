@extends(Theme::getThemeNamespace() . '::views.ecommerce.customers.master')

@section('content')
   <h2 class="customer-page-title">{{ __('Address books') }}</h2>
    <br>
    <div class="profile-content">
        {!! Form::open(['route' => ['customer.address.edit', $address->id]]) !!}
            <div class="form-group">
                <label for="name">{{ __('Full Name') }}:</label>
                <input id="name" type="text" class="form-control" name="name" value="{{ $address->name }}">
                {!! Form::error('name', $errors) !!}
            </div>

            <div class="form-group">
                <label for="email">{{ __('Email') }}:</label>
                <input id="email" type="text" class="form-control" name="email" value="{{ $address->email }}">
                {!! Form::error('email', $errors) !!}
            </div>

           <div class="form-group">
                <label>{{ __('Phone') }}:</label>
                <input id="phone" type="text" class="form-control" name="phone" value="{{ $address->phone }}">
                {!! Form::error('phone', $errors) !!}
            </div>

            @if (EcommerceHelper::isUsingInMultipleCountries())
                <div class="form-group @if ($errors->has('country')) has-error @endif">
                    <label for="country">{{ __('Country') }}:</label>
                    <select name="country" class="form-control" id="country" data-type="country">
                        @foreach(EcommerceHelper::getAvailableCountries() as $countryCode => $countryName)
                            <option value="{{ $countryCode }}" @if ($address->country == $countryCode) selected @endif>{{ $countryName }}</option>
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
                        @if (old('country', $address->country) || !EcommerceHelper::isUsingInMultipleCountries())
                            @foreach(EcommerceHelper::getAvailableStatesByCountry(old('country', $address->country)) as $stateId => $stateName)
                                <option value="{{ $stateId }}" @if (old('state', $address->state) == $stateId) selected @endif>{{ $stateName }}</option>
                            @endforeach
                        @endif
                    </select>
                @else
                    <input id="state" type="text" class="form-control" name="state" value="{{ $address->state }}">
                @endif
                {!! Form::error('state', $errors) !!}
            </div>

            <div class="form-group @if ($errors->has('city')) has-error @endif">
                <label>{{ __('City') }}:</label>
                @if (EcommerceHelper::loadCountriesStatesCitiesFromPluginLocation())
                    <select name="city" class="form-control" id="city" data-type="city" data-url="{{ route('ajax.cities-by-state') }}">
                        <option value="">{{ __('Select city...') }}</option>
                        @if (old('state', $address->state))
                            @foreach(EcommerceHelper::getAvailableCitiesByState(old('state', $address->state)) as $cityId => $cityName)
                                <option value="{{ $cityId }}" @if (old('city', $address->city) == $cityId) selected @endif>{{ $cityName }}</option>
                            @endforeach
                        @endif
                    </select>
                @else
                    <input id="city" type="text" class="form-control" name="city" value="{{ $address->city }}">
                @endif
                {!! Form::error('city', $errors) !!}
            </div>

            <div class="form-group">
                <label>{{ __('Address') }}:</label>
                <input id="address" type="text" class="form-control" name="address" value="{{ $address->address }}">
                {!! Form::error('address', $errors) !!}
            </div>

            @if (EcommerceHelper::isZipCodeEnabled())
                <div class="form-group">
                    <label>{{ __('Zip code') }}:</label>
                    <input id="zip_code" type="text" class="form-control" name="zip_code" value="{{ $address->zip_code }}">
                    {!! Form::error('zip_code', $errors) !!}
                </div>
            @endif

            <div class="form-group">
                <label for="is_default">
                    <input class="customer-checkbox" type="checkbox" name="is_default" value="1" @if ($address->is_default) checked @endif id="is_default">
                    {{ __('Use this address as default.') }}
                    {!! Form::error('is_default', $errors) !!}
                </label>
            </div>

            <div class="form-group">
                <button class="btn--custom btn--rounded btn--outline btn--sm" type="submit">{{ __('Update') }}</button>
            </div>
        {!! Form::close() !!}
    </div>
@endsection
