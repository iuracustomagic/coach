@extends(backpack_view('blank'))

<style>
    table{
        max-width: 768px;
        margin: auto;
    }
    .point{
        display: block;
        border: 1px solid #ccc;
        padding: 5px;
        cursor: pointer;
        border-radius: 5px;
        padding-right: 20px;
        position: relative;
    }
    .point span{
        position: absolute;
        top: 4px;
        right: 5px;
    }
    input[type="radio"]{
        position: absolute;
        visibility: hidden;
    }
    input[type="radio"]:checked+label.good,
    .point.good:hover{
        background-color: #2ecc71;
        color: #fff;
    }
    input[type="radio"]:checked+label.medium,
    .point.medium:hover{
        background-color: #f1c40f;
        color: #fff;
    }
    input[type="radio"]:checked+label.bad,
    .point.bad:hover{
        background-color: #c0392b;
        color: #fff;
    }
    .points{
        text-align: center;
        vertical-align: middle !important;
        font-size: 30px;
    }
    .total-points{
        text-align: center;
    }
    .date{
        text-align: center;
        vertical-align: middle !important;
        white-space: nowrap;
    }
</style>

@section('content')
    @if($evaluation)
        <form id="evaluation" action="/admin/evaluation/{{$employee->id}}/save" method="post">
            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
            <input type="hidden" name="signature" value="{{md5($employee->id)}}" />
            <table class="table">
                <tr>
                    <th>
                        <h3>{{$employee->name}}</h3>
                        <h6>Должность: <b>{{$employee->profession->name}}</b></h6>
                        <h6>Начальник: <b>{{$employee->supervisor ? $employee->supervisor->name : '-'}}</b></h6>
                        <h6>Оценивает: <b>{{backpack_user()->name}}</b></h6>
                    </th>
                    <th class="date">
                        <p>Дата проверки</p>
                        <p>{{date('Y-m-d')}}</p>
                    </th>
                </tr>
                @foreach($evaluation as $criteria => $criterias)
                    <tr>
                        <th colspan="2">
                            <h5 class="m-0">{{$criterias['title']}}</h5>
                        </th>
                    </tr>
                    @foreach($criterias['points'] as $groupName => $group)
                    <tr>
                        <td>
                            @foreach($group as $inputId => $point)
                                <input id="{{$inputId}}" type="radio" name="{{$groupName}}" value="{{$point['mark']}}" data-criteria="{{$criteria}}" />
                                <label for="{{$inputId}}" class="point @if($point['mark'] == $criterias['best']) good @elseif($point['mark'] == $criterias['worst']) bad @else medium @endif">
                                    {{$point['title']}} <span><b>{{$point['mark']}}</b></span>
                                </label>
                            @endforeach
                        </td>
                        <td id="points_for_{{$groupName}}" class="points">-</td>
                    </tr>
                    @endforeach
                @endforeach
                <tr>
                    <th>Набрано баллов:</th>
                    <th id="total_points" class="total-points">-</th>
                </tr>
                <tr>
                    <th>Оценка:</th>
                    <th id="evaluation_mark" class="total-points" data-counter="{{$counter}}">-</th>
                </tr>
                <tr>
                    <th colspan="2">
                        <input type="hidden" name="result" value="" />
                        <input type="hidden" name="total_points" value="0" />
                        <input type="hidden" name="total_questions" value="{{$counter}}" />
                        <input type="hidden" name="mark" value="0" />
                        <textarea name="comment" rows="5" maxlength="255" placeholder="Комментарий" style="width: 100%; padding: 10px; margin-bottom: 10px"></textarea>
                        <button type="submit" class="btn btn-primary float-right">Сохранить</button>
                    </th>
                </tr>
            </table>
        </form>
    @else
        <h3>Нет оценочных листов для должности <b>{{$employee->profession->name}}</b></h3>
    @endif
@endsection

@push('after_scripts')
<script>
jQuery(document).ready(function($) {
  var json = {!! json_encode($json) !!};
    let evaluation = JSON.parse(json);
    let validation = JSON.parse('{!!$validation!!}');

	$('input[type="radio"]').on('change', function(){
        let criteria = $(this).data('criteria'),
            name = $(this).attr('name'),
            id = $(this).attr('id');
		$('#points_for_'+name).html($(this).val());
        validation[name] = true;

        for (const [key, value] of Object.entries(evaluation[criteria]['points'][name])) {
            evaluation[criteria]['points'][name][key]['selected'] = false;
        }
        evaluation[criteria]['points'][name][id]['selected'] = true;

        let totalPoints = 0;
        $('input[type="radio"]:checked').each(function(){
            totalPoints += parseFloat($(this).val());
        });

        $('#total_points').html(totalPoints);
        $('input[name="total_points"]').val(totalPoints);
        let mark = totalPoints / parseInt($('#evaluation_mark').data('counter'));
        $('#evaluation_mark').html(mark.toFixed(2));
        $('input[name="mark"]').val(mark.toFixed(2));
	});

    $('form#evaluation').on('submit', function(){
        $('input[name="result"]').val(JSON.stringify(evaluation));
        for (const key in validation) {
            if(!validation[key]){
                new Noty({
                    type: "error",
                    text: 'Остались незаполненные поля!',
                }).show();
                return false;
            }
        }
        return true;
    });
});
</script>
@endpush
