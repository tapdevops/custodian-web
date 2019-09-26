<?php
$this->headScript()->appendFile('js/jquery.dataTables.min.js');
$this->headLink()->appendStylesheet('css/dataTables.css'); ?>
<table border="0" cellpadding="0" cellspacing="0" class="display" id="table1">
    <thead>
        <tr>
            <?php foreach ($this->columns['headers'] as $header) {
                echo "<th>$header</th>\n";
            } ?>
        </tr>
        <tr>
            <td class="ui-state-default">&nbsp;</td>
            <?php for ($i=1;$i<count($this->columns['headers']);$i++) { ?>
            <td class="ui-state-default">
                <input type="text" name="search_<?php echo $i; ?>" id="search_<?php echo $i; ?>" value=""
                 class="dataTables_search" />
            </td>
            <?php } ?>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="<?php echo count($this->columns['headers']); ?>" class="dataTables_empty">
                Loading...
            </td>
        </tr>
    </tbody>
</table>
<script type="text/javascript">
var oTable;
$(document).ready(function() {
    oTable = $("#table1").dataTable({
        "bJQueryUI": true,
        "bProcessing": true,
        "bServerSide": true,
        "sPaginationType": "full_numbers",
        "bLengthChange": true,
        "iDisplayLength": <?php echo (isset($this->iDisplayLength) ? $this->iDisplayLength : '50'); ?>,
        "aaSorting": [
            <?php
            $sort = array();
            foreach ($this->columns['sorts'] as $key => $val) {
                foreach ($this->columns['headers'] as $key2 => $val2) {
                    if ($key == $val2) {
                        $sort[] = "[ $key2, \"$val\" ]";
                    }
                }
            }
            echo implode(",\n", $sort); ?>
        ],
        "bSortClasses": true,
        "bAutoWidth": true,
        //"sDom": '<"H"pli>rt<"F"pli>',
        "sDom": '<?php echo (isset($this->sDom) ? $this->sDom : 'rt') ; ?>',
        "oLanguage": {
            "sUrl": "js/dataTables.langEN.txt"
        },
        //"sAjaxSource": "<?php echo $this->controllerName() . '/' . $this->actionName(); ?>",
        "sAjaxSource": self.location.toString(),
        "fnServerData": function(sSource, aoData, fnCallback) {
            $.ajax( {
                "dataType": "json",
                "type": "post",
                "cache": false,
                "url": sSource,
                "data": aoData,
                "success": function(data) {
                    //alert(JSON.stringify(data));
                    fnCallback(data);
                },
                "error": function(jqXHR) {
                    var data = jqXHR.responseText;
                    if (data == 'session is expired') {
                        //alert("Sorry, your session is expired!");
                        top.location.href = "index/login";
                    } else {
                        alert(data);
                    }
                }
            } );
        },
        "aoColumns": [
            { "sClass": "center", "bSortable": false },
            <?php
            $col = array();
            for ($i=1;$i<count($this->columns['aligns']);$i++) {
                $col[] = "{ \"sClass\": \"" . $this->columns['aligns'][$i] . "\" }";
            }
            echo implode(",\n", $col); ?>
        ],
        "sScrollX": "100%",
        <?php if (isset($this->sScrollXInner)) { ?>
        "sScrollXInner": "<?php echo $this->sScrollXInner; ?>",
        <?php } ?>
    });
    $("thead input").keyup(function(e) {
        if(e.keyCode == 13) {
            //oTable.fnFilter(this.value, ($("thead input").index(this) + 1));
            var arr = new Array();
            arr[0] = '';
            $("thead").find("input").each(function() {
                arr[arr.length] = $(this).val();
            });
            oTable.fnFilter(arr.join("~"), null);
        }
    });
});
</script>
