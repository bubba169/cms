<?php

namespace Helium\Support;

use Helium\Form\FormHandler;
use Helium\Form\RelatedOptionsHandler;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class EntityConfig
{
    protected const DEFAULT = [
        'table' => [
            'view' => 'helium::table',
            'columns' => [],
            'actions' => [],
        ],
        'form' => [
            'view' => 'helium::form',
            'fields' => [],
            'actions' => [],
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
        // Fill in the title
        if (!isset($config['table']['title'])) {
            $config['table']['title'] = Str::plural(str_humanize(Str::camel($config['name'])));
        }

        $config = $this->normaliseTableColumns($config);
        $config['table']['actions'] = $this->normaliseActions($config['table']['actions'], $config);

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
     * Builds a table config. This will also normalise the data and fill in the blanks
     */
    protected function normaliseForm(array $config) : array
    {
        // Fill in the title
        if (!isset($config['form']['title'])) {
            $config['form']['title'] = 'Update ' . str_humanize(Str::camel($config['name']));
        }

        $config = $this->normaliseFormFields($config);
        $config['form']['actions'] = $this->normaliseActions($config['form']['actions'], $config);

        return $config;
    }

    /**
     * Builds a table config. This will also normalise the data and fill in the blanks
     */
    protected function normaliseFormFields(array $config) : array
    {
       // Normalise actions
        $config['form']['fields'] = array_normalise_keys($config['form']['fields'], 'name', 'type');
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
                $field['id'] = $field['name'];
            }
            // Set the type to text by default
            if (!isset($field['column'])) {
                $field['column'] = $field['name'];
            }
            // Set the type to text by default
            if (!isset($field['value'])) {
                $field['value'] = '{entry.' . $field['column'] . '}';
            }
            // Set the view based on the type
            if (!isset($field['view'])) {
                switch ($field['type']) {
                    case 'select':
                    case 'belongsTo':
                        $field['view'] = 'helium::form-fields.select';
                        break;
                    case 'belongsToMany':
                    case 'multicheck':
                        $field['view'] = 'helium::form-fields.multicheck';
                        break;
                    case 'radio':
                        $field['view'] = 'helium::form-fields.radios';
                        break;
                    case 'checkbox':
                        $field['view'] = 'helium::form-fields.checkbox';
                        break;
                    case 'textarea':
                        $field['view'] = 'helium::form-fields.textarea';
                        break;
                    case 'datetime':
                        $field['view'] = 'helium::form-fields.datetime';
                        break;
                    case 'password':
                        $field['view'] = 'helium::form-fields.password';
                        break;
                    default:
                        $field['view'] = 'helium::form-fields.input';
                }
            }

            if (in_array($field['type'], ['belongsTo', 'belongsToMany'])) {
                if (!isset($field['options'])) {
                    $field['options'] = RelatedOptionsHandler::class;
                }
                if (!isset($field['related_id'])) {
                    $field['related_id'] = 'id';
                }
                if (!isset($field['relationship'])) {
                    $field['relationship'] = $field['name'];
                }
            }

            if (
                isset($field['options']) &&
                is_string($field['options']) &&
                strpos($field['options'], '@') === false
            ) {
                $field['options'] .= '@handle';
            }
        }

        return $config;
    }

    /**
     * Normalises the actions and fills in the gaps with sensible defaults
     */
    protected function normaliseActions(array $config, array $mainConfig) : array
    {
        // Normalise actions
        $config = array_normalise_keys($config, 'name', 'action');
        foreach ($config as &$action) {
            // Get the action from the name if not set separately
            if (!isset($action['action'])) {
                $action['action'] = $action['name'];
            }
            // Try to build a label from the name if not set
            if (!isset($action['label'])) {
                $action['label'] = Str::title(str_humanize($action['name']));
            }

            // Default save to a submit type, others will not
            if (!isset($action['submit'])) {
                $action['submit'] = ($action['action'] == 'save');
            }

            // Default save to a submit type, others will not
            if (!isset($action['view'])) {
                $action['view'] = 'helium::partials.action-button';
            }

            // Default save to a submit type, others will not
            if (!isset($action['colour'])) {
                $action['colour'] = 'gray-700';
            }

            // Default save to a submit type, others will not
            if (!isset($action['hoverColour'])) {
                $action['hoverColour'] = preg_replace_callback(
                    '/(\w+)-(\d+)/',
                    function ($matches) {
                        return $matches[1] . '-' . ($matches[2] + 100);
                    },
                    $action['colour']
                );
            }

            // Create a url.
            if (!$action['submit'] && !isset($action['url']) && Route::has('helium.entity.' . $action['action'])) {
                $action['url'] = str_replace(
                    '%id%',
                    '{entry.id}',
                    route(
                        'helium.entity.' . $action['action'],
                        [
                            'type' => $mainConfig['slug'],
                            'id' => '%id%'
                        ]
                    )
                );
            }

            // Some preset icons
            if (!isset($action['iconClass'])) {
                switch ($action['action']) {
                    case 'save':
                        $action['iconClass'] = 'fas fa-save';
                        break;
                    case 'edit':
                        $action['iconClass'] = 'fas fa-edit';
                        break;
                }
            }

            // Some preset handlers for form submitting
            if ($action['submit'] && !isset($action['handler'])) {
                switch ($action['action']) {
                    case 'save':
                        $action['handler'] = FormHandler::class;
                        break;
                }
            }

            // If a class name is given but no function call handle
            if (
                isset($action['handler']) &&
                is_string($action['handler']) &&
                strpos($action['handler'], '@') === false
            ) {
                $action['handler'] .= '@handle';
            }
        }

        return $config;
    }
}
