<?php

namespace Helium\Support;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EntityConfig
{
    protected const DEFAULT = [
        'table' => [
            'view' => 'helium::table'
        ],
        'form' => [
            'view' => 'helium::form'
        ]
    ];

    /**
     * Builds a table config. This will also normalise the data and fill in the blanks
     */
    public function getConfig(string $entityName) : array
    {
        $config = config('helium.entities.' . $entityName);

        if (empty($config)) {
            throw new NotFoundHttpException();
        }

        $config = array_merge_deep(self::DEFAULT, $config);
        $config['slug'] = $entityName;
        if (!isset($config['name'])) {
            $config['name'] = class_basename($config['model']);
        }

        $config = $this->normaliseTable($config);
        $config = $this->normaliseForm($config);

        return $config;
    }

    /**
     * Builds a table config. This will also normalise the data and fill in the blanks
     */
    protected function normaliseTable(array $config) : array
    {
        $config = $this->normaliseTableColumns($config);
        $config = $this->normaliseTableActions($config);

        // Fill in the title
        if (!isset($config['table']['title'])) {
            $config['table']['title'] = Str::plural(str_humanize(Str::camel($config['name'])));
        }

        return $config;
    }

    /**
     * Normalises the columns and fills in the gaps with sensible defaults
     */
    protected function normaliseTableColumns(array $config) : array
    {
        // Normalise columns
        $config['table']['columns'] = array_normalise_keys($config['table']['columns']);
        foreach ($config['table']['columns'] as &$column) {
            // Use the name as the value if not set
            if (!isset($column['value'])) {
                $column['value'] = '{entry.' . $column['name'] . '}';
            }
            // Try to build a label from the value
            if (!isset($column['label'])) {
                $column['label'] = Str::title(str_humanize($column['name']));
            }

            if (!isset($column['view'])) {
                $column['view'] = 'helium::table-cell.text';
            }
        }
        return $config;
    }

    /**
     * Normalises the actions and fills in the gaps with sensible defaults
     */
    protected function normaliseTableActions(array $config) : array
    {
        // Normalise actions
        $config['table']['actions'] = array_normalise_keys($config['table']['actions'], 'name', 'action');
        foreach ($config['table']['actions'] as &$action) {
            // Get the action from the name if not set separately
            if (!isset($action['action'])) {
                $action['action'] = $action['name'];
            }
            // Try to build a label from the name if not set
            if (!isset($action['label'])) {
                $action['label'] = Str::title(str_humanize($action['name']));
            }
            // Create a url.
            if (!isset($action['url']) && Route::has('helium.entity.' . $action['action'])) {
                $action['url'] = str_replace(
                    '%id%',
                    '{entry.id}',
                    route(
                        'helium.entity.' . $action['action'],
                        [
                            'type' => $config['slug'],
                            'id' => '%id%'
                        ]
                    )
                );
            }

            // Some preset icons
            if (!isset($action['iconClass'])) {
                switch ($action['action']) {
                    case 'edit':
                        $action['iconClass'] = 'fas fa-edit';
                        break;
                }
            }
        }

        return $config;
    }

    /**
     * Builds a table config. This will also normalise the data and fill in the blanks
     */
    protected function normaliseForm(array $config) : array
    {
        // Fill in the title
        if (!isset($config['form']['title'])) {
            $config['form']['title'] = 'Update ' . str_humanize(Str::camel($config['name']));
        }

        $config = $this->normaliseFormFields($config);

        return $config;
    }

    /**
     * Builds a table config. This will also normalise the data and fill in the blanks
     */
    protected function normaliseFormFields(array $config) : array
    {
       // Normalise actions
        $config['form']['fields'] = array_normalise_keys($config['form']['fields'], 'name', 'value');
        foreach ($config['form']['fields'] as &$field) {
            // Set the type to text by default
            if (!isset($field['label'])) {
                $field['label'] = Str::title(str_humanize($field['name']));
            }
            // Set the type to text by default
            if (!isset($field['type'])) {
                $field['type'] = 'text';
            }
            // Set the type to text by default
            if (!isset($field['id'])) {
                $field['id'] = 'name';
            }
            // Set the type to text by default
            if (!isset($field['value'])) {
                $field['value'] = $field['name'];
            }
            // Set the view based on the type
            if (!isset($field['view'])) {
                switch ($field['type']) {
                    default:
                        $field['view'] = 'helium::form-fields.input';
                }
            }
        }

        return $config;
    }
}
