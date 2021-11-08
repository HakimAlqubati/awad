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
                    <form role="form" class="form-edit-add"
                        action="{{ $edit ? url('update-user/' . $dataTypeContent->getKey()) : url('add-user') }}"
                        method="POST" enctype="multipart/form-data">
                        <!-- PUT Method if we are editing -->
                        @if ($edit)
                            {{ method_field('PUT') }}
                        @endif

                        <!-- CSRF TOKEN -->
                        {{ csrf_field() }}

                        <div class="panel-body">

                            <div class="row">
                                <div class="col-md-4">
                                    Username :
                                    <?php if($edit){ ?>
                                    <input type="hidden" name="uid" value='<?php echo $userDataObject->id; ?>'  class="form-control">
                                    <input type="text" name="username" class="form-control" value='<?php echo $userDataObject->name; ?>'
                                        required placeholder="Username">
                                    <?php }else{  ?>
                                    <input type="text" placeholder="Username" name="username" class="form-control"
                                        required>
                                    <?php } ?>
                                </div>
                                <div class="col-md-4">
                                    Phone number :
                                    <?php if($edit){ ?>
                                    <input type="number" name="phonenumber" class="form-control"
                                        value='<?php echo $userDataObject->phone; ?>' placeholder="Phone number" required>
                                    <?php }else{ ?>
                                    <input type="number" name="phonenumber" placeholder="Phone number"
                                        class="form-control" value='' required>
                                    <?php } ?>
                                </div>
                                <div class="col-md-4">
                                    Email :
                                    <?php if($edit){ ?>
                                    <input type="email" name="email" placeholder="Email" class="form-control"
                                        value='<?php echo $userDataObject->email; ?>' required>
                                    <?php }else{  ?>
                                    <input type="email" name="email" placeholder="Email" class="form-control" value=''
                                        required>
                                    <?php } ?>

                                </div>


                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    Password :
                                    <input type="password" name="password" class="form-control" value='' required>
                                </div>

                                <div class="col-md-4">
                                    Password confirmation :
                                    <input type="password" class="form-control" value=''>
                                </div>

                                <div class="col-md-4">

                                    Role :

                                    <?php if($edit){ ?>

                                    <select type="role" name="role" class="form-control" required>

                                        <option value="<?php echo $userDataObject->role_id; ?>"><?php echo $userDataObject->role_name; ?></option>

                                    </select>
                                    <?php }else { ?>
                                    <select type="role" name="role" class="form-control" required>
                                        <?php foreach ($roles as $key => $value) { ?>
                                        <option value="<?php echo $value->id; ?>"><?php echo $value->name; ?></option>
                                        <?php  } } ?>
                                    </select>

                                </div>
                            </div>




                        </div><!-- panel-body -->
                        <div class="row" style="text-align: center;">
                            <input type="submit" class="btn btn-primary" value="Save" style="width: 200px" />
                        </div>

                    </form>

                    <iframe id="form_target" name="form_target" style="display:none"></iframe>
                    <form id="my_form" action="{{ route('voyager.upload') }}" target="form_target" method="post"
                        enctype="multipart/form-data" style="width:0;height:0;overflow:hidden">
                        <input name="image" id="upload_file" type="file" onchange="$('#my_form').submit();this.value='';">
                        <input type="hidden" name="type_slug" id="type_slug" value="{{ $dataType->slug }}">
                        {{ csrf_field() }}
                    </form>

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
