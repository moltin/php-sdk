<?php

namespace Moltin\SDK\Tests;

use Moltin\SDK\Flows;

class FlowsTest extends \PHPUnit_Framework_TestCase
{
    public function test_string_type()
    {
        $field = array(
            'name' => 'Title',
            'slug' => 'title',
            'type' => 'string',
            'options' => array(),
            'required' => false,
        );

        $flow = $this->newBuiltFlowTypeFromField($field);

        $this->assertEquals($field['name'], $flow['name']);
        $this->assertEquals($field['slug'], $flow['slug']);
        $this->assertEquals($field['type'], $flow['type']);
        $this->assertEquals($field['options'], $flow['options']);
        $this->assertEquals($field['required'], $flow['required']);

        $this->assertEquals('<input name="title" id="title" class="form-control" data-fieldtype="string" type="text" />', $flow['input']);
    }

    public function test_date_type()
    {
        $field = array(
            'name' => 'When',
            'slug' => 'when',
            'type' => 'date',
            'options' => array(),
            'required' => false,
        );

        $flow = $this->newBuiltFlowTypeFromField($field);

        $this->assertEquals($field['name'], $flow['name']);
        $this->assertEquals($field['slug'], $flow['slug']);
        $this->assertEquals($field['type'], $flow['type']);
        $this->assertEquals($field['options'], $flow['options']);
        $this->assertEquals($field['required'], $flow['required']);

        $this->assertEquals('<input name="when" id="when" class="form-control datepicker" data-fieldtype="date" type="text" />', $flow['input']);
    }

    public function test_email_type()
    {
        $field = array(
            'name' => 'Email',
            'slug' => 'email',
            'type' => 'email',
            'options' => array(),
            'required' => false,
        );

        $flow = $this->newBuiltFlowTypeFromField($field);

        $this->assertEquals($field['name'], $flow['name']);
        $this->assertEquals($field['slug'], $flow['slug']);
        $this->assertEquals($field['type'], $flow['type']);
        $this->assertEquals($field['options'], $flow['options']);
        $this->assertEquals($field['required'], $flow['required']);

        $this->assertEquals('<input name="email" id="email" class="form-control" data-fieldtype="email" type="email" />', $flow['input']);
    }

    public function test_encrypted_type()
    {
        $field = array(
            'name' => 'Password',
            'slug' => 'password',
            'type' => 'encrypted',
            'options' => array(),
            'required' => false,
        );

        $flow = $this->newBuiltFlowTypeFromField($field);

        $this->assertEquals($field['name'], $flow['name']);
        $this->assertEquals($field['slug'], $flow['slug']);
        $this->assertEquals($field['type'], $flow['type']);
        $this->assertEquals($field['options'], $flow['options']);
        $this->assertEquals($field['required'], $flow['required']);

        $this->assertEquals('<input name="password" id="password" class="form-control" data-fieldtype="encrypted" type="password" />', $flow['input']);
    }

    public function test_slug_type()
    {
        $field = array(
            'name' => 'Slug',
            'slug' => 'slug',
            'type' => 'slug',
            'options' => array('parent' => 'parent'),
            'required' => false,
        );

        $flow = $this->newBuiltFlowTypeFromField($field);

        $this->assertEquals($field['name'], $flow['name']);
        $this->assertEquals($field['slug'], $flow['slug']);
        $this->assertEquals($field['type'], $flow['type']);
        $this->assertEquals($field['options'], $flow['options']);
        $this->assertEquals($field['required'], $flow['required']);

        $this->assertEquals('<input name="slug" id="slug" class="form-control slug" data-fieldtype="slug" type="text" data-parent="#parent" />', $flow['input']);
    }

    public function test_integer_type()
    {
        $field = array(
            'name' => 'Age',
            'slug' => 'age',
            'type' => 'integer',
            'options' => array(),
            'required' => false,
        );

        $flow = $this->newBuiltFlowTypeFromField($field);

        $this->assertEquals($field['name'], $flow['name']);
        $this->assertEquals($field['slug'], $flow['slug']);
        $this->assertEquals($field['type'], $flow['type']);
        $this->assertEquals($field['options'], $flow['options']);
        $this->assertEquals($field['required'], $flow['required']);

        $this->assertEquals('<input name="age" id="age" class="form-control" data-fieldtype="integer" type="number" />', $flow['input']);
    }

    public function test_decimal_type()
    {
        $field = array(
            'name' => 'Weight',
            'slug' => 'weight',
            'type' => 'decimal',
            'options' => array('decimal_places' => 5),
            'required' => false,
        );

        $flow = $this->newBuiltFlowTypeFromField($field);

        $this->assertEquals($field['name'], $flow['name']);
        $this->assertEquals($field['slug'], $flow['slug']);
        $this->assertEquals($field['type'], $flow['type']);
        $this->assertEquals($field['options'], $flow['options']);
        $this->assertEquals($field['required'], $flow['required']);

        $this->assertEquals('<input name="weight" id="weight" class="form-control decimal" data-fieldtype="decimal" type="text" data-places="5" />', $flow['input']);
    }

    public function test_choice_type()
    {
        $field = array(
            'name' => 'Colour',
            'slug' => 'color',
            'type' => 'choice',
            'options' => array('choices' => array('red' => 'Red', 'green' => 'Green'), 'default' => 'red'),
            'required' => false,
        );

        $flow = $this->newBuiltFlowTypeFromField($field);

        $this->assertEquals($field['name'], $flow['name']);
        $this->assertEquals($field['slug'], $flow['slug']);
        $this->assertEquals($field['type'], $flow['type']);
        $this->assertEquals($field['options'], $flow['options']);
        $this->assertEquals($field['required'], $flow['required']);

        $this->assertEquals('<select name="color" id="color" class="form-control" data-fieldtype="choice"><option value="">Select a Colour</option><option value="red" selected="selected">Red</option><option value="green">Green</option></select>', $flow['input']);
    }

    public function test_money_type()
    {
        $field = array(
            'name' => 'Value',
            'slug' => 'value',
            'type' => 'money',
            'options' => array('decimal_places' => 2),
            'required' => false,
        );

        $flow = $this->newBuiltFlowTypeFromField($field);

        $this->assertEquals($field['name'], $flow['name']);
        $this->assertEquals($field['slug'], $flow['slug']);
        $this->assertEquals($field['type'], $flow['type']);
        $this->assertEquals($field['options'], $flow['options']);
        $this->assertEquals($field['required'], $flow['required']);

        $this->assertEquals('<input min="0" placeholder="0.00" step="0.01" name="value" id="value" class="form-control money" data-fieldtype="money" type="number" />', $flow['input']);
    }

    private function newBuiltFlowTypeFromField($field, $wrap = false)
    {
        $flows = $this->newFlowsInstance(array($field), $wrap);
        $fields = $flows->build();

        return current($fields);
    }

    private function newFlowsInstance($fields, $wrap = false)
    {
        return new Flows($fields, $wrap);
    }
}
