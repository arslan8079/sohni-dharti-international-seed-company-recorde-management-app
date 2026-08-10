<?php
session_start();
include "stamp_logic.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sohni Dharti International | Stamps</title>
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
<link rel="stylesheet" href="style.css">


</head>
<body class="min-h-screen bg-gradient-to-br from-brand-50 via-white to-gold-400/10 text-slate-700">

<div class="container mx-auto max-w-6xl px-4 py-6 md:py-10">
 
    <!-- Header -->
  <?php include "header.php" ?>

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
            <th class="px-5 py-4 font-semibold">Party Name</th>
            <th class="px-5 py-4 font-semibold">Stamp</th>
            <th class="px-5 py-4 font-semibold">Order</th>
            <th class="px-5 py-4 font-semibold text-center">Delete</th>
            <th class="px-5 py-4 font-semibold text-center">Update</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-brand-50">
          <?php
          while($rec = mysqli_fetch_array($select)){
          ?>
          <tr class="transition hover:bg-brand-50/70">
            <td class="px-5 py-3.5 font-medium text-slate-700"><?php echo htmlspecialchars($rec['party']); ?></td>
            <td class="px-5 py-3.5 text-slate-600"><?php echo htmlspecialchars($rec['stamp']); ?></td>
            <td class="px-5 py-3.5 text-slate-600"><?php echo htmlspecialchars($rec['order']); ?></td>
            <td class="px-5 py-3.5 text-center">
              <a id="dlt_btn" href="?delet=<?php echo $rec['id']; ?>" onclick="return confirm('Delete this stamp?');" class="inline-flex items-center gap-1.5 rounded-full bg-red-500 px-4 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-red-600 active:scale-95">
                <i class="fa-solid fa-trash"></i> Delete
              </a>
            </td>
            <td class="px-5 py-3.5 text-center">
              <button type="button" class="upd_btn inline-flex items-center gap-1.5 rounded-full bg-brand-600 px-4 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-brand-700 active:scale-95"
                data-id="<?php echo $rec['id']; ?>"
                data-name="<?php echo htmlspecialchars($rec['party'], ENT_QUOTES); ?>"
                data-address="<?php echo htmlspecialchars($rec['stamp'], ENT_QUOTES); ?>"
                data-area="<?php echo htmlspecialchars($rec['order'], ENT_QUOTES); ?>">
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
        <input type="text" placeholder="Enter Name" name="party" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-100">
      </div>
      <div>
        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-brand-700">Address</label>
        <input type="text" placeholder="Enter Address" name="stamp" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-100">
      </div>
      <div>
        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-brand-700">Area</label>
        <input type="text" placeholder="Enter Area" name="order" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-100">
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
        <input type="text" id="u_name" placeholder="Enter Name" name="party" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-100">
      </div>
      <div>
        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-brand-700">Address</label>
        <input type="text" id="u_address" placeholder="Enter Address" name="stamp" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-100">
      </div>
      <div>
        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-brand-700">Area</label>
        <input type="text" id="u_area" placeholder="Enter Area" name="order" required class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm outline-none transition focus:border-brand-500 focus:ring-4 focus:ring-brand-100">
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