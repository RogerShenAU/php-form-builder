<?php
/*
** To use this form builder library
** 1. define(PHP_FORM_BUILDER_LIB_DIR, 'relative_path_to_lib_folder'); in parent application,
**    replace relative_path_to_lib_folder with your form.php folder path, e.g. /lib/php-form-builder/
** 2. add require_once('absolute_path_to_lib_folder/form.php'); in parent application
**    replace absolute_path_to_lib_folder with your form.php folder path, e.g. /var/www/vhosts/domain.com/httpdocs/lib/php-form-builder/
** 3. un-comment the code in displayForm function to add bootstrap support 
*/
/*
echo $_SERVER['SERVER_NAME'] . "<br>";
echo $_SERVER['PHP_SELF'] . "<br>";
echo $_SERVER['DOCUMENT_ROOT'] . "<br>";

$path = getcwd();
$path = str_replace($_SERVER['DOCUMENT_ROOT'], '', $path);
echo $path. "<br>";
echo getcwd() . "<br>";
echo basename(__DIR__) . "<br>";
echo dirname(__FILE__) . "<br>";
echo dirname(dirname(__FILE__)). "<br>";
*/

class PHP_FORM_BUILDER_TEST{

	/*
	** $formData should follow the format of $formData array in form-builder-example.php
	** form-builder-example.php can store multiple form data e.g. $forms array in form-builder-example.php, then use $forms['loan-computer'] or $forms['other-form'] as $formData
	*/
	function displayChillApplicationsForm($formData){
		
		echo $_SERVER['SERVER_NAME'] . "<br>";
		echo $_SERVER['PHP_SELF'] . "<br>";
		echo $_SERVER['DOCUMENT_ROOT'] . "<br>";
		
		echo getcwd(). "<br>";
		echo basename(__DIR__) . "<br>";
		echo dirname(__FILE__) . "<br>";

		$path = str_replace($_SERVER['DOCUMENT_ROOT'], '', dirname(__FILE__));
		echo $path. "<br>";


		$this->displayForm($formData);
	}

	function isAuthorized($formID,$userID,$action){

	}

	function displayForm($formData){

		echo '<link href="'.PHP_FORM_BUILDER_LIB_DIR.'/css/form.css" rel="stylesheet" type="text/css">';

		// un-comment below to add bootstrap support
		// echo '<link href="'.PHP_FORM_BUILDER_LIB_DIR.'css/bootstrap.css" rel="stylesheet" type="text/css">';
		// echo '<script src="'.PHP_FORM_BUILDER_LIB_DIR.'js/bootstrap.min.js" type="text/javascript"></script>';

		$this->displayFormTemplate($formData);
	}

	function displayFormTemplate($formData){

		$formTemplate = $formData['template']['form'];
		$fieldTemplate = $formData['template']['field'];

		if(isset($formTemplate) && $formTemplate != ""){

			$this->displayFormPart($formTemplate, "top",  $formData['name'], $formData['form_action']);
			$this->displayFormTitle($formTemplate, $formData['title']);

			if(isset($fieldTemplate) && $fieldTemplate != ""){

				$this->displayFormFields($fieldTemplate, $formData['fields'], $formData['edit']);
			}else{

				echo 'Please define a correct field template.';
			}

			$this->displayFormPart($formTemplate, "bottom");
		}else{

			echo 'Please define a correct form template.';
		}
	}

	function displayFormPart($template,$part,$name='',$action=''){

		if(isset($template) && $template == "default"){

			if(isset($part) && $part == "top"){

				echo '
					<div id="'.$name.'-div-default">
						<form id="'.$name.'" name="'.$name.'" class="container-fluid form-horizontal app-form-default" method="post" action="'.$action.'">
							<div class="field-container">
				';
			}elseif (isset($part) && $part == "bottom") {
				echo '
							</div>
						</form>
					</div>
				';
			}else{
				echo 'Please define a correct part for form part template';
			}
		}else{
			echo 'Please define a correct template for form part';
		}
	}

	function displayFormTitle($template, $title){

		if($template == "default"){

			echo '
				<div class="app-form-title-default">
					<h2 class="text-center">'.$title.'</h2>
				</div>
				';
		}else{
			echo 'Please define a correct form title template.';
		}
	}

	function displayFormFields($fieldTemplate,$fields,$edit){

		foreach ($fields as $key => $args) {

			$this->displayFormField($fieldTemplate,$key,$args,$edit);
		}
	}

	function displayFormField($fieldTemplate,$key,$args,$edit){

		/*
			$args example
				array(
					'type' => 'text',						// <input type="text" ...> - with extra code to be added
					'display_name' => 'Display Name',		// <label>Display Name</lable>
					'placeholder' => 'Enter...',			// <input placeholder="Enter..." ...>
					'required' => true,
					'other' => 'required ',					// <inpput required ...>
				)
		*/

		if ($fieldTemplate == "default") {

			if($args['type'] == 'text'){
				$this->displayFormFieldTypeText($fieldTemplate,$key,$args,$edit);
			}elseif ($args['type'] == '123') {
				# code...
			}else{
				echo 'Please add support for <b>'.$args['type'].'</b> field template.<br>';
			}
		}else{

			echo 'Please define a correct form template for input fields.';
		}
	}

	function displayFormFieldRequiredText($required){

		if($required){
			return array(
				'attr' => 'required',
				'text' => ' <small class="text-danger"><i>(required)</i></small>'
			);
		}else{
			return array(
				'attr' => '',
				'text' => ''
			);
		}
	}

	function displayFormFieldTypeText($fieldTemplate,$key,$args,$edit){

		if (isset($key) && isset($args)) {

			if($fieldTemplate == "default"){

				$leftColumn = 4;
				$rightColumn = 4;
				$required = $this->displayFormFieldRequiredText($args['required']);

				// get value from db if the form is editable
				if($edit){
					$formID = $this->getCurrentFormIDFromURL();
					$value = $this->getFormFieldValue($formID,$key);
				}else{
					$value = '';
				}

				echo '
					<div class="row">
						<div class="form-group">
							<label for="'.$key.'" class="col-md-'.$leftColumn.' control-label">
								'.$args['display_name'].$required['text'].':
							</label>
							<div class="col-md-'.$rightColumn.'">
								<input type="text" class="form-control" name="'.$key.'" id="'.$key.'" placeholder="'.$args['placeholder'].'" '.$required['attr'].' value="'.$value.'" >
							</div>
						</div>
					</div>
				';
			}else{
				echo 'Please define a correct form template for input text field.';
			}
		}
	}

	function getCurrentFormIDFromURL(){

	}

	function getFormFieldValue(){

	}
}