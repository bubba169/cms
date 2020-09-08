<div class="form-group">
    @if($field->getAttribute('type') != 'hidden')
        @include('helium::input.common.label')
    @endif

    <input
        @include('helium::input.common.attributes')
        value="{{ $field->getAttribute('type') != 'password' ? $field->getValue() : '' }}"
    >
</div>