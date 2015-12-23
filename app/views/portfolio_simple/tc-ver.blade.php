@extends($project_name.'-master')

@section('contenido')
@if(Session::has('mensaje'))
<script src="{{URL::to('js/divAlertaFuncs.js')}}"></script>
@endif
<section>
    @if (Session::has('mensaje'))
        <div class="divAlerta ok alert-success">{{ Session::get('mensaje') }}<i onclick="" class="cerrarDivAlerta fa fa-times fa-lg"></i></div>
    @endif
    <div class="container">
        <h2><span class=""><a class="volveraSeccion" href="{{URL::to('/'.$item -> seccionItem() -> menuSeccion()->lang() -> url)}}">{{ $item -> seccionItem() -> menuSeccion()->lang() -> nombre }}</a></span></h2>
       
        @if(Auth::check())
            @if(Auth::user()->can("editar_item"))
            <a href="{{URL::to('admin/portfolio/editar/'.$item->portfolio()->id)}}" data='{{$item -> seccionItem() -> id}}' style="display:none">Editar<i class="fa fa-pencil fa-lg"></i></a>
            @endif
        @endif
        
        <!--columna producto y descripcion -->
        <div class="col70">
            <div class="imgProd">
                        @if(count($item->imagen_destacada()) > 0)
                                <img src="{{ URL::to($item->imagen_destacada()->carpeta.$item->imagen_destacada()->nombre) }}" alt="{{$item->lang()->titulo}}">
                                <p>{{$item->imagen_destacada()->lang()->epigrafe}}</p>
                        @else
                            <li><img src="{{ URL::to('images/sinImg.gif') }}" alt="{{$item->lang()->titulo}}"></li>
                        @endif
            </div>
            
            <div class="detalleProd">
                <h2>{{ $item->lang() -> titulo }}</h2> 
                <div class="editor">
                    <h4>Descripcion</h4>
                    {{ $item->lang()->descripcion }}
                </div>
            </div>
            <div class="clear"></div>
        </div>
        <div class="clear"></div>
    </div>
</section>
@stop