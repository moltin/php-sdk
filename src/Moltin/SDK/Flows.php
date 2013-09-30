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
	protected $args;

	public function __construct($assignments)
	{
		$this->assignments = $assignments;
	}

	public function build()
	{
		// Loop assignments
		foreach ( $this->assignments as &$assignment ) {

			// Variables
			$method = 'type'.ucfirst($assignment['type']);

			// Check for method
			if ( method_exists($this, $method) ) {

				// Setup args
				$this->args = array(
					'name'     => $assignment['slug'],
					'id'       => $assignment['slug'],
					'value'    => ( isset($_POST[$assignment['slug']]) ? $_POST[$assignment['slug']] : ( isset($assignment['value']) ? $assignment['value'] : null ) ),
					'required' => ( $assignment['required'] == 1 ? 'required' : false )
				);

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

	protected function typeSlug($a)
	{
		$this->args['type']        = 'text';
		$this->args['class']       = 'slug';
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
		$this->args['class']       = 'decimal';
		$this->args['data-places'] = $a['options']['decimal_places'];
		return '<input '.$this->_buildArgs($this->args).' />';
	}

	protected function typeChoice($a)
	{
		if ( is_array($this->args['value']) ) { $this->args['value'] = $this->args['value']['key']; }
		$options = $this->_buildOptions($a['options']['choices'], $this->args['value'], $a['options']['default']);
		return '<select '.$this->_buildArgs($this->args).'>'.$options.'</select>';
	}

	protected function typeRelationship($a)
	{
		if ( is_array($this->args['value']) ) { $this->args['value'] = $this->args['value']['id']; }
		$options = $this->_buildOptions($a['available'], $this->args['value']);
		return '<select '.$this->_buildArgs($this->args).'>'.$options.'</select>';
	}

	protected function typeText($a)
	{
		return '<textarea '.$this->_buildArgs($this->args).'>'.$this->args['value'].'</textarea>';
	}

	protected function _buildArgs($args)
	{
		$string = '';
		foreach ( $args as $key => $value ) { if ( $value !== false ) { $string .= $key.'="'.$value.'" '; } }
		return trim($string);
	}

	protected function _buildOptions($options, $value = null, $default = null )
	{
		$string = '';
		foreach ( $options as $id => $title ) { $string .= '<option value="'.$id.'"'.( $value == $id || ( $value == null && $default == $id ) ? ' selected="selected"' : '' ).'>'.$title.'</option>'; }
		return $string;
	}

}