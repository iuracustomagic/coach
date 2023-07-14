@extends(backpack_view('blank'))
<style type = "text/css">
    .own:before{
        content: "";
        width: 13px;
        border-bottom: 1px solid #ccc;
        position: absolute;
        top: 50%;
    }
    .table_container {
        position: relative;
        min-height: 300px;
    }
    .evaluation_title {
        position: absolute;
        top: 44px;
        left: 0;
        color: #366a8d;
        font-style: italic;

    }
    .table td, .table th{
        vertical-align: middle !important;
    }
    .table td table {
        width: 100%;
    }
    .toggler{
        display: block;
        width: 20px;
        height: 20px;
        border: 1px solid #ccc;
        text-align: center;
        line-height: 18px;
        /*position: absolute;*/
        /*top: calc(50% - 10px);*/
        /*bottom: 0;*/
        left: -10px;
        background-color: #fff;
        z-index: 2;
        cursor: pointer;
    }
    .toggler.revealed:before{
        content: "";
        position: absolute;
        border-left: 1px solid #ccc;
        height: 50%;
        z-index: 1;
        top: calc(50% + 10px);
    }
    .toggler.t-lvl-0,
    .toggler.t-lvl-0:before{
        border-color: #7c69ef;
    }
    .toggler.t-lvl-1{
        border-color: #00a65a;
        margin-left: 20px;
    }
    .toggler.t-lvl-1:before{
        border-color: #00a65a;
    }
    .toggler.t-lvl-2{
        border-color: #1b2a4e;
        margin-left: 40px;
    }
    .toggler.t-lvl-2:before{
        border-color: #1b2a4e;
    }
    tr.may-hide{
        position: relative;
    }
    tr.may-hide.hidden > td > table{
        display: none;
    }
    tr.may-hide > td {
        padding: 0;
    }
    table.lvl:before{
        content: "";
        position: absolute;
        height: 100%;
        z-index: 1;
    }
    table.lvl-0:before{
        border-left: 1px solid #7c69ef;
        left: 20px;
    }
    .own.o-lvl-1:before{
       border-color: #7c69ef;
       left: 20px;
    }
    table.lvl-1:before{
        border-left: 1px solid #00a65a;
        left: 40px;
    }
    .own.o-lvl-2:before{
       border-color: #00a65a;
       left: 40px;
    }
    table.lvl-2:before{
        border-left: 1px solid #1b2a4e;
        left: 60px;
    }
    .own.o-lvl-3:before{
       border-color: #1b2a4e;
       left: 60px;
    }
    td.controls{
        position: relative;
        width: 86px;
        padding: .75rem 10px;
    }
    td.index{
        width: 50px;
    }
    td.prof{
        width: 20%;
    }
    td.employee{
        width: 10%;
    }
    td.date{
        width: 120px;
        text-align: center;
    }
    td.available,
    td.passed{
        width: 110px;
        text-align: center;
    }
    .ev-list p{
        margin: 0
    }
    .ev-list button{
        float: right;
    }
    td.avg{
        width: 130px;
        text-align: center;
    }
    td.res{
        width: 90px;
        text-align: center;
    }
    td.supervisor{
        width: 10%;
    }
    .table-danger > td{
        background-color: rgba(214, 48, 49, .4) !important;
    }
    .table-warning > td{
        background-color: rgba(253, 203, 110, .4) !important;
    }
    .table-success > td{
        background-color: rgba(0, 184, 148, .4) !important;
    }
    .table-perfect{
        background-color: rgba(129, 236, 236, .4) !important;
    }
    .filter{
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /*-------------------------------*/

    .pull-left ul {
        list-style: none;
        margin: 0;
        padding-left: 0;
    }
    .pull-left a {
        text-decoration: none;
        color: #ffffff;
    }
    .pull-left li
  {
        color: #ffffff;
        background-color: #456e9a;
        border-color: #456e9a;
        display: block;
        float: left;
        position: relative;
        text-decoration: none;
        transition-duration: 0.5s;
        padding: 10px 30px;
        font-size: .75rem;
        font-weight: 400;
        line-height: 1.428571;
    }
    .pull-left li:hover
     {
        cursor: pointer;
        color: #00bb00;
    }
    .pull-left li a:hover
   {
        color: #00bb00;
    }
    .pull-left ul li ul {
        visibility: hidden;
        opacity: 0;
        min-width: 8.2rem;
        position: absolute;

        transition: all 0.5s ease;
        margin-top: 8px;
        left: 0;
        bottom: 34px;
        display: none;
    }

    .pull-left ul li:hover>ul,
    .pull-left ul li ul:hover  {
        visibility: visible;
        opacity: 1;
        display: block;
    }
    .pull-left ul li ul li
      {
        clear: both;
        width: 100%;
        color: #ffffff;
    }

    .ul-choose li.not-export-col {
        background-color: white;
        color: #0e111c;
    }
    .ul-dropdown {
        margin: 0.3125rem 1px !important;
        outline: 0;
    }
    .firstli {
        border-radius: 0.2rem;
        margin-bottom: 20px;
        margin-right: 25px;
    }
    .firstli i {
        position: relative;
        display: inline-block;
        top: 0;
        margin-top: -1.1em;
        margin-bottom: -1em;
        font-size: 0.8rem;
        vertical-align: middle;
        margin-right: 5px;
    }
    .dt-buttons {
        display: none;
    }

    /* print styles */
    @media print {
        .evaluations_container {
            display: flex;
        }
        p {
            margin-right: 10px;

        }

    }
</style>
@section('content')
    <div class="table_container">
        <h3 class="evaluation_title"></h3>
    <table class="bg-white table table-striped nowrap rounded" id="evaluationTable" >
        <thead>
            <tr>
{{--                <th class="controls"></th>--}}
                <th>№</th>
                <th>Сотрудник</th>
                <th>Должность</th>
                <th>Куратор</th>
                <th class="filter">
                    Оценочные листы
                    <form style="margin: 0">
                        <input type="date" name="from" value="{{$range['from']}}" class="date-filter">
                        <input type="date" name="to" value="{{$range['to']}}" class="date-filter">
                    </form>
                </th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $row)
                {!! $row !!}
            @endforeach
        </tbody>
    </table>
    </div>
        <div id="bottom_buttons" class="d-print-none text-center text-sm-left">
            <div class="pull-left">

                <ul class="ul-dropdown">
                    <li class="firstli">
                        <i class="la la-download"></i><a href="#">Экспорт</a>
                        <ul class="ul-export">
                            <li>Export CSV</li>
                            <li>Export Excel</li>
                            <li>Export PDF</li>
                            <li>Print</li>
                        </ul>
                    </li>
                </ul>

            </div>

        </div>
@endsection

<!-- Modal -->
<div class="modal fade" id="evaluationsModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                ...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@section('after_styles')
    <!-- DATA TABLES -->
        <link rel="stylesheet" type="text/css" href="{{ asset('packages/datatables.net-bs4/css/dataTables.bootstrap4.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('packages/datatables.net-fixedheader-bs4/css/fixedHeader.bootstrap4.min.css') }}">
        <link rel="stylesheet" type="text/css" href="{{ asset('packages/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css') }}">

        <link rel="stylesheet" href="{{ asset('packages/backpack/crud/css/crud.css').'?v='.config('backpack.base.cachebusting_string') }}">
        <link rel="stylesheet" href="{{ asset('packages/backpack/crud/css/form.css').'?v='.config('backpack.base.cachebusting_string') }}">

    <!-- CRUD LIST CONTENT - crud_list_styles stack -->
    @stack('crud_list_styles')
@endsection


@push('after_scripts')


    <!-- DataTable -->
        <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js" type="text/javascript"></script>
        <script src="https://cdn.datatables.net/1.10.22/js/dataTables.bootstrap4.min.js" type="text/javascript"></script>
        <script src="https://cdn.datatables.net/buttons/1.6.5/js/dataTables.buttons.min.js" type="text/javascript"></script>
        <script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.flash.min.js" type="text/javascript"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js" type="text/javascript"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js" type="text/javascript"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js" type="text/javascript"></script>
        <script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.html5.min.js" type="text/javascript"></script>
        <script src="https://cdn.datatables.net/buttons/1.6.5/js/buttons.print.min.js" type="text/javascript"></script>


<script>
    jQuery(document).ready(function($) {
        $(".evaluation_title").text('{!! $title !!}')
        $(".ul-export li").click(function() {
            var i = $(this).index() + 1
            var table = $('#evaluationTable').DataTable();
            if (i == 1) {
                table.button('.buttons-csv').trigger();
            } else if (i == 2) {
                table.button('.buttons-excel').trigger();
            } else if (i == 3) {
                table.button('.buttons-pdf').trigger();
            } else if (i == 4) {
                table.button('.buttons-print').trigger();
            }
        });


        $('#evaluationTable').DataTable({
            dom: "Blfrtip",
            "language": {
                "lengthMenu": "_MENU_  записей на странице",
                "info": "Показано _START_ до _END_ из _TOTAL_ совпадений",
                "search": "Поиск:",
                "paginate": {
                    "first":      "First",
                    "last":       "Last",
                    "next":       ">",
                    "previous":   "<"
                },
            },

            dropup: true,
            buttons: [
                {
                    text: 'csv',
                    extend: 'csvHtml5',
                    messageTop: '{{$title}}',
                    exportOptions: {
                        columns: ':visible:not(.not-export-col)'
                    }
                },
                {
                    text: 'excel',
                    extend: 'excelHtml5',
                    messageTop: '{{$title}}',
                    exportOptions: {
                        columns: ':visible:not(.not-export-col)'
                    }
                },
                {
                    text: 'pdf',
                    extend: 'pdfHtml5',
                    messageTop: '{{$title}}',
                    exportOptions: {
                        columns: ':visible:not(.not-export-col)'
                    }
                },
                {
                    text: 'print',
                    extend: 'print',
                    messageTop: '{{$title}}',
                    exportOptions: {
                        columns: ':visible:not(.not-export-col)'
                    }
                },
            ],
            columnDefs: [{
                orderable: false,
                targets: -1
            }]

        });

        $('.toggler').on('click', function(e){
            let $this = $(this),
                supervisor = $this.data('subordinates-to'),
                row = $('tr[data-supervisor="'+supervisor+'"]');

            if(row.hasClass('hidden')){
                row.removeClass('hidden');
                $this.addClass('revealed').html('-');
            } else {
                row.addClass('hidden');
                $this.removeClass('revealed').html('+');
            }
        });
        $('input.date-filter').on('change', function(){
            let param = $(this).attr('name'),
                value = $(this).val()
            const urlParams = new URLSearchParams(window.location.search)
            urlParams.set(param, value)
            const newUrl = `${window.location.pathname}?${urlParams.toString()}`
            window.location.href = newUrl
        })
    });
</script>
@endpush
