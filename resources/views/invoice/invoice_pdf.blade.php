<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Invoice_PDF</title>
    <link rel="shortcut icon"
        href="{{ isset(settings()->favicon) ? Storage::url(settings()->favicon) : asset('isotope/metronic/img/favicon.ico') }}" />
    <style>
        @page {
            size: A4;
            margin: 20mm;
        }

        body {
            font-family: 'solaimanlipi', sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
            background-color: #fff;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 25px;
        }

        h2 {
            margin: 0;
            padding: 10px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }


        th,
        td {
            border: 1px solid #000;
            padding: 6px 8px;
        }

        strong {
            padding-bottom: 5px;
            display: inline-block;
        }

        .text-head {
            text-align: center;
            text-transform: uppercase;
            text-decoration: underline;
        }

        .label {
            font-weight: bold;
            color: #333;
        }

        .no-border {
            border: none;
            padding-left: 0px;
        }

        .text-center {
            text-align: center;
        }

        .text-end {
            text-align: right;
        }

        .text-left {
            text-align: left;
        }

        .fw-bold {
            font-weight: bold;
        }

        .text-success {
            color: green;
        }

        .text-danger {
            color: red;
        }

        .text-dark {
            color: #000;
        }

        .rt-table {
            border: none;
            padding-right: 0;
        }
    </style>
</head>

<body>

    <div class="container">

        <h2 class="text-head">Invoice</h2>
        <br>
        <!-- Header Info -->
        <table width="100%" style="margin-bottom: 20px;">
            <tr>
                <!-- Left Column -->
                <td class="no-border" style="width: 30%;">
                    <strong class="label">InvoiceId: {{ $invoice->id }}</strong><br>
                    <strong class="label">Buyer: {{ $invoice->buyer_name }}</strong><br>
                    <strong class="label">Mobile:
                        {{ $invoice->buyer_mobile }}</strong><br>
                    <strong class="label">Date:
                        {{ \Carbon\Carbon::parse($invoice->payment->last()->paid_at)->format('d M Y, h:i A') }}
                </td>
                <td class="rt-table" style="width: 30%; text-align: right; vertical-align: top;">
                    <strong>ABC Company Ltd.</strong><br>
                    <span>123 Main Street</span><br>
                    <span>City, Country</span><br>
                    <span>contact@company.com</span>
                </td>
            </tr>
        </table>
        <!-- Items Table -->
        <table>
            <thead>
                <tr>
                    <th class="text-left">Sl</th>
                    <th class="text-left">Product</th>
                    <th class="text-end">Unit Price</th>
                    <th class="text-end">Quantity</th>
                    <th class="text-end">Subtotal</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->items as $index => $item)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $item->product_name }}</td>
                        <td class="text-end">{{ $item->unit_price }}</td>
                        <td class="text-end">{{ $item->quantity }}</td>
                        <td class="text-end">{{ $item->sub_total }}.Tk</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="4" class="text-end fw-bold">Total</td>
                    <td class="text-end fw-bold">{{ $invoice->total_amount }}.Tk</td>
                </tr>
            </tbody>
        </table>

        <!-- Payment Info -->
        <table class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th colspan="2" class="text-center">Payment History</th>
                </tr>
                <tr>
                    <th class="sm-color">Date</th>
                    <th class="sm-color text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoice->payment as $payment)
                    <tr>
                        <td>
                            {{ $payment->paid_at->format('d M Y, h:i A') }}
                        </td>
                        <td class="text-end">
                            {{ $payment->amount_paid }}.Tk
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-center text-muted">No payments found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Summary -->
        <table>
            <thead>
                <tr>
                    <th colspan="2" class="text-center">Summary</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>Total Amount</strong></td>
                    <td class="text-end fw-bold">{{ $invoice->total_amount }}.Tk</td>
                </tr>
                <tr>
                    <td><strong>Paid Amount</strong></td>
                    <td class="text-end fw-bold"> <span
                            class="{{ ($invoice->payment->sum('amount_paid') ?? 0) > 0 ? 'text-success' : 'text-danger' }}">
                            {{ $invoice->payment->sum('amount_paid') ?? 0 }}
                        </span>.Tk</td>
                </tr>
                <tr>
                    <td><strong>Due Amount</strong></td>
                    @php $due = $invoice->total_amount - ($invoice->payment->sum('amount_paid') ?? 0); @endphp
                    <td class="text-end fw-bold">
                        <span class="{{ $due > 0 ? 'text-danger' : 'text-dark' }}">
                            {{ $due }}
                        </span>.Tk
                    </td>
                </tr>
                <tr>
                    <td><strong>Payment</strong></td>
                    <td class="text-end">
                        @php
                            $due = $invoice->total_amount - ($invoice->payment->sum('amount_paid') ?? 0);
                        @endphp
                        @if ($due > 0)
                            <span class="text-danger fw-bold">DUE ({{ $due }})</span>
                        @else
                            <span class="text-success fw-bold">PAID</span>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- <div class="container">

        <h2 class="text-head">{{ __(key: 'Invoice') }}</h2>
        <br>
        <!-- Header Info -->
        <table width="100%" style="margin-bottom: 20px;">
            <tr>
                <!-- Left Column -->
                <td class="no-border" style="width: 30%;">
                    <strong class="label">{{ __(key: 'InvoiceId') }}: {{ en2bn($invoice->id) }}</strong><br>
                    <strong class="label">{{ __(key: 'Buyer') }}: {{ $invoice->buyer_name }}</strong><br>
                    <strong class="label">{{ __(key: 'Mobile') }}:
                        {{ en2bnMobile($invoice->buyer_mobile) }}</strong><br>
                    <strong class="label">{{ __(key: 'Date') }}:
                        {{ dateToBn(\Carbon\Carbon::parse($invoice->payment->last()->paid_at)->format('d M Y, h:i A')) }}
                </td>
                <td class="rt-table" style="width: 30%; text-align: right; vertical-align: top;">
                    <strong>{{ __(key: 'ABC Company Ltd.') }}</strong><br>
                    <span>{{ __(key: '123 Main Street') }}</span><br>
                    <span>{{ __(key: 'City, Country') }}</span><br>
                    <span>contact@company.com</span>
                </td>
            </tr>
        </table>
        <!-- Items Table -->
        <table>
            <thead>
                <tr>
                    <th class="text-left">{{ __(key: 'Sl') }}</th>
                    <th class="text-left">{{ __(key: 'Product') }}</th>
                    <th class="text-end">{{ __(key: 'Unit Price') }}</th>
                    <th class="text-end">{{ __(key: 'Quantity') }}</th>
                    <th class="text-end">{{ __(key: 'Subtotal') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($invoice->items as $index => $item)
                    <tr>
                        <td>{{ en2bn($index + 1) }}</td>
                        <td>{{ $item->product_name }}</td>
                        <td class="text-end">{{ en2bn($item->unit_price) }}</td>
                        <td class="text-end">{{ en2bn($item->quantity) }}</td>
                        <td class="text-end">{{ en2bn($item->sub_total) }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="4" class="text-end fw-bold">{{ __(key: 'Total') }}</td>
                    <td class="text-end fw-bold">{{ en2bnMoney($invoice->total_amount) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Payment Info -->
        <table class="table table-bordered table-sm">
            <thead>
                <tr>
                    <th colspan="2" class="text-center">{{ __(key: 'Payment History') }}</th>
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
                            {{ en2bnMoney($payment->amount_paid) }}
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

        <!-- Summary -->
        <table>
            <thead>
                <tr>
                    <th colspan="2" class="text-center">{{ __(key: 'Summary') }}</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><strong>{{ __(key: 'Total Amount') }}</strong></td>
                    <td class="text-end fw-bold">{{ en2bnMoney($invoice->total_amount) }}</td>
                </tr>
                <tr>
                    <td><strong>{{ __(key: 'Paid Amount') }}</strong></td>
                    <td class="text-end fw-bold"> <span
                            class="{{ ($invoice->payment->sum('amount_paid') ?? 0) > 0 ? 'text-success' : 'text-danger' }}">
                            {{ en2bnMoney($invoice->payment->sum('amount_paid') ?? 0) }}
                        </span></td>
                </tr>
                <tr>
                    <td><strong>{{ __(key: 'Due Amount') }}</strong></td>
                    @php $due = $invoice->total_amount - ($invoice->payment->sum('amount_paid') ?? 0); @endphp
                    <td class="text-end fw-bold">
                        <span class="{{ $due > 0 ? 'text-danger' : 'text-dark' }}">
                            {{ en2bnMoney($due) }}
                        </span>
                    </td>
                </tr>
                <tr>
                    <td><strong>{{ __(key: 'Payment') }}</strong></td>
                    <td class="text-end">
                        @php
                            $due = $invoice->total_amount - ($invoice->payment->sum('amount_paid') ?? 0);
                        @endphp
                        @if ($due > 0)
                            <span class="text-danger fw-bold">{{ __(key: 'DUE') }} ({{ en2bnMoney($due) }})</span>
                        @else
                            <span class="text-success fw-bold">{{ __(key: 'PAID') }}</span>
                        @endif
                    </td>
                </tr>
            </tbody>
        </table>
    </div> --}}
</body>

</html>
