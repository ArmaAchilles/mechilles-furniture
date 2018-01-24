@extends('layouts.app')

@section('content')
<div class="container card container-margin-top">
    <form method="post">
        {{csrf_field()}}
        <div class="card-body">
            <div class="row">
                <div class="col-xs">
                    <img src="{{asset('images/achilles.webp')}}" height="100" width="62" alt="Achilles Logo">
                </div>
                <div class="col-md">
                    <h1>Mechilles</h1>
                    <h2>Furniture templates</h2>
                    <p class="text-muted"><i>Note! You can paste more than one template!</i></p>
                </div>
                {{--  <div class="col-xs">
                    <span class="text-right">
                        <a href="/login">Login</a>
                    </span>
                </div>  --}}
            </div>
            @include('includes.messages')
            <div class="row row-textarea">
                <div class="col">
                    <div class="form-group">
                        <textarea
                            class="form-control"
                            name="furnitureData"
                            id="furnitureData"
                            rows="20"
                            required
                            placeholder="Enter your copied furniture data here.">
                        </textarea>
                    </div>
                </div>
            </div>
            <div class="row text-right">
                <div class="col">
                    <div class="g-recaptcha" data-callback="enableButton" data-sitekey="6Lc3UUIUAAAAAHM6K-SaK1_YAuGn9sPv4Ha2BdWr"></div>
                </div>
                <div class="col mt-2">
                    <input
                        class="btn btn-lg btn-outline-primary"
                        type="submit"
                        value="Submit"
                        id="btn-submit"
                        disabled>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
