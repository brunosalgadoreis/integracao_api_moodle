<div>
    
 
@foreach ($data as $index => $obj)
@if ($obj->shortname === 'MATEMATICA I')
<div>
            <p><strong>Nome:</strong> {{$obj->id}}</p>

        </div>
        <hr>
@endif
        
    @endforeach
    
</div>
