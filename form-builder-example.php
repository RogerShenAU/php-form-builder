<?php

// fields and forms use "-", this is to keep consistent with css classes and ids
// other keys use "_",
function getFormData($index = ''){
	$forms = array(
				'demo-form' => array(
					'application' => 'WordPress',
					'template' => array(
						'form' => 'default',
						'field' => 'default',
						'formlist' => 'default',
					),
					'form_action' => 'next-step.php',		
					'title' => 'Demo Form',							// form name to be displayed at the top of the page
					'name' => 'demo-form',							// form id; form name to be stored in the database
					'fields' => array(
						'sth' => array(								// <input name="sth" id="sth" ...>
							'type' => 'text',						// <input type="text" ...> - with extra code to be added. avaiable types: text, date, select, signature ...
							'display_name' => 'Display Name',		// <label>Display Name</lable>
							'placeholder' => 'Enter...',			// <input placeholder="Enter..." ...>
							'required' => true,
							'other' => 'required ',					// <inpput required ...>
						),
						'sth-else' => array(						// <input name="sth" id="sth" ...>
							'type' => 'text',						// <input type="text" ...> - with extra code to be added
							'display_name' => 'Some other field',	// <label>Display Name</lable>
							'placeholder' => 'Enter...',			// <input placeholder="Enter..." ...>
							'required' => true,
							'other' => 'required ',					// <inpput required ...>
						),
					),
					'buttons' => array(
						'submit' => array(
							'type' => 'submit',
							'value' => 'Submit',
							'class' => 'btn',
							'other' => '',
						),
					),
					'form_table' => 'fb_forms',
					'form_meta_table' => 'fb_forms_meta',
					'permission' => array(							// permission can be overwrite
						'edit' => false,
						'delete' => false,
						'download' => false
					),
				),	
				'demo-form-two' => array(
					'application' => 'WordPress',
					'template' => array(
						'form' => 'default',
						'field' => 'default',
						'formlist' => 'default',
					),
					'form_action' => 'next-step.php',
					'title' => 'Demo Form Two',
					'name' => 'demo-form-two',						// form id, form name to be stored in the database
					'fields' => array(
						'date' => array(							// use 'date' as name and id to reduce input
							'type' => 'date',						// <input type="text" ...> - with extra code to be added
							'display_name' => 'Date',				// <label>Date</lable>
							'placeholder' => 'dd-mm-yyyy',			// <input place ...>
							'required' => true,
							'other' => '',							// <inpput sth ...> - reserve a space at end just in case
						),
						'business-name' => array(
							'type' => 'text',
							'display_name' => 'Business Name',
							'placeholder' => 'Enter Business Name',
							'required' => true,
							'other' => '',
						),
						'name' => array(
							'type' => 'text',
							'display_name' => 'Name',
							'placeholder' => 'Enter Name',
							'required' => true,
							'other' => '',
						),
						'job-type' => array(
							'type' => 'select',
							'display_name' => 'Job Type',
							'options' => array(
								'Developer',
								'Super Hero'
							),
							'other' => '',
						),
						'address' => array(
							'type' => 'text',
							'display_name' => 'Address',
							'placeholder' => 'Enter Address',
							'required' => true,
							'other' => '',
						),
						'signature' => array(
							'type' => 'signature',
							'display_name' => 'Signature',
							'required' => true,
							'other' => '',
						),
					),
					'buttons' => array(
						'submit' => array(
							'type' => 'submit',
							'value' => 'Submit',
							'class' => 'btn',
							'other' => '',
						),
					),
					'form_table' => 'fb_forms',
					'form_meta_table' => 'fb_forms_meta',
					'permission' => array(
						'edit' => false,
						'delete' => false,
						'download' => false
					),
				)
			);
	
	if(isset($index) && count($forms[$index]) > 0 ){
		return $forms[$index];
	}else{
		return $forms;
	}
}