{{-- Text Backpack CRUD filter --}}

<li filter-name="{{ $filter->name }}"
    filter-type="{{ $filter->type }}"
    class="dropdown {{ Request::get($filter->name) ? 'active' : '' }}">
    
    <h5>{{ $filter->label }}</h5>

    <ul class="root"> 
        @if (is_array($filter->values) && count($filter->values))
            @foreach($filter->values as $key => $value)
                <li>
                    <label>
                        <input type="checkbox" name="{{ $filter->name }}" value="{{$key}}" {{ ($filter->isActive() && $filter->currentValue == $key)?'checked':'' }} >
                        {{ $value }}
                    </label>
                    
                </li>
            @endforeach
        @endif
    </ul>
    
    <input  class="form-control pull-right"
            id="text-filter-{{ Illuminate\Support\Str::slug($filter->name) }}"
            type="hidden"
            @if ($filter->currentValue)
                value="{{ $filter->currentValue }}"
            @else
                value=""
            @endif
    >
                
</li>

{{-- ########################################### --}}
{{-- Extra CSS and JS for this particular filter --}}

{{-- FILTERS EXTRA JS --}}
{{-- push things in the after_scripts section --}}

@push('crud_list_scripts')
  <!-- include select2 js-->
  <script>
    jQuery(document).ready(function($) {
    
        $('input[type="checkbox"]').on('change', function(e){
            let checked = [];

            $('input:checkbox[name="{{ $filter->name }}"]:checked').each(function(){
                checked.push($(this).val());
            });
            
            if(checked.length){
                $('#text-filter-{{ Illuminate\Support\Str::slug($filter->name) }}').val(JSON.stringify(checked)).trigger('change');
            } else {
                $('li[filter-name={{ Illuminate\Support\Str::slug($filter->name) }}]').trigger('filter:clear');
                $('#text-filter-{{ Illuminate\Support\Str::slug($filter->name) }}').val('');
                $('#text-filter-{{ Illuminate\Support\Str::slug($filter->name) }}').trigger('change');
            }
            
        });

        $('#text-filter-{{ Illuminate\Support\Str::slug($filter->name) }}').on('change', function(e) {

            var parameter = '{{ $filter->name }}';
            var value = $(this).val();

              // behaviour for ajax table
            var ajax_table = $('#crudTable').DataTable();
            var current_url = ajax_table.ajax.url();
            var new_url = addOrUpdateUriParameter(current_url, parameter, value);

            // replace the datatables ajax url with new_url and reload it
            new_url = normalizeAmpersand(new_url.toString());
            ajax_table.ajax.url(new_url).load();

            // mark this filter as active in the navbar-filters
            if (URI(new_url).hasQuery('{{ $filter->name }}', true)) {
              $('li[filter-name={{ $filter->name }}]').removeClass('active').addClass('active');
            } else {
              $('li[filter-name={{ $filter->name }}]').trigger('filter:clear');
            }
        });

        $('li[filter-name={{ Illuminate\Support\Str::slug($filter->name) }}]').on('filter:clear', function(e) {
            $('li[filter-name={{ $filter->name }}]').removeClass('active');
            $('#text-filter-{{ Illuminate\Support\Str::slug($filter->name) }}').val('');

            $('input:checkbox[name="{{ $filter->name }}"]').each(function(){
                $(this).removeAttr('checked').prop("checked", false);
            });
        });

        $('#remove_filters_button').on('click', function(){
            $('input:checkbox[name="{{ $filter->name }}"]').each(function(){
                $(this).removeAttr('checked').prop("checked", false);
            });
        })

        // datepicker clear button
        /*$(".text-filter-{{ Illuminate\Support\Str::slug($filter->name) }}-clear-button").click(function(e) {
            e.preventDefault();

            $('li[filter-name={{ Illuminate\Support\Str::slug($filter->name) }}]').trigger('filter:clear');
            $('#text-filter-{{ Illuminate\Support\Str::slug($filter->name) }}').val('');
            $('#text-filter-{{ Illuminate\Support\Str::slug($filter->name) }}').trigger('change');
        })*/
    });
  </script>
@endpush
{{-- End of Extra CSS and JS --}}
{{-- ########################################## --}}