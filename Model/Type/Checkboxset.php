<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ConfigFields\Model\Type;

use Magento\Framework\Data\Form\Element\Checkboxes;

/**
 * Class Checkbox
 *
 * Implementation of a checkbox set input element that works inside the Magento system configuration and mimics a
 * multiselect, concatenating the values of all selected options separated with a comma inside a hidden input.
 * Used by entering the class name into the "type" attribute of a system.xml field element.
 *
 * @api
 */
class Checkboxset extends Checkboxes
{
    private const PSEUDO_POSTFIX = '_hidden'; // used to create the hidden input id.

    /**
     * @return string
     */
    public function getElementHtml(): string
    {
        $this->setData('value', $this->filterUnavailableValues());
        $this->setData('after_element_html', $this->getAfterHtml());

        return '<div class="checkboxset" style="font-size: 14px">' . parent::getElementHtml() . '</div>';
    }

    /**
     * Add a hidden input whose value is kept in sync with the checked status of the checkboxes
     *
     * @return string
     */
    private function getAfterHtml(): string
    {
        $html = '<input type="hidden" id="%s" value="%s" %s/>
        <script>
            (function() {
                var checkboxes = document.querySelectorAll("[name=\'%s\']");
                var hidden = document.getElementById("%s");
                /** Make the hidden input the submitted one. **/
                hidden.name = checkboxes.item(0).name;

                for (var i = 0; i < checkboxes.length; i++) {
                    checkboxes[i].name = "";
                    var values = hidden.value.split(",");
                    if (values.indexOf(checkboxes[i].value) !== -1) {
                        checkboxes[i].checked = true;
                    }
                    checkboxes[i].disabled = hidden.disabled;
                    /** keep the hidden input value in sync with the checkboxes. **/
                    checkboxes[i].addEventListener("change", function (event) {
                        var checkbox = event.target;
                        var values = hidden.value.split(",");
                        hidden.disabled = event.target.disabled;
                        var valueAlreadyIncluded = values.indexOf(checkbox.value) !== -1; 
                        if (checkbox.checked && !valueAlreadyIncluded) {
                            values.push(checkbox.value);
                        } else if (!checkbox.checked && valueAlreadyIncluded) {
                            values.splice(values.indexOf(checkbox.value), 1)
                        }
                        hidden.value = values.filter(Boolean).join();
                    });
                };
            })();
        </script>';

        return sprintf(
            $html,
            $this->getHtmlId() . self::PSEUDO_POSTFIX,
            $this->getData('value'),
            $this->getData('disabled') ? 'disabled' : '',
            $this->getName(),
            $this->getHtmlId() . self::PSEUDO_POSTFIX
        );
    }

    /**
     * Remove previously selected values whose option is not available any more.
     *
     * @return string
     */
    private function filterUnavailableValues(): string
    {
        $value  = $this->getData('value');
        $values = $value ? explode(',', $value) : [];

        $availableValues = array_map(
            static function ($value) {
                return $value['value'];
            },
            $this->getData('values')
        );

        return implode(',', array_intersect($values, $availableValues));
    }
}
