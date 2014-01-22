<?php
$administratorID = intval($_REQUEST['AdministratorID']);
$formID = intval($_REQUEST['FormID']);
?>
<html>
    <head></head>
    <body>
        <div style="width:90%;height:90% ;position: absolute;z-index: 100"></div>
        <script type='text/javascript' src='http://v2.envialosimple.com/form/show/AdministratorID/<?php echo $administratorID;?>/FormID/<?php echo $formID;?>/format/widget'></script>
    </body>
</html>