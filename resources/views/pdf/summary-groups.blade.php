@foreach (array_chunk($groups, 2) as $row)
    <table class="pair">
        <tr>
            @foreach ($row as $group)
                <td>
                    <table class="skills">
                        <thead>
                            <tr>
                                <th>{{ $group['label'] }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($group['skills'] as $skill)
                                <tr>
                                    <td>
                                        {{ $skill['name'] }}
                                        @if ($skill['level'] !== '')
                                            <span class="badge">{{ $skill['level'] }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </td>
            @endforeach
            @if (count($row) === 1)
                <td></td>
            @endif
        </tr>
    </table>
@endforeach
