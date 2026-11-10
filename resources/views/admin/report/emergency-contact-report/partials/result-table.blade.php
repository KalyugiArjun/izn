@foreach ($records as $index => $record)
    <tr>
        <td>{{ $index + 1 }}</td>
        <td>{{ $record->employee->name ?? $record->name }}</td>
        <td>{{ $record->employee->department->name ?? '-' }}</td>
        <td>{{ $record->employee->designation->name ?? '-' }}</td>
        <td>{{ $record->mobile_number }}</td>
        <td>{{ $record->email ?? '-' }}</td>
        <td>{{ $record->address ?? '-' }}</td>
        <td>
            @if ($record->relation === 'Other')
                {{ $record->other_relation }}
            @else
                {{ $record->relation }}
            @endif
        </td>
    </tr>
@endforeach
