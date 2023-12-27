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
    .date_title {
        font-size: 24px;
        color: #00B0E8;
        text-align: center;
    }

</style>

@section('content')
    @if($results)
            <table class="table">

                <tr>
                    <th>
                        <h3>
                            {{$employee->name}}
                            <a class="btn btn-sm btn-success" href="/admin/evaluation/{{$employee->id}}/start"><i class="la la-chalkboard"></i></a>
                        </h3>
                        <h6>{{ trans('labels.profession') }}: <b>{{$employee->profession->name}}</b></h6>
                        <h6>{{ trans('labels.director') }}: <b>{{$employee->supervisor ? $employee->supervisor->name : '-'}}</b></h6>
                        <h6>{{ trans('labels.average_score_per_month') }}: <b>{{$avg_points}}</b></h6>
                        <h6>{{ trans('labels.average_rating_per_month') }}: <b>{{$avg_mark}}</b></h6>
                    </th>
                    <th class="date">
                    @foreach($dates as $date)

                            <p>{{ trans('labels.inspection_date') }}: {{$date['date']}}</p>
                            <p>{{ trans('labels.checked') }}: {{$date['examiner']}}</p>
                            @if($date['comment'])
                                <button class="btn btn-sm btn-info show-comment" data-comment="{{$date['comment']}}">
                                    <i class="la la-comment"></i>
                                </button>
                            @endif

                    @endforeach
                    </th>
                </tr>
{{--@dump($results)--}}
                @foreach($results as  $key=>$criteria)

                    <tr>
                        <th>
                            @if(Illuminate\Support\Facades\App::getLocale() == 'en' && isset($criteria['title_en']))
                                {{$criteria['title_en']}}
                            @elseif(Illuminate\Support\Facades\App::getLocale() == 'ro' && isset($criteria['title_ro']))
                                {{$criteria['title_ro']}}
                            @else
                                {{$criteria['title']}}
                            @endif
                        </th>
                        <th></th>

                    </tr>

                    @foreach($criteria['points'] as $group => $point)
                        <tr> <td class="date_title">{{$group}}</td><td></td> </tr>


                            @foreach($point as $key => $titleArr)
                            <tr>
                            <td>

                                <ul class="legend">
                                    @if(Illuminate\Support\Facades\App::getLocale() == 'en' && isset($titleArr['legend_en']))
                                    @foreach($titleArr['legend_en'] as $title)
                                        <li>{{$title}}</li>
                                    @endforeach
                                    @elseif(Illuminate\Support\Facades\App::getLocale() == 'ro' && isset($titleArr['legend_ro']))
                                        @foreach($titleArr['legend_ro'] as $title)
                                            <li>{{$title}}</li>
                                        @endforeach
                                    @else
                                        @foreach($titleArr['legend'] as $title)
                                            <li>{{$title}}</li>
                                        @endforeach
                                    @endif
                                </ul>
                            </td>

                                    @foreach($titleArr['marks'] as $date => $mark)
                                        <td class="point">

                                           <p>{{$mark}}</p>
                                        </td>
                                    @endforeach

                            </tr>
                            @endforeach


                    @endforeach


                @endforeach

                <tr>
                    <th>{{ trans('labels.points_gained') }}:</th>
                    <th class="totals">
                    @foreach($totals['points'] as $date => $points)
                            <div>
                                <span style="margin-right: 10px">{{$date}}  - </span>
                                <span>{{$points}}</span>
                            </div>
                    @endforeach
                    </th>
                </tr>

                <tr>
                    <th colspan="1">{{ trans('labels.mark') }}:</th>
                    <th class="totals">
                    @foreach($totals['marks'] as $date => $mark)
                    <div>
                       <span style="margin-right: 10px">{{$date}}  - </span>
                       <span>{{$mark}}</span>
                    </div>
                    @endforeach
                    </th>
                </tr>

            </table>
    @else
        <h3>{{ trans('labels.no_data_for_month') }}</h3>
    @endif
@endsection

<div class="modal fade" id="commentModal" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ trans('labels.comment') }}</h5>
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
