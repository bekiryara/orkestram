@foreach(($shellNav ?? []) as $item)
    <a class="btn" href="{{ $item['href'] }}">{{ $item['label'] }}</a>
@endforeach
