<style>
#imageDiv {
    background-image: url(image.jpg);
    background-repeat: no-repeat;
    background-position: 10px 25px;
}
</style>
<div class="controller">
    <div class="row">
        <div id="imageDiv" class="col-sm-12">
            <img style="width: 100%;" id="theimage" src="{{asset('/uploads/store_images/')}}/<?= $shop->image; ?>"
                alt="" />
        </div>
    </div>
    <br />
    <div class="row">
        <div class="col-sm-6">Shop Name :</div>
        <div class="col-sm-6">{{$shop->name}}</div>
    </div>
    <div class="row">
        <div class="col-sm-6">Shop Description :</div>
        <div class="col-sm-6">{{$shop->description}}</div>
    </div>
    <div class="row">
        <div class="col-sm-6">email :</div>
        <div class="col-sm-6">{{$shop->email}}</div>
    </div>
    <div class="row">
        <div class="col-sm-6">Phone Number :</div>
        <div class="col-sm-6">{{$shop->phoneNumber}}</div>
    </div>
    <div class="row">
        <div class="col-sm-6">Address :</div>
        <div class="col-sm-6">{{$shop->address}}</div>
    </div>
    <div class="row">
        <div class="col-sm-6">Street :</div>
        <div class="col-sm-6">{{$shop->street}}</div>
    </div>
    <div class="row">
        <div class="col-sm-6">City :</div>
        <div class="col-sm-6">{{$shop->city}}</div>
    </div>
    <div class="row">
        <div class="col-sm-6">State :</div>
        <div class="col-sm-6">{{$shop->state}}</div>
    </div>
    <div class="row">
        <div class="col-sm-6">Country :</div>
        <div class="col-sm-6">{{$shop->country}}</div>
    </div>
    <div class="row">
        <div class="col-sm-6">Break :</div>
        <div class="col-sm-6">
            @if(!empty($shop->shopBreakTimings))
            {{$shop->shopBreakTimings->startTime}} - {{$shop->shopBreakTimings->endTime}}
            @else
            --
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">Working Days :</div>
        <div class="col-sm-6">
            @if(!empty($shop->shopFunctionalTimings))
            @foreach($shop->shopFunctionalTimings as $timing)
            <strong>
                {{$timing->days}} :
            </strong>
            {{$timing->startTime}} - {{$timing->endTime}}<br>
            @endforeach
            @else
            --
            @endif
        </div>
    </div>
</div>