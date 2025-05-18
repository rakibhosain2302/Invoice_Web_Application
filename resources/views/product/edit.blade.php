@extends('isotope::master')

@section('title', __('Product Create'))

@push('buttons')
    <a href="{{ route('products.index') }}" class="btn btn-sm btn-isotope fw-bold mb-2 me-2">
        <i class="bi bi-card-checklist"></i>
        {{ __(key: 'Product List') }}
    </a>
@endpush

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('products.update', $products->id) }}" method="POST" id="product-Update">
                @csrf
                @method('PUT')
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
                                <input type="text" name="name" class="form-control form-control-sm"
                                    placeholder="{{ __('Product Name') }}" value="{{ $products->name }}" required>
                            </div>
                            <div class="col-md-5">
                                <input type="number" step="0.01" name="price"
                                    class="form-control form-control-sm unit-price" placeholder="{{ __('Unit Price') }}"
                                    value="{{ $products->price }}" required>
                            </div>
                            <div class="col-md-2 d-grid align-items-end">
                                <button type="submit" class="btn btn-sm btn-isotope fw-bold mb-2">
                                    <i class="fas fa-paper-plane"></i>
                                    {{ __(key: 'Update') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
