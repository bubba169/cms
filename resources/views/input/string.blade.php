<div class="form-group">
    @if ($field->getAttribute('type') !== 'hidden')
        <label for="{{ $field->getId() }}">{{ $field->getLabel() }}</label>
    @endif

    <input
        id="{{ $field->getId() }}"
        name="{{ $field->getName() }}"
        value="{{ old($field->getName(), $field->getAttribute('type') !== 'password' ? $field->getValue() : '') }}"

        class="{{ $field->getClasses()->join(' ') }}"

        @foreach ($field->getAttributes() as $attribute => $value)
            {{ $attribute }}="{{ $value }}"
        @endforeach
    >
</div>
