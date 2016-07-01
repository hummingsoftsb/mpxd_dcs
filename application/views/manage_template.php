<body>
<div class="container">
    <div><h3>jsTree Demo : Simple Example to create tree structure using jsTree,php and MySQL</h3></div>
    <div id="tree-container" style="padding:21px 60px;"></div>
</div>

<div id="mydialogTemplate" title="Project Template List" class="ui-helper-hidden">
    <p>Please choose the template from the list given below.</p>
    <select class="template" id="template">
    </select>
</div>
</body>

<!-- done by jane for managing template list hierarchy-->
<script type="text/javascript">
    $(document).ready(function(){
        //fill data to tree  with AJAX call
        var jtree=$('#tree-container').jstree({
            'core': {
                'data': {
                    'url': 'template_node?operation=get_node',
                    'data': function (node) {
                        return {'id': node.id};
                    },
                    "dataType": "json"
                }, 'check_callback': true,
                'themes': {
                    'responsive': false
                }
            },
            'plugins': ['state', 'contextmenu', 'wholerow'],
            "checkbox": {
                "three_state": false
            },
            "contextmenu":{
                "items": function($node) {
                    var tree = $("#tree-container").jstree(true);
                    /*if ($node['original']['parent_id']=='0')*/
                    if ($node['original']['parent_id']=='0') {

                    return {
                        /*if($node['original']['parent_id']!='0'){*/
                        "Create": {
                            "separator_before": false,
                            "separator_after": false,
                            "label": "Create",
                            "action": function (obj) {
                                $node = tree.create_node($node);
                                tree.edit($node);
                            }
                        },
                        "Rename": {
                            "separator_before": false,
                            "separator_after": false,
                            "label": "Rename",
                            "action": function (obj) {
                                tree.edit($node);
                            }
                        }
                    }}
                    else{
                        return {
                            "Create": {
                                "separator_before": false,
                                "separator_after": false,
                                "label": "Create",
                                "action": function (obj) {
                                    $node = tree.create_node($node);
                                    tree.edit($node);
                                }
                            },
                            /*"Rename": {
                             "separator_before": false,
                             "separator_after": false,
                             "label": "Rename",
                             "action": function (obj) {
                             tree.edit($node);
                             }
                             },*/
                            "Remove": {
                                "separator_before": false,
                                "separator_after": false,
                                "label": "Remove",
                                "action": function (obj) {
                                    tree.delete_node($node);
                                }
                            }
                        }

                    }
                }
            }
        });
        jtree.bind("loaded.jstree", function (event, data) {
            // you get two params - event & data - check the core docs for a detailed description
            $(this).jstree("open_all");
        });

        jtree.on('create_node.jstree', function (e, data) {
            data.node.text = 'New Station';
            jtree.jstree("refresh");
            $("#mydialogTemplate").dialog({modal: true, height: 300,width: 490});
            $("#template").change(function(){
                var text=$("#template option:selected").text();
                var temp_id=$("#template option:selected").val();
                $.get('template_node?operation=create_node', { 'id' : data.node.parent, 'position' : data.position, 'text' : text,'temp_id':temp_id })
                    .done(function (d) {
                        $("#mydialogTemplate").dialog( "close" );
//                        data.instance.set_id(data.node, d.id);
//                        data.instance.refresh();
                        jtree.jstree("refresh");

                    })
                    .fail(function () {
                        data.instance.refresh();
                    });
                get_template_list();
                location.reload(true);
            });
        });

        jtree.on('rename_node.jstree', function (e, data) {
            $.get('template_node?operation=rename_node', { 'id' : data.node.id, 'text' : data.text })
                .fail(function () {
                    data.instance.refresh();
                });
        });

        jtree.on('delete_node.jstree', function (e, data) {
            $.get('template_node?operation=delete_node', { 'id' : data.node.id })
                .done(function (d) {
                    if(d["status"] == "Fail"){
                        alert("Cannot delete root node of association list!");
                    }
                    jtree.jstree("refresh");
                    get_template_list();
                })
                .fail(function () {
                    data.instance.refresh();
                });

        });
        $( "#mydialogTemplate" ).on( "dialogclose", function( event, ui ) {
            jtree.jstree("refresh");
        } );
        get_template_list();
    });

    function get_template_list() {
            $.ajax({
                type:'POST',
                url: "<?php echo site_url('designtemplate/get_template_list'); ?>",
                async: false,
                dataType: "json",
                success:function (data) {
                    if(data.status=="success"){
                        $('.template').html('');
                        jQuery(".template").append('<option>Select</option>');
                        for(var j=0;j<data.template.length;j++)
                        {
                            var a = data.template[j].project_no;
                            var b = data.template[j].project_name;
                            jQuery(".template").append(jQuery('<option></option>').val(a).html(b));
                        }
                    }else{
                        console.log(data.status);
                    }
                },
                failure : function () {
                    console.log(' Ajax Failure');
                },
                complete: function () {
                    console.log("complete");
                }
            })
    }
</script>