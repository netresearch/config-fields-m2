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
use Netresearch\ConfigFields\Factory\ViewModelFactory;

/**
 * Class InfoBox
 *
 * Use this Block as your system.xml field frontend_model to render an Infobox in your config section.
 * Available optional parameters via your element's field_config:
 *
 * - logo            Template path to an image to display in the header of the box
 * - css_class       Additional CSS class for the box
 * - header_template Template to render in the box header
 * - body_template   Template to render in the box body
 * - view_model      View Model class available in the box header and body
 *
 * You can declare field_config entries like this in your system.xml <field> node:
 * <attribute type="logo">Vendor_ModuleName::images/logo.svg</attribute>
 *
 * @api
 */
class InfoBox extends Field
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var ViewModelFactory
     */
    private $viewModelFactory;

    /**
     * @var AbstractElement
     */
    private $element;

    /**
     * InfoBox constructor.
     *
     * @param Context $context
     * @param Repository $repository
     * @param ViewModelFactory $viewModelFactory
     */
    public function __construct(Context $context, Repository $repository, ViewModelFactory $viewModelFactory)
    {
        $this->viewModelFactory = $viewModelFactory;
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
    public function getCssClass(): string
    {
        return $this->element->getData('field_config', 'css_class') ?? '';
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
    public function renderHeader(): string
    {
        $viewModel = $this->element->getData('field_config', 'view_model');
        $template = $this->element->getData('field_config', 'header_template');

        $block = $this->_layout->createBlock(
            Template::class,
            'infobox_header_' . $this->element->getHtmlId(),
            [
                'data' => [
                    'template' => $template,
                    'view_model' => $viewModel ? $this->viewModelFactory->create($viewModel) : null
                ],
            ]
        );

        return $block->toHtml();
    }

    /**
     * @return string
     */
    public function renderBody(): string
    {
        $viewModel = $this->element->getData('field_config', 'view_model');
        $template = $this->element->getData('field_config', 'body_template');

        $block = $this->_layout->createBlock(
            Template::class,
            'infobox_body_' . $this->element->getHtmlId(),
            [
                'data' => [
                    'template' => $template,
                    'view_model' => $viewModel ? $this->viewModelFactory->create($viewModel) : null
                ],
            ]
        );

        return $block->toHtml();
    }
}
