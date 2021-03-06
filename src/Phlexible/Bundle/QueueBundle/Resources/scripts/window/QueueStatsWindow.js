Ext.provide('Phlexible.queue.QueueStatsWindow');

Ext.require('Phlexible.queue.model.Job');
Ext.require('Ext.grid.RowExpander');

Phlexible.queue.QueueStatsWindow = Ext.extend(Ext.Window, {
    title: Phlexible.queue.Strings.jobs,
    strings: Phlexible.queue.Strings,
    width: 900,
    height: 600,
    iconCls: 'p-queue-stats-icon',
    layout: 'fit',
    constrainHeader: true,
    maximizable: true,
    modal: true,

    initComponent: function () {
        var expander = new Ext.grid.RowExpander({
            dataIndex: 'output',
            tpl: new Ext.Template(
                '<p>{output}</p>'
            )
        });

        var store = new Ext.data.JsonStore({
            url: Phlexible.Router.generate('queue_list'),
            root: 'data',
            id: 'id',
            fields: Phlexible.queue.model.Job,
            autoLoad: true,
            totalProperty: 'total'
        });

        this.items = {
            xtype: 'grid',
            border: false,
            autoExpandColumn: 2,
            store: store,
            selModel: new Ext.grid.RowSelectionModel(),
            viewConfig: {
                emptyText: this.strings.no_jobs
            },
            bbar: new Ext.PagingToolbar({
                pageSize: 25,
                store: store,
                displayInfo: true,
                displayMsg: this.strings.display_msg,
                emptyMsg: this.strings.empty_msg,
                plugins: this.filters
            }),
            columns: [
                expander,
                {
                    header: this.strings.id,
                    dataIndex: 'id',
                    width: 250,
                    hidden: true
                }, {
                    header: this.strings.command,
                    dataIndex: 'command',
                    width: 250
                }, {
                    header: this.strings.priority,
                    dataIndex: 'priority',
                    width: 50
                }, {
                    header: this.strings.status,
                    dataIndex: 'status',
                    width: 60
                }, {
                    header: this.strings.create_time,
                    dataIndex: 'create_time',
                    width: 120
                }, {
                    header: this.strings.start_time,
                    dataIndex: 'start_time',
                    width: 120
                }, {
                    header: this.strings.end_time,
                    dataIndex: 'end_time',
                    width: 120
                }
            ],
            plugins: [
                expander
            ]
        };

        this.tbar = [
            {
                text: this.strings.reload,
                iconCls: 'p-queue-reload-icon',
                handler: function () {
                    this.getComponent(0).store.reload();
                },
                scope: this
            }
        ];

        Phlexible.queue.QueueStatsWindow.superclass.initComponent.call(this);
    }

});
