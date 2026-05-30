<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Memo - {{ $memo->title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #333;
            line-height: 1.6;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .title {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
        }
        .company {
            font-size: 18px;
            color: #555;
            margin: 5px 0 0 0;
        }
        .meta-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .meta-table td {
            padding: 5px;
        }
        .meta-label {
            font-weight: bold;
            width: 150px;
        }
        .content {
            margin-bottom: 40px;
        }
        .signatures {
            margin-top: 50px;
            width: 100%;
        }
        .signature-box {
            width: 24%;
            display: inline-block;
            vertical-align: top;
            text-align: center;
        }
        .signature-img {
            max-width: 200px;
            max-height: 80px;
            margin-bottom: 10px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin: 10px auto 5px auto;
            width: 80%;
        }
        .signature-name {
            font-weight: bold;
        }
        .signature-role {
            font-size: 12px;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1 class="title">Memo</h1>
        <p class="company">{{ $memo->company->name ?? 'Company Name' }}</p>
    </div>

    <!-- <table class="meta-table">
        <tr>
            <td class="meta-label">To:</td>   
            <td>The Managing Director</td>
        </tr>
        <tr>
            <td class="meta-label">From:</td>   
            <td>{{ $memo->creator->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="meta-label">CC:</td>   
            <td>{{ $memo->managers->pluck('name')->implode(', ') }}</td>
        </tr>
        <tr>
            <td class="meta-label">Re:</td>
            <td>{{ $memo->title }}</td>
        </tr>
        <tr>
            <td class="meta-label">Date:</td>
            <td>{{ $memo->formatted_meeting_date }}</td>
        </tr>
        <tr>
            <td class="meta-label">Status:</td>
            <td style="text-transform: capitalize;">{{ str_replace('_', ' ', $memo->status) }}</td>
        </tr>
    </table> -->
    <!-- <span style="text-transform: capitalize;">{{ str_replace('_', ' ', $memo->status) }}</span>
    <hr> -->

    <div class="content">
        {!! $memo->content !!}
    </div>

    <div class="signatures">
        @php
            $checkerReview = $memo->reviews->where('reviewed_by', $memo->checker_id)->where('status', 'approved')->first();
            $verifierReview = $memo->reviews->where('reviewed_by', $memo->verifier_id)->where('status', 'approved')->first();
            $approverReview = $memo->reviews->where('reviewed_by', $memo->approver_id)->where('status', 'approved')->first();
        @endphp

        <div class="signature-box">
            <div class="signature-role">Prepared By</div>
            @if($memo->creator->signature_path)
                @php
                    $sigPath = storage_path('app/public/' . $memo->creator->signature_path);
                    if(file_exists($sigPath)) {
                        $type = pathinfo($sigPath, PATHINFO_EXTENSION);
                        $data = file_get_contents($sigPath);
                        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                    } else {
                        $base64 = null;
                    }
                @endphp
                @if($base64)
                    <img src="{{ $base64 }}" class="signature-img" />
                @else
                    <div style="height: 80px;"></div>
                @endif
            @else
                <div style="height: 80px;"></div>
            @endif
            <div class="signature-line"></div>
            <div class="signature-name">{{ $memo->creator->name }}</div>
            <!-- <div style="font-size: 11px;">{{ $memo->created_at->format('M d, Y h:i A') }}</div> -->
        </div>

        @if($checkerReview && $memo->checker)
        <div class="signature-box">
            <div class="signature-role">Checked By</div>
            @if($memo->checker->signature_path)
                @php
                    $sigPath = storage_path('app/public/' . $memo->checker->signature_path);
                    if(file_exists($sigPath)) {
                        $type = pathinfo($sigPath, PATHINFO_EXTENSION);
                        $data = file_get_contents($sigPath);
                        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                    } else {
                        $base64 = null;
                    }
                @endphp
                @if($base64)
                    <img src="{{ $base64 }}" class="signature-img" />
                @else
                    <div style="height: 80px;"></div>
                @endif
            @else
                <div style="height: 80px;"></div>
            @endif
            <div class="signature-line"></div>
            <div class="signature-name">{{ $memo->checker->name }}</div>
            <!-- <div style="font-size: 11px;">{{ $checkerReview->created_at->format('M d, Y h:i A') }}</div> -->
        </div>
        @endif

        @if($verifierReview && $memo->verifier)
        <div class="signature-box">
            <div class="signature-role">Verified By</div>
            @if($memo->verifier->signature_path)
                @php
                    $sigPath = storage_path('app/public/' . $memo->verifier->signature_path);
                    if(file_exists($sigPath)) {
                        $type = pathinfo($sigPath, PATHINFO_EXTENSION);
                        $data = file_get_contents($sigPath);
                        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                    } else {
                        $base64 = null;
                    }
                @endphp
                @if($base64)
                    <img src="{{ $base64 }}" class="signature-img" />
                @else
                    <div style="height: 80px;"></div>
                @endif
            @else
                <div style="height: 80px;"></div>
            @endif
            <div class="signature-line"></div>
            <div class="signature-name">{{ $memo->verifier->name }}</div>
            <!-- <div style="font-size: 11px;">{{ $verifierReview->created_at->format('M d, Y h:i A') }}</div> -->
        </div>
        @endif

        @if($approverReview && $memo->approver)
        <div class="signature-box">
            <div class="signature-role">Approved By</div>
            @if($memo->approver->signature_path)
                @php
                    $sigPath = storage_path('app/public/' . $memo->approver->signature_path);
                    if(file_exists($sigPath)) {
                        $type = pathinfo($sigPath, PATHINFO_EXTENSION);
                        $data = file_get_contents($sigPath);
                        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                    } else {
                        $base64 = null;
                    }
                @endphp
                @if($base64)
                    <img src="{{ $base64 }}" class="signature-img" />
                @else
                    <div style="height: 80px;"></div>
                @endif
            @else
                <div style="height: 80px;"></div>
            @endif
            <div class="signature-line"></div>
            <div class="signature-name">{{ $memo->approver->name }}</div>
            <!-- <div style="font-size: 11px;">{{ $approverReview->created_at->format('M d, Y h:i A') }}</div> -->
        </div>
        @endif
    </div>
</body>
</html>
