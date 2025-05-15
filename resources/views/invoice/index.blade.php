@extends('isotope::master')

@section('title', __('Invoice List'))

@push('buttons')
    <a href="{{ route('invoices.create') }}" form="invoice-form" class="btn btn-sm btn-isotope fw-bold mb-2 me-2">
        <i class="bi bi-file-earmark-plus"></i>
        {{ __(key: 'Create') }}
    </a>
@endpush

@section('content')
    <div class="card">
        <div class="card-body pt-3">
            <div class="mb-3 d-flex justify-content-start">
                <form id="search-form" action="{{ route('invoices.index') }}" method="GET" class="w-100 w-md-auto">
                    <div class="row gx-2 gy-2 align-items-end flex-wrap">
                        <div class="col-md-3">
                            <label class="form-label">{{__(key: 'Buyer Name')}}</label>
                            <input type="text" name="buyer_name" class="form-control form-control-sm"
                                placeholder="{{__(key: 'Buyer Name')}}" value="{{ request('buyer_name') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">{{__(key: 'Mobile')}}</label>
                            <input type="text" name="buyer_mobile" class="form-control form-control-sm"
                                placeholder="{{__(key: 'Mobile')}}" value="{{ request('buyer_mobile') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">{{__(key: 'Start Date')}}</label>
                            <input type="date" name="start_date" class="form-control form-control-sm"
                                value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">{{__(key: 'End Date')}}</label>
                            <input type="date" name="end_date" class="form-control form-control-sm"
                                value="{{ request('end_date') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label d-none d-md-block">&nbsp;</label>
                            <button class="btn btn-sm btn-isotope fw-bold me-2" type="submit">{{__(key: "Search")}}</button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered rounded table-striped align-middle">
                    <thead class="text-light" style="background-color: #0E475D">
                        <tr>
                            <th>{{ __(key: 'Sl') }}</th>
                            <th>{{ __(key: 'Buyer') }}</th>
                            <th>{{ __(key: 'Mobile') }}</th>
                            <th>{{ __(key: 'Total') }}</th>
                            <th>{{ __(key: 'Paid') }}</th>
                            <th>{{ __(key: 'Due') }}</th>
                            <th>{{ __(key: 'Date') }}</th>
                            <th class="text-center">{{ __(key: 'Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($invoices as $index => $invoice)
                            <tr>
                                <td>{{ en2bn($index + 1) }}</td>
                                <td>{{ $invoice->buyer_name }}</td>
                                <td>{{ en2bn($invoice->buyer_mobile) }}</td>
                                <td>{{ en2bnMoney($invoice->total_amount) }}</td>
                                <td>{{ en2bnMoney($invoice->payment->sum('amount_paid') ?? '00') }}</td>
                                <td
                                    class="{{ $invoice->total_amount - ($invoice->payment->sum('amount_paid') ?? 0) > 0 ? 'text-danger' : 'text-dark' }}">
                                    {{ en2bnMoney($invoice->total_amount - ($invoice->payment->sum('amount_paid') ?? 0)) }}
                                </td>
                                <td>
                                    {{ dateToBn(\Carbon\Carbon::parse($invoice->payment->last()->paid_at)->format('d M Y, h:i A')) }}
                                </td>

                                <td class="d-flex justify-content-md-center">
                                    <a href="{{ route('invoices.show', $invoice->id) }}"
                                        class="btn btn-outline btn-outline-dashed btn-outline-info p-0 ps-1 me-1"
                                        title="View">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                    <a href="{{ route('invoice.repayment', $invoice->id) }}"
                                        class="btn btn-outline btn-outline-dashed btn-outline-primary p-0 ps-1 me-1"
                                        title="Due Colletion">
                                        <i class="fa-solid fa-money-check"></i>
                                    </a>
                                    <a href="{{ route('invoices.edit', $invoice->id) }}"
                                        class="btn btn-outline btn-outline-dashed btn-outline-success p-0 ps-1 me-1"
                                        title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form action="{{ route('invoices.destroy', $invoice->id) }}" method="POST">
                                        @csrf
                                        @method('delete')
                                        <button title="Delete" onclick="return confirm('Are You Want to Delete This?')"
                                            type="submit"
                                            class="btn btn-outline btn-outline-dashed btn-outline-danger p-0 rounded-0 me-1">
                                            <i class="bi bi-trash-fill ms-1"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $invoices->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

    @push('css')
        <style>
            .btn i {
                font-size: 14px;
            }
        </style>
    @endpush
    @include('isotope::elements.footer')
@endsection
