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
 * @package Netresearch\ConfigFields\Model\Type
 * @author    Max Melzer <max.melzer@netresearch.de>
 * @copyright 2018 Netresearch DTT GmbH
 * @link      http://www.netresearch.de/
 * @api
 */
class Checkboxset extends Checkboxes
{
    const PSEUDO_POSTFIX = '_hidden'; // used to create the hidden input id.

    /**
     * @return string
     */
    public function getElementHtml(): string
    {
        $this->setData('value', $this->filterUnavailableValues());
        $this->setData('after_element_html', $this->getAfterHtml());

        return parent::getElementHtml();
    }

    /**
     * Add a hidden input whose value is kept in sync with the checked status of the checkboxes
     *
     * @return string
     */
    private function getAfterHtml(): string
    {
        $html = '<input type="hidden" id="%s" value="%s"/>
        <script>
            (function() {
                let checkboxes = document.querySelectorAll("[name=\'%s\']");
                let hidden = document.getElementById("%s");
                /** Make the hidden input the submitted one. **/
                hidden.name = checkboxes.item(0).name;

                for (let i = 0; i < checkboxes.length; i++) {
                    checkboxes[i].name = "";
                    let values = hidden.value.split(",");
                    if (values.indexOf(checkboxes[i].value) !== -1) {
                        checkboxes[i].checked = true;
                    }
                    /** keep the hidden input value in sync with the checkboxes. **/
                    checkboxes[i].addEventListener("change", function (event) {
                        let checkbox = event.target;
                        let values = hidden.value.split(",");
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
            function ($value) {
                return $value['value'];
            },
            $this->getData('values')
        );

        return implode(',', array_intersect($values, $availableValues));
    }
}
