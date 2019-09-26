<?php
print_r($this->option["group"]);
?>
<table>
<thead>
    <tr>
        <th>#</th>
        <th>Code</th>
        <th>Nama Lokasi</th>
    </tr>
</thead>
<tbody>
<?php
foreach($this->option['group'] as $idx => $val) {
    echo "<tr>".
         "<td></td>".
         "<td>".$idx."</td>".
         "<td>".$val."</td>".
         "</tr>";
}
?>
</tbody>
</table>

<script type="text/javascript">
$(document).ready(function() {
    $("#table1 tr input[id^=pick]").live("click", function() {
        var id = ($(this).attr("id")).split("-");
        var tr = $(this).parent().parent();
        var td = $(tr).children();
        var rowValues = new Array();
        $(td).each(function(rowIndex) {
            if (rowIndex == 0) {
                rowValues[rowIndex] = id[1];
            } else {
                rowValues[rowIndex] = $(this).html();
            }
        });
        opener.addRow(rowValues);
    });
});
</script>

