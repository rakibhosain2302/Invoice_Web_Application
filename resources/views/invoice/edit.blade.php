@extends('isotope::master')

@section('title', __('Update Invoice'))

@push('buttons')
    <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-isotope fw-bold mb-2 me-2">
        <i class="bi bi-card-checklist"></i>
        {{ __(key: 'Invoice List') }}
    </a>
    <button type="submit" form="update-invoice" class="btn btn-sm btn-isotope fw-bold mb-2">
        <i class="fas fa-paper-plane"></i>
        {{ __(key: 'Update Invoice') }}
    </button>
@endpush

@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('invoices.update', $invoices->id) }}" method="POST" id="update-invoice">
                @csrf
                @method('PUT')

                <div id="deleted-items-container"></div>

                <!-- Buyer Info -->
                <h6 class="cm-color-s">{{ __(key: 'Buyer Info') }}<i
                        class="bi bi-question-circle-fill text-active-gray-100"></i></h6>
                <hr>
                <div class="mb-3">
                    <label class="form-label">{{ __('Name') }}</label>
                    <input type="text" name="buyer_name" class="form-control form-control-sm" required
                        value="{{ old('buyer_name', $invoices->buyer_name) }}" placeholder="{{ __(key: 'Buyer Name') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('Mobile') }}</label>
                    <input type="text" name="buyer_mobile" class="form-control form-control-sm" required
                        value="{{ old('buyer_mobile', $invoices->buyer_mobile) }}"
                        placeholder="{{ __(key: 'Buyer Mobile') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('Note (Optional)') }}</label>
                    <input type="text" name="note" class="form-control form-control-sm"
                        value="{{ old('note', $invoices->note) }}" placeholder="{{ __(key: 'Note') }}">
                </div>
                <div id="placeholders" data-product-name="{{ __(key: 'Product Name') }}"
                    data-unit-price="{{ __(key: 'Unit Price') }}" data-quantity="{{ __(key: 'Quantity') }}"
                    data-subtotal="{{ __(key: 'Subtotal') }}" data-remove-btn="{{ __(key: 'Remove') }}">
                </div>
                <!-- Invoice Items -->
                <div id="items-container" class=" mt-6">
                    <h6 class="cm-color-s">{{ __(key: 'Invoice Items') }} <i
                            class="bi bi-question-circle-fill text-active-gray-100"></i>
                    </h6>
                    <hr>
                    @foreach ($invoices->items as $index => $item)
                        <div class="row g-3 invoice-item mb-3">
                            <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">
                            <div class="col-md-4">
                                <select class="form-control form-control-sm select2-product"
                                    name="items[{{ $index }}][product_id]" id="product_id_{{ $index }}">
                                    <option value="{{ $item->product_id }}" selected>
                                        {{ $item->product_name }}
                                    </option>
                                </select>
                                <input type="hidden" name="items[{{ $index }}][product_name]"
                                    value="{{ $item->product_name }}" class="product-name-hidden"
                                    id="product_name_{{ $index }}">
                            </div>
                            <div class="col-md-2">
                                <input type="text" name="items[{{ $index }}][unit_price]"
                                    value="{{ $item->unit_price }}" class="form-control form-control-sm unit-price"
                                    readonly>
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="items[{{ $index }}][quantity]"
                                    value="{{ $item->quantity }}" class="form-control form-control-sm quantity" required>
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="items[{{ $index }}][sub_total]"
                                    value="{{ $item->sub_total }}" class="form-control form-control-sm total" readonly>
                            </div>
                            <div class="col-md-2 d-grid align-items-end">
                                <button type="button" class="btn btn-sm btn-danger remove-item">
                                    <i class="bi bi-trash3"></i> Remove
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="text-end">
                    <button type="button" id="add-item" class="btn btn-sm btn-isotope fw-bold mt-1">+
                        {{ __(key: 'Add Item') }}</button>
                </div>

                <!-- Payment Info -->
                <div class="row g-3 mt-4">
                    <h6 class="cm-color-s">{{ __(key: 'Payment') }} <i
                            class="bi bi-question-circle-fill text-active-gray-100"></i></h6>
                    <hr>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Total Amount') }}</label>
                        <input type="text" id="grand-total" name="total_amount"
                            value="{{ old('total_amount', $invoices->total_amount) }}"
                            class="form-control form-control-sm bg-secondary" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Initial Payment Amount') }}</label>
                        <input type="text" id="paid-amount"
                            value="{{ old('amount_paid', $invoices->payment->sum('amount_paid')) }}"
                            class="form-control form-control-sm bg-secondary" readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Due Amount') }}</label>
                        <input type="text" id="due-amount"
                            class="form-control form-control-sm bg-secondary {{ $invoices->total_amount - ($invoices->payment->sum('amount_paid') ?? 0) > 0 ? 'text-danger' : 'text-success' }}"
                            value="{{ old('due_amount', $invoices->total_amount - ($invoices->payment->sum('amount_paid') ?? 0)) }}"
                            readonly>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Payment') }} {{ __(key: 'Date') }}</label>
                        <input type="date" name="paid_at" class="form-control form-control-sm" id="paid_at">
                    </div>
                </div>
            </form>
        </div>
    </div>
    <script src="{{ asset('assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/select2.min.js') }}"></script>
    <script>
        window.getProductsUrl = "{{ url('/get-products') }}";
        window.translations = {
            productPlaceholder: "{{ __('Search for a product') }}"
        };
    </script>
    <script src="{{ asset('assets/js/main.js') }}"></script>
    @push('css')
        <style>
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
