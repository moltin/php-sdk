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

class Flows {

	protected $assignments;
	protected $wrap;
	protected $args;

	public function __construct($assignments, $wrap = false)
	{
		$this->assignments = $assignments;
	}

	public function build()
	{
		// Loop assignments
		foreach ( $this->assignments as &$assignment ) {

			// Variables
			$method = 'type'.str_replace(' ', '', ucwords(str_replace('-', ' ', $assignment['type'])));

			// Check for method
			if ( method_exists($this, $method) ) {

				// Setup args
				$this->args = array(
					'name'     => $assignment['slug'],
					'id'       => $assignment['slug'],
					'value'    => ( isset($_POST[$assignment['slug']]) ? $_POST[$assignment['slug']] : ( isset($assignment['value']) ? $assignment['value'] : null ) ),
					'required' => ( $assignment['required'] == 1 ? 'required' : false ),
					'class'    => ['form-control']
				);

				// Wrap form value
				if ( $this->wrap !== false ) { $this->args['name'] = $wrap.'['.$assignment['slug'].']'; }

				// Build input
				$assignment['input'] = $this->$method($assignment);

			// Not found
			} else {
				throw new InvalidFieldType('Field type '.$assignment['type'].' was not found');
			}
		}

		return $this->assignments;
	}

	protected function typeString($a)
	{
		$this->args['type'] = 'text';
		return '<input '.$this->_buildArgs($this->args).' />';
	}

	protected function typeEmail($a)
	{
		$this->args['type'] = 'email';
		return '<input '.$this->_buildArgs($this->args).' />';
	}

	protected function typeSlug($a)
	{
		$this->args['type']        = 'text';
		$this->args['class'][]     = 'slug';
		$this->args['data-parent'] = '#'.$a['options']['parent'];
		return '<input '.$this->_buildArgs($this->args).' />';
	}

	protected function typeInteger($a)
	{
		$this->args['type'] = 'text';
		return '<input '.$this->_buildArgs($this->args).' />';
	}

	protected function typeDecimal($a)
	{
		$this->args['type']        = 'text';
		$this->args['class'][]     = 'decimal';
		$this->args['data-places'] = $a['options']['decimal_places'];
		return '<input '.$this->_buildArgs($this->args).' />';
	}

	protected function typeChoice($a)
	{
		if ( is_array($this->args['value']) ) { $this->args['value'] = $this->args['value']['key']; }
		$options = $this->_buildOptions($a['options']['choices'], $a['name'], $this->args['value'], $a['options']['default'], $a['required']);
		return '<select '.$this->_buildArgs($this->args).'>'.$options.'</select>';
	}

	protected function typeRelationship($a)
	{
		if ( is_array($this->args['value']) && isset($this->args['value']['id'])) { $this->args['value'] = $this->args['value']['id']; }
		$options = $this->_buildOptions($a['available'], $a['name'], $this->args['value'], null, $a['required']);
		return '<select '.$this->_buildArgs($this->args).'>'.$options.'</select>';
	}

	protected function typeMultiple($a)
	{
		if ( ! isset($_POST[$this->args['name']]) && is_array($this->args['value']) ) { $this->args['value'] = array_keys($this->args['value']); }
		$this->args['multiple'] = 'multiple';
		$this->args['name']    .= '[]';
		return $this->typeRelationship($a);
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
		return '<textarea '.$this->_buildArgs($this->args).'>'.$value.'</textarea>';
	}

	protected function _buildArgs($args)
	{
		$string = '';
		foreach ( $args as $key => $value ) { if ( $value !== false ) { $string .= $key.'="'.( is_array($value) ? implode(' ', $value) : $value ).'" '; } elseif ($key != "required") { $string .= $key.' '; } }
		return trim($string);
	}

	protected function _buildOptions($options, $title, $value = null, $default = null, $required = false)
	{
		$string = ( ! $required ? '<option value="">Select a '.$title.'</option>' : '' );
		foreach ( $options as $id => $title ) { $string .= '<option value="'.$id.'"'.( ( is_array($value) && in_array($id, $value) ) || $value == $id || ( $value == null && $default == $id ) ? ' selected="selected"' : '' ).'>'.$title.'</option>'; }
		return $string;
	}

}
