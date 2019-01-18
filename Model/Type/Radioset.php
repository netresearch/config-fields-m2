<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Netresearch\ConfigFields\Model\Type;

use Magento\Framework\Data\Form\Element\Radios;

/**
 * Class Radioset
 *
 * Implementation of a radio set input element that works inside the Magento system configuration.
 * Used by entering the class name into the "type" attribute of a system.xml field element.
 *
 * @package Netresearch\ConfigFields\Model\Type
 * @author    Max Melzer <max.melzer@netresearch.de>
 * @copyright 2018 Netresearch DTT GmbH
 * @link      http://www.netresearch.de/
 * @api
 */
class Radioset extends Radios
{
    /**
     * Add a display none style since the css directive that hides the original input element is missing in
     * system_config.
     *
     * @param mixed $value
     * @return string
     */
    public function getStyle($value): string
    {
        return 'display:none';
    }

    /**
     * @return string
     */
    public function getElementHtml()
    {
        $this->setData('after_element_html', $this->getSecondaryLabelHtml() . $this->getJsHtml());

        return parent::getElementHtml();
    }

    /**
     * Add a hidden input whose value is kept in sync with the checked status of the checkbox.
     *
     * @return string
     */
    private function getJsHtml()
    {
        return <<<HTML
<input type="hidden"
       id="{$this->getHtmlId()}"
       class="{$this->getData('class')}"
       name="{$this->getName()}"
       value="{$this->getValue()}"/>
<script>
    (function() {
        let radios = document.querySelectorAll("input[type='radio'][name='{$this->getName()}']");
        let hidden = document.getElementById("{$this->getId()}");

        for (let i = 0; i < radios.length; i++) {
            if (radios[i].type === "radio") {
                radios[i].name += "[pseudo]";

                // Keep the hidden input value in sync with the radio inputs. We also create a change event for the
                // hidden input because core functionality might listen for it (and the original radio inputs will not
                // report the correct ID).
                //
                // @see module-backend/view/adminhtml/templates/system/shipping/applicable_country.phtml
                radios[i].addEventListener("change", function (event) {
                    event.stopPropagation();
                    hidden.value = event.target.value;

                    let newEvent = document.createEvent("HTMLEvents");
                    newEvent.initEvent("change", false, true);
                    hidden.dispatchEvent(newEvent);
                });
            }
        }
    })();
</script>
HTML;
    }

    /**
     * @return string
     */
    private function getSecondaryLabelHtml()
    {
        $html = '<label for="%s" class="admin__field-label">%s</label>';

        return sprintf(
            $html,
            $this->getHtmlId(),
            $this->getButtonLabel()
        );
    }
}
