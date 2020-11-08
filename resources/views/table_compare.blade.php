<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>CSV Compare</title>
    </head>
    <body>
        <table class="results">
            <thead>
                <tr>
                    @foreach($header as $colName)
                        <th>{{ $colName }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($csvData as $csvLine)
                    <tr
                        class="{{ $csvLine['compare'] }}"
                    >
                        @foreach($csvLine as $key => $value)
                            @if (is_int($key))
                                <td class="{{ $csvLine['compare'] === 'different' && array_key_exists($key, $csvLine['difference']) ? 'diff' : '' }}" >{{ $value }}</td>
                            @endif
                        @endforeach
                    </tr>
                @endforeach
            </tbody>
        </table>
        <link rel="stylesheet" type="text/css" href="{{ url('/css/app.css') }}" />
    </body>
</html>


