<?php

namespace Netresearch\ConfigFields\Block;

use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class ModalGroup
 *
 * @package Netresearch\ConfigFields\Block
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class ModalGroup extends \Magento\Backend\Block\AbstractBlock implements
    \Magento\Framework\Data\Form\Element\Renderer\RendererInterface
{
    /**
     * Render a system config fieldset (group) as a jQuery modal
     *
     * @TODO: "depends" configuration is not yet working for elements inside the modal.
     *
     * @param AbstractElement $element
     * @return string
     */
    public function render(AbstractElement $element): string
    {
        $this->setElement($element);
        $button = $this->getButtonHtml($element);

        $footer = $this->getFooterHtml($element);

        return $button . $footer;
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    private function getInputsHtml(AbstractElement $element): string
    {
        $html = '';
        $modalId = $element->getHtmlId() . '_modal';

        /*
         * Render canonical inputs as hidden inputs
         */
        foreach ($element->getElements() as $field) {
            $fieldId = $field->getHtmlId();
            $fieldName = $field->getName();
            $fieldValue = $field->getValue();
            $html .= "<span id='row_$fieldId'>
                <input type='hidden' id='$fieldId' name='$fieldName' value='$fieldValue'>
            </span>";
        }

        /*
         * Render proxy inputs for modal
         */
        $proxyHtml = '';
        foreach ($element->getElements() as $field) {
            $field->setHtmlId($field->getHtmlId() . '_proxy');
            $field->setName($field->getName() . '_proxy');
            if ($field instanceof \Magento\Framework\Data\Form\Element\Fieldset) {
                $proxyHtml .= '<tr id="row_' . $field->getHtmlId() . '">'
                    . '<td colspan="4">' . $field->toHtml() . '</td></tr>';
            } else {
                $proxyHtml .= $field->toHtml();
            }
            $field->setHtmlId(str_replace('_proxy', '', $field->getHtmlId()));
            $field->setName(str_replace('_proxy', '', $field->getName()));
        }

        $html .= "<table class='accordion'
                         style='width:100%'
                         id='${modalId}'>
                         <tbody class='config'>$proxyHtml</tbody>
        </table>";

        return $html;
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    private function getButtonHtml(AbstractElement $element): string
    {
        $html = '<table><tr><td class="label">';

        $html .= '<label for="' . $element->getHtmlId() . '">
                <span>' . $element->getData('group/label') . '</span>
            </label>
        </td>';
        $html .= '<td class="value">
            <button type="button"
                    class="button action-default"
                    id="' . $element->getHtmlId() . '_button">
                <span>' . __('Configure') . '</span>
            </button>
            ' . $this->getHeaderCommentHtml($element)
            . $this->getInputsHtml($element) . '
        </td>';

        $html .= '<td class=""></td></tr></table>';

        return $html;
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    private function getHeaderCommentHtml(AbstractElement $element): string
    {
        return $element->getComment() ? '<div class="comment">' . $element->getComment() . '</div>' : '';
    }

    /**
     * @param AbstractElement $element
     * @return string
     */
    private function getFooterHtml(AbstractElement $element): string
    {
        return $this->getModalJs($element);
    }

    /**
     * Create a modal and sync the proxy
     *
     * @param AbstractElement $element
     * @return string
     */
    private function getModalJs(AbstractElement $element): string
    {
        $buttonId = $element->getHtmlId() . '_button';
        $modalId = $element->getHtmlId() . '_modal';
        $label = $element->getData('group/label');
        $fieldIds = [];
        foreach ($element->getElements() as $field) {
            $fieldIds[] = $field->getHtmlId();
        }
        $fieldIdsJson = \json_encode($fieldIds);

        return "<script type='text/javascript'>
        window.addEventListener('DOMContentLoaded', function () {
            require(['jquery', 'Magento_Ui/js/modal/modal'], function($) {
               $('#$modalId').modal({
                    title: '$label',
                    trigger: '#$buttonId',
                    clickableOverlay: true,
                    responsive: true,
                    closed: function() {
                        $fieldIdsJson.forEach(function(id) {
                            $('#' + id)[0].value = $('#' + id + '_proxy')[0].value;
                        });
                    }
               });
            });
        });
        </script>";
    }
}
