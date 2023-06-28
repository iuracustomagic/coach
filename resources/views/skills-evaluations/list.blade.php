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

    ul li {
        list-style: none;

    }

</style>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

@section('content')
    @if($marks && $values )
        <table class="table">
            <!-- header -->
            <tr>
                <th>
                    <h3>
                        {{$employee->name}}
                        <a class="btn btn-sm btn-success" href="/admin/skills-evaluation/{{$employee->id}}/start"><i
                                class="la la-area-chart"></i></a>
                    </h3>
                    <h6>Должность: <b>{{$employee->profession->name}}</b></h6>
                    <h6>Подразделение: <b>{{$employee->divisions ? $employee->divisions[0]['name'] : '-'}}</b></h6>
                </th>
                @foreach($dates as $date)
                    <th class="date">
                        <p>Дата <br> {{$date['date']}}</p>
                        <p>Проверял:<br> {{$date['examiner']}}</p>

                    </th>
                @endforeach
            </tr>
            <!-- /header -->
        @php $counter = 1; @endphp
        <!-- questions -->
            @foreach($title_questions as $title => $question_arr)

                <tr>
                    <th colspan="{{count($title_questions)+1}}" class="bg-info-subtle">
                        <h5 class="m-0 fw-bold">{{$counter++}}. {{$title}}</h5>
                    </th>
                </tr>
                @foreach($question_arr as $k => $question)

                    <tr>
                        <td class="bg-body-tertiary">
                            <span>{{$k+1}}.{{$question}}</span>
                        </td>
                        @foreach($values as $eval_nr => $val)
                            @if(isset($values[$eval_nr][$title][$k]))
                            <td class="bg-body-tertiary text-end">
                                <span>{{$values[$eval_nr][$title][$k]}}</span>
                            </td>
                            @else
                                <td class="bg-body-tertiary text-end">
                                    <span>-</span>
                                </td>
                            @endif
                        @endforeach


                    </tr>
                @endforeach
                <tr>
                    <td class="bg-success-subtle border-bottom-3 border-top-3 border-start-0 border-end-0 border-info text-end">
                        <span class="fw-bold">Sub total</span>
                    </td>
                    @foreach($subtotals as $eval_nr => $val)
                        @if(isset($subtotals[$eval_nr][$title]))
                        <td class="bg-success-subtle border-bottom-3 border-top-3 border-start-0 border-end-0 border-info text-end">
                            <span>{{$subtotals[$eval_nr][$title]}}</span>
                        </td>
                        @else
                            <td class="bg-success-subtle border-bottom-3 border-top-3 border-start-0 border-end-0 border-info text-end">
                                <span>-</span>
                            </td>
                        @endif
                    @endforeach
                </tr>

            @endforeach
            <tr>
                <td class="bg-warning-subtle border-bottom-3 border-top-3 border-start-0 border-end-0 border-info text-end">
                    <span class="fw-bold">Total</span>
                </td>
                @foreach( $marks as $key => $mark)
                    <td class="bg-warning-subtle border-bottom-3 border-top-3 border-start-0 border-end-0 border-info text-end">
                        <span class="fw-bold" id="total">{{$mark}}</span>
                    </td>
                @endforeach

            </tr>
        </table>
        <div class="conclusion">
            <h4 class="fw-bold">Concluzii / ce a fost corectat</h4>
            <div class="d-flex justify-content-between">
                @foreach($dates as $date)
                    <ul class="pl-0 w-25">
                        @php $counter = 1; @endphp

                        @foreach($date['conclusion'] as $key => $conclusion)
                            <li class="mb-3">
                                <p class="fw-bold fs-5 ">{{$counter++}}. {{$key}}</p>
                                <p>{{$conclusion}}</p>
                            </li>
                        @endforeach

                    </ul>
                @endforeach
            </div>
        </div>
        <div class="recommendations">
            <p class="fw-bold fs-5 ">Recomendari:</p>
            <div class="d-flex justify-content-between">
                @foreach($dates as $date)
                    <p class="w-25">{{$date['recommendation']}}</p>
                @endforeach
            </div>
        </div>

    @else
        <h3>Нет данных за текущий месяц</h3>
    @endif
@endsection



@push('after_scripts')
<script>

</script>
@endpush
