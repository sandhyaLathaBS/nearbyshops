@extends('layouts.app')
@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <button type="button" class="btn btn-success btn-lg ml-3">
                <a class="text-light" href="{{url('shop/create')}}">Add New Shop</a>
            </button>
        </div>
    </div><br><br>
    <div class="container ">
        <div class="row">
            <div class="col-md-12">
                <table id="datatable-list" class="shadow-lg table table-hover table-bordered ">
                    <thead>
                        <tr>
                            <th>sl no:</th>
                            <th>Image</th>
                            <th>Shop</th>
                            <th>Description</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(!empty($shops))
                        <?php $i = 1; ?>
                        @foreach($shops as $shop)
                        <tr>
                            <td>{{$i++;}}</td>
                            <td>
                                <img border="0" src="{{url('uploads/store_images/')}}/<?php echo $shop->image; ?>"
                                    width="100">
                            </td>
                            <td>{{$shop->name}}</td>
                            <td>{{$shop->description}}</td>
                            <td>
                                <a class="btn btn-info"
                                    onclick="viewThisShop('<?= base64_encode($shop->id) ?>', '<?= ($shop->name) ?>')"
                                    data-toggle="modal" data-target="#myModal">View</a>
                                <a class="btn btn-success"
                                    href="{{url('/shop')}}/<?= base64_encode($shop->id) ?> /edit">
                                    Edit
                                </a>
                                <a onclick="deleteThisShop('<?= base64_encode($shop->id) ?>')" class="btn btn-warning ">
                                    Delete
                                </a>
                            </td>
                        </tr>
                        @endforeach
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- The Modal -->
<div class="modal" id="myModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 id="modalheader" class="modal-title">Modal Heading</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <!-- Modal body -->
            <div id="modalBody" class="modal-body">
                Modal body..
            </div>
            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
<script>
function viewThisShop(id, name) {
    if (id) {
        $("#myModal #modalheader").empty().html(name);
        $("#myModal #modalBody").empty().html(' Modal body..');
        $.ajax({
            type: "GET",
            url: "{{url('shop')}}/" + id,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $("#myModal #modalBody").empty().html(response);
            }
        });
    }
}

function deleteThisShop(id) {
    if (id) {
        swal({
            title: "Delete",
            text: "Do you really want to delete?",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes",
            cancelButtonText: "cancel",
            closeOnConfirm: false,
            closeOnCancel: false
        }).then((isConfirm) => {
            if (isConfirm) {
                $.ajax({
                    type: "DELETE",
                    url: "{{url('shop')}}/" + id,
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        swal("Deleted", "Shop deleted successfully", "success");
                        location.reload();
                    }
                });
            }
        });
    }

}
</script>
@endpush