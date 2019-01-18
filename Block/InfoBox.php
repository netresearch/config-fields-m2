<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Netresearch\ConfigFields\Block;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\View\Element\Template;

/**
 * Class InfoBox
 *
 * Use this Block as your system.xml field frontend_model to render an Infobox in your config section.
 * Available optional parameters via your element's field_config:
 *
 * - logo            Template path to an image to display in the header of the box
 * - background      Background CSS for the box header
 * - header_block    Block class to render in the box header
 * - header_template Template to render in the box header
 * - body_block      Block class to render in the box body
 * - body_template   Template to render in the box body
 *
 * You can declare field_config entries like this in your system.xml <field> node:
 * <attribute type="logo">Vendor_ModuleName::images/logo.svg</attribute>
 *
 * @package Netresearch\ConfigFields\Block
 * @author    Max Melzer <max.melzer@netresearch.de>
 * @copyright 2018 Netresearch DTT GmbH
 * @link      http://www.netresearch.de/
 * @api
 */
class InfoBox extends Field
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var AbstractElement
     */
    private $element;

    /**
     * InfoBox constructor.
     *
     * @param Context $context
     * @param Repository $repository
     */
    public function __construct(Context $context, Repository $repository)
    {
        $this->repository = $repository;

        parent::__construct($context);
    }

    /**
     * @param AbstractElement $element
     *
     * @return string
     */
    public function render(AbstractElement $element): string
    {
        $this->element = $element;

        return $this->toHtml();
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return 'Netresearch_ConfigFields::infoBox.phtml';
    }

    /**
     * @return string
     */
    public function getLogoUrl(): string
    {
        $logoUrl = $this->element->getData('field_config', 'logo');

        return $logoUrl ? $this->repository->getUrl($logoUrl) : '';
    }

    /**
     * @return string
     */
    public function getBackgroundCss(): string
    {
        return $this->element->getData('field_config', 'background') ?? '';
    }

    /**
     * @return string
     */
    public function renderHeader(): string
    {
        $blockClass = $this->element->getData('field_config', 'header_block') ?? Template::class;
        $blockTemplate = $this->element->getData('field_config', 'header_template') ?? '';

        $block = $this->_layout->createBlock(
            $blockClass,
            'infobox_header_' . $this->element->getHtmlId(),
            ['data' => ['template' => $blockTemplate]]
        );

        return $block->toHtml();
    }

    /**
     * @return string
     */
    public function renderBody(): string
    {
        $blockClass = $this->element->getData('field_config', 'body_block') ?? Template::class;
        $blockTemplate = $this->element->getData('field_config', 'body_template') ?? '';

        $block = $this->_layout->createBlock(
            $blockClass,
            'infobox_body_' . $this->element->getHtmlId(),
            ['data' => ['template' => $blockTemplate]]
        );

        return $block->toHtml();
    }
}
