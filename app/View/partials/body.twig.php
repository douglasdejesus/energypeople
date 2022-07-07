<!DOCTYPE html>
<html>
	<head>
		<title>{% block title %}{% endblock %}</title>
		<meta charset="utf-8" />
		<link  rel="stylesheet" type="text/css"  media="all" href="{{URL_BASE}}/assets/css/style.css" />
		<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,800" rel="stylesheet">
		<meta name="viewport" content="width=device-width,minimum-scale=1">
		<style amp-boilerplate>body{-webkit-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-moz-animation:-amp-start 8s steps(1,end) 0s 1 normal both;-ms-animation:-amp-start 8s steps(1,end) 0s 1 normal both;animation:-amp-start 8s steps(1,end) 0s 1 normal both}@-webkit-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-moz-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-ms-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@-o-keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}@keyframes -amp-start{from{visibility:hidden}to{visibility:visible}}</style><noscript><style amp-boilerplate>body{-webkit-animation:none;-moz-animation:none;-ms-animation:none;animation:none}</style></noscript>
		<script async src="https://cdn.ampproject.org/v0.js"></script>
		<script async custom-element="amp-fit-text" src="https://cdn.ampproject.org/v0/amp-fit-text-0.1.js"></script>
		<script async custom-element="amp-sidebar" src="https://cdn.ampproject.org/v0/amp-sidebar-0.1.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/jquery.validate.min.js" integrity="sha512-37T7leoNS06R80c8Ulq7cdCDU5MNQBwlYoy1TX/WUsLFC2eYNqtKlV0QjH7r8JpG/S0GUMZwebnVFLPd6SU5yg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.3/localization/messages_pt_BR.min.js" integrity="sha512-Gvvp9aP/OTNnd+4OUOPeTeT02Z553hAQeEvurLLWQzXN3QC7Oh7Z+ysRHhA5d9uhhw5R0rzS3f3kGg+Ityn/4Q==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-maskmoney/3.0.2/jquery.maskMoney.min.js"></script>
		<script type="text/javascript" src="http://localhost/cronos/publico/js/croppie.js"></script>
		<script type="text/javascript" src="{{URL_BASE}}/assets/js/jquery-mask.js"></script>
	</head>
	<body>
		{% include 'partials/header.twig.php' %}
		{% block body %}{% endblock %}
		{% include 'partials/footer.twig.php' %}
	</body>
</html>
