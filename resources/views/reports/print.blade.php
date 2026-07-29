<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Report #{{ $report->id }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { border-bottom: 2px solid #1a56db; padding-bottom: 10px; margin-bottom: 20px; }
        .header h1 { color: #1a56db; margin: 0; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px; }
        .info-item { margin-bottom: 10px; }
        .info-label { font-weight: bold; color: #6b7280; font-size: 12px; text-transform: uppercase; }
        .info-value { font-size: 14px; margin-top: 2px; }
        .section { margin-top: 20px; padding: 15px; background: #f9fafb; border-radius: 5px; }
        .section h3 { margin-top: 0; color: #1a56db; }
        .status { display: inline-block; padding: 5px 10px; border-radius: 3px; font-size: 12px; }
        .status-approved { background: #d1fae5; color: #065f46; }
        .status-submitted { background: #dbeafe; color: #1e40af; }
        .footer { margin-top: 30px; text-align: center; font-size: 11px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>TNT Construction & Trading</h1>
        <p>Daily Site Report #{{ $report->id }}</p>
    </div>

    <div class="info-grid">
        <div class="info-item">
            <div class="info-label">Date</div>
            <div class="info-value">{{ $report->report_date->format('F d, Y') }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">Status</div>
            <div class="info-value">
                <span class="status status-{{ $report->status }}">
                    {{ strtoupper($report->status) }}
                </span>
            </div>
        </div>
        <div class="info-item">
            <div class="info-label">Project</div>
            <div class="info-value">{{ $report->project->name ?? 'N/A' }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">Site</div>
            <div class="info-value">{{ $report->site->site_name ?? 'N/A' }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">Submitted By</div>
            <div class="info-value">{{ $report->submittedBy->full_name ?? 'N/A' }}</div>
        </div>
        <div class="info-item">
            <div class="info-label">Approved By</div>
            <div class="info-value">{{ $report->approvedBy->full_name ?? 'Pending' }}</div>
        </div>
    </div>

    <div class="section">
        <h3>Workforce & Progress</h3>
        <p><strong>Workers Present:</strong> {{ $report->workforce_count }}</p>
        <p><strong>Subcontractors:</strong> {{ $report->subcontractor_count }}</p>
        <p><strong>Absent:</strong> {{ $report->absent_count }}</p>
        <p><strong>Progress:</strong> {{ $report->progress_percentage }}%</p>
    </div>

    @if($report->summary_text)
    <div class="section">
        <h3>Work Summary</h3>
        <p>{{ $report->summary_text }}</p>
    </div>
    @endif

    @if($report->challenges_encountered)
    <div class="section">
        <h3>Challenges</h3>
        <p>{{ $report->challenges_encountered }}</p>
    </div>
    @endif

    <div class="footer">
        <p>TNT Construction & Trading | This is a computer-generated document</p>
        <p>Printed: {{ now()->format('F d, Y H:i') }}</p>
    </div>
</body>
</html>
