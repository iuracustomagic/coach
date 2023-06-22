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
    @if($result)
            <table class="table">

                <tr>
                    <th>
                        <h3>
                            {{$employee->name}}
                            <a class="btn btn-sm btn-success" href="/admin/evaluation/{{$employee->id}}/start"><i class="la la-chalkboard"></i></a>
                        </h3>
                        <h6>Должность: <b>{{$employee->profession->name}}</b></h6>
                        <h6>Подразделение: <b>{{$employee->divisions ? $employee->divisions[0]['name'] : '-'}}</b></h6>

                    </th>
                    @foreach($dates as $date)
                        <th class="date">
                            <p>{{$date['date']}}</p>
                            <p>{{$date['examiner']}}</p>
                            <p>{{$date['recommendation']}}</p>


                        </th>
                    @endforeach
                </tr>
                @php $counter = 1; @endphp
                @php echo '<pre>';
                print_r($result);

                echo '</pre>';
                exit();
                @endphp
                @foreach($result as $key => $criterias)
                    @php echo '<pre>';
                print_r($key['items']);
                echo '</pre>';
                exit();
                    @endphp
                    <tr>
                        <th colspan="2" class="bg-info-subtle">
                            <h5 class="m-0 fw-bold">{{$counter++}}. {{$key}}</h5>
                        </th>
                    </tr>
                    @foreach($criterias as $criteria)
                        <tr>
                            <td class="bg-body-tertiary" >
                                <span>{{$criteria['criteria']}}</span>

                            </td>
                            <td class="w-25 bg-body-tertiary">
                                <div class="input-group ">

                                </div>

                            </td>

                        </tr>
                    @endforeach
                    <tr>
                        <td class="bg-success-subtle border-bottom-3 border-top-3 border-start-0 border-end-0 border-info text-end" >
                            <span class="fw-bold">Sub total</span>
                        </td>
                        <td class="bg-success-subtle border-bottom-3 border-top-3 border-start-0 border-end-0 border-info text-end" >

                            <span id="{{$key}}" class="count-medium fw-bold" data-mark="{{$key}}">0</span>
                        </td>
                    </tr>
                @endforeach

                <tr>
                    <th>Набрано баллов:</th>
                    @foreach($totals['points'] as $date => $points)
                        <th class="totals">{{$points}}</th>
                    @endforeach
                </tr>

                <tr>
                    <th>Оценка:</th>
                    @foreach($totals['marks'] as $date => $mark)
                        <th class="totals">{{$mark}}</th>
                    @endforeach
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
