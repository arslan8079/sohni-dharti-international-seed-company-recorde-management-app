<?php
session_start();

$host='localhost';
$username='root';
$password='';
$db='sohnidherti';
$data_base=mysqli_connect($host,$username,$password,$db);

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
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sohni Dharti International | Records</title>
 <link rel="icon" type="image/x-icon" href="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQZ9FIZonqEIfAq0iQ5XUGeBKmZ7TNCxDolt_kFF2y-AXrZROBfA-Gy0_E&s=10">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>

<script>
  tailwind.config = {
    theme: {
      extend: {
        fontFamily: { poppins: ['Poppins', 'sans-serif'] },
        colors: {
          brand: {
            50:  '#eefdf3',
            100: '#d6f9e2',
            200: '#aef0c6',
            300: '#78e1a4',
            400: '#3fca7e',
            500: '#1aab60',
            600: '#0f8a4c',
            700: '#0c6d3f',
            800: '#0d5734',
            900: '#0b472c',
          },
          gold: {
            400: '#f2c94c',
            500: '#e5b32f',
            600: '#c9941c',
          }
        },
        boxShadow: {
          soft: '0 10px 30px -12px rgba(11,71,44,0.25)',
        }
      }
    }
  }
</script>

<style>
  body{ font-family:'Poppins', sans-serif; }
  .pattern-bg{
    background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,0.18) 1px, transparent 0);
    background-size: 22px 22px;
  }

  /* ---- DataTables re-skin to match Tailwind theme ---- */
  .dataTables_wrapper .dataTables_length,
  .dataTables_wrapper .dataTables_filter,
  .dataTables_wrapper .dataTables_info,
  .dataTables_wrapper .dataTables_processing,
  .dataTables_wrapper .dataTables_paginate {
    font-family: 'Poppins', sans-serif;
  }
  .dataTables_wrapper .dataTables_filter { display: none; } /* using our own search box */
  .dataTables_wrapper .dataTables_length { display: none; } /* fixed page length of 20 */

  .dataTables_wrapper .dataTables_info {
    padding: 14px 20px !important;
    font-size: 13px;
    color: #6b7280;
  }
  .dataTables_wrapper .dataTables_paginate {
    padding: 10px 16px !important;
  }
  .dataTables_wrapper .dataTables_paginate .paginate_button {
    border-radius: 9999px !important;
    padding: 6px 13px !important;
    margin-left: 4px !important;
    border: 1px solid transparent !important;
    color: #0d5734 !important;
    font-size: 13px;
  }
  .dataTables_wrapper .dataTables_paginate .paginate_button.current,
  .dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
    background: linear-gradient(to right, #0f8a4c, #0c6d3f) !important;
    color: #fff !important;
    border: none !important;
  }
  .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: #eefdf3 !important;
    color: #0c6d3f !important;
  }
  .dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
    opacity: .4;
  }
  table.dataTable thead th { position: sticky; top: 0; }
  table.dataTable thead .sorting:before,
  table.dataTable thead .sorting:after,
  table.dataTable thead .sorting_asc:after,
  table.dataTable thead .sorting_desc:after { opacity: .6; }

  /* Modal */
  #addModal.hidden { display:none; }
  @keyframes pop { from { opacity:0; transform: scale(.95) translateY(8px); } to { opacity:1; transform: scale(1) translateY(0); } }
  #addModal .modal-card { animation: pop .18s ease-out; }
</style>
</head>
<body class="min-h-screen bg-gradient-to-br from-brand-50 via-white to-gold-400/10 text-slate-700">

<div class="container mx-auto max-w-6xl px-4 py-6 md:py-10">

  <!-- Header -->
  <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-brand-700 via-brand-600 to-brand-800 shadow-soft pattern-bg">
    <div class="flex flex-col md:flex-row items-center md:items-center justify-between gap-4 px-6 py-8 md:px-10 md:py-10">
      <div class="flex items-center gap-4">
        <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/15 backdrop-blur-md ring-1 ring-white/30">
          <i class="fa-solid fa-earth-asia text-2xl text-gold-400"></i>
        </div>
        <div>
          <h1 class="text-2xl md:text-3xl font-extrabold text-white tracking-tight">Sohni Dharti <span class="text-gold-400">International</span></h1>
          <p class="text-brand-100/90 text-sm md:text-base">Records Management System</p>
        </div>
      </div>
      <div class="flex items-center gap-2 rounded-full bg-white/10 px-4 py-2 ring-1 ring-white/25">
        <i class="fa-regular fa-circle-user text-white/90"></i>
        <span class="text-white/90 text-sm font-medium">Admin Panel</span>
      </div>
    </div>
  </div>

  <?php
  if($toast_message){
      echo "<script>window.toastMessage = " . json_encode($toast_message) . ";</script>";
  }
  ?>

  <!-- Stats + Search + Add bar -->
  <div class="mt-6 flex flex-col gap-4">

    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
      <form id="frm" method="GET" action="" class="w-full sm:w-96">
        <div class="relative">
          <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-brand-500">
            <i class="fa-solid fa-magnifying-glass"></i>
          </span>
          <input id="myInput" type="text" name="Search" class="search w-full rounded-full border border-brand-200 bg-white py-3 pl-11 pr-4 text-sm shadow-sm outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-100" placeholder="Search by name, area or address...">
        </div>
      </form>

      <div class="flex flex-wrap items-center gap-2">
        <form method="POST" class="m-0">
          <button type="submit" name="backup_db" class="group inline-flex items-center justify-center gap-2 rounded-full bg-white px-6 py-3 text-sm font-semibold text-brand-700 shadow-sm ring-1 ring-brand-200 transition hover:bg-brand-50 active:scale-95">
            <i class="fa-solid fa-download"></i>
            Backup DB
          </button>
        </form>

        <button type="button" id="exp_btn" class="group inline-flex items-center justify-center gap-2 rounded-full bg-gradient-to-r from-gold-500 to-gold-600 px-6 py-3 text-sm font-semibold text-brand-900 shadow-soft transition hover:shadow-lg hover:scale-[1.02] active:scale-95">
          <i class="fa-solid fa-plus transition group-hover:rotate-90"></i>
          Add New
        </button>
      </div>
    </div>
  </div>

  <!-- Table -->
  <div class="mt-6 overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-soft">
    <div class="overflow-x-auto">
      <table id="myTable" class="w-full min-w-[640px] text-left text-sm">
        <thead>
          <tr class="bg-gradient-to-r from-brand-700 to-brand-600 text-white">
            <th class="px-5 py-4 font-semibold">Name</th>
            <th class="px-5 py-4 font-semibold">Address</th>
            <th class="px-5 py-4 font-semibold">Area</th>
            <th class="px-5 py-4 font-semibold text-center">Delete</th>
            <th class="px-5 py-4 font-semibold text-center">Update</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-brand-50">
          <?php
          while($rec = mysqli_fetch_array($select)){
          ?>
          <tr class="transition hover:bg-brand-50/70">
            <td class="px-5 py-3.5 font-medium text-slate-700"><?php echo htmlspecialchars($rec['Name']); ?></td>
            <td class="px-5 py-3.5 text-slate-600"><?php echo htmlspecialchars($rec['Adress']); ?></td>
            <td class="px-5 py-3.5 text-slate-600"><?php echo htmlspecialchars($rec['Area']); ?></td>
            <td class="px-5 py-3.5 text-center">
              <a id="dlt_btn" href="?delet=<?php echo $rec['Id']; ?>" onclick="return confirm('Delete this record?');" class="inline-flex items-center gap-1.5 rounded-full bg-red-500 px-4 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-red-600 active:scale-95">
                <i class="fa-solid fa-trash"></i> Delete
              </a>
            </td>
            <td class="px-5 py-3.5 text-center">
              <button type="button" class="upd_btn inline-flex items-center gap-1.5 rounded-full bg-brand-600 px-4 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-brand-700 active:scale-95"
                data-id="<?php echo $rec['Id']; ?>"
                data-name="<?php echo htmlspecialchars($rec['Name'], ENT_QUOTES); ?>"
                data-address="<?php echo htmlspecialchars($rec['Adress'], ENT_QUOTES); ?>"
                data-area="<?php echo htmlspecialchars($rec['Area'], ENT_QUOTES); ?>">
                <i class="fa-solid fa-pen"></i> Update
              </button>
            </td>
          </tr>
          <?php
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>

  <p class="mt-6 text-center text-xs text-slate-400">© <?php echo date("Y"); ?> Sohni Dharti International — All rights reserved</p>

</div>

<!-- Add New Modal -->
<div id="addModal" class="hidden fixed inset-0 z-50 items-center justify-center bg-brand-900/50 backdrop-blur-sm px-4">
  <div class="modal-card w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl">
    <div class="mb-5 flex items-center justify-between">
      <h2 class="flex items-center gap-2 text-lg font-bold text-brand-800">
        <i class="fa-solid fa-user-plus text-brand-500"></i> Add New Record
      </h2>
      <button type="button" id="closeModal" class="flex h-8 w-8 items-center justify-center rounded-full text-slate-400 transition hover:bg-slate-100 hover:text-slate-600">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <form id="frm1" method="POST" class="space-y-4">
      <div>
        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-brand-700">Name</label>
        <input type="text" placeholder="Enter Name" name="m_name" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-100">
      </div>
      <div>
        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-brand-700">Address</label>
        <input type="text" placeholder="Enter Address" name="m_adress" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-100">
      </div>
      <div>
        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-brand-700">Area</label>
        <input type="text" placeholder="Enter Area" name="m_area" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-100">
      </div>
      <div class="flex justify-end gap-3 pt-2">
        <button type="button" id="cancelModal" class="rounded-full px-6 py-2.5 text-sm font-semibold text-slate-500 transition hover:bg-slate-100">Cancel</button>
        <button type="submit" name="insert_btn" class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-brand-600 to-brand-700 px-8 py-2.5 text-sm font-semibold text-white shadow-soft transition hover:from-brand-700 hover:to-brand-800 active:scale-95">
          <i class="fa-solid fa-floppy-disk"></i> Submit
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Update Record Modal -->
<div id="updateModal" class="hidden fixed inset-0 z-50 items-center justify-center bg-brand-900/50 backdrop-blur-sm px-4">
  <div class="modal-card w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl">
    <div class="mb-5 flex items-center justify-between">
      <h2 class="flex items-center gap-2 text-lg font-bold text-brand-800">
        <i class="fa-solid fa-pen-to-square text-brand-500"></i> Update Record
      </h2>
      <button type="button" id="closeUpdateModal" class="flex h-8 w-8 items-center justify-center rounded-full text-slate-400 transition hover:bg-slate-100 hover:text-slate-600">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    <form id="frmUpdate" method="POST" class="space-y-4">
      <input type="hidden" name="u_id" id="u_id">
      <div>
        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-brand-700">Name</label>
        <input type="text" id="u_name" placeholder="Enter Name" name="u_name" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-100">
      </div>
      <div>
        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-brand-700">Address</label>
        <input type="text" id="u_address" placeholder="Enter Address" name="u_adress" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-100">
      </div>
      <div>
        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-brand-700">Area</label>
        <input type="text" id="u_area" placeholder="Enter Area" name="u_area" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-100">
      </div>
      <div class="flex justify-end gap-3 pt-2">
        <button type="button" id="cancelUpdateModal" class="rounded-full px-6 py-2.5 text-sm font-semibold text-slate-500 transition hover:bg-slate-100">Cancel</button>
        <button type="submit" name="update_btn" class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-brand-600 to-brand-700 px-8 py-2.5 text-sm font-semibold text-white shadow-soft transition hover:from-brand-700 hover:to-brand-800 active:scale-95">
          <i class="fa-solid fa-floppy-disk"></i> Update Record
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  // ---- Modal open/close ----
  function openModal(modalId){
    $(modalId).removeClass('hidden').addClass('flex');
  }
  function closeModal(modalId){
    $(modalId).addClass('hidden').removeClass('flex');
  }
  $("#exp_btn").click(function(){ openModal('#addModal'); });
  $("#closeModal, #cancelModal").click(function(){ closeModal('#addModal'); });
  $("#addModal").on('click', function(e){
    if (e.target.id === 'addModal') closeModal('#addModal');
  });

  $(".upd_btn").on('click', function(){
    const button = $(this);
    $('#u_id').val(button.data('id'));
    $('#u_name').val(button.data('name'));
    $('#u_address').val(button.data('address'));
    $('#u_area').val(button.data('area'));
    openModal('#updateModal');
  });

  $("#closeUpdateModal, #cancelUpdateModal").click(function(){ closeModal('#updateModal'); });
  $("#updateModal").on('click', function(e){
    if (e.target.id === 'updateModal') closeModal('#updateModal');
  });
  $(document).on('keyup', function(e){
    if (e.key === 'Escape') {
      closeModal('#addModal');
      closeModal('#updateModal');
    }
  });

  // ---- DataTables init: 20 rows per page, built-in "Showing X to Y of Z entries" ----
  $(document).ready(function(){
    var table = $('#myTable').DataTable({
      pageLength: 20,
      lengthChange: false,
      order: [],
      language: {
        info: "Showing _START_ to _END_ of _TOTAL_ entries",
        infoEmpty: "No records found",
        paginate: { previous: "<i class='fa-solid fa-chevron-left'></i>", next: "<i class='fa-solid fa-chevron-right'></i>" }
      }
    });

    // wire our custom search box into DataTables
    $("#myInput").on("keyup", function() {
      table.search($(this).val()).draw();
    });
  });
</script>

<?php if($toast_message): ?>
<script>
  toastr.options = {
    closeButton: true,
    progressBar: true,
    newestOnTop: true,
    positionClass: 'toast-top-right',
    timeOut: 4000
  };

  <?php if($toast_message['type'] === 'success'): ?>
    toastr.success('<?php echo addslashes($toast_message['msg']); ?>');
  <?php else: ?>
    toastr.error('<?php echo addslashes($toast_message['msg']); ?>');
  <?php endif; ?>
</script>
<?php endif; ?>

</body>
</html>