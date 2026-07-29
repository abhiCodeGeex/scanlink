<style>
    h1 { font-size: 16px; }
    h2 { font-size: 12px; margin: 10px 0 4px; }
    .meta { font-size: 10px; color: #555; }
    table { width: 100%; border-collapse: collapse; font-size: 10px; }
    th, td { border: 1px solid #ccc; padding: 4px; }
    th { background: #f2f2f2; text-align: left; }
</style>

<h1>Cumulative Analytics</h1>

<p class="meta">
    @foreach ($profiles as $profile)
        Profile {{ (int) $profile['id'] }}. {{ ucwords((string) $profile['name']) }}<br>
    @endforeach
</p>

<p class="meta">
    Form Submissions: {{ $formSubmissionCount }} &nbsp;&nbsp; Analytics Count: {{ $scanTotal }}
</p>

@forelse ($charts as $chart)
    @php($total = array_sum(array_column($chart['slices'], 'value')))
    <h2>{{ $chart['title'] }}</h2>
    <table>
        <thead>
            <tr><th>Option</th><th>Count</th><th>Percentage</th></tr>
        </thead>
        <tbody>
            @foreach ($chart['slices'] as $slice)
                <tr>
                    <td>{{ $slice['label'] }}</td>
                    <td>{{ (int) $slice['value'] }}</td>
                    <td>{{ $total > 0 ? round(((int) $slice['value']) * 100 / $total, 1).'%' : '0%' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@empty
    <p class="meta">No form analytics questions for these profiles.</p>
@endforelse

<p class="meta" style="margin-top:16px;">Date: {{ $date }}</p>
