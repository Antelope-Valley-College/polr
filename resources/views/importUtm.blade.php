<form action="">
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-warning" id="errors" hidden></div>
        </div>

        <div class="col-md-12" id="loading">
            <label style="text-align: center;">
                <span>Loading Excel</span>
                <div class="loader"></div>
            </label>
        </div>

        <div class="col-md-12">
            <form action="" method="POST" id="file-upload" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12" id="uploadFile">
                            <div class="form-group">
                                <label class=" btn-success btn">
                                    <input type="file" name="form-id" id="submit" class="form-control btn btn-info"/>
                                    <i class="fa fa-cloud-upload"></i> Upload file
                                </label>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12" id="linkDwonload">
                            <div class="form-group">
                                <a href="javascript:window.location.reload(true)">New Excel</a>
                            </div>

                            <div class="form-group col-md-4">
                                <a href="" id="link" class="btn btn-danger form-control " target="_blank"> Link
                                    Download</a>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</form>
<script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
<script type="text/javascript">
    $(document).ready(function () {
        $("#linkDwonload").hide();
        $("#loading").hide();

        $("#submit").change(function (e) {
            $("#loading").show();
            e.preventDefault();
            const formData = new FormData();
            formData.append('file', $('input[type=file]')[0].files[0]);
            formData.append('CSRF_TOKEN', $('meta[name="csrf-token"]').attr('content'));
            $("#uploadFile").hide();
            $.ajax({
                enctype: 'multipart/form-data',
                url: '/api/v2/admin/importExcel',
                type: 'post',
                data: formData,
                processData: false,
                contentType: false,
                cache: false,
                timeout: 600000,
                success: function (res) {
                    if (res.status === true) {
                        $("#loading").hide();
                        $("#linkDwonload").show();
                        $("#uploadFile").hide();
                        $("#link").attr("href", '{{url('/')}}' + res.file);
                    }
                },
                error: function (data) {
                    var errors = data.responseJSON;
                    if (errors.status === false) {
                        $("#uploadFile").show();
                        $("#errors").show();
                        $("#loading").hide();
                        $("#errors").text(errors.error[0]);
                        $("#errors").show("slow").delay(3000).hide("slow");
                    }
                }
            });
        })
        function display() {

        }
    })
</script>
<style>
    input[type="file"] {
        display: none;
    }

    .custom-file-upload {
        border: 1px solid #ccc;
        display: inline-block;
        padding: 6px 12px;
        cursor: pointer;
    }

    .loader {
        border: 16px solid #f3f3f3;
        border-radius: 50%;
        border-top: 16px solid #3498db;
        width: 120px;
        height: 120px;
        -webkit-animation: spin 2s linear infinite; /* Safari */
        animation: spin 2s linear infinite;
    }

    /* Safari */
    @-webkit-keyframes spin {
        0% {
            -webkit-transform: rotate(0deg);
        }
        100% {
            -webkit-transform: rotate(360deg);
        }
    }

    @keyframes spin {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }
</style>
