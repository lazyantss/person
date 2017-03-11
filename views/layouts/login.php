<?php
use yii\helpers\Html;

?>

<html lang="en">
<head>
	<title><?= Html::encode($this->title).' - '. Yii::$app->params['company'] ?></title>

	<meta charset="UTF-8">
	<meta name="apple-mobile-web-app-capable" content="yes" />
	<meta name="viewport" content="user-scalable=no,initial-scale=1,width=device-width" />
	
	<link rel="stylesheet" href="/css/base.css" />
	<link rel="stylesheet" href="/css/login.css" />

	<script src="/js/jquery.min.js"></script>
	<script src="/js/jquery.transit.min.js"></script>
	<script src="/js/widget.min.js"></script>
</head>
<body>
	<header>
		<a href="http://www.<?= strtolower(Yii::$app->params['company']) ?>.com/" title="Welcome to " class="logo"><img src="/img/logo.png" alt="" width="280" height="48" /></a>
	</header>

	<!-- Content Begin -->
	<?= $content ?>

	<!-- Content End -->
	
	<footer>
		&#xa9; 2017 <?= Yii::$app->params['company'] ?>  Inc.
	</footer>
</body>
</html>