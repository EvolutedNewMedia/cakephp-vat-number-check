<?php
App::uses('AppHelper', 'View/Helper');
App::uses('HtmlHelper', 'View/Helper');
App::uses('FormHelper', 'View/Helper');

/**
 * VatNumberCheck Helper
 *
 * @property HtmlHelper $Html
 * @property FormHelper $Form
 */
class VatNumberCheckHelper extends AppHelper {

/**
 * An array of names of helpers to load.
 *
 * @var array
 */
	public $helpers = array('Html', 'Form');

/**
 * The number of times this helper is called
 *
 * @var int
 */
	protected $_helperCount = 0;

/**
 * The css class name to trigger `check` logic.
 *
 * @var string
 */
	protected $_inputClass = 'vat-number-check';

/**
 * Generates a vat number check form field.
 *
 *  See `FormHelper::input`.
 *
 * @param string $fieldName This should be "Modelname.fieldname"
 * @param array $options Each type of input takes different options.
 * @return string Html output for a form field
 */
	public function input($fieldName, $options = array()) {
		$this->_helperCount += 1;
		if ($this->_helperCount === 1) {
			$this->_addJs();
		}

		$options = array_merge($options, array('type' => 'text'));

		$class = $this->_inputClass;
		if (empty($options['class'])) {
			$options['class'] = $class;
		} else {
			$options['class'] = sprintf('%s %s', $options['class'], $class);
		}

		return $this->Form->input($fieldName, $options);
	}

/**
 * Adds the needed javascript to the DOM (once).
 *
 * @return void
 */
	protected function _addJs() {
		$checkUrl = $this->Html->url(array(
			'plugin' => 'vat_number_check', 'controller' => 'vat_number_checks', 'action' => 'check', 'ext' => 'json'
		));
		$checkImages = array(
			'ok' => $this->Html->url('/vat_number_check/img/ok.png'),
			'failure' => $this->Html->url('/vat_number_check/img/failure.png'),
			'serviceUnavailable' => $this->Html->url('/vat_number_check/img/service-unavailable.png'),
		);

		$script = "
			/* jshint jquery:true */

			(function($) {
				$(function () {
					var options = {
						elementSelector: '" . sprintf('input.%s', $this->_inputClass) . "',
						checkUrl: '" . $checkUrl . "',
						checkImages: " . json_encode($checkImages) . ",
					};
					var vatNumberCheck = new VatNumberCheck(options);
				});
			})(jQuery);
		";

		$this->Html->script(array(
			'VatNumberCheck.klass.min',
			'VatNumberCheck.vat_number_check'
		), array('inline' => false, 'once' => true));
		$this->Html->scriptBlock($script, array('inline' => false));
	}

}
