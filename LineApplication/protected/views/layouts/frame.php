<?php
include dirname(__FILE__).'/frame/global_variable.php';
?>
<!DOCTYPE html>
<html lang="zh">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="language" content="zh" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <?php include dirname(__FILE__).'/frame/css.php'; ?>
    <title><?=CHtml::encode($this->pageTitle); ?> - 訂單系統</title>
</head>
<body>
<?php include dirname(__FILE__).'/frame/header.php';?>
<div id="frameContent">
    <?=$content ?>
</div>
<?php 
include dirname(__FILE__).'/frame/footer.php';
include dirname(__FILE__).'/frame/js.php';
?>
</body>
</html>
