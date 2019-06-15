@extends('/inc.admin-template')

@section('content')
@php
    
    $positionsArray = [];
    for ($i = 0; $i < count($positions); $i++){
        $positionsArray[$i] = $positions[$i]{'position'};
    }
    $weekToggler = explode('_', $currWeek);
    $nextW = (((int)$weekToggler[0])+1) . '_' . $weekToggler[1];
    $prevW = (((int)$weekToggler[0])-1) . '_' . $weekToggler[1];
    $yr = new DateTime('December 28' . ', ' . (string)$weekToggler[1]);
    $limit = (int)$yr->format('W');
    $k=0;
    // add empty element at the beginning of the dates array, for display purposes
    array_unshift($dates, " ");

@endphp 
<div class="wrapper">
    <div class="row">
        <div class="col-6 text-left">   
            <div class="next">
                @php
                    if((int)$weekToggler[0] <2){
                        $currentY = (((int)$weekToggler[1])-1);
                        $dateTime = new DateTime('December 28' . ', ' . $currentY);
                        $tempPrevW = $dateTime->format('W');
                        $prevW = $tempPrevW . '_' . $currentY;
                    }
                @endphp
                <a href="{{ action ('RotaController@show', "$prevW")}}" class="btn btn-outline-secondary mb-3">
                    <span> Past week </span>
                </a>
            </div>
        </div>
        <div class="col-6 text-right">   
            <div class="next">
                @php
                    if((int)$weekToggler[0] >= $limit){
                        $currentY = (((int)$weekToggler[1])+1);
                        $nextW = '1_' . $currentY;
                    }
                @endphp
                <a href="{{ action ('RotaController@show', "$nextW")}}" class="btn btn-outline-secondary mb-3">
                    <span> Next week </span>
                </a>
            </div>
        </div>
    </div>
        <div class="mr-3 ml-3" id="maintb"> 
            {!! Form::open(['action' => ['RotaController@update', $currWeek], 'method' => 'POST', 'enctype' => 'multipart/form-data']) !!}
            <div class="table pr-3 pl-3 mb-1">
                <div class="table-row">
                    <div class="head-table-cell">Name</div>
                    <div class="head-table-cell">Sunday</div>
                    <div class="head-table-cell">Monday</div>
                    <div class="head-table-cell">Tuesday</div>
                    <div class="head-table-cell">Wednesday</div>
                    <div class="head-table-cell">Thursday</div>
                    <div class="head-table-cell">Friday</div>
                    <div class="head-table-cell">Saturday</div>
                </div>
                <div class="table-row">
                    @for ($i = 0; $i < count($dates); $i++)
                        <div class="head-table-cell"> {!!$dates[$i]!!} </div>
                    @endfor
                </div>
                <!--
                    * create a loop to iterate through each instance in the weekly rota database
                    * and create a row for each one; in case the username coincides with the name in the rota
                    * add class="active" to each row, so it is easier for the user to identify its own row
                -->
                {{--
                Rota table; a little explanation on that ternary operator
                class statement: 
                    if the day is not set in the 'rota' table, 
                        then color the text in red
                    else if the day is set in the draft table (in which case it would be set in both tables)
                        if the values are dfferent
                            then color the text in red
                
                value statement:
                    if the value exists in the 'draft rotas' table and the user is confirmed,
                        then output it
                    else if the value exists in the 'rota' table and the user is confirmed,
                        then output it
                    else
                        set value to null
                --}}
                @csrf
                @for ($j = 0; $j < count($positions); $j++)
                    @foreach ($users as $user)                
                        @if ($user->position == $positionsArray[$j]) 
                            @php
                                for($i=0; $i<count($users); $i++){
                                    if(!isset($rotas[$i])){
                                        if(!isset($draftRotas[$i])){
                                            $k=$i;
                                        }else{
                                            if ($draftRotas[$i]->user_id == $user->id){
                                                $k = $i;
                                                break;
                                            }
                                        }
                                    }else{
                                        if ($rotas[$i]->user_id == $user->id){
                                            $k = $i;
                                            break;
                                        }       
                                    }
                                }
                            @endphp
                            <div class="table-row">
                                <div class="table-cell">
                                    {{$user->first_name}}
                                    <input name="{!! $user->id !!}_user_id" type="hidden" value="{{$user->id}}">
                                    <input name="{!! $user->id !!}_week_no" type="hidden" value="{{$currWeek}}">
                                </div>
                                <div class="table-cell">
                                    <input type="text" name="{!! $user->id !!}_sunday" class="form-control {!!(!isset($rotas[$k]->sunday)) ? 'text-danger' : ((isset($draftRotas[$k]->sunday)) ? ((($rotas[$k]->sunday) != ($draftRotas[$k]->sunday)) ? 'text-danger' : ' ') : ' ') !!}" value="{!!(isset($draftRotas[$k])) ? $draftRotas[$k]->sunday : ((isset($rotas[$k])) ? $rotas[$k]->sunday : ' ') !!}">         
                                </div>
                                <div class="table-cell">
                                    <input type="text" name="{!! $user->id !!}_monday" class="form-control {!! (!isset($rotas[$k]->monday))? 'text-danger' : ((isset($draftRotas[$k]->monday)) ? (($rotas[$k]->monday != $draftRotas[$k]->monday) ? 'text-danger' : ' ') : ' ') !!}" value="{!! (isset($draftRotas[$k])) ? $draftRotas[$k]->monday : ((isset($rotas[$k])) ? $rotas[$k]->monday : ' ') !!}">   
                                </div>
                                <div class="table-cell">
                                    <input type="text" name="{!! $user->id !!}_tuesday" class="form-control {!!(!isset($rotas[$k]->tuesday))? 'text-danger' : ((isset($draftRotas[$k]->tuesday)) ? ((($rotas[$k]->tuesday) != ($draftRotas[$k]->tuesday)) ? 'text-danger' : ' ') : ' ')  !!}" value="{!! (isset($draftRotas[$k])) ? $draftRotas[$k]->tuesday : ((isset($rotas[$k])) ? $rotas[$k]->tuesday : ' ') !!}">  
                                </div>
                                <div class="table-cell">
                                    <input type="text" name="{!! $user->id !!}_wednesday" class="form-control {!!(!isset($rotas[$k]->wednesday))? 'text-danger' : ((isset($draftRotas[$k]->wednesday)) ? ((($rotas[$k]->wednesday) != ($draftRotas[$k]->wednesday)) ? 'text-danger' : ' ') : ' ')  !!}" value="{!! (isset($draftRotas[$k])) ? $draftRotas[$k]->wednesday : ((isset($rotas[$k])) ? $rotas[$k]->wednesday : ' ') !!}">   
                                </div>
                                <div class="table-cell">
                                    <input type="text" name="{!! $user->id !!}_thursday" class="form-control {!!(!isset($rotas[$k]->thursday))? 'text-danger' : ((isset($draftRotas[$k]->thursday)) ? ((($rotas[$k]->thursday) != ($draftRotas[$k]->thursday)) ? 'text-danger' : ' ') : ' ')  !!}" value="{!! (isset($draftRotas[$k])) ? $draftRotas[$k]->thursday : ((isset($rotas[$k])) ? $rotas[$k]->thursday : ' ') !!}">      
                                </div>
                                <div class="table-cell">
                                    <input type="text" name="{!! $user->id !!}_friday" class="form-control {!!(!isset($rotas[$k]->friday))? 'text-danger' : ((isset($draftRotas[$k]->friday)) ? ((($rotas[$k]->friday) != ($draftRotas[$k]->friday)) ? 'text-danger' : ' ') : ' ')  !!}" value="{!! (isset($draftRotas[$k])) ? $draftRotas[$k]->friday : ((isset($rotas[$k])) ? $rotas[$k]->friday : ' ') !!}">      
                                </div>
                                <div class="table-cell">
                                    <input type="text" name="{!! $user->id !!}_saturday" class="form-control {!!(!isset($rotas[$k]->saturday))? 'text-danger' : ((isset($draftRotas[$k]->saturday)) ? ((($rotas[$k]->saturday) != ($draftRotas[$k]->saturday)) ? 'text-danger' : ' ') : ' ')  !!}" value="{!! (isset($draftRotas[$k])) ? $draftRotas[$k]->saturday : ((isset($rotas[$k])) ? $rotas[$k]->saturday : ' ') !!}">        
                                </div>   
                            </div>
                        @endif
                    @endforeach
                    <div class="table-row">
                        @for ($i = 0; $i < 8; $i++)
                            <div class="dark-table-cell bg-dark"></div>
                        @endfor
                    </div>
                @endfor
                </div>
                <div class="row mt-3">
                    <div class="col-md-1">
                        {{Form::hidden('_method', 'PUT')}}
                        {{Form::submit('Submit', ['class' => 'btn btn-primary text-center float-left'])}}
                    </div>
                    <div class="col-md-1">
                        <a href="{{ action ('DraftsController@show', "$currWeek")}}" class="btn btn-danger float-left ">
                            <span> Edit </span>
                        </a>
                    </div>
                    {{-- <div class="col-md-1">
                        <a href="{{ action ('RotaController@pdf', "$currWeek")}}" class="btn btn-outline-dark float-left ">
                            <span> Export to PDF </span>
                        </a>
                    </div> --}}
                </div>
        {!! Form::close() !!}
    </div>  
</div>
  
@endsection