{{-- Text Backpack CRUD filter --}}

<li filter-name="{{ $filter->name }}"
    filter-type="{{ $filter->type }}"
    class="dropdown {{ Request::get($filter->name) ? 'active' : '' }}">
    
    <ul class="root">
        @foreach (App\Models\CommodityGroup::where('depth', 1)->get() as $group)
            <li>
                <span class="toggle-nested">+</span>
                <a href="#" class="load-from-group" data-group-id="{{ $group->id }}">
                    {{ $group->title }}
                </a>
                @if(!$group->children->isEmpty())
                    @foreach ($group->children as $lvlOneChild)
                        <ul class="nested">
                            <li>
                                <span class="toggle-nested">+</span>
                                <a href="#" class="load-from-group" data-group-id="{{ $lvlOneChild->id }}">
                                    {{ $lvlOneChild->title }}
                                </a>
                                @if(!$lvlOneChild->children->isEmpty())
                                    @foreach ($lvlOneChild->children as $lvlTwoChild)
                                        <ul class="nested">
                                            <li>
                                                <a href="#" class="load-from-group" data-group-id="{{ $lvlTwoChild->id }}">
                                                    {{ $lvlTwoChild->title }}
                                                </a>
                                            </li>
                                        </ul>
                                    @endforeach
                                @endif
                            </li>
                        </ul>
                    @endforeach
                @endif
            </li>
        @endforeach
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
        $('.toggle-nested').on('click', function(e){
            e.preventDefault();
            $(this).siblings('.nested').toggle('medium');
            if(!$(this).hasClass('shown')){
                $(this).addClass('shown').html('-');
                $(this).closest('li').addClass('revealed');
            } else {
                $(this).removeClass('shown').html('+');
                $(this).closest('li').removeClass('revealed');
            }
        });

        $('.load-from-group').on('click', function(e){
            e.preventDefault();
            $('#text-filter-{{ Illuminate\Support\Str::slug($filter->name) }}').val($(this).data('group-id')).trigger('change');
            $('.load-from-group').removeClass('loaded');
            $(this).addClass('loaded');
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
      });

      // datepicker clear button
      $(".text-filter-{{ Illuminate\Support\Str::slug($filter->name) }}-clear-button").click(function(e) {
        e.preventDefault();

        $('li[filter-name={{ Illuminate\Support\Str::slug($filter->name) }}]').trigger('filter:clear');
        $('#text-filter-{{ Illuminate\Support\Str::slug($filter->name) }}').val('');
        $('#text-filter-{{ Illuminate\Support\Str::slug($filter->name) }}').trigger('change');
      })
    });
  </script>
@endpush
{{-- End of Extra CSS and JS --}}
{{-- ########################################## --}}