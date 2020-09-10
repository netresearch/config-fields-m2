<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Netresearch\ConfigFields\Block;

use Magento\Backend\Block\AbstractBlock;
use Magento\Backend\Block\Context;
use Magento\Backend\Block\Widget\Form\Element\Dependence;
use Magento\Config\Model\Config\Structure\Element\Dependency\Field;
use Magento\Config\Model\Config\Structure\Element\Dependency\Mapper;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Data\Form\Element\Fieldset;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;
use Magento\Framework\Exception\LocalizedException;

class ModalGroup extends AbstractBlock implements RendererInterface
{
    /**
     * Dependency mapper instance.
     *
     * @var Mapper
     */
    private $dependencyMapper;

    /**
     * @param Context $context
     * @param Mapper  $dependencyMapper
     * @param array   $data
     */
    public function __construct(
        Context $context,
        Mapper $dependencyMapper,
        array $data = []
    ) {
        $this->dependencyMapper = $dependencyMapper;

        parent::__construct($context, $data);
    }

    /**
     * Render a system config fieldset (group) as a jQuery modal
     *
     * @param AbstractElement $element
     *
     * @return string
     * @throws LocalizedException
     */
    public function render(AbstractElement $element): string
    {
        $this->setElement($element);

        $button = $this->getButtonHtml($element);
        $footer = $this->getFooterHtml($element);

        return $button . $footer;
    }

    /**
     * Returns the store code.
     *
     * @return string
     *
     * @see \Magento\Config\Block\System\Config\Form::getStoreCode
     */
    private function getStoreCode(): string
    {
        return $this->getRequest()->getParam('store', '');
    }

    /**
     * Returns the id of the proxy element.
     *
     * @param AbstractElement $field
     *
     * @return string
     */
    private function getProxyElementId(AbstractElement $field): string
    {
        return $field->getHtmlId() . '_proxy';
    }

    /**
     * Returns the name of the proxy element.
     *
     * @param AbstractElement $field
     *
     * @return string
     */
    private function getProxyElementName(AbstractElement $field): string
    {
        return $field->getName() . '[proxy]';
    }

    /**
     * @param AbstractElement $element
     *
     * @return string
     */
    private function getInputsHtml(AbstractElement $element): string
    {
        $html      = '<div>';
        $proxyHtml = '';
        $modalId   = $element->getHtmlId() . '_modal';

        /** @var AbstractElement $field */
        foreach ($element->getElements() as $field) {
            $fieldId    = $field->getHtmlId();
            $fieldName  = $field->getName();
            $fieldValue = $field->getValue();

            // Render canonical inputs as hidden inputs
            $html .= <<<HTML
<span id="row_{$fieldId}">
    <input type="hidden" id="{$fieldId}" name="{$fieldName}" value="{$fieldValue}">
</span>
HTML;

            $field->setHtmlId($this->getProxyElementId($field));
            $field->setName($this->getProxyElementName($field));

            // Render proxy inputs for modal
            if ($field instanceof Fieldset) {
                $proxyHtml .= <<<HTML
<tr id="row_{$field->getHtmlId()}">
    <td colspan="4">{$field->toHtml()}</td>
</tr>
HTML;
            } else {
                $proxyHtml .= $field->toHtml();
            }

            $field->setHtmlId(str_replace('_proxy', '', $field->getHtmlId()));
            $field->setName(str_replace('[proxy]', '', $field->getName()));
        }

        $html .= '</div>';
        $html .= <<<HTML
<table class="accordion" style="width: 100%" id="{$modalId}">
    <tbody class="config">{$proxyHtml}</tbody>
</table>
HTML;

        return $html;
    }

    /**
     * @param AbstractElement $element
     *
     * @return string
     */
    private function getButtonHtml(AbstractElement $element): string
    {
        $buttonLabel = __('Configure');
        $groupLabel  = __($element->getData('group/label'));

        return <<<HTML
<table>
    <tr>
        <td class="label">
            <label for="{$element->getHtmlId()}">
                <span>{$groupLabel}</span>
            </label>
        </td>
        <td class="value">
            <button type="button" class="button action-default" id="{$element->getHtmlId()}_button">
                <span>{$buttonLabel}</span>
            </button>
            {$this->getHeaderCommentHtml($element)}
            {$this->getInputsHtml($element)}
        </td>
        <td class=""></td>
    </tr>
</table>
HTML;
    }

    /**
     * @param AbstractElement $element
     *
     * @return string
     */
    private function getHeaderCommentHtml(AbstractElement $element): string
    {
        return $element->getComment() ? '<p class="note">' . __($element->getComment()) . '</p>' : '';
    }

    /**
     * @param AbstractElement $element
     *
     * @return string
     * @throws LocalizedException
     */
    private function getFooterHtml(AbstractElement $element): string
    {
        return $this->getDependencyJs($element)
            . $this->getModalJs($element);
    }

    /**
     * Retrieve field dependencies.
     *
     * @param AbstractElement $field
     * @param string          $storeCode
     *
     * @return array
     *
     * @see Slightly modified version of \Magento\Config\Model\Config\Structure\Element\Field::getDependencies
     */
    private function getDependencies(AbstractElement $field, string $storeCode): array
    {
        $fieldData = $field->getData('field_config');

        $dependencies = [];
        if (!isset($fieldData['depends']['fields'])) {
            return $dependencies;
        }

        return $this->dependencyMapper->getDependencies(
            $fieldData['depends']['fields'],
            $storeCode
        );
    }

    /**
     * Generate element name.
     *
     * @param string $elementPath
     *
     * @return string
     *
     * @see Slightly modified version of \Magento\Config\Block\System\Config\Form::_generateElementName
     */
    private function generateElementName(string $elementPath): string
    {
        $part = explode('_', $elementPath);
        array_shift($part);
        $fieldId   = array_pop($part);
        $groupName = implode('][groups][', $part);

        return 'groups[' . $groupName . '][fields][' . $fieldId . '][value]';
    }

    /**
     * Returns the dependence js block.
     *
     * @param AbstractElement $element
     *
     * @return string
     * @throws LocalizedException
     */
    private function getDependencyJs(AbstractElement $element): string
    {
        /** @var Dependence $dependenceBlock */
        $dependenceBlock = $this->getLayout()->createBlock(Dependence::class);

        /** @var AbstractElement $field */
        foreach ($element->getElements() as $field) {
            $fieldId      = $this->getProxyElementId($field);
            $fieldName    = $this->getProxyElementName($field);
            $dependencies = $this->getDependencies($field, $this->getStoreCode());

            /** @var Field $dependentField */
            foreach ($dependencies as $dependentField) {
                $dependentFieldId   = $dependentField->getId() . '_proxy';
                $dependentFieldName = $this->generateElementName($dependentField->getId());

                $dependenceBlock->addFieldMap(
                    $fieldId,
                    $fieldName
                )->addFieldMap(
                    $dependentFieldId,
                    $dependentFieldName
                )->addFieldDependence(
                    $fieldName,
                    $dependentFieldName,
                    $dependentField
                );
            }
        }

        return $dependenceBlock->toHtml();
    }

    /**
     * Create a modal and sync the proxy
     *
     * @param AbstractElement $element
     *
     * @return string
     */
    private function getModalJs(AbstractElement $element): string
    {
        $buttonId = $element->getHtmlId() . '_button';
        $modalId  = $element->getHtmlId() . '_modal';
        $label    = __($element->getData('group/label'));

        $hiddenFieldIds = [];
        foreach ($element->getElements() as $field) {
            $hiddenFieldIds[] = $field->getHtmlId();
        }

        $hiddenFieldIdsJson = \json_encode($hiddenFieldIds);

        return <<<JS
<script type="text/javascript">
    window.addEventListener("DOMContentLoaded", function () {
        require(["jquery", "Magento_Ui/js/modal/modal"], function ($) {
            let hiddenFieldIds = {$hiddenFieldIdsJson};

            $("#{$modalId}").modal({
                title: "{$label}",
                trigger: "#{$buttonId}",
                clickableOverlay: true,
                responsive: true,

                // Store selected values in hidden inputs
                closed: function() {
                    hiddenFieldIds.forEach(function(id) {
                        $("#" + id).val($("#" + id + "_proxy").val());
                    });
                }
            });
        });
    });
</script>
JS;
    }
}
