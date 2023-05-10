<form action="">
    <div class="row">
        <div class="col-md-12">
            <div class="alert alert-warning" id="errorsSms" hidden></div>
        </div>

        <div class="col-md-12" id="loadings" style="text-align: center;">
            <label style="text-align: center;">
                <div class="loader"></div>
            </label>
        </div>
        <div class="col-md-12">
            <form action="" method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12" id="uploadFile">
                            <div class="form-group">
                                <input type="text"  id="phoneNumber"  class="form-control" placeholder="Please enter your phoneNumber here">
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12" id="uploadFile">
                            <div class="form-group">
                                <label for="">Please enter your text here</label>
                                    <textarea name="textSms" id="textSms" class="form-control" rows="10"> </textarea>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-12" id="uploadFile">
                            <div class="form-group">
                                 <input type="button" id="submitSend"  class="btn btn-danger form-control" value="ارسال پیامک">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</form>
<script src="{{asset('/js/jquery-3.4.1.min.js')}}"></script>
<script type="text/javascript">
    $(document).ready(function () {

        $("#loadings").hide();
        $("#submitSend").click(function (e) {
            $("#loadings").show();
            e.preventDefault();
            const formData = new FormData();
            formData.append('text', $("#textSms").val());
            formData.append('phoneNumber', $("#phoneNumber").val());
            formData.append('CSRF_TOKEN', $('meta[name="csrf-token"]').attr('content'));
            $.ajax({
                url: '/api/v2/admin/sendSms',
                type: 'post',
                data: formData,
                processData: false,
                contentType: false,
                cache: false,
                timeout: 600000,
                success: function (res) {
                    if (res.status === true) {
                        $("#loadings").hide();
                        $("#phoneNumber").val('');
                        $("#textSms").val('');
                        $("#errorsSms").text(res.messages);
                        $("#errorsSms").show("slow").delay(3000).hide("slow");

                    }
                },
                error: function (data) {
                    var errors = data.responseJSON;
                    if (errors.status === false) {
                        $("#errorsSms").show();
                        $("#loadings").hide();
                        $("#errorsSms").text(errors.error[0]);
                        $("#errorsSms").show("slow").delay(3000).hide("slow");
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
