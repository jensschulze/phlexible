Phlexible.fields.Registry.addFactory('numberfield', function (parentConfig, item, valueStructure, element, repeatableId) {
    element.prototypes.incCount(item.dsId);

    var config = Phlexible.fields.FieldHelper.defaults(parentConfig, item, element, repeatableId);

    Ext.apply(config, {
        xtype: 'numberfield',
        allowDecimals: (item.validation.allow_decimals ? true : false),
        allowNegative: (item.validation.allow_negative ? true : false),
        minLength: (item.validation.min_length || 0),
        maxLength: (item.validation.max_length || Number.MAX_VALUE),
        minValue: (item.validation.min_value || Number.NEGATIVE_INFINITY),
        maxValue: (item.validation.max_value || Number.MAX_VALUE),
        regex: (item.validation.regexp ? new RegExp(item.validation.regexp, (item.validation.global ? 'g' : '') + (item.validation.ignore ? 'i' : '') + (item.validation.multiline ? 'm' : '')) : null),

        supportsPrefix: true,
        supportsSuffix: true,
        supportsDiff: true,
        supportsInlineDiff: true,
        supportsUnlink: true,
        supportsRepeatable: true
    });

    Ext.each(valueStructure.values, function (value) {
        if (value.dsId === item.dsId) {
            config.value = value.content;
        }
    });

    return config;
});

Phlexible.fields.FieldTypes.addField('numberfield', {
    titles: {
        de: 'Zahlenfeld',
        en: 'Numberfield'
    },
    iconCls: 'p-elementtype-field_number-icon',
    allowedIn: ['tab', 'accordion', 'group', 'referenceroot'],
    defaultValueField: 'default_value_numberfield',
    config: {
        labels: {
            field: 1,
            box: 0,
            prefix: 1,
            suffix: 1,
            help: 1
        },
        configuration: {
            sync: 1,
            width: 1,
            height: 0,
            readonly: 1,
            hide_label: 1,
            sortable: 0
        },
        values: {
            default_text: 0,
            default_number: 1,
            default_textarea: 0,
            default_date: 0,
            default_time: 0,
            default_select: 0,
            default_link: 0,
            default_checkbox: 0,
            default_table: 0,
            source: 0,
            source_values: 0,
            source_function: 0,
            source_datasource: 0,
            text: 0
        },
        validation: {
            required: 1,
            text: 1,
            numeric: 1,
            content: 0
        }
    }
});
