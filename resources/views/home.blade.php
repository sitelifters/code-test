@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">API Token</div>

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
                <div class="card-header">All Products</div>

                <div class="card-body">
                    @isset($products)
                        @foreach ($products->chunk(3) as $products_chunk)
                            <div class="row mb-3">
                                @foreach ($products_chunk as $product)
                                    <div class="col-4">
                                        <div class="card">
                                            <div class="card-body product-card" data-product-id="{{ $product->id }}">
                                                <h5 class="card-title">{{ $product->name }}</h5>
                                                <p class="card-text">{{ $product->description }}</p>

                                                <div class="btn btn-success btn-add-product {{ Auth::user()->products->contains($product->id) ? 'd-none' : '' }}">Add Product to User</div>

                                                <div class="btn btn-danger btn-remove-product {{ !Auth::user()->products->contains($product->id) ? 'd-none' : '' }}">Remove Product From User</div>
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

            // Make the call to get the token
            $.ajax({
                url: "{{ route('user.generateApiToken') }}",
                method: "get",
                success: function(result) {
                    $('#token-alert').removeClass('d-none');
                    $('#display-token').html('<h3>Your API Token:</h3>' + result.token);

                    // Add the authorization token to the header so we can use it in subsequent requests
                    $.ajaxSetup({
                        headers: {
                            'Authorization': 'Bearer ' + result.token,
                        }
                    });                   
                }
            });
        });

        // Attach product to user
        $('.btn-add-product').click(function() {
            var product_id = $(this).parent().data('product-id');
            $.ajax({
                url: "/api/product/attach/" + product_id,
                method: "post",
                success: function(result) {
                    $('.product-card[data-product-id="' + product_id + '"').find('.btn-add-product').addClass('d-none');
                    $('.product-card[data-product-id="' + product_id + '"').find('.btn-remove-product').removeClass('d-none');
                },
                error: function(xhr, status, error) {
                    var response = JSON.parse(xhr.responseText);
                    alert(response.message);
                }
            });
        });

        // Remove product from user
        $('.btn-remove-product').click(function() {
            var product_id = $(this).parent().data('product-id');
            $.ajax({
                url: "/api/product/detach/" + product_id,
                method: "post",
                success: function(result) {
                    $('.product-card[data-product-id="' + product_id + '"').find('.btn-add-product').removeClass('d-none');
                    $('.product-card[data-product-id="' + product_id + '"').find('.btn-remove-product').addClass('d-none');
                },
                error: function(xhr, status, error) {
                    var response = JSON.parse(xhr.responseText);
                    alert(response.message);
                }
            });
        });

    </script>
@endpush

