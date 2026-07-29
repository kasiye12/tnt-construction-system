<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Daily Reports - {{ now()->format('Y-m-d') }}</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { color: #1a56db; margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #1a56db; color: white; padding: 10px; text-align: left; }
        td { padding: 8px; border-bottom: 1px solid #e5e7eb; }
        .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="header">
        <h1>TNT Construction</h1>
        <h2>Daily Reports Export</h2>
        <p>Generated: {{ now()->format('F d, Y H:i') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Project</th>
                <th>Site</th>
                <th>Submitted By</th>
                <th>Workforce</th>
                <th>Progress</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reports as $report)
            <tr>
                <td>{{ $report->report_date->format('M d, Y') }}</td>
                <td>{{ $report->project->name ?? 'N/A' }}</td>
                <td>{{ $report->site->site_name ?? 'N/A' }}</td>
                <td>{{ $report->submittedBy->full_name ?? 'N/A' }}</td>
                <td>{{ $report->workforce_count }}</td>
                <td>{{ $report->progress_percentage }}%</td>
                <td>{{ ucfirst($report->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>TNT Construction & Trading | Confidential Document</p>
        <p>Page 1 of 1</p>
    </div>
</body>
</html>
