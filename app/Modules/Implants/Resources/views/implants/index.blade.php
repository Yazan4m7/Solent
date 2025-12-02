@extends('layouts.app' ,[ 'pageSlug' => 'Implant List'])
@section('content')

    <div class="row">
        <div class="col-lg-12 col-sm-12">
            <div class=" m-b-30">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">       </div>
                        <div class="col-md-6" style="text-align: right">  <a href="{{route('new-implant-view')}}" ><button type="button"  class="btn btn-secondary"><i class="fa fa-plus-circle"></i> NEW IMPLANT</button></a>   </div>
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
                                <th>Date Created</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($implants as $implant)
                            <tr id="{{$implant->id}}"  class="odd clickable"  data-toggle="modal" data-target="#actionsDialog{{$implant->id}}">
                                <td><span class="tabledit-span tabledit-identifier">{{$implant->id}}</span><input class="tabledit-input tabledit-identifier" type="hidden" name="id" value="1" disabled=""></td>
                                <td class="tabledit-view-mode"><span class="tabledit-span">{{$implant->name}}</span></td>
                                <td class="tabledit-view-mode"><span class="tabledit-span">{{$implant->created_at }}</span></td>

                                </td>

                             </tr>

                            <div class="modal fade" tabindex="-1" role="dialog" id="actionsDialog{{$implant->id}}">

                                <input type="hidden" name="case_id" value="{{$implant->id}}">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Implant Actions</h5>

                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">

                                            <div class="form-group row" style="margin-bottom: 0px">
                                                <div class="form-group col-6 " style="margin-bottom: 0px">
                                                    <label for="doctor">Name: </label>
                                                    <h5 id="doctor"><b>{{$implant->name}}</b></h5>
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
                                                        <a  href="{{route('edit-implant-view', $implant->id)}}">
                                                            <button type="button" class="btn btn-warning "><i class="fa-solid fa-pen-to-square"></i> Edit Implant</button>
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
