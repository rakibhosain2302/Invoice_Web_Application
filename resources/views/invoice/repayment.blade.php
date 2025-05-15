@extends('isotope::master')

@section('title', __('Due Colletion'))

@push('buttons')
    <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-isotope fw-bold mb-2 me-2">
        <i class="bi bi-card-checklist"></i>
        {{ __(key: 'Invoice List') }}
    </a>
    <button type="submit" form="update-invoice" class="btn btn-sm btn-isotope fw-bold mb-2">
        <i class="fas fa-paper-plane"></i>
        {{ __(key: 'Due Colletion') }}
    </button>
@endpush

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive mb-4">
                <table class="table">
                    <tbody>
                        <tr>
                            <!-- Buyer Info -->
                            <h6 class="cm-color-s">{{ __(key: 'Buyer Info') }}<i
                                    class="bi bi-question-circle-fill text-active-gray-100"></i></h6>
                            <hr>
                            <td class="ps-5">
                                <div class="card-body p-3">
                                    <small class="fs-size sm-color">{{ __(key: 'InvoiceNo') }}:
                                        {{ en2bn($invoices->id) }}
                                    </small>

                                    <div class="row mb-2">
                                        <div class="col-5 col-md-2">
                                            <small class="sm-color fs-size">{{ __('Buyer') }}:</small>
                                            <p class="mb-0 f-size">{{ $invoices->buyer_name }}</p>
                                        </div>
                                        <div class="col-7 col-md-2">
                                            <small class="sm-color fs-size">{{ __('Date') }}:</small>
                                            <p class="mb-0 f-size">
                                                {{ dateToBn(\Carbon\Carbon::parse($invoices->payment->last()->paid_at)->format('d M Y, h:i A')) }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="row mb-2">
                                        <div class="col-5 col-md-2">
                                            <small class="sm-color fs-size">{{ __(key: 'Mobile') }}:</small>
                                            <p class="mb-0 f-size">{{ en2bnMobile($invoices->buyer_mobile) }}</p>
                                        </div>
                                        <div class="col-7 col-md-2">
                                            <small class="sm-color fs-size">{{ __(key: 'Total') }}:</small>
                                            <p class="mb-0 f-size">{{ en2bnMoney($invoices->total_amount) }}</p>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-2">
                                            <small class="sm-color fs-size">{{ __(key: 'Payment') }}:</small>
                                            <p class="mb-0">
                                                @php
                                                    $due =
                                                        $invoices->total_amount -
                                                        ($invoices->payment->sum('amount_paid') ?? 0);
                                                @endphp
                                                @if ($due > 0)
                                                    <span class="text-danger fw-semibold">{{ __(key: 'DUE') }}
                                                        ({{ en2bnMoney($due) }})
                                                    </span>
                                                @else
                                                    <span class="text-success fw-semibold">{{ __(key: 'PAID') }}</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- Invoice Items -->
            <div class="table-responsive mb-4">
                <table class="table table-bordered table-striped">
                    <thead>
                        <h5 class="mb-0 sm-color">{{ __(key: 'Invoice Items') }} <i
                                class="bi bi-question-circle-fill text-active-gray-100"></i>
                        </h5>
                        <hr>
                        <tr class="items-text sm-color bg-secondary">
                            <th>{{ __('Sl') }}</th>
                            <th>{{ __('Product') }}</th>
                            <th class="text-end">{{ __(key: 'Unit Price') }}</th>
                            <th class="text-end">{{ __(key: 'Quantity') }}</th>
                            <th class="text-end">{{ __(key: 'Subtotal') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoices->items as $id => $item)
                            <tr>
                                <td>{{ en2bn(++$id) }}</td>
                                <td>{{ $item->product_name }}</td>
                                <td class="text-end">{{ en2bn($item->unit_price) }}</td>
                                <td class="text-end">{{ en2bn($item->quantity) }}</td>
                                <td class="text-end">{{ en2bn($item->sub_total) }}</td>
                            </tr>
                        @endforeach
                        <tr>
                            <td class="text-end fw-bold sm-color" colspan="5">
                                {{ __('Total') }} = {{ en2bn($invoices->total_amount) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <form action="{{ route('invoices.update', $invoices->id) }}" method="POST" id="update-invoice">
                @csrf
                @method('PUT')
                <!-- Payment Info -->
                <div class="row g-3 mt-4">
                    <h6 class="cm-color-s">{{ __('Payment') }} <i
                            class="bi bi-question-circle-fill text-active-gray-100"></i></h6>
                    <hr>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Total Amount') }}</label>
                        <input type="text" id="re-paygrand-total" name="total_amount"
                            value="{{ old('total_amount', $invoices->total_amount) }}"
                            class="form-control form-control-sm bg-secondary">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Due Colletion Amount') }}</label>
                        <input type="hidden" id="already-paid" value="{{ $invoices->payment->sum('amount_paid') }}">
                        <input type="text" id="paid-amount" name="amount_paid" class="form-control form-control-sm">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">{{ __('Due Amount') }}</label>
                        <input type="text" id="due-amount-visible"
                            class="form-control form-control-sm bg-secondary {{ $invoices->total_amount - $invoices->payment->sum('amount_paid') > 0 ? 'text-danger' : 'text-success' }}"
                            value="{{ $invoices->total_amount - $invoices->payment->sum('amount_paid') }}" readonly>
                        <input type="hidden" id="due-amount" name="due_amount" class="form-control bg-secondary"
                            value="{{ $invoices->total_amount - $invoices->payment->sum('amount_paid') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">{{ __('Total') }} {{ __('Payment') }}</label>
                        <input type="number" name="total" id="total"
                            class="form-control form-control-sm bg-secondary"
                            value="{{ $invoices->payment->sum('amount_paid') ?? '00' }}" readonly>
                    </div>
                    <div class="col-md-6 d-none">
                        <label class="form-label">{{ __('Payment') }} {{ __('Date') }}</label>
                        <input type="date" name="paid_at" id="paid_at" class="form-control form-control-sm"
                            formnovalidate>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="{{ asset('assets/js/main.js') }}"></script>
    @include('isotope::elements.footer')
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
