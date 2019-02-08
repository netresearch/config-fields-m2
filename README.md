# Custom Config Fields for Magento 2

No more fiddly "Yes/No" dropdowns in your configuration.

This Magento 2 extension offers a selection of custom "Field Types" and "Frontend Models"
for use in Magento 2 extensions with a system configuration section.

## Features

### Custom field types

- Checkboxset: Functionally equivalent to native Magento field type `multiselect`
- Radioset: Functionally equivalent to native Magento field type `select`
- Checkbox: Functionally equivalent to native Magento field type `select` with `YesNo` source model
- Toggle: Same as "Checkbox", but with special styling

### Custom frontend model blocks

- InfoBox: A customizable information section for your extension

## Installation via composer

Requires PHP >=7.0 and Magento >=2.2

```bash
composer require netresearch/config-fields-m2:*
```

## Usage

In your Magento 2 extension's `etc/adminhtml/system.xml`, enter the class name of one
of the Types at `Netresearch\ConfigFields\Model\Type`.

For example:

```xml
<field id="logging"
       type="Netresearch\ConfigFields\Model\Type\Checkbox">
    <label>Logging</label>
    <button_label>Record messages to Magento logs</button_label>
    <comment>You must have global logging activated for this to work.</comment>
</field>
<field id="loglevel" 
       type="Netresearch\ConfigFields\Model\Type\Radioset">
    <label>Logging Level</label>
    <depends>
        <field id="logging">1</field>
    </depends>
    <source_model>Some\Module\Model\Config\Source\LogLevel</source_model>
    <comment>The log level Debug may result in very large log files.</comment>
</field>
```

Custom frontend model blocks may use additional configuration values transmitted via `<attribute>` nodes:

For example:

```xml
<field id="plugin-info">
    <frontend_model>Netresearch\ConfigTypes\Block\InfoBox</frontend_model>
    <attribute type="logo">Some_Module::images/logo.svg</attribute>
    <attribute type="body_template">Some_Module::system/config/infoBoxBody.phtml</attribute>
    <attribute type="header_template">Some_Module::system/config/infoBoxHeader.phtml</attribute>
    <attribute type="view_model">Some\Module\ViewModel\Adminhtml\System\InfoBox</attribute>
    <attribute type="background">#ffcc01</attribute>
</field>
```

The available attributes are documented inside the Block source code file.

## Support & Issues

This extension is provided "as is", the author does not offer or promise any support.
However, feel free to open a GitHub issue for any problems you encounter.
