@extends('layouts.app' ,[ 'pageSlug' => 'Tags List' ])
@section('content')

    <div class="row">
        <div class="col-lg-12 col-sm-12">
            <div class="m-b-30">
                <div class="">
                    <div class="row">
                        <div class="col-md-6">     </div>
                        <div class="col-md-6" style="text-align: right">  <a href="{{route('new-tag-view')}}" ><button type="button"  class="btn btn-secondary"><i class="fa fa-plus-circle"></i> NEW TAG</button></a>   </div>
                    </div>

                    <p class="text-muted"></p>
                    <div class="">
                        <table class="table-striped table-bordered compact sunriseTable"
                               role="grid" aria-describedby="datatable_info"
                               style="width:100%">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Text</th>
                                <th>Icon</th>
                                <th>Date Created</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($tags as $tag)
                            <tr id="{{$tag->id}}"  class="odd clickable"  data-toggle="modal" data-target="#actionsDialog{{$tag->id}}">
                                <td><span class="tabledit-span tabledit-identifier">{{$tag->id}}</span><input class="tabledit-input tabledit-identifier" type="hidden" name="id" value="1" disabled=""></td>
                                <td class="tabledit-view-mode"><span class="tabledit-span">{{$tag->text}}</span></td>
                                <td class="tabledit-view-mode"><span class="tabledit-span"> <i style="color:{{$tag->color}}" class="{{$tag->icon}}  fa-lg"></i></span></td>
                                <td class="tabledit-view-mode"><span class="tabledit-span">{{$tag->created_at }}</span></td>

                                </tr>


                            <div class="modal fade" tabindex="-1" role="dialog" id="actionsDialog{{$tag->id}}">

                                <input type="hidden" name="case_id" value="{{$tag->id}}">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Tag Actions</h5>

                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">

                                            <div class="form-group row" style="margin-bottom: 0px">
                                                <div class="form-group col-6 " style="margin-bottom: 0px">
                                                    <label for="doctor">Name: </label>
                                                    <h5 id="doctor"><b>{{$tag->text}}</b></h5>
                                                </div>
                                                <div class="form-group col-6 " style="margin-bottom: 0px">
                                                    <label for="doctor">Icon: </label>
                                                    <h5 id="doctor"><b><i style="color:{{$tag->color}}" class="{{$tag->icon}}  fa-lg"></i></b></h5>
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
                                                        <a  href="{{route('edit-tag-view', $tag->id)}}">
                                                            <button type="button" class="btn btn-warning "><i class="fa-solid fa-pen-to-square"></i> Edit Tag</button>
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
