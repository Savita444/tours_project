<?php if(isset($_GET['view'])): 
require_once('../../config.php');
endif;?>
<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<?php 
if(!isset($_GET['id'])){
    $_settings->set_flashdata('error','No order ID Provided.');
    redirect('admin/?page=orders');
}
$order = $conn->query("SELECT o.*,concat(c.firstname,' ',c.lastname) as client FROM `orders` o inner join clients c on c.id = o.client_id where o.id = '{$_GET['id']}' ");
if($order->num_rows > 0){
    foreach($order->fetch_assoc() as $k => $v){
        $$k = $v;
    }
}else{
    $_settings->set_flashdata('error','Order ID provided is Unknown');
    redirect('admin/?page=orders');
}
?>

<div class="card card-outline card-primary">
    <div class="card-body">
        <div class="conitaner-fluid">
            <p><b>Client Name: <?php echo $client ?></b></p>
            <p><b>Delivery Address: <?php echo $delivery_address ?></b></p>
            <p><b>Mobile Number: <?php echo $mobile_number ?></b></p>
            <p><b>Email: <?php echo $email ?></b></p>
            <!-- <p><b>Traveller Details: <?php echo $traveller_details ?></b></p> -->
            <?php 
            // Assuming you already have $traveller_details variable containing the JSON string
            $traveller_details_array = json_decode($traveller_details, true); // Decode JSON into an associative array
            ?>

            <p><b>Traveller Details:</b></p>
            <?php if(!empty($traveller_details_array)): ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Sr. No.</th>
                            <th>Name</th>
                            <th>Gender</th>
                            <th>Date of Birth</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $serial_no = 1; // Initialize the serial number
                            foreach($traveller_details_array as $traveller): ?>
                            <tr>
                                <td><?php echo $serial_no++; ?></td>
                                <td><?php echo htmlspecialchars($traveller['name']); ?></td>
                                <td><?php echo htmlspecialchars($traveller['gender']); ?></td>
                                <td><?php echo htmlspecialchars($traveller['date_of_birth']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No traveller details available.</p>
            <?php endif; ?>
            <table class="table-striped table table-bordered">
                <colgroup>
                    <col width="15%">
                    <col width="15%">
                    <col width="30%">
                    <col width="20%">
                    <col width="20%">
                </colgroup>
                <thead>
                    <tr>
                        <th>QTY</th>
                        <th>Unit</th>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $olist = $conn->query("SELECT o.*,p.product_name FROM order_list o inner join products p on o.product_id = p.id where o.order_id = '{$id}' ");
                        while($row = $olist->fetch_assoc()):
                    ?>
                    <tr>
                        <td><?php echo $row['quantity'] ?></td>
                        <td><?php echo $row['unit'] ?></td>
                        <td><?php echo $row['product_name'] ." ({$row['size']}) " ?></td>
                        <td class="text-right"><?php echo number_format($row['price']) ?></td>
                        <td class="text-right"><?php echo number_format($row['price'] * $row['quantity']) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan='4'  class="text-right">Total</th>
                        <th class="text-right"><?php echo number_format($amount) ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="row">
            <div class="col-6">
                <p>Payment Method: <?php echo $payment_method ?></p>
                <p>Payment Status: <?php echo $paid == 0 ? '<span class="badge badge-light">Unpaid</span>' : '<span class="badge badge-success">Paid</span>' ?></p>
            </div>
            <div class="col-6 row row-cols-2">
                <div class="col-3">Order Status:</div>
                <div class="col-9">
                <?php 
                    switch($status){
                        case '0':
                            echo '<span class="badge badge-light text-dark">Pending</span>';
	                    break;
                        case '1':
                            echo '<span class="badge badge-primary">Packed</span>';
	                    break;
                        case '2':
                            echo '<span class="badge badge-warning">Out for Delivery</span>';
	                    break;
                        case '3':
                            echo '<span class="badge badge-success">Delivered</span>';
	                    break;
                        default:
                            echo '<span class="badge badge-danger">Cancelled</span>';
	                    break;
                    }
                ?>
                </div>
                <?php if(!isset($_GET['view'])): ?>
                <div class="col-3"></div>
                <div class="col">
                    <button type="button" id="update_status" class="btn btn-sm btn-flat btn-primary">Update Status</button>
                </div>
                <?php endif; ?>
                
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
</div>
<?php if(isset($_GET['view'])): ?>
<style>
    #uni_modal>.modal-dialog>.modal-content>.modal-footer{
        display:none;
    }
</style>
<?php endif; ?>
<script>
    $(function(){
        $('#update_status').click(function(){
            uni_modal("Update Status", "./orders/update_status.php?oid=<?php echo $id ?>&status=<?php echo $status ?>")
        })
    })
</script>