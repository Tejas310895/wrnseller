<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
            <h4 class="card-title">PAYMENTS HISTORY</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table text-center">
                        <thead class=" text-primary">
                            <th>
                            DATE
                            </th>
                            <th>
                            TOTAL AMOUNT
                            </th>
                            <th>
                            AMT RECEVIED
                            </th>
                            <th>
                            AMT BILLED
                            </th>
                            <th>
                            CREDIT
                            </th>
                            <th>
                            DEBIT
                            </th>
                            <th>
                            SUMMARY
                            </th>
                        </thead>

                        <tbody>
                        <?php 
                            $get_reports = "SELECT * FROM customer_orders where client_id='$client_id' GROUP BY CAST(del_date as DATE) order by del_date desc";
                            $run_reports = mysqli_query($con,$get_reports);
                            $counter = 0;
                            $ledger_balance = 0;
                            while($row_reports = mysqli_fetch_array($run_reports)){
                            $del_date = $row_reports['del_date'];
                            $delivery_date = date('Y-m-d',strtotime($del_date));
                            $display_delivery_date = date('d-M-Y',strtotime($del_date));

                            $get_order = "select * from customer_orders where CAST(del_date as DATE)='$delivery_date' and client_id='$client_id' and order_status='Delivered' and product_status='Deliver' group by invoice_no";
                            $run_order = mysqli_query($con,$get_order);
                            $order_total = 0;
                            while($row_order = mysqli_fetch_array($run_order)){

                                $ord_invoice_no = $row_order['invoice_no'];

                                $get_order_amt = "select sum(due_amount) as order_amt from customer_orders where invoice_no='$ord_invoice_no'";
                                $run_order_amt = mysqli_query($con,$get_order_amt);
                                $row_order_amt = mysqli_fetch_array($run_order_amt);

                                $order_amt = $row_order_amt['order_amt'];

                                $get_cust_dis = "select sum(discount_amount) as cust_dis from customer_discounts where invoice_no='$ord_invoice_no'";
                                $run_cust_dis = mysqli_query($con,$get_cust_dis);
                                $row_cust_dis = mysqli_fetch_array($run_cust_dis);
        
                                $cust_dis = $row_cust_dis['cust_dis'];

                                $order_total += $order_amt-$cust_dis;

                            }

                            $get_v_cash = "select * from vendor_cash where CAST(updated_date as DATE)='$delivery_date' and client_id='$client_id' and vendor_cash_status='paid'";
                            $run_v_cash = mysqli_query($con,$get_v_cash);
                            $v_cash_total = 0;
                            while($row_v_cash = mysqli_fetch_array($run_v_cash)){

                                $v_invoice_no = $row_v_cash['invoice_no'];

                                $get_v_amt = "select sum(due_amount) as v_amt from customer_orders where invoice_no='$v_invoice_no'";
                                $run_v_amt = mysqli_query($con,$get_v_amt);
                                $row_v_amt = mysqli_fetch_array($run_v_amt);

                                $v_amt = $row_v_amt['v_amt'];

                                $get_cust_dis = "select sum(discount_amount) as cust_dis from customer_discounts where invoice_no='$v_invoice_no'";
                                $run_cust_dis = mysqli_query($con,$get_cust_dis);
                                $row_cust_dis = mysqli_fetch_array($run_cust_dis);
        
                                $cust_dis = $row_cust_dis['cust_dis'];

                                $v_cash_total += $v_amt-$cust_dis;

                            }

                            $get_v_order_total = "select sum(vendor_due_amount) as order_total from customer_orders where CAST(del_date as DATE)='$delivery_date' and client_id='$client_id' and order_status='Delivered' and product_status='Deliver'";
                            $run_v_order_total = mysqli_query($con,$get_v_order_total);
                            $row_v_order_total = mysqli_fetch_array($run_v_order_total);

                            $v_order_total = $row_v_order_total['order_total'];

                            $get_v_credit = "select sum(amount) as v_credit from client_ledger where client_id='$client_id' and CAST(updated_date as DATE)='$delivery_date' and amt_type='credit'";
                            $run_v_credit = mysqli_query($con,$get_v_credit);
                            $row_v_credit = mysqli_fetch_array($run_v_credit);

                            $v_credit = $row_v_credit['v_credit'];

                            $get_v_debit = "select sum(amount) as v_debit from client_ledger where client_id='$client_id' and CAST(updated_date as DATE)='$delivery_date' and amt_type='debit'";
                            $run_v_debit = mysqli_query($con,$get_v_debit);
                            $row_v_debit = mysqli_fetch_array($run_v_debit);

                            $v_debit = $row_v_debit['v_debit'];

                            $ledger_balance += $v_order_total-$v_cash_total-$v_credit-$v_debit;

                            ?>
                            <tr>
                                <td>
                                    <?php echo date('d-M-Y',strtotime($del_date)); ?>
                                </td>
                                <td>
                                    <?php echo $order_total; ?>
                                </td>
                                <td>
                                    <?php echo $v_cash_total; ?>
                                </td>
                                <td>
                                    <?php echo $v_order_total; ?>
                                </td>
                                <td>
                                    <?php echo $v_credit; ?>
                                </td>
                                <td>
                                    <?php echo $v_debit; ?>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#ord<?php echo date('Ymd',strtotime($del_date)); ?>">
                                        VIEW ORDERS
                                    </button>
                                    <!-- Modal -->
                                    <div class="modal fade" id="ord<?php echo date('Ymd',strtotime($del_date)); ?>" tabindex="-1" role="dialog" aria-labelledby="ord<?php echo date('Ymd',strtotime($del_date)); ?>" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <table class="table">
                                                        <thead >
                                                            <tr>
                                                                <th>Invoice No.</th>
                                                                <th>Customer Name</th>
                                                                <th>Amount</th>
                                                            </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php 
                                                                
                                                                $get_order_sum = "select * from customer_orders where CAST(del_date as DATE)='$delivery_date' and client_id='$client_id' and order_status='Delivered' and product_status='Deliver' group by invoice_no";
                                                                $run_order_sum = mysqli_query($con,$get_order_sum);
                                                                while($row_order_sum = mysqli_fetch_array($run_order_sum)){

                                                                    $sum_invoice_no = $row_order_sum['invoice_no'];
                                                                    $customer_id = $row_order_sum['customer_id'];

                                                                    $get_cust_del = "select * from customers where customer_id='$customer_id'";
                                                                    $run_cust_del = mysqli_query($con,$get_cust_del);
                                                                    $row_cust_del = mysqli_fetch_array($run_cust_del);

                                                                    $customer_name = $row_cust_del['customer_name'];

                                                                    $get_order_sum_amt = "select sum(vendor_due_amount) as vendor_amount from customer_orders where invoice_no='$sum_invoice_no'";
                                                                    $run_order_sum_amt = mysqli_query($con,$get_order_sum_amt);
                                                                    $row_order_sum_amt = mysqli_fetch_array($run_order_sum_amt);

                                                                    $vendor_amount = $row_order_sum_amt['vendor_amount'];
                                                                
                                                                ?>
                                                                <tr>
                                                                    <td><?php echo $sum_invoice_no; ?></td>
                                                                    <td><?php echo $customer_name; ?></td>
                                                                    <td><?php echo $vendor_amount; ?></td>
                                                                </tr>
                                                                <?php } ?>
                                                            </tbody>
                                                    </table>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php }?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12 fixed-bottom bg-white">
        <h4 class="text-right">BALANCE : <?php echo "₹ ".round($ledger_balance, 2) ?></h4>
    </div>
</div>