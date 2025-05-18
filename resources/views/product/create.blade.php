@extends('isotope::master')

@section('title', __('Product Create'))

@push('buttons')
    <a href="{{ route('products.index') }}" class="btn btn-sm btn-isotope fw-bold mb-2 me-2">
        <i class="bi bi-card-checklist"></i>
        {{ __(key: 'Product List') }}
    </a>
    <button type="submit" form="product-form" class="btn btn-sm btn-isotope fw-bold mb-2">
        <i class="fas fa-paper-plane"></i>
        {{ __(key: 'Save') }}
    </button>
@endpush

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('products.store') }}" method="POST" id="product-form">
                @csrf
                <div class="mt-4">
                    <h6 class="cm-color-s">{{ __('Add Products') }} <i
                            class="bi bi-question-circle-fill text-active-gray-100"></i>
                    </h6>
                    <hr>
                    <div id="placeholders" data-product-name="{{ __('Product Name') }}"
                        data-unit-price="{{ __('Unit Price') }}" data-remove-btn="{{ __('Remove') }}">
                    </div>
                    <div id="product-container">
                        <div class="row g-3 product-item mb-1">
                            <div class="col-md-5">
                                <input type="text" name="products[0][name]" class="form-control form-control-sm"
                                    placeholder="{{ __('Product Name') }}" required>
                            </div>
                            <div class="col-md-5">
                                <input type="number" step="0.01" name="products[0][price]"
                                    class="form-control form-control-sm unit-price" placeholder="{{ __('Unit Price') }}"
                                    required>
                            </div>
                            <div class="col-md-2 d-grid align-items-end">
                                <button type="button" class="btn btn-sm btn-danger remove-product"><i
                                        class="bi bi-trash3"></i>{{ __('Remove') }}</button>
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="button" id="add-product" class="btn btn-sm btn-isotope fw-bold mt-1">
                            <i class="fa-solid fa-plus"></i>
                            {{ __('Add Product') }}</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @include('isotope::elements.footer')
    @push('js')
        <script>
            let productIndex = 1;

            $("#add-product").on("click", function() {
                const $container = $("#product-container");
                const $placeholders = $("#placeholders");

                const $product = $(`
            <div class="row g-3 product-item mb-3">
                <div class="col-md-5">
                    <input type="text" name="products[${productIndex}][product_name]" class="form-control form-control-sm" placeholder="${$placeholders.data(
            "product-name"
        )}" required>
                </div>
                <div class="col-md-5">
                    <input type="number" step="0.01" name="products[${productIndex}][unit_price]" class="form-control form-control-sm unit-price" placeholder="${$placeholders.data(
            "unit-price"
        )}" required>
                </div>
                <div class="col-md-2 d-grid align-items-end">
                    <button type="button" class="btn btn-sm btn-danger remove-product">
                        <i class="bi bi-trash3"></i> ${$placeholders.data(
                            "remove-btn"
                        )}
                    </button>
                </div>
            </div>
        `);

                $container.append($product);
                productIndex++;
            });

            $(document).on("click", ".remove-product", function() {
                $(this).closest(".product-item").remove();
            });
        </script>
    @endpush
@endsection
