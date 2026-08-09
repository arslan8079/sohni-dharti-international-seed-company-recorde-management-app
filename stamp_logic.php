<?php

include "db.php";

$toast_message = null;
$select = false;

if(!$data_base){
    $toast_message = ['type' => 'error', 'msg' => 'Database is not connected.'];
} else {
    if(isset($_GET['delet'])){
        $d=$_GET['delet'];
        $dlet=mysqli_query($data_base, "DELETE FROM `stamp` WHERE `id`='$d'");
        if($dlet){
            $_SESSION['toast'] = ['type' => 'success', 'msg' => 'Stamp deleted successfully.'];
        } else {
            $_SESSION['toast'] = ['type' => 'error', 'msg' => 'Stamp could not be deleted.'];
        }
        header('Location: stamp.php');
        exit;
    }

    if(isset($_POST['insert_btn'])){
        $party=mysqli_real_escape_string($data_base, trim($_POST['party']));
        $stamp=mysqli_real_escape_string($data_base, trim($_POST['stamp']));
        $order=mysqli_real_escape_string($data_base, trim($_POST['order']));
        $date = date("Y-m-d");
        $check_duplicate = mysqli_query($data_base, "SELECT `id` FROM `stamp` WHERE `party`='$party' AND `stamp`='$stamp' AND `order`='$order' LIMIT 1");
        if(mysqli_num_rows($check_duplicate) > 0){
            $_SESSION['toast'] = ['type' => 'error', 'msg' => 'Duplicate stamp already exists.'];
        } else {
            $inst=mysqli_query($data_base,"INSERT INTO `stamp`(`party`,`stamp`,`order`,`created_at`)VALUE('$party','$stamp','$order','$date')");
            if($inst){
                $_SESSION['toast'] = ['type' => 'success', 'msg' => 'Stamp created successfully.'];
            } else {
                $_SESSION['toast'] = ['type' => 'error', 'msg' => 'Stamp could not be created.'];
            }
        }
        header('Location: stamp.php');
        exit;
    }

    if(isset($_POST['update_btn'])){
        $up_id=$_POST['u_id'];
        $party=mysqli_real_escape_string($data_base, trim($_POST['party']));
        $stamp=mysqli_real_escape_string($data_base, trim($_POST['stamp']));
        $order=mysqli_real_escape_string($data_base, trim($_POST['order']));
        $date = date("Y-m-d");

        $check_duplicate=mysqli_query($data_base, "SELECT `id` FROM `stamp` WHERE `party`='$party' AND `stamp`='$stamp' AND `order`='$order' LIMIT 1");

        if(mysqli_num_rows($check_duplicate) > 0){
            $_SESSION['toast'] = ['type' => 'error', 'msg' => 'Duplicate Stamp already exists.'];
        } else {
            $upd=mysqli_query($data_base,"UPDATE `stamp` SET `party`='$party',`stamp`='$stamp',`order`='$order',`created_at`='$date' WHERE `id`='$up_id'");
            if($upd){
                $_SESSION['toast'] = ['type' => 'success', 'msg' => 'stamp updated successfully.'];
            } else {
                $_SESSION['toast'] = ['type' => 'error', 'msg' => 'stamp could not be updated.'];
            }
        }
        header('Location: stamp.php');
        exit;
    }

  

    if(isset($_SESSION['toast'])){
        $toast_message = $_SESSION['toast'];
        unset($_SESSION['toast']);
    }

    if(isset($_GET['Search'])){
        $search=$_GET['Search'];
        $select= mysqli_query($data_base, "SELECT * FROM `stamp` WHERE `party`  like '%".$search."%' OR `stamp` like '%".$search."%' OR `order` like '%".$search."%' ");
    }else{
        $select= mysqli_query($data_base, "SELECT * FROM `stamp`");
    }
}

$total_records = $select ? mysqli_num_rows($select) : 0;

?>