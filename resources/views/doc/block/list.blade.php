@if(!empty($doc_list))
@foreach($doc_list as $ckey => $doc)
 <li class="list-group-item"><a href="{{ Helper::route('help_view', [$doc['id']]) }}">{{ $doc['name'] }}<span class="to">></span></a></li>
@endforeach
@endif