<style>
    h1 { font-size: 16px; color: #008C00; }
    h2 { font-size: 12px; margin: 12px 0 4px; }
    .meta { font-size: 10px; color: #555; }
    table { width: 100%; border-collapse: collapse; font-size: 8px; }
    th, td { border: 1px solid #ccc; padding: 3px; }
    th { background: #f2f2f2; text-align: left; }
</style>

<h1>Scan Analytics — Profile {{ $profileId }}. {{ $profileName }}</h1>
<p class="meta">Total Scans: {{ $analyticsCount }} &nbsp;&nbsp; Form Submissions: {{ $formSubmissionCount }} &nbsp;&nbsp; Submission Rate: {{ $submissionRate ?? 0 }}%</p>

@foreach ($barCharts as $title => $img)
    <h2>{{ $title }}</h2>
    @if (! empty($img))
        <img src="{{ $img }}" alt="{{ $title }}">
    @else
        <p class="meta">No data.</p>
    @endif
@endforeach

@if (! empty($formPies))
    <h2>Form Analytics</h2>
    @foreach ($formPies as $pie)
        <h2>{{ $pie['title'] }}</h2>
        @if (! empty($pie['image']))
            <img src="{{ $pie['image'] }}" alt="{{ $pie['title'] }}">
        @endif
    @endforeach
@endif

@if ($locationRows->isNotEmpty())
    <h2>Scan Locations</h2>
    <table>
        <thead>
            <tr><th>#</th><th>Date</th><th>IP</th><th>Location</th><th>Device</th><th>Browser</th><th>Scan Type</th></tr>
        </thead>
        <tbody>
            @foreach ($locationRows->take(100) as $i => $row)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $row->created_at?->format('d/m/Y H:i') ?? '' }}</td>
                    <td>{{ $row->ip_add }}</td>
                    <td>{{ $row->location_label }}</td>
                    <td>{{ $row->device_name }}</td>
                    <td>{{ $row->browser_name }}</td>
                    <td>{{ $row->scan_type }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

<p class="meta" style="margin-top:14px;">Date: {{ $date }}</p>
