@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">API Tokens</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <div id="generate-token" class="btn btn-primary">Generate New API Token</div>

                    <div class="card mt-3">
                        <div class="card-body">
                            <div id="token-alert" class="alert alert-warning d-none" role="alert">
                                <strong>IMPORTANT!</strong> Please copy and save this API token. You won't be able to access it again.
                            </div>
                            <div id="display-token">Please click "Generate New API Token" to generate a token.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center mt-4">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">Products</div>

                <div class="card-body">
                    @isset($products)
                        @foreach ($products->chunk(3) as $products_chunk)
                            <div class="row mb-3">
                                @foreach ($products_chunk as $product)
                                    <div class="col-4">
                                        <div class="card">
                                            <div class="card-body">
                                                <h5 class="card-title">{{ $product->name }}</h5>
                                                <p class="card-text">{{ $product->description }}</p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    @else
                        No products found.
                    @endisset

                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
    <script>
        // Generate new API token for user and display it
        $('#generate-token').click(function() {
            $.ajax({
                url: "{{ route('user.generateApiToken') }}",
                method: "get",
                success: function(result) {
                    $('#token-alert').removeClass('d-none');
                    $('#display-token').html('<h3>Your API Token:</h3>' + result.token);
                }
            });
        });
    </script>
@endpush

