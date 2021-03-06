<?php

namespace Helium\Handler\Value;

use Helium\Config\Form\Field\Field;
use Illuminate\Database\Eloquent\Model;

class EntryValueHandler
{
    // Fetches the value from the entry
    public function __invoke(?Model $source = null, Field $field) : ?string
    {
        return $source->{$field->column} ?? $field->defaultValue;
    }
}
