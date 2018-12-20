<?php
	require_once('form-builder-example.php');
	require_once('form.php');

	$formBuilder = new PHP_FORM_BUILDER();

	$demoFormOne = getFormData('demo-form');
	$formBuilder->displayForm($demoFormOne);

	$demoFormTwo = getFormData('demo-form-two');
	$formBuilder->displayForm($demoFormTwo);
