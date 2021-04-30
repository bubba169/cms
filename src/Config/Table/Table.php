<?php

namespace Helium\Config\Table;

use Helium\Config\Action;
use Helium\Config\Button;
use Helium\Config\Entity;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Helium\Traits\HasConfig;
use Helium\Config\Table\Filter\Filter;
use Helium\Handler\DefaultListingHandler;
use Helium\Config\Table\Filter\SearchFilter;

class Table
{
    use HasConfig;

    public ?SearchFilter $search;
    public $columns = [];
    public $filters = [];
    public $actions = [];
    public $buttons = [];

    protected Entity $entity;

    /**
     * Builds a table config
     *
     * @param string|array $config
     */
    public function __construct($table, Entity $entity)
    {
        // If table is a string use it to call a class to get the initial table config
        if (is_string($table)) {
            $table = app()->call($table, ['entity' => $entity]);
        }

        // Set the current config
        $this->entity = $entity;
        $this->mergeConfig($table);

        if (!empty($table['search'])) {
            $this->search = new SearchFilter($table['search'], $entity);
        }

        $table['filters'] = array_normalise_keys(Arr::get($table, 'filters', []), 'slug', 'column');
        foreach ($table['filters'] as $filter) {
            $class = Arr::get($filter, 'field', Filter::class);
            $this->filters[$filter['slug']] = new $class($filter, $entity);
        }

        $table['columns'] = array_normalise_keys(Arr::get($table, 'columns', []), 'slug', 'value');
        foreach ($table['columns'] as $column) {
            $this->columns[$column['slug']] = new Column($column, $entity);
        }

        $table['actions'] = array_normalise_keys(Arr::get($table, 'actions', []), 'slug', 'action');
        foreach ($table['actions'] as $action) {
            $this->actions[$action['slug']] = new Action($action, $entity);
        }

        $table['buttons'] = array_normalise_keys(Arr::get($table, 'buttons', []), 'slug', 'url');
        foreach ($table['buttons'] as $button) {
            $this->buttons[$button['slug']] = new Button($button, $entity);
        }
    }

    public function getDefault(string $key)
    {
        switch ($key) {
            case 'title':
                return Str::plural(str_humanise(Str::camel($this->entity->name)));
            case 'query':
                return DefaultListingHandler::class;
            case 'view':
                return 'helium::table';
        }
    }
}