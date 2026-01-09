<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title ?? 'Financial Report' }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            margin: 0;
            padding: 0;
            font-size: 20px;
        }
        .header p {
            margin: 5px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table th {
            background-color: #f2f2f2;
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
            font-weight: bold;
        }
        table td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .text-right {
            text-align: right;
        }
        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>{{ $title ?? 'Financial Report' }}</h1>
        <p><strong>{{ $company ?? 'Company Name' }}</strong></p>
        @if(isset($period))
            <p>{{ $period }}</p>
        @endif
        @if(isset($as_of_date))
            <p>As of {{ $as_of_date }}</p>
        @endif
    </div>

    @if(isset($headers) && isset($rows))
        <table>
            <thead>
                <tr>
                    @foreach($headers as $header)
                        <th>{{ $header }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($rows as $row)
                    <tr>
                        @foreach($row as $cell)
                            <td class="{{ is_numeric($cell) ? 'text-right' : '' }}">
                                {{ is_numeric($cell) ? number_format($cell, 2) : $cell }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">
        <p>Generated on {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>
</body>
</html>
