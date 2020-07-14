<h2>Full name:</h2>
<h2>Position:</h2>
@foreach($sheets as $sheet)
    <h4>{{ $sheet['name'] }}</h4>
    @foreach($sheet['data'] as $index => $item)
        <p><b>{{$index + 1 . '. '}}{!! nl2br($item['question']) !!}</b></p>
        @foreach($item['options'] as $option)
            <p style="margin-left: 10px">{{ $option }}</p>
        @endforeach
    @endforeach
@endforeach
