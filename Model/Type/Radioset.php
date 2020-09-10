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
 * @api
 */
class Radioset extends Radios
{
    /**
     * Add a display none style since the css directive that hides the original input element is missing in
     * system_config.
     *
     * @return string
     */
    public function getStyle(): string
    {
        return 'display:none';
    }

    /**
     * @return string
     */
    public function getElementHtml()
    {
        $this->setData('after_element_html', $this->getSecondaryLabelHtml() . $this->getJsHtml());

        return '<div class="radioset" style="font-size: 14px">' . parent::getElementHtml() . '</div>';
    }

    /**
     * Add a hidden input whose value is kept in sync with the checked status of the checkbox.
     *
     * @return string
     */
    private function getJsHtml()
    {
        $disabledAttr = $this->getData('disabled') ? 'disabled' : '';

        return <<<HTML
<input type="hidden"
       id="{$this->getHtmlId()}"
       class="{$this->getData('class')}"
       name="{$this->getName()}"
       {$disabledAttr}
       value="{$this->getValue()}"/>
<script>
    (function() {
        var radios = document.querySelectorAll("input[type='radio'][name='{$this->getName()}']");
        var hidden = document.getElementById("{$this->getHtmlId()}");

        for (var i = 0; i < radios.length; i++) {
            if (radios[i].type === "radio") {
                radios[i].name += "[pseudo]";

                radios[i].disabled = hidden.disabled;

                // Keep the hidden input value in sync with the radio inputs. We also create a change event for the
                // hidden input because core functionality might listen for it (and the original radio inputs will not
                // report the correct ID).
                //
                // @see module-backend/view/adminhtml/templates/system/shipping/applicable_country.phtml
                radios[i].addEventListener("change", function (event) {
                    event.stopPropagation();
                    hidden.value = event.target.value;
                    hidden.disabled = event.target.disabled;

                    var newEvent = document.createEvent("HTMLEvents");
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
