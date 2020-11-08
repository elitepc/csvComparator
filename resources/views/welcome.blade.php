<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>CSV Compare</title>
    </head>
    <body>
        @foreach ($errors->all() as $error)
            <p class="error">{{ $error }}</p>
        @endforeach

        <form action="{{route('csv_compare.upload')}}" method="post" enctype="multipart/form-data">
            @csrf
            <label>
                Old File
                <input type="file" name="file">
            </label>
            <label>
                New File
                <input type="file" name="new_file">
            </label>
            <button type="submit">Submit</button>
        </form>
        <link rel="stylesheet" type="text/css" href="{{ url('/css/app.css') }}" />
    </body>
</html>
