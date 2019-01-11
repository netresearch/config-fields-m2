# Custom Config Types for Magento 2

This Magento 2 exteions offers a selection of custom "Field Types" for use in Magento 2 extensions with a system configuration section.
No more fiddly "Yes/No" dropdowns in your configuration.

## Features

### Custom field types

- Checkboxset: Functionally equivalent to native Magenoto field type `multiselect`
- Radioset: Functionally equivalent to native Magenoto field type `select`
- Checkbox: Functionally equivalent to native Magenoto field type `select` with `YesNo` source model
- Toggle: Same as "Checkbox", but with special styling

### Custom frontend model blocks

- CustomInformation: A customizable "Infobox" for your extension

## Installation via composer

Requires PHP >=7.0 and Magento >=2.2

```bash
composer require netresearch/config-types-m2:*
```

## Usage

In your Magento 2 extension's `etc/adminhtml/system.xml`, enter the class name of one of the Types at `Netresearch\ConfigTypes\Model\Type`.

For example:

```xml
<field id="logging"
       type="Netresearch\ConfigTypes\Model\Type\Checkbox">
    <label>Logging</label>
    <button_label>Record messages to Magento logs</button_label>
    <comment>You must have global logging activated for this to work.</comment>
</field>
<field id="loglevel" 
       type="Netresearch\ConfigTypes\Model\Type\Radioset">
    <label>Logging Level</label>
    <depends>
        <field id="logging">1</field>
    </depends>
    <source_model>Some\Module\Model\Config\Source\LogLevel</source_model>
    <comment>The log level Debug may result in very large log files.</comment>
</field>

```

