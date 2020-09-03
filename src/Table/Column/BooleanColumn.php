<?php namespace Helium\Table\Column;

use Helium\Table\Column\Column;
use Illuminate\Database\Eloquent\Model;

class BooleanColumn extends Column
{
    /**
     * {@inheritDoc}
     */
    public function renderValue(Model $row) : string
    {
        return $row->{$this->getName()} ? 'Yes' : 'No';
    }
}
