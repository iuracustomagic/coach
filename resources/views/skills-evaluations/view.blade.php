@extends(backpack_view('blank'))

<style>
    table{
        max-width: 768px;
        margin: auto;
    }
    ul.legend{
        margin: 0;
        padding: 0;
        list-style: none;
    }
    .point{
        text-align: center;
        vertical-align: middle !important;
        font-size: 30px;
    }
    .date{
        text-align: center;
        vertical-align: middle !important;
        white-space: nowrap;
    }
    .totals{
        text-align: center;
    }
</style>

@section('content')
    @if($info)
            <table class="table">
                
                <tr>
                    <th>
                        <h3>
                            {{$employee->name}}
                            <a class="btn btn-sm btn-success" href="/admin/evaluation/{{$employee->id}}/start"><i class="la la-chalkboard"></i></a>
                        </h3>
                        <h6>Должность: <b>{{$employee->profession->name}}</b></h6>
                        <h6>Начальник: <b>{{$employee->supervisor ? $employee->supervisor->name : '-'}}</b></h6>
                    </th>
                    <th class="date">
                        <p>{{$date['date']}}</p>
                        <p>{{$date['examiner']}}</p>
                        @if($date['comment'])
                            <button class="btn btn-sm btn-info show-comment" data-comment="{{$date['comment']}}">
                                <i class="la la-comment"></i>
                            </button>
                        @endif
                    </th>
                </tr>
                
                @foreach($info as $criteria)
                    <tr>
                        <th>{{$criteria['title']}}</th>
                        <th></th>
                    </tr>
                    @foreach($criteria['points'] as $group => $point)
                        <tr>
                            <td>
                                <ul class="legend">
                                    @foreach($point['legend'] as $title)
                                        <li>{{$title}}</li>
                                    @endforeach
                                </ul>
                            </td>
                            @foreach($point['marks'] as $date => $mark)
                                <td class="point">
                                    {{$mark}}
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                @endforeach

                <tr>
                    <th>Набрано баллов:</th>
                    <th class="totals">{{$totals['points']}}</th>
                </tr>

                <tr>
                    <th>Оценка:</th>
                    <th class="totals">{{$totals['mark']}}</th>
                </tr>
            </table>
    @else 
        <h3>Нет данных за текущий месяц</h3>
    @endif
@endsection	

<div class="modal fade" id="commentModal" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Комментарий</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                -----
            </div>
        </div>
    </div>
</div>

@push('after_scripts')
<script>
jQuery(document).ready(function($) {
    $('.show-comment').on('click', function(e){
        e.preventDefault()
        let modal = $('#commentModal')
        modal.find('.modal-body').html($(this).data('comment'))
        modal.modal('show')
    })
});
</script>
@endpush