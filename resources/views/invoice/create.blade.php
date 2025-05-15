@extends('isotope::master')

@section('title', __('Invoice') . ' ' . __('Create'))

@push('buttons')
    <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-isotope fw-bold mb-2 me-2">
        <i class="bi bi-card-checklist"></i>
        {{ __(key: 'Invoice List') }}
    </a>
    <button type="submit" form="invoice-form" class="btn btn-sm btn-isotope fw-bold mb-2">
        <i class="fas fa-paper-plane"></i>
        {{ __(key: 'Save Invoice') }}
    </button>
@endpush

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('invoices.store') }}" method="POST" id="invoice-form">
                @csrf
                <!-- Buyer and Company Info -->
                <div class="row g-3">
                    <div class="col-md-6 order-2 order-lg-1">
                        <div class="p-3 h-100">
                            <h6 class="cm-color-s">{{ __(key: 'Buyer Info') }} <i
                                    class="bi bi-question-circle-fill text-active-gray-100"></i></h6>
                            <hr>
                            <div class="mb-3">
                                <label class="form-label">{{ __('Name') }}</label>
                                <input type="text" name="buyer_name" class="form-control form-control-sm" required
                                    placeholder="{{ __(key: 'Buyer Name') }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('Mobile') }}</label>
                                <input type="text" name="buyer_mobile" class="form-control form-control-sm" required
                                    placeholder="{{ __(key: 'Buyer Mobile') }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">{{ __('Note (Optional)') }}</label>
                                <input type="text" name="note" class="form-control form-control-sm"
                                    placeholder="{{ __(key: 'Note') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Company Info -->
                    <div class="col-md-6 order-1 order-lg-2">
                        <div class="p-3 h-100">
                            <h6 class="cm-color-s">{{ __(key: 'Company Info') }} <i
                                    class="bi bi-question-circle-fill text-active-gray-100"></i></h6>
                            <hr>
                            <p class="mb-1 fw-bold cm-color-s">{{ __(key: 'ABC Company Ltd.') }}</p>
                            <address class="cm-color-s">
                                {{ __(key: '123 Main Street') }}<br>
                                {{ __(key: 'City, Country') }}<br>
                                contact@company.com
                            </address>
                        </div>
                    </div>
                </div>

                <!-- Invoice Items -->
                <div class="mt-4">
                    <h6 class="cm-color-s">{{ __(key: 'Invoice Items') }} <i
                            class="bi bi-question-circle-fill text-active-gray-100"></i>
                    </h6>
                    <hr>
                    <div id="placeholders" data-product-name="{{ __(key: 'Product Name') }}"
                        data-unit-price="{{ __(key: 'Unit Price') }}" data-quantity="{{ __(key: 'Quantity') }}"
                        data-subtotal="{{ __(key: 'Subtotal') }}" data-remove-btn="{{ __(key: 'Remove') }}">
                    </div>
                    <div id="items-container">
                        <div class="row g-3 invoice-item mb-1">
                            <div class="col-md-4">
                                <input type="text" name="items[0][product_name]" class="form-control form-control-sm"
                                    placeholder="{{ __(key: 'Product Name') }}" required>
                            </div>
                            <div class="col-md-2">
                                <input type="number" step="0.01" name="items[0][unit_price]"
                                    class="form-control form-control-sm unit-price"
                                    placeholder="{{ __(key: 'Unit Price') }}" required>
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="items[0][quantity]" min="1" max="10"
                                    class="form-control form-control-sm quantity" placeholder="{{ __(key: 'Quantity') }}"
                                    required>
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="items[0][sub_total]" class="form-control form-control-sm total"
                                    placeholder="{{ __(key: 'Subtotal') }}" readonly>
                            </div>
                            <div class="col-md-2 d-grid align-items-end">
                                <button type="button" class="btn btn-sm btn-danger remove-item"><i
                                        class="bi bi-trash3"></i>{{ __(key: 'Remove') }}</button>
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="button" id="add-item" class="btn btn-sm btn-isotope fw-bold mt-1">+
                            {{ __(key: 'Add Item') }}</button>
                    </div>
                </div>

                <!-- Payment Section -->
                <div class="mt-4">
                    <h6 class="cm-color-s">{{ __(key: 'Payment') }} <i
                            class="bi bi-question-circle-fill text-active-gray-100"></i></h6>
                    <hr>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Total Amount') }}</label>
                            <input type="number" id="grand-total" name="total_amount"
                                class="form-control form-control-sm bg-secondary" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Initial Payment Amount') }}</label>
                            <input type="number" id="paid-amount" name="amount_paid" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Due Amount') }}</label>
                            <input type="number" id="due-amount" name="due_amount"
                                class="form-control form-control-sm bg-secondary" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Payment') }} {{ __(key: 'Date') }}</label>
                            <input type="date" name="paid_at" class="form-control form-control-sm" id="paid_at"
                                formnovalidate>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="{{ asset('assets/js/main.js') }}"></script>
    @include('isotope::elements.footer')
    @push('css')
        <style>
            .card-header {
                min-height: 0px !important;
                margin-top: 0px !important;
                background-color: #0E475D !important;
                padding: 10px !important;
                font-size: 16px;
                color: #fff !important;
            }

            label {
                color: #0E475D;
            }

            .cm-color {
                color: #fff
            }

            .cm-color-s {
                color: #0E475D
            }
        </style>
    @endpush
@endsection
