<?php
// This file is part of Stack - http://stack.maths.ed.ac.uk/
//
// Stack is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Stack is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Stack.  If not, see <http://www.gnu.org/licenses/>.

defined('MOODLE_INTERNAL') || die();

/**
 * An input which uses a list of teacher provided options to autocomplete the
 * student's input.
 *
 * @copyright  2019 University of edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class stack_autocomplete_input extends stack_input {
    

    /*
     * completeoptions is an array of the possible values the teacher suggests.
    */
    protected $completeoptions = array();

    /*
     * This holds the value of those which the teacher has indicated are correct.
     */
    protected $teacheranswervalue = '';

    /*
     * This holds a displayed form of $this->teacheranswer. We need to generate this from those
     * entries which the teacher has indicated are correct.
     */
    protected $teacheranswerdisplay = '';

    protected $extraoptions = array(
        'simp' => false,
        'rationalized' => false,
        'allowempty' => false
    );

    public function adapt_to_model_answer($teacheranswer) {

        // We need to reset the errors here, now we have a new teacher's answer.
        $this->errors = null;

        $values = stack_utils::list_to_array($teacheranswer, true);
        // TODO error check this returned lists, and create run-time error when the teacher didn't give a list.
        $this->teacheranswervalue = $values[0];
        $this->completeoptions = $values[1];

        // TODO: call the CAS and fill in the values...
        $this->teacheranswerdisplay = 'TODO';
        return;
    }

    public function render(stack_input_state $state, $fieldname, $readonly, $tavalue) {
        global $PAGE;

        if ($this->errors) {
            return $this->render_error($this->errors);
        }

        $size = $this->parameters['boxWidth'] * 0.9 + 0.1;
        $attributes = array(
            'type'  => 'text',
            'name'  => $fieldname,
            'id'    => $fieldname,
            'class' => 'autocompleteinput',
            'size'  => $this->parameters['boxWidth'] * 1.1,
            'style' => 'width: '.$size.'em',
            'autocapitalize' => 'none',
            'spellcheck'     => 'false',
        );

        $value = $this->contents_to_maxima($state->contents);
        if ($value == 'EMPTYANSWER') {
            // Active empty choices don't result in a syntax hint again (with that option set).
            $attributes['value'] = '';
        } else if ($this->is_blank_response($state->contents)) {
            $field = 'value';
            if ($this->parameters['syntaxAttribute'] == '1') {
                $field = 'placeholder';
            }
            $attributes[$field] = stack_utils::logic_nouns_sort($this->parameters['syntaxHint'], 'remove');
        } else {
            $attributes['value'] = $value;
        }

        if ($readonly) {
            $attributes['readonly'] = 'readonly';
        }
        // Put in the Javascript magic!
        $PAGE->requires->js_call_amd('qtype_stack/inputautocomplete','setAutocomplete',[$attributes['id']]);

        $attributes['data-options'] = '["'.implode($this->completeoptions, '","').'"]';
        return html_writer::empty_tag('input', $attributes);
    }

    public function add_to_moodleform_testinput(MoodleQuickForm $mform) {
        $mform->addElement('text', $this->name, $this->name, array('size' => $this->parameters['boxWidth']));
        $mform->setDefault($this->name, $this->parameters['syntaxHint']);
        $mform->setType($this->name, PARAM_RAW);
    }

    /**
     * Return the default values for the parameters.
     * Parameters are options a teacher might set.
     * @return array parameters` => default value.
     */
    public static function get_parameters_defaults() {
        return array(
            'mustVerify'         => true,
            'showValidation'     => 1,
            'boxWidth'           => 15,
            'strictSyntax'       => false,
            'insertStars'        => 0,
            'syntaxHint'         => '',
            'syntaxAttribute'    => 0,
            'forbidWords'        => '',
            'allowWords'         => '',
            'forbidFloats'       => true,
            'lowestTerms'        => true,
            'sameType'           => true,
            'options'            => '');
    }

    /**
     * Each actual extension of this base class must decide what parameter values are valid
     * @return array of parameters names.
     */
    public function internal_validate_parameter($parameter, $value) {
        $valid = true;
        switch($parameter) {
            case 'boxWidth':
                $valid = is_int($value) && $value > 0;
                break;
        }
        return $valid;
    }

    /**
     * This is used by the question to get the teacher's correct response.
     * The dropdown type needs to intercept this to filter the correct answers.
     * @param unknown_type $in
     */
    public function get_correct_response($in) {
        $this->adapt_to_model_answer($in);
        return $this->maxima_to_response_array($this->teacheranswervalue);
    }

    /**
     * @return string the teacher's answer, displayed to the student in the general feedback.
     */
    public function get_teacher_answer_display($value, $display) {
        return stack_string('teacheranswershow',
                array('value' => '<code>'.$this->teacheranswervalue.'</code>', 'display' => $this->teacheranswerdisplay));
    }
}