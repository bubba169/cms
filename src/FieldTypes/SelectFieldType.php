<?php namespace Helium\FieldTypes;

use Illuminate\Support\Collection;

class SelectFieldType extends FieldType
{
    protected $view = 'helium::input.select';

    /**
     * @var Collection|string
     */
    protected $options;

    /**
     * Gets the current options
     *
     * @return Collection
     */
    public function getOptions() : Collection
    {
        return $this->options;
    }

    /**
     * Sets the options
     *
     * @param array $options
     * @return self
     */
    public function setOptions(array $options) : self
    {
        $this->options = collect($options);
        return $this;
    }
}
