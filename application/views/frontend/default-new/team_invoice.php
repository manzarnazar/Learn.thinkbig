<?php
$package_details = $this->db->where('id', $invoice['package_id'])->get('team_packages')->row_array();
$buyer_details = $this->user_model->get_all_user($invoice['user_id'])->row_array();
$course_details = $this->db->where('id', $package_details['course_id'])->get('course')->row_array();
$instructor_details = $this->user_model->get_all_user($package_details['user_id'])->row_array();
?>

<!------------ Invoice section start ----->
<section class="invoice">
    <div class="container print-content">
        <div class="invoice-heading mt-5">
            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                    <h3 class="text-uppercase"><?php echo get_phrase('invoice') ?></h3>
                    <div class="invoice-no">
                        <h6 class="invoice-color"><?php echo get_phrase('Invoice ID') ?> :</h6>
                        <h6>#<?php
                                $serial = $invoice['id'];
                                $padded_serial = str_pad($serial, 4, '0', STR_PAD_LEFT);
                                echo $padded_serial; ?>
                        </h6>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                    <div class="invioce-logo d-flex justify-content-end">
                        <a href="#"><img loading="lazy" src="<?php echo base_url('uploads/system/') . get_frontend_settings('dark_logo'); ?>" alt="" style="height: 55px; width: auto;"></a>
                    </div>
                </div>
            </div>
        </div>
        <div class="invoice-bill">

            <div class="row">
                <div class="col-lg-6 col-md-6 col-sm-7 col-8">
                    <div class="row">
                        <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                            <p><?php echo get_phrase('Billed To') ?>:</p>
                            <h6><?php echo ucfirst($buyer_details['first_name']) . ' ' . $buyer_details['last_name']; ?></h6>
                            <h6><?php echo $buyer_details['email']; ?></h6>
                            <h6><?php echo $buyer_details['address']; ?></h6>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 col-6">
                            <p><?php echo get_phrase('Date Of Issue') ?>:</p>
                            <h6><?php echo date('d-M-Y', $invoice['date_added']) ?></h6>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-5 col-4">
                    <div class="invoice-total text-end">
                        <p><?php echo get_phrase('Invoice Total') ?></p>
                        <h2><?php echo currency($invoice['paid_amount']); ?></h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="invoice-dec">
            <div class="invoice-bill--scroll-bar">
                <table class="table">
                    <thead class="invoice-2-table-head">
                        <tr>
                            <th class="pe-5" scope="col">
                                <h6><?php echo get_phrase('Course'); ?></h6>
                            </th>
                            <th scope="col">
                                <h6><?php echo get_phrase('Instructor'); ?></h6>
                            </th>
                            <th scope="col">
                                <h6 class="text-end"><?php echo get_phrase('QTY') ?></h6>
                            </th>
                            <th scope="col">
                                <h6 class="text-end"><?php echo get_phrase('Price') ?></h6>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="row">
                                <p><?php echo $course_details['title']; ?></p>
                                <p class="text-12px">
                                    <span><?php echo get_phrase('team'); ?> :</span>
                                    <span><?php echo $package_details['title']; ?></span>
                                </p>
                            </th>
                            <td>
                                <p><?php echo $instructor_details['first_name'] . ' ' . $instructor_details['last_name']; ?></p>
                            </td>
                            <td>
                                <p class="text-end">1</p>
                            </td>
                            <td>
                                <p class="text-end"><?php echo currency($invoice['paid_amount']) ?></p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="invoice-2-payment">
                <div class="row">
                    <div class="col-6">
                        <h5><?php echo get_phrase('Paid By'); ?>:</h5>
                        <h6><a href="#" class="badge bg-light"><?php echo ucfirst($invoice['payment_method']); ?></a></h6>
                    </div>
                    <div class="col-6">
                        <div class="row justify-content-end">
                            <div class="col-lg-6 col-12">
                                <div class="invoice-2-last-total">
                                    <div class="row">
                                        <div class="col-lg-7 col-md-7 col-sm-7 col-7">
                                            <h5><?php echo get_phrase('Subtotal') ?></h5>
                                            <h5><?php echo get_phrase('Tax'); ?></h5>
                                        </div>
                                        <div class="col-lg-1 col-md-1 col-sm-1 col-1">
                                            <h5 class="text-end">:</h5>
                                            <h5 class="text-end">:</h5>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-3 pe-0">
                                            <h4><?php echo currency($invoice['paid_amount']); ?></h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row justify-content-end">
                            <div class="col-lg-6 col-md-6 col-sm-6 col-12">
                                <div class="invoice-right-total">
                                    <div class="row">
                                        <div class="col-lg-7 col-md-7 col-sm-7 col-7">
                                            <h5 class="invoice-ml text-end"><?php echo get_phrase('Grand Total') ?></h5>
                                        </div>
                                        <div class="col-lg-1 col-md-1 col-sm-1 col-1">
                                            <h5 class="text-end">:</h5>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-3 col-3 pe-0">
                                            <h5 class="text-end"><?php echo currency($invoice['paid_amount']); ?></h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="print-btn print-d-none">
                <a href="#" onclick="window.print()"><i class="fa-solid fa-print"></i><?php echo get_phrase('Print'); ?></a>
                <a href="<?php echo site_url('home/purchase_history') ?>"><i class="fa-solid fa-arrow-left"></i><?php echo get_phrase('Back'); ?></a>
            </div>
        </div>
    </div>
</section>
<!------------ Invoice secton end -------->