<?php helper('form'); ?>
<?= $this->extend('layouts/main'); ?>

<?= $this->section('content'); ?>

<div class="main-content-inner">
  <div class="col-lg-12 mt-5">


    <div class="card mb-4">
      <div class="card-body">
        <?php echo form_open("/", 'id="user_search"') ?>
        <div class="row">

          <div class="col-md-4">
            <label>Kategori Surat</label>
            <select name="stase" id="stase" class="form-control">
              <option value="0" class="semua">Semua Stase</option>
              <div></div>
              <?php foreach ($stase as $stase) { ?>
                <option value="<?= $stase['id']; ?>"><?= $stase['stase']; ?></option>
              <?php } ?>
            </select>
          </div>

          <?php echo form_close(); ?>
        </div>
      </div>
    </div>

    <div class="card">

      <div class="card-body">
        <?php if (session('success')) { ?>
          <div class="alert-dismiss">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <?= session('success'); ?>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span class="fa fa-times"></span>
              </button>
            </div>
          </div>
        <?php } ?>
        <?php if (session('danger')) { ?>
          <div class="alert-dismiss">
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <?= session('danger'); ?>
              <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span class="fa fa-times"></span>
              </button>
            </div>
          </div>
        <?php } ?>

        <div class="data-tables datatable-dark">
          <table id="dataTable-arsip" class="text-center">
            <thead class="text-capitalize">
              <tr class="text-left">
                <th style="width: 20%;">Nama Lengkap</th>

                <th style="width: 7%;">Stase</th>
                <th style="width: 15%;">Tanggal Mulai</th>
                <th style="width: 15%;">Tanggal Selesai</th>
                <th style="width: 10%;"></th>
                <th style="width: 10%;"></th>
              </tr>
            </thead>

          </table>
        </div>

      </div>
    </div>
  </div>
</div>

<!-- Modal -->
<div class="modal fade" id="exampleModalCenter" aria-hidden="true" style="display: none;">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Pilih Stase</h5>
        <button type="button" class="close" data-dismiss="modal"><span>×</span></button>
      </div>
      <form action="<?= base_url('admin/ppds/selectstaseppds'); ?>" method="post">
        <div class="modal-body">
          <div class="form-group row">
            <label for="exampleInputEmail1" class="col-sm-4 col-form-label">Stase</label>
            <div class="col-sm-8">
              <select id="daftar_stase" class="custom-select" name="id_stase">
                <option value="">Daftar Stase</option>
              </select>
            </div>
          </div>
          <input type="hidden" id="id_ppds" name="id_ppds">
          <input type="hidden" id="id_stase_ppds" name="id_stase_ppds">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-flat btn-outline-danger mb-3 btn-xs" data-dismiss="modal">Close</button>
          <input type="submit" class="btn btn-flat btn-outline-success mb-3 btn-xs" value="Simpan">
        </div>
      </form>
    </div>
  </div>
</div>
<?= $this->endSection(); ?>

<?= $this->section('data_table'); ?>
<!-- Start datatable js -->
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
<script src="https://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.18/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap.min.js"></script>


<!-- button untuk export data ke excel -->
<script src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.print.min.js"></script>
<?= $this->endSection(); ?>

<?= $this->section('js'); ?>
<script>
  $('#<?= $menu_id; ?>').addClass('active');

  $('#<?= $menu_id; ?> ul.collapse').addClass('in');
  $('#<?= $menu_id; ?> ul.collapse li.<?= $menu_class; ?>').addClass('active');



  //---------------------------------------------------
  var table = $('#dataTable-arsip').DataTable({
    "processing": true,
    "serverSide": false,
    "dom": 'Bfrtip',
    "buttons": [{
      extend: 'collection',
      text: 'Export Table',
      buttons: [
        'excel',
        'print'
      ]
    }],
    "ajax": "<?= base_url('admin/users/get_ppds_pertahap_json/' . $tahap) ?>",
    "order": [
      [2, 'desc']
    ],
    "columnDefs": [{
        "targets": 0,
        "name": "nama_lengkap",
        'searchable': true,
        'orderable': true
      },
      {
        "targets": 1,
        "name": "stase",
        'searchable': true,
        'orderable': true
      },
      {
        "targets": 2,
        "name": "tanggal_mulai",
        'searchable': true,
        'orderable': true
      },
      {
        "targets": 3,
        "name": "tanggal_selesai",
        'searchable': true,
        'orderable': true
      },
    ]
  });

  //---------------------------------------------------
  $('select').on('change', function() {
    var s_form = $("#user_search").serialize();
    $.ajax({
      data: s_form,
      type: 'post',
      url: '<?php echo base_url('admin/users/get_ppds_filter_json'); ?>',
      async: true,
      success: function(output) {
        table.ajax.reload();
        console.log(output);
      }
    });
  });

  $(".pilih-stase").click(function(button) {
    var id_ppds = $(this).attr("id");
    var stase_ppds_id = $(this).attr("name");
    // console.log(stase_ppds_id);
    $("#id_ppds").val(id_ppds);
    $("#id_stase_ppds").val(stase_ppds_id);
    $.ajax({
      type: 'post',
      url: '<?= base_url('admin/stase') ?>',
      data: {
        id_ppds
      },
      dataType: 'json',
      success: function(data) {
        var html = '';
        var i;
        for (i = 0; i < data.length; i++) {
          html += '<option value=' + data[i].id + '>' + data[i].stase + '</option>';
        }
        $('#daftar_stase').html(html);
      }
    });
  });
</script>


<?= $this->endSection(); ?>


<?= $this->section('data_css'); ?>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.18/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap.min.css">
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.jqueryui.min.css">

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.6.1/css/buttons.dataTables.min.css">

<?= $this->endSection(); ?>