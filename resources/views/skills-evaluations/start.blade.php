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
                    <p>{{trans('labels.date')}}</p>
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
                    @php
                         $locale= Illuminate\Support\Facades\App::getLocale();

                         if($locale == 'ru'){
                             $name = $criteria['criteria'];
                         }elseif($locale == 'ro') {
                             if(isset( $criteria['criteria_ro'])) {
                                 $name = $criteria['criteria_ro'];
                             } else $name = $criteria['criteria'];

                         } else {
                             if(isset($criteria['criteria_en'])) {
                                  $name = $criteria['criteria_en'];
                             }else $name = $criteria['criteria'];

                         };
                    @endphp
                <tr>
                    <td class="bg-body-tertiary" >
                        <span>{{$name}}</span>

                    </td>
                    <td class="w-25 bg-body-tertiary">
                        <div class="input-group ">
                        <select class="form-select" id="{{$name}}" name="{{$key}}" data-key="{{$key}}" >
                            <option value="0" data-key="{{$key}}">{{trans('labels.grade')}}</option>
                            <option value="1" data-key="{{$key}}">1 - {{trans('labels.grade_1')}}</option>
                            <option value="2" data-key="{{$key}}">2 - {{trans('labels.grade_2')}}</option>
                            <option value="3" data-key="{{$key}}">3 - {{trans('labels.grade_3')}}</option>
                            <option value="4" data-key="{{$key}}">4 - {{trans('labels.grade_4')}}</option>
                            <option value="5" data-key="{{$key}}">5 - {{trans('labels.grade_5')}}</option>
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
            <h4>{{trans('labels.conclusions')}}</h4>
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
            <input type="hidden" name="result_ro" value="" />
            <input type="hidden" name="result_en" value="" />
            <input type="hidden" name="mark" value="0" />
            <input type="hidden" name="conclusion" value="" />
            <label for="recommendationsTextarea" class="form-label recom_label">{{trans('labels.recommendations')}}:</label>
            <textarea class="form-control mb-3" name="recommendation" id="recommendationsTextarea" rows="3"></textarea>
            <button type="submit" class="btn btn-primary float-right">{{trans('labels.save')}}</button>
        </div>
    </form>
@else
    <h3>Нет оценочных листов для должности <b>{{$employee->profession->name}}</b></h3>
@endif
@endsection

@push('after_scripts')

<script>
jQuery(document).ready(function($) {
const evaluationList = {!! json_encode($evaluation_ru) !!};
const evaluationListRo = {!! json_encode($evaluation_ro) !!};
const evaluationListEn = {!! json_encode($evaluation_en) !!};
const lang = '{{\Illuminate\Support\Facades\App::getLocale()}}';


function setTotalObj(evaluationList, lang){
    let Total = {}
    let subTotal = {}
    Object.entries(evaluationList).map((el,idx,) => {
        let key1 = el[0];

        let obj = {}
        let key2 =null
        Object.values(el[1]).map((el2,idx,) => {
            if(lang === 'ru') {
                key2 = el2['criteria'];
            } else if(lang === 'ro') {
                if(el2['criteria_ro']) {
                    key2 = el2['criteria_ro'];
                } else key2 = el2['criteria'];
                }
                else if(lang === 'en') {
                    if(el2['criteria_en']) {
                        key2 = el2['criteria_en'];
                    } else key2 = el2['criteria'];
            }

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

let Total = setTotalObj(evaluationList, 'ru')
let TotalRo = setTotalObj(evaluationListRo, 'ro')
let TotalEn = setTotalObj(evaluationListEn, 'en')

    console.log(Total)
    console.log(TotalRo)
    console.log(TotalEn)
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

         console.log(Name)


         let res = null;
         if(lang === 'ru') {
             Total.items[Name].items[Id].value = $(element).find('option:selected').val();
             const key =Object.keys(Total.items[Name])
             console.log(key)
             res = countTotal(Total)
         } else if(lang === 'ro') {
             TotalRo.items[Name].items[Id].value = $(element).find('option:selected').val();
            res = countTotal(TotalRo)
             render_total(res);
         } else {
             TotalEn.items[Name].items[Id].value = $(element).find('option:selected').val();
             res = countTotal(TotalEn)
         }
             render_total(res);
     })

});


$('form#skill_evaluation').on('submit', function(){
    $('input[name="result"]').val(JSON.stringify(Total));
    $('input[name="result_ro"]').val(JSON.stringify(TotalRo));
    $('input[name="result_en"]').val(JSON.stringify(TotalEn));
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
