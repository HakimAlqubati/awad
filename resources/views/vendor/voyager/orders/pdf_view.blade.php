<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"
    integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
<style>
    .a4-paper {
        height: 29.7cm;
        /* width: 21cm; */
        position: relative;
    }

    .footer {
        bottom: 0;
        position: absolute;
    }

</style>
<div class="page-content read container-fluid a4-paper">
    <div class="row">
        <div class="col-md-12">

            <div class="panel panel-bordered" style="padding-bottom:5px;">



                <table class="table table-striped">
                    <tbody>
                        <tr>
                            <td>
                                Order ID : #<?php echo $finalResult[0]->orderId; ?>
                            </td>
                            <td>
                                Created by : {{ $finalResult[0]->createdByUserName }}
                            </td>
                            <td>
                                Date : {{ $finalResult[0]->createdAt }}
                            </td>

                        </tr>
                        {{-- <tr>
                            <td>
                                Order state : {{ $finalResult[0]->state_name }}
                            </td>
                            <td></td>
                            <td>
                                ( {{ $finalResult[0]->restricted_state_name }})
                            </td>


                        </tr> --}}
                        <tr>
                            <td colspan="3">
                                Details:
                                <p> {{ $finalResult[0]->desc }} </p>
                            </td>
                        </tr>
                    </tbody>












                </table>

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Product id</th>
                            <th>Product name</th>
                            <th>Unit</th>
                            <th>Quantity</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php  foreach($finalResult as $key => $value) { 
                            $index = 0;
                            if($key > 0) {
                            ?>
                        <tr>
                            <td>{{ $key }}</td>
                            <td>{{ $value->product_id }}</td>
                            <td>{{ $value->product_name }}</td>
                            <td>{{ $value->unit_name }}</td>
                            <td>{{ $value->qty }}</td>
                            <td>{{ $value->price }}</td>
                        </tr>
                        <?php
                            }
                     } ?>
                    </tbody>
                </table>

            </div>
        </div>
        <table style="/*width: 100%;*/" class="footer">
            <tr>
                <td style="width:50%;">Store manager: <h6><?php echo $finalResult[0]->manager_name; ?> </h6>
                </td>

                <td style="width: 50%"> Created by:
                    <h6> <?php echo $finalResult[0]->createdByUserName; ?> -
                        Manager of branch: <?php echo $finalResult[0]->branch_name; ?>
                    </h6>
                </td>
            </tr>
        </table>
    </div>

</div>
