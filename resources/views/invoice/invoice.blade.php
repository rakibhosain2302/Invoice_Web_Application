@extends('isotope::master')


@section('title', __('Invoice'))

@push('buttons')
    <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-isotope fw-bold mb-2 me-2">
        <i class="bi bi-card-checklist"></i>
        {{ __(key: 'Invoice List') }}
    </a>
    <a href="{{ route('invoices.show', $invoice->id) }}?download=pdf" class="btn btn-sm btn-isotope fw-bold mb-2 me-2">
        <i class="bi bi-filetype-pdf"></i>
        {{ __(key: 'Download') }}
    </a>
@endpush
@section('content')
    <div class="card p-4 border rounded">
        <div class="card-body">
            <h2 class="text-center sm-color">{{ __(key: 'Invoice') }}</h2>
            <hr>
            <!-- Invoice Header Info -->
            <div class="table-responsive mb-4">
                <table class="table">
                    <tbody>
                        <tr>
                            <!-- Buyer Info -->
                            <td class="ps-5">
                                <div class="card-body p-3">
                                    <h6 class="mb-3 fw-bold sm-color">{{ __(key: 'InvoiceNo') }}: {{ en2bn($invoice->id) }}
                                    </h6>

                                    <div class="row mb-2">
                                        <div class="col-md-6">
                                            <small class="sm-color fs-size">{{ __(key: 'Buyer') }}:</small>
                                            <p class="mb-0 f-size">{{ $invoice->buyer_name }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <small class="sm-color fs-size">{{ __(key: 'Date') }}:</small>
                                            <p class="mb-0 f-size">
                                                {{ dateToBn(\Carbon\Carbon::parse($invoice->payment->last()->paid_at)->format('d M Y, h:i A')) }}
                                            </p>
                                        </div>
                                    </div>

                                    <div class="row mb-2">
                                        <div class="col-md-6">
                                            <small class="sm-color fs-size">{{ __(key: 'Mobile') }}:</small>
                                            <p class="mb-0 f-size">{{ en2bnMobile($invoice->buyer_mobile) }}</p>
                                        </div>
                                        <div class="col-md-6">
                                            <small class="sm-color fs-size">{{ __(key: 'Total') }}:</small>
                                            <p class="mb-0 f-size">{{ en2bnMoney($invoice->total_amount) }}</p>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <small class="sm-color fs-size">{{ __(key: 'Payment') }}:</small>
                                            <p class="mb-0">
                                                @php
                                                    $due =
                                                        $invoice->total_amount -
                                                        ($invoice->payment->sum('amount_paid') ?? 0);
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

                            <!-- Company Info -->
                            <td class="pe-5">
                                <div class="text-end">
                                    <div class="card-body p-3">
                                        <h5 class="fw-bold mb-2 sm-color">{{ __(key: 'ABC Company Ltd.') }}</h5>
                                        <p class="mb-1">{{ __(key: '123 Main Street') }}</p>
                                        <p class="mb-1">{{ __(key: 'City, Country') }}</p>
                                        <p class="mb-0"><a href="mailto:contact@company.com"
                                                class="text-decoration-none text-dark">contact@company.com</a></p>
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
                            <th>{{ __(key: 'Sl') }}</th>
                            <th>{{ __(key: 'Product') }}</th>
                            <th class="text-end">{{ __(key: 'Unit Price') }}</th>
                            <th class="text-end">{{ __(key: 'Quantity') }}</th>
                            <th class="text-end">{{ __(key: 'Subtotal') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoice->items as $id => $item)
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
                                {{ __(key: 'Total') }} = {{ en2bn($invoice->total_amount) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Payment History & Summary -->
            <div class="row g-3">
                <!-- Payment History -->
                <div class="col-md-6">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <td colspan="2" class="bg-secondary text-white text-center">
                                        <h6 class="mb-0 sm-color">{{ __('Payment History') }}</h6>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="sm-color">{{ __('Date') }}</th>
                                    <th class="sm-color text-end">{{ __('Amount') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoice->payment as $payment)
                                    <tr>
                                        <td>
                                            {{ dateToBn($payment->paid_at->format('d M Y, h:i A')) }}
                                        </td>
                                        <td class="text-end">
                                            {{ en2bn($payment->amount_paid) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center text-muted">{{ __('No payments found.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Summary -->
                <div class="col-md-6">
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <td colspan="2" class="bg-secondary text-center">
                                        <h6 class="mb-0 sm-color">{{ __(key: 'Summary') }}</h6>
                                    </td>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <th class="sm-color">{{ __(key: 'Total Amount') }}</th>
                                    <td class="text-end">{{ en2bn($invoice->total_amount) }}</td>
                                </tr>
                                <tr>
                                    <th class="sm-color">{{ __(key: 'Paid Amount') }}</th>
                                    <td class="text-end">
                                        <span
                                            class="{{ ($invoice->payment->sum('amount_paid') ?? 0) > 0 ? 'text-success' : 'text-danger' }}">
                                            {{ en2bn($invoice->payment->sum('amount_paid') ?? 0) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="sm-color">{{ __(key: 'Due Amount') }}</th>
                                    <td class="text-end">
                                        <span
                                            class="{{ $invoice->total_amount - ($invoice->payment->sum('amount_paid') ?? 0) > 0 ? 'text-danger' : 'text-success' }}">
                                            {{ en2bn($invoice->total_amount - ($invoice->payment->sum('amount_paid') ?? 0)) }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @push('css')
        <style>
            .w-color {
                color: #fff;
            }

            .f-size {
                font-size: 14px;
            }

            .fs-size {
                font-size: 13px;
                font-weight: 400;
            }

            td {
                font-size: 15px;
                font-weight: 400;
            }

            .sm-color {
                color: #0E475D;
            }
        </style>
    @endpush
@endsection
