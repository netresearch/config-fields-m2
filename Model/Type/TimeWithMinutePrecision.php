<?php

/**
 * See LICENSE.md for license details.
 */

namespace Netresearch\ConfigFields\Model\Type;

use Magento\Framework\Data\Form\Element\Time;

/**
 * Class TimeWithMinutePrecision
 *
 * Use this to configure a time value where you don't need second precision.
 * TimeWithMinutePrecision is data compatible to the 'time' element type
 * but will always store '00' as seconds value.
 *
 * @api
 */
class TimeWithMinutePrecision extends Time
{
    /**
     * This is largely copied from the parent class. The third <select> element is replaced with a hidden input with
     * fixed value '00'.
     *
     * @return string
     */
    public function getElementHtml(): string
    {
        $this->addClass('select admin__control-select');

        $valueHrs = 0;
        $valueMin = 0;

        $value = $this->getData('value');
        if ($value) {
            $values = explode(',', $value);
            if (\is_array($values) && count($values) === 3) {
                $valueHrs = (int)$values[0];
                $valueMin = (int)$values[1];
            }
        }

        $html = '<input type="hidden" id="' . $this->getHtmlId() . '" ' . $this->_getUiId() . '/>';
        $html .= '<select name="' . $this->getName() . '" style="width:80px" '
            . $this->serialize($this->getHtmlAttributes())
            . $this->_getUiId('hour') . '>' . "\n";
        for ($i = 0; $i < 24; $i++) {
            $hour = str_pad($i, 2, '0', STR_PAD_LEFT);
            $html .= '<option value="' . $hour . '" ' . ($valueHrs ===
                $i ? 'selected="selected"' : '') . '>' . $hour . '</option>';
        }
        $html .= '</select>' . "\n";

        $html .= ':&nbsp;<select name="' . $this->getName() . '" style="width:80px" '
            . $this->serialize($this->getHtmlAttributes())
            . $this->_getUiId('minute') . '>' . "\n";
        for ($i = 0; $i < 60; $i++) {
            $hour = str_pad($i, 2, '0', STR_PAD_LEFT);
            $html .= '<option value="' . $hour . '" ' . ($valueMin ===
                $i ? 'selected="selected"' : '') . '>' . $hour . '</option>';
        }
        $html .= '</select>';

        $html .= '<input type="hidden"
                         name="' . $this->getName() . '"
                         value="00"' .
                         $this->_getUiId('second') . '>';

        $html .= $this->getAfterElementHtml();

        return $html;
    }
}
