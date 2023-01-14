<?php 


session_start();
$con = mysqli_connect("localhost","root","","demo");
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setCellValue('A1', 'Hello World !');

$writer = new Xlsx($spreadsheet);
$writer->save('hello world.xlsx');

if(isset($_POST['import_file_btn'])){
    $allowed_ext=['xls','csv','xlsx'];
    $fileName = $_FILES['import_file']['name'];
    $checking = explode(".",$fileName);
    $file_ext = end($checking);

    if(in_array($file_ext,$allowed_ext)){
        $targetPath = $_FILES['import_file']['tmp_name'];

/** Load $inputFileName to a Spreadsheet object **/
$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($targetPath);

$data = $spreadsheet->getActiveSheet()->toArray();
foreach($data as $row){
    $id= $row['0'];
    $stud_name= $row['1'];
    $stud_class  = $row['2'];
    $stud_phone  = $row['3'];

    $checkStudent ="SELECT id FROM student WHERE id='$id'";
    $checkStudent_result = mysqli_query($con,$checkStudent);
   
    if(mysqli_num_rows($checkStudent_result)>0){
        //already Exists means please  Update
        $up_query="UPDATE student SET stud_name='$stud_name',stud_class='$stud_class', stud_phone='$stud_phone' WHERE id='$id'";
        $up_result = mysqli_query($con, $up_query);
        $msg=1;
    }else{
        // Now record has to Insert
        $in_query= "INSERT INTO student(stud_name,stud_class,stud_phone) VALUES ('$stud_name','$stud_class','$stud_phone')";
        $in_result = mysqli_query($con, $in_query);
        $msg=1;
    }  
}
if(isset($msg)){
    $_SESSION['status'] = "file imported successfully";
    header("Location:index.php");
    exit(0);

}else{
    $_SESSION['status'] = "not file imported successfully";
    header("Location:index.php");
    exit(0);

}

    }else{
        $_SESSION['status'] = "Invalid File";
        header("Location:index.php");
        exit(0);
    }
}

?>