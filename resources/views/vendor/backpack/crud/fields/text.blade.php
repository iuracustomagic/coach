<!-- text input -->
@include('crud::fields.inc.wrapper_start')
    <label>{!! $field['label'] !!}</label>
    @include('crud::fields.inc.translatable_icon')

    @if(isset($field['prefix']) || isset($field['suffix'])) <div class="input-group"> @endif
        @if(isset($field['prefix'])) <div class="input-group-prepend"><span class="input-group-text">{!! $field['prefix'] !!}</span></div> @endif
        <input
            type="text"
            name="{{ $field['name'] }}"
            value="{{ old(square_brackets_to_dots($field['name'])) ?? $field['value'] ?? $field['default'] ?? '' }}"
            @include('crud::fields.inc.attributes')
        >
        @if(isset($field['suffix'])) <div class="input-group-append"><span class="input-group-text">{!! $field['suffix'] !!}</span></div> @endif
    @if(isset($field['prefix']) || isset($field['suffix'])) </div> @endif

    {{-- HINT --}}
    @if (isset($field['hint']))
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
@include('crud::fields.inc.wrapper_end')

{{-- If a field type is shown multiple times on a form, the CSS and JS will only be loaded once --}}
@if ($crud->fieldTypeNotLoaded($field))
    {{-- FIELD JS - will be loaded in the after_scripts section --}}
    @push('crud_fields_scripts')
        <script type="text/javascript">
            $.fn.inputFilter = function(callback, errMsg) {
                return this.on("input keydown keyup mousedown mouseup select contextmenu drop focusout", function(e) {
                    if (callback(this.value)) {
                        // Accepted value
                        if (["keydown","mousedown","focusout"].indexOf(e.type) >= 0){
                            $(this).removeClass("input-error");
                            this.setCustomValidity("");
                        }
                        this.oldValue = this.value;
                        this.oldSelectionStart = this.selectionStart;
                        this.oldSelectionEnd = this.selectionEnd;
                    } else if (this.hasOwnProperty("oldValue")) {
                        // Rejected value - restore the previous one
                        $(this).addClass("input-error");
                        this.setCustomValidity(errMsg);
                        this.reportValidity();
                        this.value = this.oldValue;
                        this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
                    } else {
                        // Rejected value - nothing to restore
                        this.value = "";
                    }
                });
            }

            $('input.only-digits').inputFilter(function(value) {
                return /^\d*$/.test(value);    // Allow digits only, using a RegExp
            },"Только цифры");

            $('input.only-latin-chars').inputFilter(function(value) {
                return /^[a-zA-Z\s]*$/.test(value);    // Allow digits only, using a RegExp
            },"Только буквы латинского алфавита и пробел");

            $('input[name="password_confirmation"]').on("input keydown keyup mousedown mouseup select contextmenu drop focusout", function(e) {
                if($(this).val() != $('input[name="password"]').val()){
                    $(this).prev('label').html('<span class="badge badge-danger">Пароли не совпадают</span>')
                } else {
                    $(this).prev('label').html('<span class="badge badge-success">Пароли совпадают</span>')
                }
            })

        </script>
    @endpush
@endif
{{-- End of Extra CSS and JS --}}