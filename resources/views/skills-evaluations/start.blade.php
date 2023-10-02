@extends(backpack_view('blank'))

<style>
    table{
        max-width: 868px;
        margin: auto;
    }

    .date{
        text-align: center;
        vertical-align: middle !important;
        white-space: nowrap;
    }
    .conclusion {
        max-width: 868px;
        margin: 40px auto;
    }
    .recommendations {
        max-width: 868px;
        margin: 40px auto;
    }
    .recom_label {
        font-size: 22px;
    }
    ul li {
        list-style: none;

    }
</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
@section('content')
    @if($evaluation)
        <form id="skill_evaluation" action="/admin/skills-evaluation/{{$employee->id}}/save" method="post">
            <input type="hidden" name="_token" value="{{ csrf_token() }}" />
            <input type="hidden" name="signature" value="{{md5($employee->id)}}" />
            <table class="table">

                <tr>
                    <th>
                        <h3>{{$employee->name}}</h3>
                        <h6>{{trans('labels.profession')}}: <b> {{$employee->profession->name}}</b></h6>
                        <h6>{{trans('labels.division')}}: <b> {{$employee->divisions ? $employee->divisions[0]['name'] : '-'}}</b></h6>
                        <h6>{{trans('nav.locality')}}: <b> {{$employee->getLocalityName() ? $employee->getLocalityName() : '-'}}</b></h6>
                        <h6>{{trans('labels.evaluates')}}: <b> {{backpack_user()->name}}</b></h6>
                </th>
                <th class="date">
                    <p>{{trans('labels.date')}}/p>
                    <p>{{date('Y-m-d')}}</p>
                </th>
            </tr>

            @php $counter = 1; @endphp
            @foreach($evaluation as $key => $criterias)
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
                        <select class="form-select" id="{{$criteria['criteria']}}" name="{{$key}}" data-key="{{$key}}" >
                            <option value="0" data-key="{{$key}}">Nota</option>
                            <option value="1" data-key="{{$key}}">1 - Nu știe</option>
                            <option value="2" data-key="{{$key}}">2 - Știe, dar nu are abilitățile necesare</option>
                            <option value="3" data-key="{{$key}}">3 - Știe cum, dar nu o face ( demotivare)</option>
                            <option value="4" data-key="{{$key}}">4 - Demonstrează parțial</option>
                            <option value="5" data-key="{{$key}}">5 - Demonstrează întotdeauna</option>
                        </select>
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
                <td  class="bg-warning-subtle border-bottom-3 border-top-3 border-start-0 border-end-0 border-info text-end">
                    <span class="fw-bold">Total</span>
                </td>
                <td class="bg-warning-subtle border-bottom-3 border-top-3 border-start-0 border-end-0 border-info text-end" >

                    <span class="fw-bold" id="total" >0</span>
                </td>
            </tr>


        </table>
        <div class="conclusion">
            <h4>Concluzii / ce a fost corectat</h4>
            <ul class="pl-0">
                @php $counter = 1; @endphp
                @foreach($evaluation as $key => $criterias)
                <li class="mb-3">
                    <label for="formControlTextarea{{$counter}}" class="form-label">{{$counter++}}. {{$key}}</label>
                    <textarea class="form-control" name="{{$key}}" id="formControlTextarea{{$counter}}" rows="3"></textarea>
                </li>
                @endforeach
            </ul>
        </div>
        <div class="recommendations">
            <input type="hidden" name="result" value="" />
            <input type="hidden" name="mark" value="0" />
            <input type="hidden" name="conclusion" value="" />
            <label for="recommendationsTextarea" class="form-label recom_label">Recomendari:</label>
            <textarea class="form-control mb-3" name="recommendation" id="recommendationsTextarea" rows="3"></textarea>
            <button type="submit" class="btn btn-primary float-right">Сохранить</button>
        </div>
    </form>
@else
    <h3>Нет оценочных листов для должности <b>{{$employee->profession->name}}</b></h3>
@endif
@endsection

@push('after_scripts')

<script>
jQuery(document).ready(function($) {
const evaluationList = {!! json_encode($evaluation) !!};

function setTotalObj(evaluationList){
    let Total = {}
    let subTotal = {}
    Object.entries(evaluationList).map((el,idx,) => {
        let key1 = el[0];

        let obj = {}
        Object.values(el[1]).map((el2,idx,) => {
            let key2 = el2['criteria'];
            obj[key2] = {value:0}

        })
        subTotal[key1] = {
            total:0,
            items:obj
        };
        Total = {
            finalTotal : 0,
            items:subTotal
        }
    })

    return Total;
}

let Total = setTotalObj(evaluationList)


function countTotal(Total){
    let FinalTotal = 0;
    let FinalNotNullable = 0;

    Object.entries(Total.items).map((el,idx) => {
        el[1].total = 0
        let LocalTotal = el[1].total;
        let LocalNotNullable = 0;

        Object.entries(el[1].items).map((item,idx2) => {
            let itemValue = Number(item[1].value);
            if(itemValue > 0){
                LocalNotNullable++
            }
            LocalTotal += itemValue

            // console.log('-------------------------------------')
        })

         LocalTotal = (LocalNotNullable > 0) ?  LocalTotal/LocalNotNullable : 0;

        el[1].total = Number(LocalTotal.toFixed(2))

        if(el[1].total > 0){
            FinalNotNullable++
        }

        FinalTotal+=LocalTotal;

        // console.log('LocalNotNullable =',LocalNotNullable)
        // console.log('Total =',Total)
        // console.log('==========================================')
    })
    FinalTotal = (FinalNotNullable > 0) ?  FinalTotal/FinalNotNullable : 0;


    Total.finalTotal = Number(FinalTotal.toFixed(2));

    return Total;
}
function render_total(Total){
    Object.entries(Total.items).map((el,idx) => {

        let id = el[0]
        Object.entries(el[1].items).map((item,idx2) => {
            let name = item[0];

            let value = Total.items[id].total
            $('span[id="'+id+'"]').text(value)
        })
    })
    $('#total').text(Total.finalTotal)
    $('input[name="mark"]').val(Total.finalTotal);
}

$('select.form-select').each(function(index,element){
     $(element).on('change', function(){
         let Name = element.name;
         let Id = element.id;

         Total.items[Name].items[Id].value = $(element).find('option:selected').val();
         let res = countTotal(Total)
         render_total(res);
     })

});


$('form#skill_evaluation').on('submit', function(){
    $('input[name="result"]').val(JSON.stringify(Total));
    console.log('Total =',Total)
    let conclusion = {}
    Object.entries(evaluationList).map((el,idx,) => {
        let key1 = el[0];
        let conclusionText = $('textarea[name="'+key1+'"]').val();
        conclusion[key1] =  conclusionText ? conclusionText : ''
    });
    $('input[name="conclusion"]').val(JSON.stringify(conclusion));
        return true;
});
});
</script>
@endpush
