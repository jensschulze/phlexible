Phlexible.mediatemplates.TemplatesGrid = Ext.extend(Ext.grid.GridPanel, {
    title: Phlexible.mediatemplates.Strings.mediatemplates,
    strings: Phlexible.mediatemplates.Strings,
    border: true,
    autoExpandColumn: 1,

    initComponent: function() {
        this.store = new Ext.data.JsonStore({
            url: Phlexible.Router.generate('mediatemplates_templates_list'),
            root: 'templates',
            id: 'key',
            fields: ['key', 'type'],
            sortInfo: {
                field: 'key',
                direction: 'ASC'
            },
            autoLoad: true
        });

        this.columns = [{
            header: this.strings.type,
            dataIndex: 'type',
            width: 35,
            renderer: function(s) {
                return Phlexible.inlineIcon('p-mediatemplate-type_' + s + '-icon');
            }
        },{
            header: this.strings.title,
            dataIndex: 'key',
            width: 170,
            sortable: true
        }];

        this.selModel = new Ext.grid.RowSelectionModel();

        this.tbar = [{
           text: this.strings.add,
           iconCls: 'p-mediatemplate-add-icon',
           menu: [{
               text: this.strings.image,
               iconCls: 'p-mediatemplate-type_image-icon',
               handler: this.newImageTemplate,
               scope: this
           },{
               text: this.strings.video,
               iconCls: 'p-mediatemplate-type_video-icon',
               handler: this.newVideoTemplate,
               scope: this
           },{
               text: this.strings.audio,
               iconCls: 'p-mediatemplate-type_audio-icon',
               handler: this.newAudioTemplate,
               scope: this
           },{
               text: this.strings.pdf2swf,
               iconCls: 'p-mediatemplate-type_pdf-icon',
               handler: this.newPdfTemplate,
               scope: this
           }]
        }];

        this.addListener({
            rowdblclick: {
                fn: function(grid, rowIndex) {
                    var r = grid.store.getAt(rowIndex);
                    this.fireEvent('templatechange', r);
                },
                scope: this
            }
        });

        Phlexible.mediatemplates.TemplatesGrid.superclass.initComponent.call(this);
    },

    newImageTemplate: function() {
        this.newTemplate('image');
    },

    newVideoTemplate: function() {
        this.newTemplate('video');
    },

    newAudioTemplate: function() {
        this.newTemplate('audio');
    },

    newPdfTemplate: function() {
        this.newTemplate('pdf');
    },

    newTemplate: function(type){
        if(!type || (type != 'image' && type != 'video' && type != 'audio' && type != 'pdf')) {
            return;
        }

        Ext.MessageBox.prompt('_title', '_title', function(btn, key) {
            if (btn !== 'ok') {
                return;
            }

            Ext.Ajax.request({
                url: Phlexible.Router.generate('mediatemplates_templates_create'),
                params: {
                    type: type,
                    key: key
                },
                success: function(response){
                    var data = Ext.decode(response.responseText);
                    if(data.success) {
                        Phlexible.success(data.msg);

                        // store reload
                        this.store.reload({
                            callback: function(template_id) {
                                var r = this.store.getById(template_id);
                                var index = this.store.indexOf(r);
                                this.selModel.selectRange(index);
                                this.fireEvent('create', template_id, r.get('key'), r.get('type'));
                            }.createDelegate(this, [data.id])
                        });
                    } else {
                        Ext.Msg.alert('Failure', data.msg);
                    }
                },
                scope:this

             });
        }, this);
    }
});
