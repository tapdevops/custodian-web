<html>
<head>
<script type="text/javascript">
function timedMsg()
{
var t=setTimeout("alert('I am displayed after 3 seconds!')",3000)
}
</script>
</head>
<body>

<form>
<input type="button" value="Display timed alertbox!" onclick="timedMsg()" />
</form>

</body>
</html> 