@extends(backpack_view('blank'))

<style>
    table{
        max-width: 868px;
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
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
@section('content')
    @if($evaluation)
        <form id="evaluation" action="/admin/skills-evaluation/{{$employee->id}}/save" method="post">
            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
            <input type="hidden" name="signature" value="{{md5($employee->id)}}" />
            <table class="table">
{{--                {{dd($employee->divisions)}}--}}
                <tr>
                    <th>
                        <h3>{{$employee->name}}</h3>
                        <h6>Должность: <b>{{$employee->profession->name}}</b></h6>
{{--                        <h6>Регион: <b>{{$employee->supervisor ? $employee->supervisor->name : '-'}}</b></h6>--}}
                        <h6>Подразделение: <b>{{$employee->divisions ? $employee->divisions[0]['name'] : '-'}}</b></h6>
                        <h6>Оценивает: <b>{{backpack_user()->name}}</b></h6>
                    </th>
                    <th class="date">
                        <p>Дата проверки</p>
                        <p>{{date('Y-m-d')}}</p>
                    </th>
                </tr>
{{--                {{dd($evaluation)}}--}}
                {{$counter = 1}}
                @foreach($evaluation as $key => $criterias)
                    <tr>
                        <th colspan="2" class="bg-info-subtle">
                            <h5 class="m-0">{{$counter++}}.{{$key}}</h5>
                        </th>
                    </tr>
                    @foreach($criterias as $criteria)
                    <tr>
                        <td class="bg-body-tertiary" >
                            <span><b>{{$criteria['criteria']}}</b></span>
{{--                            @foreach($group as $inputId => $point)--}}
{{--                                <input id="{{$inputId}}" type="radio" name="{{$groupName}}" value="{{$point['mark']}}" data-criteria="{{$criteria}}" />--}}
{{--                                <label for="{{$inputId}}" class="point @if($point['mark'] == $criterias['best']) good @elseif($point['mark'] == $criterias['worst']) bad @else medium @endif">--}}
{{--                                    {{$point['title']}} <span><b>{{$point['mark']}}</b></span>--}}
{{--                                </label>--}}
{{--                            @endforeach--}}
                        </td>
                        <td class="w-25 bg-body-tertiary">
                            <div class="input-group ">
                            <select class="form-select" id="" name="{{$criteria['criteria']}}" data-criteria="{{$criteria['criteria']}}" >
                                <option >Nota</option>
                                <option value="1">1 - Nu știe</option>
                                <option value="2">2 - Știe, dar nu are abilitățile necesare</option>
                                <option value="3">3 - Știe cum, dar nu o face ( demotivare)</option>
                                <option value="4">4 - Demonstrează parțial</option>
                                <option value="5">5 - Demonstrează întotdeauna</option>
                            </select>
                            </div>
{{--                            <input id="" type="number" name="mark" class="form-control" value="0"  />--}}
                        </td>

                    </tr>
                    @endforeach
                    <tr>
                        <td class="bg-success-subtle border-bottom-3 border-top-3 border-start-0 border-end-0 border-info" >
                            <span>Оценка</span>
                        </td>
                        <td class="bg-success-subtle border-bottom-3 border-top-3 border-start-0 border-end-0 border-info text-end" >
                            <span id="" data-mark="{{$key}}">0</span>
                        </td>
                    </tr>
                @endforeach
{{--                <tr>--}}
{{--                    <th>Набрано баллов:</th>--}}
{{--                    <th id="total_points" class="total-points">-</th>--}}
{{--                </tr>--}}
{{--                <tr>--}}
{{--                    <th>Оценка:</th>--}}
{{--                    <th id="evaluation_mark" class="total-points" data-counter="{{$counter}}">-</th>--}}
{{--                </tr>--}}
{{--                <tr>--}}
{{--                    <th colspan="2">--}}
{{--                        <input type="hidden" name="result" value="" />--}}
{{--                        <input type="hidden" name="total_points" value="0" />--}}
{{--                        <input type="hidden" name="total_questions" value="{{$counter}}" />--}}
{{--                        <input type="hidden" name="mark" value="0" />--}}
{{--                        <textarea name="comment" rows="5" maxlength="255" placeholder="Комментарий" style="width: 100%; padding: 10px; margin-bottom: 10px"></textarea>--}}
{{--                        <button type="submit" class="btn btn-primary float-right">Сохранить</button>--}}
{{--                    </th>--}}
{{--                </tr>--}}
            </table>
        </form>
    @else
        <h3>Нет оценочных листов для должности <b>{{$employee->profession->name}}</b></h3>
    @endif
@endsection

@push('after_scripts')

<script>
jQuery(document).ready(function($) {
  const evaluationList = {!! json_encode($evaluation) !!};
    var result = Object.entries(evaluationList);
  {{--  let evaluationArr = JSON.parse({!! json_encode($evaluation) !!});--}}
{{--    let validation = JSON.parse('{!!$validation!!}');--}}
{{--const evaluationList = {{$evaluation}};--}}

 $('select.form-select').on('change', () =>{
        let criteria = $(this).data('criteria');
        let name = $(this).attr('name');

      var selectedValue = $(this).children("option").val();
        console.log('criteria', criteria)
        console.log('name', name)
        console.log('value', selectedValue)
      console.log($(this))
    })
// for (const evaluation of result) {
//     console.log(evaluation[0])
//
//     let criteriaList = evaluation[1];
//     for (const criteria of criteriaList) {
//         $('select[name="+criteria+"]').change(function(){
//             let criteria = $(this).data('criteria');
//             let name = $(this).attr('name');
//             let value = $(this).val();
//
//             var selectedValue = $(this).children("option").val();
//             console.log('criteria', criteria)
//             console.log('name', name)
//             console.log('value', selectedValue)
//             console.log($(this))
//         })
//         // let criteria = criteria['criteria'];
//         //[name="+criteria+"]
//
//
//     }
// }
    // evaluationArr.forEach((evaluation, index, array)=> {
    //     evaluation.forEach((item)=> {
    //         console.log(item.criteria)
    //     })
    // })
{{--	$('input[type="radio"]').on('change', function(){--}}
{{--        let criteria = $(this).data('criteria'),--}}
{{--            name = $(this).attr('name'),--}}
{{--            id = $(this).attr('id');--}}
{{--		$('#points_for_'+name).html($(this).val());--}}
{{--        validation[name] = true;--}}

{{--        for (const [key, value] of Object.entries(evaluation[criteria]['points'][name])) {--}}
{{--            evaluation[criteria]['points'][name][key]['selected'] = false;--}}
{{--        }--}}
{{--        evaluation[criteria]['points'][name][id]['selected'] = true;--}}

{{--        let totalPoints = 0;--}}
{{--        $('input[type="radio"]:checked').each(function(){--}}
{{--            totalPoints += parseFloat($(this).val());--}}
{{--        });--}}

{{--        $('#total_points').html(totalPoints);--}}
{{--        $('input[name="total_points"]').val(totalPoints);--}}
{{--        let mark = totalPoints / parseInt($('#evaluation_mark').data('counter'));--}}
{{--        $('#evaluation_mark').html(mark.toFixed(2));--}}
{{--        $('input[name="mark"]').val(mark.toFixed(2));--}}
{{--	});--}}

{{--    $('form#evaluation').on('submit', function(){--}}
{{--        $('input[name="result"]').val(JSON.stringify(evaluation));--}}
{{--        for (const key in validation) {--}}
{{--            if(!validation[key]){--}}
{{--                new Noty({--}}
{{--                    type: "error",--}}
{{--                    text: 'Остались незаполненные поля!',--}}
{{--                }).show();--}}
{{--                return false;--}}
{{--            }--}}
{{--        }--}}
{{--        return true;--}}
{{--    });--}}
});
</script>
@endpush
