<?php namespace Helium\Table;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Collection;

class Table
{
    /**
     * @var Collection
     */
    protected $columns;

    /**
     * @var array
     */
    protected $rows;

    /**
     * @var LengthAwarePaginator
     */
    protected $paginator;

    /**
     * @var string
     */
    protected $view = 'helium::table';

    /**
     * Gets the columns
     *
     * @return array
     */
    public function getColumns() : Collection
    {
        return $this->columns;
    }

    /**
     * Sets the columns
     *
     * @param array $columns
     * @return this
     */
    public function setColumns(array $columns) : self
    {
        $this->columns = collect($columns);
        return $this;
    }

    /**
     * Gets the rows
     *
     * @return array
     */
    public function getRows() : array
    {
        return $this->rows;
    }

    /**
     * Sets the rows
     *
     * @param array $rows
     * @return this
     */
    public function setRows(array $rows) : self
    {
        $this->rows = $rows;
        return $this;
    }

    /**
     * Gets the view to use to render the table
     *
     * @return string
     */
    public function getView() : string
    {
        return $this->view;
    }

    /**
     * Get the pagination
     *
     * @return LengthAwarePaginator
     */
    public function getPaginator() : LengthAwarePaginator
    {
        return $this->paginator;
    }

    /**
     * Set the paginator
     *
     * @param LengthAwarePaginator $paginator
     * @return this
     */
    public function setPaginator(LengthAwarePaginator $paginator) : self
    {
        $this->paginator = $paginator;
        return $this;
    }
}