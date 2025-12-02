@extends('layouts.app' ,[ 'pageSlug' => 'EXTERNAL LABS'])

@section('content')
    <form  class="kt-form" method="GET" action="{{route('labs-index')}}">
    <div class="col-lg-12 col-sm-12">

        <div class="row" style="padding-left: 10px;padding-top: 10px;padding-bottom: 0px">

            <div class="col-lg-3 col-md-3 ">
                <div class="kt-subheader__search" style="">
                    <label>From:</label>
                    <input type="date" class="form-control" name="from" value="{{$from ?? ''}}">
                </div>
            </div>
            <div class="col-lg-3 col-md-3 ">
                <div class="kt-subheader__search" style="">
                    <label>To:</label>
                    <input type="date" class="form-control" name="to" value="{{$to ?? ''}}">
                </div>
            </div>
            <div class="col-lg-3 col-md-3 ">

                @if(isset($labs))
                    <div class="kt-subheader__search" style="width:100%">
                        <label>Lab:</label>
                        <select style="width:100%"  class="selectpicker form-control clearOnAll" multiple name="labs[]" id="doctor" data-container="body" data-live-search="true" title="All" data-hide-disabled="true">

                            <option value="all">All</option>
                            @foreach($labs as $lab)
                                <option value="{{$lab->id}}" {{(isset($selectedLabsIds) && in_array($lab->id ,$selectedLabsIds)) ? 'selected' : ''}}>{{$lab->name}}</option>
                            @endforeach
                        </select>
                    </div>

                @endif

            </div>

            <div class="col-lg-3 col-md-3 ">

                <div class="kt-subheader__search" style="width:100%">
                    <label>&nbsp; &nbsp; </label>

                    <div class="kt-form__actions">
                        <button type="submit" class="btn btn-primary">Submit</button>

                    </div>
                </div>

            </div>


            </form>
    </div>

    <div class="row" style="padding:0px;">
        <div class="col-lg-12 col-sm-12">
            <div class=" ">
                <div class="">
                    <div class="row">
                        <div class="col-md-6">     </div>
                        <div class="col-md-6" style="text-align: right">  <a href="{{route('new-lab-view')}}" ><button type="button"  class="btn btn-secondary"><i class="fa fa-plus-circle"></i> </button></a>   </div>
                    </div>

                    <p class="text-muted"></p>
                    <div class="">
                        <table class="table-striped table-bordered compact sunriseTable"
                               role="grid" aria-describedby="datatable_info"
                               style="width:100%">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Address</th>
                                <th>Units Milled</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($selectedLabs as $lab)
                            <tr id="{{$lab->id}}" class="odd clickable"  data-toggle="modal" data-target="#actionsDialog{{$lab->id}}">
                                <td><span class="tabledit-span tabledit-identifier">{{$lab->id}}</span><input class="tabledit-input tabledit-identifier" type="hidden" name="id" value="1" disabled=""></td>
                                <td class="tabledit-view-mode"><span class="tabledit-span">{{$lab->name}}</span></td>
                                <td class="tabledit-view-mode"><span class="tabledit-span">{{$lab->phone ?? "N/A"}}</span></td>
                                <td class="tabledit-view-mode"><span class="tabledit-span">{{$lab->address ?? "N/A"}}</span></td>
                                <td class="tabledit-view-mode"><span class="tabledit-span">{{$lab->unitsMilled($from ?? -1,$to ?? -1)}}</span></td>


                               </tr>
                            <div class="modal fade" tabindex="-1" role="dialog" id="actionsDialog{{$lab->id}}">

                                <input type="hidden" name="case_id" value="{{$lab->id}}">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">External Lab Actions</h5>

                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">

                                            <div class="form-group row" style="margin-bottom: 0px">
                                                <div class="form-group col-6 " style="margin-bottom: 0px">
                                                    <label for="doctor">Name: </label>
                                                    <h5 id="doctor"><b>{{$lab->name}}</b></h5>
                                                </div>
                                                <div class="form-group col-6 " style="margin-bottom: 0px">
                                                    <label for="doctor">Phone: </label>
                                                    <h5 id="doctor"><b>{{$lab->phone}}</b></h5>
                                                </div>
                                            </div>

                                            <div class="form-group row" style="margin-bottom: 0px">
                                                <div class="form-group col-6 " style="margin-bottom: 0px">
                                                    <label for="doctor">Address: </label>
                                                    <h5 id="doctor"><b>{{$lab->address}}</b></h5>
                                                </div>
                                                <div class="form-group col-6 " style="margin-bottom: 0px">
                                                    <label for="doctor">Status: </label>
                                                    <h5 id="doctor"><b>{{$lab->unitsMilled($from ?? -1,$to ?? -1)}}</b></h5>
                                                </div>
                                            </div>
                                            <hr>

                                        </div>
                                        <div class="modal-footer fullBtnsWidth" >
                                            <div class="row"  style=" margin-right: 0px; margin-left: 0px;width:100%">


                                                <div class="row">
                                                    <!-------------------------
                                                     -------- Edit CASE --------
                                                     -------------------------->
                                                    <div class="col-12 padding5px" >
                                                        <a  href="{{route('edit-lab-view', $lab->id)}}">
                                                            <button type="button" class="btn btn-warning "><i class="fa-solid fa-pen-to-square"></i> Edit Lab</button>
                                                        </a></div>
                                                </div>

                                                <div class="col-12 padding5px" >
                                                    <button type="button" class="btn btn-secondary " data-dismiss="modal" style="width:100%">Cancel</button>
                                                </div>
                                            </div>


                                        </div>



                                    </div>
                                </div>

                            </div>

                            @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
