<?php
/*
** Use this file as a form build library
*/

$path = str_replace($_SERVER['DOCUMENT_ROOT'], '', dirname(__FILE__)); // get current folder relatvie path
define(PHP_FORM_BUILDER_LIB_DIR, $path);
define(YOUR_APPLICATION_NAME, "Your Application Name");
class PHP_FORM_BUILDER{

	/*
	** $formData should follow the format of $formData array in form-builder-example.php
	*/
	function displayForm($formData){

		echo '<link href="css/form.css" rel="stylesheet" type="text/css">';

		/* feel free to update below libraries */
		// jQuery Support
		echo '<script src="js/jquery-3.3.1.min.js" type="text/javascript"></script>';

		// bootstrap support
		echo '<script src="js/tether.min.js" type="text/javascript"></script>';
		echo '<link href="css/bootstrap.css" rel="stylesheet" type="text/css">';
		echo '<script src="js/bootstrap.min.js" type="text/javascript"></script>';
		
		// jSignature support
		echo '<script src="js/jSignature/libs/jSignature.min.js" type="text/javascript"></script>';

		$this->displayFormTemplate($formData);
	}

	function displayFormTemplate($formData){

		$formTemplate = $formData['template']['form'];
		$fieldTemplate = $formData['template']['field'];

		if(isset($formTemplate) && $formTemplate != ""){

			$this->displayFormPart($formData,'top');
			$this->displayFormTitle($formTemplate, $formData['title']);

			if(isset($fieldTemplate) && $fieldTemplate != ""){

				// $formData will be used by some sub functions.
				$this->displayFormFields($formData);

				$this->displayFormButtons($formData);

				$this->displayFormHtml($formData);
			}else{

				echo 'Please define a correct field template.';
			}

			$this->displayFormPart($formData, "bottom");
		}else{

			echo 'Please define a correct form template.';
		}
	}

	function displayFormPart($formData,$part){

		if(isset($formData['template']['form']) && $formData['template']['form'] == "default"){

			if(isset($part) && $part == "top"){

				echo '
					<div id="'.$formData['name'].'-div-default">
						<form id="'.$formData['name'].'" name="'.$formData['name'].'" class="container-fluid form-horizontal app-form-default" method="post" action="'.$formData['form_action'].'">
							<div class="field-container">
				';


				$formID = $this->getQueryStringFromURL('form_id');
				$formID = (int)$formID;
				if (isset($formID) && $formID>0){
					echo '<input type="hidden" name="form_id" value="'.$formID.'">';
					echo '<input type="hidden" name="submit_action" value="update">';
				}else{
					echo '<input type="hidden" name="submit_action" value="insert">';
				}
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

	function displayFormTitle($template,$title){

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

	function displayFormFields($formData){

		foreach ($formData['fields'] as $key => $args) {

			$this->displayFormField($key,$args,$formData);
		}
	}

	/* form field $args example
			array(
				'type' => 'text',						// <input type="text" ...> - with extra code to be added
				'display_name' => 'Display Name',		// <label>Display Name</lable>
				'placeholder' => 'Enter...',			// <input placeholder="Enter..." ...>
				'required' => true,
				'other' => 'required ',					// <inpput required ...>
				'msg' => 'Some message'
			)
	*/
		function displayFormField($key,$args,$formData){

			if ($formData['template']['field'] == "default") {

				if ($args['type'] == 'text' || $args['type'] == 'date' || $args['type'] == 'datetime'){
					$this->displayFormFieldTypeInput($key,$args,$formData);
				}elseif ($args['type'] == 'select') {
					$this->displayFormFieldTypeSelect($key,$args,$formData);
				}elseif ($args['type'] == 'signature') {
					$this->displayFormFieldTypeSignature($key,$args,$formData);
				}elseif ($args['type'] == 'checkbox') {
					$this->displayFormFieldTypeCheckbox($key,$args,$formData);
				}else{
					echo 'Please add support for <b>'.$args['type'].'</b> field template.<br>';
				}
			}else{

				echo 'Please define a correct form template for input fields.';
			}
		}

		function displayFormFieldTypeInput($key,$args,$formData){

			if (isset($key) && isset($args)) {

				if($formData['template']['field'] == "default"){

					$required = $this->displayFormFieldRequiredText($args['required']);

					// get value from db if the form is editable
					if($formData['permission']['edit']){
						$value = $this->getFormFieldValueViaURL($formData,$key);
					}else{
						$value = '';
					}

					echo '
							<div class="form-group">
								<label for="'.$key.'" class="control-label">
									'.$args['display_name'].$required['text'].': <br/>'.$args['msg'].'
								</label>
								<div class="col-md-'.$rightColumn.'">
									<input type="'.$args['type'].'" class="form-control" name="'.$key.'" id="'.$key.'" placeholder="'.$args['placeholder'].'" '.$required['attr'].' value="'.$value.'" >
								</div>
							</div>
					';
				}else{
					echo 'Please define a correct form template for '.$args['type'].' field.';
				}
			}
		}

		function displayFormFieldTypeSelect($key,$args,$formData){

			if (isset($key) && isset($args)) {

				if($formData['template']['field'] == "default"){

					$required = $this->displayFormFieldRequiredText($args['required']);

					// get value from db if the form is editable
					if($formData['permission']['edit']){
						$value = $this->getFormFieldValueViaURL($formData,$key);
					}else{
						$value = '';
					}

					echo '
							<div class="form-group">
								<label for="'.$key.'" class="control-label">
									'.$args['display_name'].$required['text'].':
								</label>
								<div class="col-md-'.$rightColumn.'">
									<select class="form-control" name="'.$key.'" id="'.$key.'" '.$required['attr'].' >
						';
									foreach ($args['options'] as $optionValue => $optionDisplay) {
										if($value == $optionValue){
											$selected = "selected";
										}else{
											$selected = "";
										}
										echo '
												<option value="'.$optionValue.'" '.$selected.'>'.$optionDisplay.'</option>
										';
									}

									echo '
									</select>
								</div>
							</div>
					';
				}else{
					echo 'Please define a correct field form template for '.$args['type'].' field.';
				}
			}
		}

		function displayFormFieldTypeSignature($key,$args,$formData){

			if (isset($key) && isset($args)) {

				if($formData['template']['field'] == "default"){

					$hidekey = 'hide-'.$key;
					echo '
						<div class="field-container">
							<input type="hidden" name="'.$key.'" id="'.$hidekey.'">
							<button id="clear-'.$hidekey.'" class="btn '.$args['btn-class'].'" type="button" onclick="$(\'#'.$key.'\').jSignature(\'clear\')">Clear Signature</button>
							<div class="signature-msg">'.$args['msg'].'</div>
							<div id="'.$key.'">
								<h3 class="sign-here">Sign Here</h3>
							</div>
						</div>
						<script>
							$(document).ready(function(){
								$("#'.$key.'").jSignature();
					            $("#'.$key.'").change(function(){
					            	$("input").blur();
					            	$("textarea").blur();
					            	$("html, body").animate({ scrollTop: $("#'.$key.'").offset().top }, "slow");
					            	$("#'.$hidekey.'").val($("#'.$key.'").jSignature("getData"));
					            });
					            $("#clear-'.$hidekey.'").click(function(){
									$("#'.$hidekey.'").val("");
					            });
							});
						</script>
					';

					if($args['required']){
						echo '
							<script>
								$(document).ready(function() {
									$("#submit").click(function(){
										var isSignatureProvided=$("#signature").jSignature("getData","base30")[1].length>1?true:false;
										if(!isSignatureProvided){
											alert("Please Sign Your Signature.");
											return false;
										}else{
											$("#'.$formData['name'].'").submit();
										}
									});
								});
							</script>
						';
					}
				}
			}
		}

		function displayFormFieldTypeCheckbox($key,$args,$formData){

			if (isset($key) && isset($args)) {

				if($formData['template']['field'] == "default"){

					echo '
					<div class="row">
						<div class="form-check">
							<label for="'.$key.'" class="form-check-label">
								<input type="checkbox" class="form-check-input" name="'.$key.'" id="'.$key.'">
								'.$args['display_name'].'
							</label>
						</div>
					</div>';
				}else{
					echo 'Please define a correct form field template for '.$args['type'].' field.';
				}
			}
		}
	/* end form field $args example */

	/* button $args example
		array(
			'type' => 'submit',
			'value' => 'Submit',
			'class' => 'btn-checklist',
			'other' => '',
		),
	*/
		function displayFormButtons($formData){

			foreach ($formData['buttons'] as $key => $args) {

				$this->displayFormButton($key,$args,$formData);
			}
		}

		function displayFormButton($key,$args,$formData){

			if ($formData['template']['field'] == "default") {

				if ($args['type'] == 'submit'){
					$this->displayFormButtonTypeSubmit($key,$args,$formData);
				}else{
					echo 'Please add support for <b>'.$args['type'].'</b> button template.<br>';
				}
			}else{

				echo 'Please define a correct form template for input fields.';
			}
		}

		function displayFormButtonTypeSubmit($key,$args,$formData){

			if($formData['template']['field'] == "default"){
				echo '<input class="'.$args['class'].'" type="'.$args['type'].'" id="'.$key.'" value="'.$args['value'].'">';
				echo $args['other'];
			}
		}
	/* end */

	function displayFormHtml($formData){
		echo $formData['html'];
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

	function getQueryStringFromURL($value){

		$url = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

		$parts = parse_url($url);

		parse_str($parts['query'], $query);

		return $query[$value];
	}

	function getFormFieldValueViaURL($formData,$key){

		if($formData['application'] == "WordPress"){

			$formID = $this->getQueryStringFromURL('form_id');
			$formID = (int)$formID;

			$fieldArgs = array(

					'application' => $formData['application'],
					'table' => $formData['form_meta_table'],
					'return_key' => 'form_field_value',	// return_key is hard coded
					'filters' => array(
						'form_id' => $formID,
						'form_field' => $key
					),
			);

			if(isset($formID) && $formID>0){

				$value = $this->getDataByFilters($fieldArgs);

				return $value[0]->form_field_value;
			}else{
				return "";
			}
		}
	}

	/* update form $args example
		array(
			'application' => 'WordPress',
			'action' => 'insert',
			'table' => 'fb_forms',
			'form_id' => '',
			'form_name' => 'demo-form',
			'user_id' => $userID
		);
	*/
		function updateForm($args){

			if($args['application'] == "WordPress"){
				$formID = $this->updateFormWP($args);
				return $formID;
			}elseif($args['application'] == YOUR_APPLICATION_NAME){
				// add your DB functions
			}else{
				echo 'Please update database functions.';
				return "";
			}
		}

		function updateFormWP($args){

			global $wpdb;

			date_default_timezone_set('Australia/Sydney');
			$currentTime = date('Y-m-d H:i:s');

			if(isset($args['submit'])){
				$submit = $args['submit'];
			}else{
				$submit = 0;
			}

			if(isset($args['assigned_to'])){
				$assignedTo = $args['assigned_to'];
			}else{
				$assignedTo = $args['user_id'];
			}

			if($args['action'] == "insert"){	// add extra permission check for user if needed

				$result = $wpdb->insert(
					$args['form_table'],
					array(
						'form_name' => $args['form_name'],
						'created_by' => $args['user_id'],
						'created_date' => $currentTime,
						'submit' => $submit,
						'assigned_to' => $assignedTo,
					),
					array(
						'%s',	// form name
						'%d',	// created_by
						'%s',  	// created_date
						'%d',
						'%d',
					)
				);

				if($result){
					$formID = $wpdb->insert_id;
				}else{
					$formID = "";
				}

				return $formID;
			}elseif($args['action'] == "update"){	// add extra permission check for user if needed

				if(isset($args['form_id']) && $args['form_id'] > 0){
					$formID = (int)$args['form_id'];
				}

				$result = $wpdb->update(
					$args['form_table'],
					array(
						'form_name' => $args['form_name'],
						'modified_by' => $args['user_id'],
						'modified_date' => $currentTime,
						'submit' => $submit,
						'assigned_to' => $assignedTo,
					),
					array(
						'form_id' => $formID,
					),
					array(
						'%s',	// form name
						'%d',	// created_by
						'%s',  	// created_date
						'%d',
						'%d',
					),
					array(
						'%d',	// form id
					)
				);

				return $formID;
			}elseif($args['action'] == "delete"){	// add extra permission check for user if needed

				if(isset($args['form_id']) && $args['form_id'] > 0){
					$formID = (int)$args['form_id'];
				}

				$result = $wpdb->update(
					$args['form_table'],
					array(
						'del' => 1
					),
					array(
						'form_id' => $formID
					),
					array(
						'%d',
					),
					array(
						'%d',	// form id
					)
				);

				return $formID;
			}else{
				echo "Please add database function for <b>".$args['action']."</b>";
				return "";
			}
		}
	/* end */

	/* update form field $args example
		array(
			'application' => 'WordPress',
			'action' => 'insert',
			'table' => 'fb_forms_meta',
			'form_id' => 1,
			'field' => $key,				// field name/id
			'value' => $value,				// field value
		);
	*/
		function updateFormFieldValue($args){

			if($args['application'] == "WordPress"){
				$this->updateFormFieldValueWP($args);
			}elseif($application == YOUR_APPLICATION_NAME){
				// add your DB functions
			}else{
				echo 'Please update database functions.';
				return "";
			}
		}

		function updateFormFieldValueWP($args){

			global $wpdb;

			date_default_timezone_set('Australia/Sydney');
			$created_date = date('Y-m-d H:i:s');

			if($args['action'] == "insert"){

				$result = $wpdb->insert(	// add extra permission check for user if needed
					$args['form_meta_table'],
					array(
						'form_id' => $args['form_id'],
						'form_field' => $args['field'],
						'form_field_value' => $args['value'],
					),
					array(
						'%d',
						'%s',
						'%s',
					)
				);

				return true;
			}elseif($args['action'] == "update"){

				$result = $wpdb->update(	// add extra permission check for user if needed
					$args['form_meta_table'],
					array(
						'form_field_value' => $args['value'],
					),
					array(
						'form_id' => $args['form_id'],
						'form_field' => $args['field'],
					),
					array(
						'%s',
					),
					array(
						'%d',
						'%s',
					)
				);

				return true;
			}else{
				echo "Please add database function for <b>".$args['action']."</b>";
				return false;
			}
		}
	/* end */

	/* display form list $args example
		array(
			'application' => 'WordPress',
			'form_name' => 'demo-form',
			'template' => 'default',
			'form_table' => 'fb_forms',
			'form_meta_table' => 'fb_forms_meta',
			'fields' => array(
				'Name' => 'name',
				'Phone Number' => 'phone-number'
			),
			'actions' => array(
				'edit' => true,
				'delete' => true,
				'download' => true,
			),
			'links' => array(
				'edit' => '/form-builder/add.php',
				'delete' => '/form-builder/delete.php',
			),
		)
	*/
		function displayFormList($args){

			if (isset($args)) {
				if($args['application'] == "WordPress"){

					$this->displayFormListWP($args);
				}elseif ($args['application'] == YOUR_APPLICATION_NAME) {
					# code...
				}else{
					return false;
				}
			}
		}

		function displayFormListWP($args){

			if (isset($args)) {

				if($args['template'] == 'default'){

					$dataSet = $this->getDataSetForFormList($args);
					$dataSetColumns = $this->getdataSetColumnsForFormList($args);

					if($dataSet && $dataSetColumns){

						$this->displayFormListInDataTable($args,$dataSet,$dataSetColumns);
					}
				}else{
					return false;
				}
			}
		}

		function displayFormListInDataTable($args,$dataSet,$dataSetColumns){

			if (isset($args)) {

				if($args['template'] == 'default'){

					if(
						$dataSet &&
						$dataSetColumns &&
						isset($dataSet) &&
						isset($dataSetColumns) &&
						count($dataSet) > 0
					  )
					{
						$aoColumns = "";
						$tableIndexes = "";
						$tableBody = "";
						$actionText = "";
						$bgClass = "even"; // default first row highlighting CSS class

						foreach ($dataSetColumns as $x => $dataSetColumn) {

							$aoColumns .= "null,";
							$tableIndexes .= '<th><strong>'.$dataSetColumn.'</strong></th>';
						}

						foreach ($dataSet as $formID => $dataSetRow) {

							$tableBody .= "<tr class='" . $bgClass . "'>";

							$submitArgs = array(
						   		'application' => 'WordPress',
								'table' => 'fb_forms',
								'return_key' => 'submit',
								'filters' => array(
									'form_id' => $formID,
									'del' => 0
								),
							);
							$submitStd = $this->getDataByFilters($submitArgs);
							$submitted = $submitStd[0]->submit;

							foreach ($dataSetRow as $columnKey => $columnValue) {
								$tableBody .= "<td>$columnValue</td>";
							}

							foreach ($args['actions'] as $action => $enable) {

								if($action == 'edit' || $action == 'delete'){
									// if the form is submitted, form can not be edited
									if($submitted == 1){
										$enable = false;
									}
								}

								if($action == 'download' ){
									// if the form has been submitted, form can not be edited/deleted
									if($submitted == 0){
										$enable = false;
									}
								}

								if($action == 'edit'){
									// if the form has not been submitted, form can not be download
									if($submitted == 1){
										$enable = false;
									}
								}

								if($enable){
									$actionCapitalizeFirst = ucfirst($action);
									$actionText .= '<a href="'.$args['links'][$action].'?form_id='.$formID.'">'.$actionCapitalizeFirst.'</a> | ';
								}
							}

							$tableBody .= "<td>$actionText</td>";

							$actionText = ''; // reset $actionText

							$tableBody .= "</tr>";

							$bgClass = ($bgClass == "even" ? "odd" : "even");
						}

						echo '
							<form enctype="multipart/form-data" action="" method="post" name="ff2">
								<table width="100%" border="0" cellspacing="0" cellpadding="0" id="grid-'.$args['form_name'].'" class="dataTable">
									<thead>
										<tr class="topRow">
											'.$tableIndexes.'
										</tr>
									</thead>
										'.$tableBody.'
									<tfoot>
										<tr class="topRow">
											'.$tableIndexes.'
										</tr>
									</tfoot>
								</table>
								<br/>
							</form>

							<script>
								$(document).ready(function() {
									$("#grid-onsite-procedure").dataTable({
										"iDisplayLength" : 10,
										"sPaginationType": "full_numbers",
										"aaSorting": [[0,"desc"]],
										"aoColumns": [
											'.$aoColumns.'
											{ "bSortable": false }
										],
										"oLanguage": {
											"sEmptyTable": " ", /* appears in the top middle of the empty table */
											"sInfoEmpty": "No matching records found."
										}
									});

									//$(\'select[name="grid_length"]\').msDropdown({mainCSS:"rows"});
								});
							</script>
						';
					}else{
						echo 'No data available.';
					}
				}
			}
		}

		function getDataSetForFormList($args){

			if (isset($args)) {

				if($args['template'] == 'default'){

					if(isset($args['form_table_filters'])){
						$formFilters = $args['form_table_filters'];
					}else{
						// need to set form filters in form-builder.php
						return false;
					}

					$formArgs = array(
	   					'application' => $args['application'],
						'table' => $args['form_table'],
						'return_key' => 'form_id',	// return_key is hard coded
						'filters' => $formFilters,
					);

					$formIDs = $this->getDataByFilters($formArgs);

					$dataSet = [];

					foreach ($formIDs as $a => $formIDStd) {

						$formID = $formIDStd->form_id;

						foreach ($args['fields'] as $fieldName => $fieldField) {

							$fieldArgs = array(
			   					'application' => $args['application'],
								'table' => $args['form_meta_table'],
								'return_key' => 'form_field_value',	// return_key is hard coded
								'filters' => array(
									'form_id' => $formID,
									'form_field' => $fieldField
								),
							);

							$fieldValueStd = $this->getDataByFilters($fieldArgs); // should only return one row
							$dataSet[$formID][$fieldField] = $fieldValueStd[0]->form_field_value;
						}
					}

					return $dataSet;
				}else{
					return false;
				}
			}
		}

		function getdataSetColumnsForFormList($args){

			if (isset($args)) {

				if($args['template'] == 'default'){
					$dataSetColumns = [];

					foreach ($args['fields'] as $key => $value) {
						$dataSetColumns[$value] = $key;
					}

					$dataSetColumns['actions'] = "Actions";

					return $dataSetColumns;
				}else{
					return false;
				}
			}
		}
	/* end */

	/* get data from database via filters
	   array(
	   		'application' => 'WordPress',
			'table' => 'fb_forms',
			'return_key' => 'form_id',
			'filters' => array(
				'form_name' => 'demo-form',
				'del' => 0
			),
	   )
	*/
		function getDataByFilters($args){

			if($args['application'] == "WordPress"){

				global $wpdb;

				$filterSQL = "";

				if(count($args['filters']) == 0){
					// do nothing
				}elseif (count($args['filters']) >= 1) {

					$filterNumbers = 1;

					foreach ($args['filters'] as $key => $value) {

						if($filterNumbers == 1){
							$filterSQL = " `$key` = '$value' ";
						}else{
							$filterSQL .= " AND `$key` = '$value' ";
						}

						$filterNumbers++;
					}

				}else{
					// error
				}

				// no user input here, user wpdb->prepare if necessary
				$data = $wpdb->get_results("SELECT `".$args['return_key']."` FROM ".$args['table']." WHERE $filterSQL");

				return $data;
			}elseif ($args['application'] == YOUR_APPLICATION_NAME) {
				# code...
			}else{
				return false;
			}
		}
	/* end */

	/* download pdf $args example
	   array(
			'form_id' => 8,
			'form_name' => 'demo-form'
			'file_type' => 'pdf',	// download file type
			'file_lib' => '/var/www/vhosts/domain.com/httpdocs/form-builder/lib/tcpdf_min/tcpdf.php',  // absolute  path
			'file_template_type' => 'pdf',	// base template file type
			'file_template' => '/form-builder/pdfs/Template_001.pdf', // relative path
			'template' => 'default',
		)
	*/
		function downloadFile($args,$formData){

			if($args['template'] == 'default'){

				if($args['file_type'] == 'pdf'){

					require_once($args['file_lib']);

					// create new PDF document
					$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

					//-- settings ---
						// set document information
						$pdf->SetCreator(PDF_CREATOR);

						// set header and footer fonts
						$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
						$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

						// remove default header/footer
						$pdf->setPrintHeader(false);
						$pdf->setPrintFooter(false);

						// set default monospaced font
						$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

						// set margins
						$pdf->SetMargins(10, 10, 10);
						$pdf->SetHeaderMargin(0);
						$pdf->SetFooterMargin(0);

						// set default font subsetting mode
						$pdf->setFontSubsetting(true);

						// Set font
						// dejavusans is a UTF-8 Unicode font, if you only need to
						// print standard ASCII chars, you can use core fonts like
						// helvetica or times to reduce file size.
						$pdf->SetFont('helvetica', '', 10);
					//-- end settings ---

					$pdf->AddPage();

					// -- set new background ---
						// get the current page break margin
						$bMargin = $pdf->getBreakMargin();
						// get current auto-page-break mode
						$auto_page_break = $pdf->getAutoPageBreak();
						// disable auto-page-break
						$pdf->SetAutoPageBreak(false, 0);

						if($args['file_template_type'] == 'pdf'){
							//add FPDI support, FPDI extends TCPDF
						}elseif ($args['file_template_type'] == 'img') {
							// set bacground image
							$img_file = $args['file_template'];
							$pdf->Image($img_file, 0, 0, 210, 297, '', '', '', false, 300, '', false, false, 0);
						}else{
							echo 'Please add file template type support.';
						}

						// restore auto-page-break status
						$pdf->SetAutoPageBreak($auto_page_break, $bMargin);
						// set the starting point for the page content
						$pdf->setPageMark();
					// -- end set new background ---

					//-- print info ---
						// PDF title
						$pdf->SetFont('helvetica', '', 20);
						$pdf->writeHTMLCell(0, 0, 20, 20, $formData['title'], $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true);

						// PDF fields
						$pdfContentY = 40;
						$formID = $args['form_id'];
						foreach ($formData['fields'] as $key => $field) {

							if(isset($field['pdf_field'])){
								$pdfField = $field['pdf_field'];
							}else{
								// by default true
								$pdfField = true;
							}

							if($pdfField){
								$pdf->SetFont('helvetica', '', 12);
								$pdf->writeHTMLCell(0, 0, 20, $pdfContentY, $field['display_name'], $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true);

								$args = array(
							   		'application' => 'WordPress',
									'table' => $formData['form_meta_table'],
									'return_key' => 'form_field_value',
									'filters' => array(
										'form_id' => $formID,
										'form_field' => $key
									),
							   	);

								$fieldValueStdArray = $this->getDataByFilters($args);

								if(isset($fieldValueStdArray) && count($fieldValueStdArray) > 0){
									$fieldValue = $fieldValueStdArray[0]->form_field_value;
									if($key == 'signature'){
										$fieldValue = '<img style="width:100px;" src="' . $fieldValue .'" alt="Signature">';
									}
								}else{
									$fieldValue = "";
								}

								$pdfContentY += 5;
								$pdf->SetFont('helvetica', '', 10);
								$pdf->writeHTMLCell(0, 0, 20, $pdfContentY, $fieldValue, $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true);
								$pdfContentY += 10;
							}
						}

						// pdf text
						$pdfContentY += 10;
						$html = $formData['html'];
						$pdf->writeHTMLCell(0, 0, 20, $pdfContentY, $html, $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true);
					//-- end print info ---

					// output
					// "I" for display on the page, "F" for store, "D" for donwload
					// $pdf->Output( 'pdfs/Prefix_' .$id . '.pdf', 'F');  // output pdf with prefix "Prefix_" and store under "pdfs" folder
					$pdf->Output( $formData['name'].'_' .$formID .'.pdf', 'D');
				}elseif($args['file_type'] == 'csv'){
					// add csv file download support
				}else{
					echo 'Please define your file type';
				}
			}else{
				echo 'Please define your file template';
			}
		}
	/* end */
}