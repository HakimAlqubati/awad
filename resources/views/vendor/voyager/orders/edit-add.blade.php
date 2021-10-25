@php
$edit = !is_null($dataTypeContent->getKey());
$add = is_null($dataTypeContent->getKey());

@endphp

@extends('voyager::master')

@section('css')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@stop

@section('page_title', __('voyager::generic.' . ($edit ? 'edit' : 'add')) . ' ' .
    $dataType->getTranslatedAttribute('display_name_singular'))

@section('page_header')
    <h1 class="page-title">
        <i class="{{ $dataType->icon }}"></i>
        {{ __('voyager::generic.' . ($edit ? 'edit' : 'add')) . ' ' . $dataType->getTranslatedAttribute('display_name_singular') }}
    </h1>
    @include('voyager::multilingual.language-selector')
@stop

@section('content')
    <div class="page-content edit-add container-fluid">
        <div class="row">
            <div class="col-md-12">

                <div class="panel panel-bordered">
                    <!-- form start -->
                   
                    <div class="container">
                        <form action="{{ url('update-order', [$dataTypeContent->getKey()]) }}" method="POST">
                            {{ method_field('PUT') }}
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-md-4">
                                    Order id :
                                    <input type="number" class="form-control" value='<?php echo $arrayOrder['id']; ?>' readonly>
                                </div>


                                <div class="col-md-4">
                                    Order state :
                                    <select class="form-select" name="request_state_id" aria-label="Default select example" style=" 
                                                                                                    height: 33px;
                                                                                                    border-radius: 5px;
                                                                                                    width: 250px;
                                                                                                    text-align: center;">
                                        <?php foreach ($requestStates as   $value) {
                                         
                                       ?>
                                        <?php if($arrayOrder['request_state_id'] == $value->id ) { ?>
                                        <option value="<?php echo $value->id; ?>" selected> <?php echo $value->name; ?> </option>
                                        <?php }else{ ?>
                                        <option value="<?php echo $value->id; ?>"> <?php echo $value->name; ?> </option>
                                        <?php }  }?>
                                    </select>
                                </div>



                                <div class="col-md-4">
                                    Date :
                                    <input type="date" class="form-control" value='<?php echo date_format($arrayOrder['created_at'], 'Y-m-d'); ?>' readonly>
                                </div>

                            </div>


                            <div class="row">

                                <div class="col-md-4">
                                    Created by :
                                    <input type="hidden" value='<?php echo $arrayOrder['created_by']; ?>'>
                                    <input type="text" class="form-control" value='<?php echo $arrayOrder['user_name']; ?>' readonly>
                                </div>


                                <div class="col-md-4">
                                    Restrected :

                                    <select class="form-select" name="restricted_state_id" aria-label="Default select example" style="
                                                                                                    height: 33px;
                                                                                                    border-radius: 5px;
                                                                                                    width: 250px;
                                                                                                    text-align: center;">

                                        <?php foreach ($destrectedStates as   $value) {
                                              ?>
                                        <?php if($arrayOrder['restricted_state_id'] == $value->id ) { ?>
                                        <option value="<?php echo $value->id; ?>" selected> <?php echo $value->name; ?> </option>
                                        <?php }else{ ?>
                                        <option value="<?php echo $value->id; ?>"> <?php echo $value->name; ?> </option>
                                        <?php }  }?>
                                    </select>

                                </div>

                            </div>
                            <div class="row">


                                <div class="form-group">
                                    <label for="details">Details : </label>
                                    <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" readonly>
                                                                            <?php echo $arrayOrder['desc']; ?>

                                                                        </textarea>
                                </div>

                            </div>

                            <div class="row" style="text-align: center;">
                                <input type="submit" class="btn btn-primary" value="Save"  style="width: 200px"/>
                            </div>

                        </form>
                    </div>




                </div>
            </div>
        </div>
    </div>

    <div class="modal fade modal-danger" id="confirm_delete_modal">
        <div class="modal-dialog">
            <div class="modal-content">

                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><i class="voyager-warning"></i> {{ __('voyager::generic.are_you_sure') }}
                    </h4>
                </div>

                <div class="modal-body">
                    <h4>{{ __('voyager::generic.are_you_sure_delete') }} '<span class="confirm_delete_name"></span>'</h4>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                        data-dismiss="modal">{{ __('voyager::generic.cancel') }}</button>
                    <button type="button" class="btn btn-danger"
                        id="confirm_delete">{{ __('voyager::generic.delete_confirm') }}</button>
                </div>
            </div>
        </div>
    </div>
    <!-- End Delete File Modal -->
@stop


@section('javascript')
    <script>
        var params = {};
        var $file;

        function deleteHandler(tag, isMulti) {
            return function() {
                $file = $(this).siblings(tag);

                params = {
                    slug: '{{ $dataType->slug }}',
                    filename: $file.data('file-name'),
                    id: $file.data('id'),
                    field: $file.parent().data('field-name'),
                    multi: isMulti,
                    _token: '{{ csrf_token() }}'
                }

                $('.confirm_delete_name').text(params.filename);
                $('#confirm_delete_modal').modal('show');
            };
        }

        $('document').ready(function() {
            $('.toggleswitch').bootstrapToggle();

            //Init datepicker for date fields if data-datepicker attribute defined
            //or if browser does not handle date inputs
            $('.form-group input[type=date]').each(function(idx, elt) {
                if (elt.hasAttribute('data-datepicker')) {
                    elt.type = 'text';
                    $(elt).datetimepicker($(elt).data('datepicker'));
                } else if (elt.type != 'date') {
                    elt.type = 'text';
                    $(elt).datetimepicker({
                        format: 'L',
                        extraFormats: ['YYYY-MM-DD']
                    }).datetimepicker($(elt).data('datepicker'));
                }
            });

            @if ($isModelTranslatable)
                $('.side-body').multilingual({"editing": true});
            @endif

            $('.side-body input[data-slug-origin]').each(function(i, el) {
                $(el).slugify();
            });

            $('.form-group').on('click', '.remove-multi-image', deleteHandler('img', true));
            $('.form-group').on('click', '.remove-single-image', deleteHandler('img', false));
            $('.form-group').on('click', '.remove-multi-file', deleteHandler('a', true));
            $('.form-group').on('click', '.remove-single-file', deleteHandler('a', false));

            $('#confirm_delete').on('click', function() {
                $.post('{{ route('voyager.' . $dataType->slug . '.media.remove') }}', params, function(
                    response) {
                    if (response &&
                        response.data &&
                        response.data.status &&
                        response.data.status == 200) {

                        toastr.success(response.data.message);
                        $file.parent().fadeOut(300, function() {
                            $(this).remove();
                        })
                    } else {
                        toastr.error("Error removing file.");
                    }
                });

                $('#confirm_delete_modal').modal('hide');
            });
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@stop
