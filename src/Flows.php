<?php

/**
* This file is part of Moltin PHP-SDK, a PHP package which
* provides convinient and rapid access to the API.
*
* Copyright (c) 2013 Moltin Ltd.
* http://github.com/moltin/php-sdk
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*
* @package moltin/php-sdk
* @author Jamie Holdroyd <jamie@molt.in>
* @copyright 2013 Moltin Ltd.
* @version dev
* @link http://github.com/moltin/php-sdk
*
*/

namespace Moltin\SDK;

use Moltin\SDK\Exception\InvalidFieldTypeException as InvalidFieldType;

class Flows
{
    protected $fields;
    protected $wrap;
    protected $args;

    public function __construct($fields, $wrap = false)
    {
        $this->fields = $fields;
        $this->wrap = $wrap;
    }

    public function build()
    {
        // Loop fields
        foreach ($this->fields as &$field) {
            if (!$this->_isValidType($field['type'])) {
                throw new InvalidFieldType('Field type '.$field['type'].' was not found');
            }

                // Setup args
            $this->args = array(
                'name' => $field['slug'],
                'id' => $field['slug'],
                'value' => (isset($_POST[$field['slug']]) ? $_POST[$field['slug']] : (isset($field['value']) ? $field['value'] : null)),
                'required' => ($field['required'] == 1 ? 'required' : false),
                'class' => array('form-control'),
                'data-fieldtype' => $field['type'],
            );

            // WYSIWYG argument
            if (isset($field['options']['wysiwyg']) && $field['options']['wysiwyg'] == 1) {
                $this->args['class'][] = 'wysiwyg';
            }

            // Multilingual argument
            if (isset($field['options']['multilingual']) && $field['options']['multilingual'] == 1) {
                $this->args['class'][] = 'multilingual';
            }

            // Wrap form value
            if (isset($this->wrap) && $this->wrap !== false) {
                $this->args['name'] = $this->wrap.'['.$field['slug'].']';
            }

            // Build input
            $field['input'] = $this->_getInputForField($field);
        }

        return $this->fields;
    }

    protected function typeString($a)
    {
        $this->args['type'] = 'text';

        return '<input ' . $this->_buildArgs($this->args) . ' />';
    }

    protected function typeFile($a)
    {
        $this->args['accept'] = '';
        $this->args['type']   = 'file';
        $this->args['value']  = $a['value']['value'];

        foreach ( explode(',', $a['options']['allowed']) as $option ) $this->args['accept'] .= ( strlen($this->args['accept']) > 0 ? ', ' : '' ).'.'.trim($option);

        if ( $this->args['value'] > 0 ) {
            $img = '<img src="https://'.$a['value']['data']['segments']['domain'].'/w64/h64/'.$a['value']['data']['segments']['suffix'].'" alt="'.$a['value']['value'].'" />';
        }

        return ( isset($img) ? $img : '' ).'<input ' . $this->_buildArgs($this->args) . ' />';
    }

    protected function typeDate($a)
    {
        $this->args['type'] = 'text';
        $this->args['class'][] = 'datepicker';

        return '<input ' . $this->_buildArgs($this->args) . ' />';
    }

    protected function typeEmail($a)
    {
        $this->args['type'] = 'email';

        return '<input ' . $this->_buildArgs($this->args) . ' />';
    }

    protected function typeSlug($a)
    {
        $this->args['type']        = 'text';
        $this->args['class'][]     = 'slug';
        $this->args['data-parent'] = '#'.$a['options']['parent'];

        return '<input ' . $this->_buildArgs($this->args) . ' />';
    }

    protected function typeInteger($a)
    {
        $this->args['type'] = 'number';

        return '<input ' . $this->_buildArgs($this->args) . ' />';
    }

    protected function typeDecimal($a)
    {
        $this->args['type']        = 'text';
        $this->args['class'][]     = 'decimal';
        $this->args['data-places'] = $a['options']['decimal_places'];

        return '<input ' . $this->_buildArgs($this->args) . ' />';
    }

    protected function typeChoice($a)
    {
        if (is_array($this->args['value'])) {
            $this->args['value'] = $this->args['value']['data']['key'];
        }

        $options = $this->_buildOptions($a['options']['choices'], $a['name'], $this->args['value'], $a['options']['default'], $a['required']);

        return '<select ' . $this->_buildArgs($this->args, true) . '>' . $options . '</select>';
    }

    protected function typeRelationship($a)
    {
        if (is_array($this->args['value']) && isset($this->args['value']['data']['id'])) {
            $this->args['value'] = $this->args['value']['data']['id'];
        }

        $options = $this->_buildOptions(( isset($a['available']) ? $a['available'] : null ), $a['name'], $this->args['value'], null, $a['required']);

        return '<select ' . $this->_buildArgs($this->args, true) . '>' . $options . '</select>';
    }

    protected function typeMultiple($a)
    {
        if (! isset($_POST[$this->args['name']]) && is_array($this->args['value'])) {
            $this->args['value'] = array_keys($this->args['value']['data']);
        }

        $this->args['multiple'] = 'multiple';
        $this->args['name'] .= '[]';

        return $this->typeRelationship($a);
    }

    protected function typeMoney($a)
    {
        $this->args['type']  = 'number';
        $this->args['class'][] = 'money';

        $step = ($a['options']['decimal_places'] !== 0) ? 1/(($a['options']['decimal_places']*100)/$a['options']['decimal_places']) : 1;
        $placeholder = number_format(0,$a['options']['decimal_places']);

        // step should be set depending on number of decimal places to round to for currency formatting
        return '<input min="0" placeholder="' . $placeholder . '" step="' . $step . '" ' . $this->_buildArgs($this->args).' />';
    }

    protected function typeTaxBand($a)
    {
        return $this->typeRelationship($a);
    }

    protected function typeCountry($a)
    {
        return $this->typeRelationship($a);
    }

    protected function typeCurrency($a)
    {
        return $this->typeRelationship($a);
    }

    protected function typeGateway($a)
    {
        return $this->typeRelationship($a);
    }

    protected function typeText($a)
    {
        $value = $this->args['value'];
        unset($this->args['value']);

        return '<textarea ' . $this->_buildArgs($this->args) . '>' . $value . '</textarea>';
    }

    protected function _buildArgs($args, $skipValue = false)
    {
        $string = '';
        foreach ($args as $key => $value) {
            if ($key == "value" && $value === 0) {
                $string .= $key . '="0"';
            } elseif ($key != "value" or ! $skipValue) {
                if ( ! empty($value)) {
                    if(is_array($value) && isset($value['data']['raw']['without_tax'])) {
                        $string .= $key . '="' . $value['data']['raw']['without_tax'] . '" ';
                    } else {
                        $string .= $key . '="' . ( is_array($value) ? implode(' ', $value) : $value ) . '" ';
                    }
                } elseif ($key != "required" && ! empty($value)) {
                    $string .= $key . ' ';
                }
            }
        }

        return trim($string);
    }

    protected function _buildOptions($options, $title, $value = null, $default = null, $required = false)
    {
        $string = ( ! $required ? '<option value="">Select a ' . $title . '</option>' : '' );

        if ($options !== null) {
            foreach ($options as $id => $title) {
                $string .= '<option value="' . $id . '"' . ( ( is_array($value) && in_array($id, $value) ) || ( isset($value['data']['code']) && $id == $value['data']['code'] ) || ( isset($value['data']['slug']) && $id == $value['data']['slug'] ) || $value == $id || ( $value == null && $default == $id ) ? ' selected="selected"' : '' ) . '>' . $title . '</option>';
            }
        }

        return $string;
    }

    protected function _getMethodForType($type)
    {
        return 'type'.str_replace(' ', '', ucwords(str_replace('-', ' ', $type)));
    }

    protected function _isValidType($type)
    {
        return method_exists($this, $this->_getMethodForType($type));
    }

    protected function _getInputForField($field)
    {
        $method = $this->_getMethodForType($field['type']);

        return $this->{$method}($field);
    }
}
