<?php

include "db.php";

$toast_message = null;
$select = false;

if(!$data_base){
    $toast_message = ['type' => 'error', 'msg' => 'Database is not connected.'];
} else {
    if(isset($_GET['delet'])){
        $d=$_GET['delet'];
        $dlet=mysqli_query($data_base, "DELETE FROM `recordes` WHERE `Id`='$d'");
        if($dlet){
            $_SESSION['toast'] = ['type' => 'success', 'msg' => 'Record deleted successfully.'];
        } else {
            $_SESSION['toast'] = ['type' => 'error', 'msg' => 'Record could not be deleted.'];
        }
        header('Location: assignment.php');
        exit;
    }

    if(isset($_POST['insert_btn'])){
        $Naame=mysqli_real_escape_string($data_base, trim($_POST['m_name']));
        $Adress=mysqli_real_escape_string($data_base, trim($_POST['m_adress']));
        $Area=mysqli_real_escape_string($data_base, trim($_POST['m_area']));

        $check_duplicate=mysqli_query($data_base, "SELECT `Id` FROM `recordes` WHERE `Name`='$Naame' AND `Adress`='$Adress' AND `Area`='$Area' LIMIT 1");
        if(mysqli_num_rows($check_duplicate) > 0){
            $_SESSION['toast'] = ['type' => 'error', 'msg' => 'Duplicate record already exists.'];
        } else {
            $inst=mysqli_query($data_base,"INSERT INTO `recordes`(`Name`,`Adress`,`Area`)VALUE('$Naame','$Adress','$Area')");
            if($inst){
                $_SESSION['toast'] = ['type' => 'success', 'msg' => 'Record created successfully.'];
            } else {
                $_SESSION['toast'] = ['type' => 'error', 'msg' => 'Record could not be created.'];
            }
        }
        header('Location: assignment.php');
        exit;
    }

    if(isset($_POST['update_btn'])){
        $up_id=$_POST['u_id'];
        $uName=mysqli_real_escape_string($data_base, trim($_POST['u_name']));
        $uAdress=mysqli_real_escape_string($data_base, trim($_POST['u_adress']));
        $uArea=mysqli_real_escape_string($data_base, trim($_POST['u_area']));

        $check_duplicate=mysqli_query($data_base, "SELECT `Id` FROM `recordes` WHERE `Id` != '$up_id' AND `Name`='$uName' AND `Adress`='$uAdress' AND `Area`='$uArea' LIMIT 1");
        if(mysqli_num_rows($check_duplicate) > 0){
            $_SESSION['toast'] = ['type' => 'error', 'msg' => 'Duplicate record already exists.'];
        } else {
            $upd=mysqli_query($data_base,"UPDATE `recordes` SET `Name`='$uName',`Adress`='$uAdress',`Area`='$uArea' WHERE `Id`='$up_id'");
            if($upd){
                $_SESSION['toast'] = ['type' => 'success', 'msg' => 'Record updated successfully.'];
            } else {
                $_SESSION['toast'] = ['type' => 'error', 'msg' => 'Record could not be updated.'];
            }
        }
        header('Location: assignment.php');
        exit;
    }

    if(isset($_POST['backup_db'])){
        $backup_dir = __DIR__;
        $backup_file = $backup_dir . '/backup_' . date('Y_m_d_H_i_s') . '.sql';

        foreach (glob($backup_dir . '/backup_*.sql') as $old_backup) {
            @unlink($old_backup);
        }

        $table_name = 'recordes';
        $create_result = mysqli_query($data_base, "SHOW CREATE TABLE `{$table_name}`");
        $create_row = mysqli_fetch_array($create_result);
        $create_sql = isset($create_row[1]) ? $create_row[1] . ";\n\n" : "";

        $columns_result = mysqli_query($data_base, "SHOW COLUMNS FROM `{$table_name}`");
        $columns = [];
        while ($column = mysqli_fetch_assoc($columns_result)) {
            $columns[] = $column['Field'];
        }

        $rows_result = mysqli_query($data_base, "SELECT * FROM `{$table_name}`");
        $insert_sql = [];
        while ($row = mysqli_fetch_assoc($rows_result)) {
            $values = [];
            foreach ($columns as $column) {
                $value = $row[$column];
                if ($value === null) {
                    $values[] = 'NULL';
                } else {
                    $values[] = "'" . mysqli_real_escape_string($data_base, $value) . "'";
                }
            }
            $insert_sql[] = "INSERT INTO `{$table_name}` (`" . implode('`, `', $columns) . "`) VALUES (" . implode(', ', $values) . ");";
        }

        $sql_content = "-- Auto backup for {$db}\n\n" . $create_sql . implode("\n", $insert_sql) . "\n";
        $written = file_put_contents($backup_file, $sql_content);

        if($written !== false && file_exists($backup_file)){
            $_SESSION['toast'] = ['type' => 'success', 'msg' => 'Database backup created successfully.'];
        } else {
            $_SESSION['toast'] = ['type' => 'error', 'msg' => 'Database backup failed.'];
        }
        header('Location: assignment.php');
        exit;
    }

    if(isset($_SESSION['toast'])){
        $toast_message = $_SESSION['toast'];
        unset($_SESSION['toast']);
    }

    if(isset($_GET['Search'])){
        $search=$_GET['Search'];
        $select= mysqli_query($data_base, "SELECT * FROM `recordes` WHERE `Name` like '%".$search."%' OR `Id` like '%".$search."%' OR `Area` like '%".$search."%' OR `Adress` like '%".$search."%' ");
    }else{
        $select= mysqli_query($data_base, "SELECT * FROM `recordes`");
    }
}

$total_records = $select ? mysqli_num_rows($select) : 0;

?>