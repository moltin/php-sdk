<?php

namespace Moltin;

class Filter
{

    private $availableFilters = [
        'eq',
        'ne',
        'lt',
        'lte',
        'gt',
        'gte',
        'in',
        'out',
        'has',
        'like',
    ];

    private $filters = [];

    /**
     *  Set a new Filter
     *
     *  new Moltin\Filter(['eq' => ['status' => 'live']]);
     *
     *  @param array $filters a set of filters to add on construct
     *
     *  @return $this
     */
    public function __construct($filters = [])
    {
        if (!empty($filters)) {
            $this->setFilters($filters);
        }
        return $this;
    }

    /**
     *  Get the filter array
     *
     *  @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    public function setFilters($filters = [])
    {
        foreach ($filters as $operator => $rules) {
            if (!in_array($operator, $this->availableFilters) || empty($rules)) {
                continue;
            }
            foreach($rules as $attribute => $value) {
                $this->addFilter($operator, $attribute, $value);
            }
        }
        return $this;        
    }

    /**
     *  Adds a filter to the resource request
     *
     *  @param string $operator the filter operator (eq,ne etc)
     *  @param string $attribute the attribute to filter on
     *  @param string $value the value of the attribute to operate on
     *
     *  @return $this
     */
    public function addFilter($operator, $attribute, $value)
    {
        if (!empty($operator) && !empty($attribute)) {
            $this->filters[$operator][$attribute] = $value;
        }
        return $this;
    }

    /**
     *  Removes a filter operation by attribute name and operator
     *
     *  @param string $operator the filter operator (eq,ne etc)
     *  @param string $attribute the attribute to remove filter
     *
     *  @return $this
     */
    public function removeFilter($operator, $attribute)
    {
        if (isset($this->filters[$operator][$attribute])) {
            unset($this->filters[$operator][$attribute]);
        }
        return $this;
    }

    /**
     *  Convert the filter object to a string for a URL
     *
     *  @return string
     */
    public function __toString()
    {
        $set = [];
        foreach ($this->filters as $operator => $values) {
            if (($compounded = $this->compound($operator, $values))) {
                $set = array_merge($set, $compounded);
            }

        }
        return implode(':', $set);
    }

    /**
     *  Removes a filter operation by attribute name and operator
     *
     *  @param string $operator the filter operator (eq,ne etc)
     *  @param string $rules the rules for this operator
     *
     *  @return array
     */
    private function compound($operator, $rules)
    {
        $out = [];
        foreach ($rules as $attribute => $value) {
            $out[] = $operator . '(' . $attribute . ',' . $value . ')';
        }
        return $out;
    }

}
