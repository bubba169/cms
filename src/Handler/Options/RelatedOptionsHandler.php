<?php

namespace Helium\Handler\Options;

use Helium\Config\Form\Field\Field;

class RelatedOptionsHandler
{
    public function __invoke(Field $field)
    {
        $results = $field->relatedModel::all();
        $options = [];

        foreach ($results as $result) {
            $options[str_resolve($field->relatedId, $result)] = str_resolve($field->relatedName, $result);
        }

        return $options;
    }
}