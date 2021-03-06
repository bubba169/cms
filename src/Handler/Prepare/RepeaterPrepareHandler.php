<?php

namespace Helium\Handler\Prepare;

use Illuminate\Http\Request;
use Helium\Config\Form\Field\Field;

class RepeaterPrepareHandler
{
    /**
     * Calls the prepare handler on all nested fields
     */
    public function __invoke(Field $field, Request $request, array $path)
    {
        $requestData = $request->input($field->getDataPath($path), []);

        foreach ($requestData as $i => $data) {
            foreach ($field->fields as $repeaterField) {
                if (!empty($repeaterField->prepareHandler)) {
                    app()->call($repeaterField->prepareHandler, [
                        'field' => $repeaterField,
                        'request' => $request,
                        'path' => [...$path, $field->name, $i]
                    ]);
                }
            }
        }
    }
}
