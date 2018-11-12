<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

	
function getFK()
{
	$c = uniqid(rand(), true);
	$md5c = md5($c);
	return $md5c;		
}

function uploadSurat($input){
	$CI =& get_instance();
	$temp = $input['tmp_name'];
	$arr = explode(".", $input['name']);
	$fileTypes = $arr[count($arr) - 1];
	$fileName = getFK().".".$fileTypes;
	$targetPath = getcwd()."/assets/media/doc/";
	$valid = true;
	$namaFile = "";

	if(strtolower($fileTypes) == "pdf" || strtolower($fileTypes) == "doc" || strtolower($fileTypes) == "docx")
	{
		move_uploaded_file($temp, $targetPath.$fileName);
	}
	else{
		$valid = false;
		$CI->session->set_flashdata('alert', 'File harus format pdf/doc/docx');
	}

	$obj = (object) array("fileName" => $fileName, "valid" => $valid);
	return $obj;

}

function uploadPeraturan($input){
	$CI =& get_instance();
	$temp = $input['tmp_name'];
	$arr = explode(".", $input['name']);
	$fileTypes = $arr[count($arr) - 1];
	$fileName = getFK().".".$fileTypes;
	$targetPath = getcwd()."/assets/media/doc/";
	$valid = true;
	$namaFile = "";

	if(strtolower($fileTypes) == "pdf" || strtolower($fileTypes) == "doc" || strtolower($fileTypes) == "docx")
	{
		move_uploaded_file($temp, $targetPath.$fileName);
	}
	else{
		$valid = false;
		$CI->session->set_flashdata('alert', 'File harus format pdf/doc/docx');
	}

	$obj = (object) array("fileName" => $fileName, "valid" => $valid);
	return $obj;

}

function uploadSlider($input){
	$CI =& get_instance();
	$temp = $input['tmp_name'];
	$arr = explode(".", $input['name']);
	$fileTypes = $arr[count($arr) - 1];
	$fileName = getFK().".".$fileTypes;
	$targetPath = getcwd()."/assets/imgslider/";
	$valid = true;
	$namaFile = "";

	if(strtolower($fileTypes) == "jpg" || strtolower($fileTypes) == "png")
	{
		move_uploaded_file($temp, $targetPath.$fileName);
	}
	else{
		$valid = false;
		$CI->session->set_flashdata('alert', 'Gambar harus format jpg atau png');
	}

	$obj = (object) array("fileName" => $fileName, "valid" => $valid);
	return $obj;

}

function dtInit($selectedField, $fromTable, $attribute = ""){
	$table = $fromTable;
	$column_search = $selectedField; //set column field database for datatable searchable 
    $column_order = array_merge(array(NULL), $selectedField); //set column field database for datatable orderable
   
    $order = array($selectedField[0] => 'asc'); // default order 
    $field = str_replace(" ", ", ", implode(" ",$selectedField));
    $sqlQuery = "SELECT ".$field." FROM ".$fromTable." ".$attribute;

    $result = array("columnSearch" => $selectedField, "field" => $selectedField, "tableName" => $fromTable, "ColumnOrder" => $column_order, "orderBy" => $order, "sqlQuery" => $sqlQuery);
    return (object) $result;
 }

 