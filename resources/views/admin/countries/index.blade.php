
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    Countries
                </div>

                <div class="card-body">

                    <table class='table table-striped'>
                        @foreach($countries as $country)
                        <tr>
                            <td>
                                <img src='/images/flags/mini/{{ $country->code }}.png ' style='max-width: 28px;' alt='{{ $country->code }}' /> &nbsp;{{ $country->name }}
                            </td>
                        </tr>
                        @endforeach
                    </table>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection
