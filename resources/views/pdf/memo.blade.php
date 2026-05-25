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
            width: 45%;
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

    <table class="meta-table">
        <tr>
            <td class="meta-label">Title:</td>
            <td>{{ $memo->title }}</td>
        </tr>
        <tr>
            <td class="meta-label">Submitted By:</td>   
            <td>{{ $memo->creator->name ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="meta-label">Date:</td>
            <td>{{ $memo->formatted_meeting_date }}</td>
        </tr>
        <tr>
            <td class="meta-label">Status:</td>
            <td style="text-transform: capitalize;">{{ str_replace('_', ' ', $memo->status) }}</td>
        </tr>
    </table>

    <hr>

    <div class="content">
        {!! $memo->content !!}
    </div>

    <div class="signatures">
        @php
            $managerReview = $memo->reviews->where('status', 'approved')->first(); // Manager review
            $adminReview = $memo->reviews->where('status', 'approved')->last(); // If admin also approved, they're the last one
        @endphp

        @if($managerReview && $managerReview->reviewer)
        <div class="signature-box">
            @if($managerReview->reviewer->signature_path)
                @php
                    $sigPath = storage_path('app/public/' . $managerReview->reviewer->signature_path);
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
            <div class="signature-name">{{ $managerReview->reviewer->name }}</div>
            <div class="signature-role">Manager</div>
            <div style="font-size: 11px;">{{ $managerReview->created_at->format('M d, Y h:i A') }}</div>
        </div>
        @endif

        @if($adminReview && $adminReview->reviewer && $adminReview->reviewer->id !== ($managerReview->reviewer->id ?? null))
        <div class="signature-box" style="float: right;">
            @if($adminReview->reviewer->signature_path)
                @php
                    $sigPath = storage_path('app/public/' . $adminReview->reviewer->signature_path);
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
            <div class="signature-name">{{ $adminReview->reviewer->name }}</div>
            <div class="signature-role">Admin</div>
            <div style="font-size: 11px;">{{ $adminReview->created_at->format('M d, Y h:i A') }}</div>
        </div>
        @endif
    </div>
</body>
</html>
