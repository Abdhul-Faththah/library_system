<?php
require_once('../layouts/header.php');
include BASE_PATH . '/models/Payment.php';
include BASE_PATH . '/models/Treatment.php';

$paymentModel = new Payment();
$payments = $paymentModel->getAllWithTreatmentAndAppointment();
?>

<div class="container-xxl flex-grow-1 container-p-y">

    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Dashboard /</span> Payments </h4>
    <div class="card">

        <!-- /.card-header -->
        <div class="card-body p-0 table-responsive">

            <table class="table table-striped">
                <thead>
                    <tr>
                        <th style="width: 10px">#</th>
                        <th class="">Appointment ID</th>
                        <th class="">Treatment</th>
                        <th class="">Registration Fee</th>
                        <th class="">Registration Fee Paid</th>
                        <th class="">Treatment Fee</th>
                        <th class="">Treatment Fee Paid</th>
                        <th class="">Quantity</th>
                        <th class="">Total</th>
                        <th class="text-center" style="width: 200px">Options</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($payments as $c) {
                        $total = $c['registration_fee'] + (($c['treatment_fee'] ?? 1) * ($c['quantity'] ?? 1));
                    ?>
                        <tr>
                            <td> <?= $c['id'] ?? ""; ?> </td>
                            <td> <?= '#' . ($c['appointment_id'] ?? "") . '-' . ($c['appointment_no'] ?? "") ?> </td>
                            <td> <?= $c['treatment_name'] ?? ""; ?> </td>
                            <td> <?= $c['registration_fee'] ?? 0.00; ?> </td>
                            <td> <?= $c['registration_fee_paid'] ? '<span class="badge bg-label-success me-1">Paid</span>' : '<span class="badge bg-label-warning me-1">Pending</span>'; ?> </td>
                            <td> <?= $c['treatment_fee'] ?? 0.00; ?> </td>
                            <td> <?= $c['treatment_fee_paid'] ? '<span class="badge bg-label-success me-1">Paid</span>' : '<span class="badge bg-label-warning me-1">Pending</span>'; ?> </td>
                            <td> <?= $c['quantity'] ?? 1; ?> </td>
                            <td> <?= $total ?? 0.00; ?> </td>
                            <td>
                                <div>
                                    <button type="button" class="btn btn-sm btn-info m-2 active payment-modal" data-treatement-name="<?= $c['treatment_name']; ?>" data-id="<?= $c['id']; ?>" data-treatment-fee-paid="<?= $c['treatment_fee_paid'] == 1 ? 1 : 0; ?>" data-registration-fee-paid="<?= $c['registration_fee_paid'] == 1 ? 1 : 0; ?>" data-treatment-fee="<?= $c['treatment_fee'] ?? 0; ?>" data-registration-fee="<?= $c['registration_fee'] ?? 0; ?>" data-quantity="<?= $c['quantity'] ?? 0; ?>" data-bs-toggle="modal" data-bs-target="#paymentModal">
                                        Pay
                                    </button>
                                    <button type="button" class="btn btn-sm btn-primary m-2 active invoice-modal" data-treatement-name="<?= $c['treatment_name']; ?>" data-id="<?= $c['id']; ?>" data-treatment-fee-paid="<?= $c['treatment_fee_paid'] == 1 ? 1 : 0; ?>" data-registration-fee-paid="<?= $c['registration_fee_paid'] == 1 ? 1 : 0; ?>" data-treatment-fee="<?= $c['treatment_fee'] ?? 0; ?>" data-registration-fee="<?= $c['registration_fee'] ?? 0; ?>" data-quantity="<?= $c['quantity'] ?? 0; ?>" data-bs-toggle="modal" data-bs-target="#invoiceModal">
                                        Invoice
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
        <!-- /.card-body -->
    </div>
</div>


<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCenterTitle">Appointment Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="payment-form" action="<?= url('services/ajax_functions.php') ?>">
                    <div class="row g-1">
                        <div class="col mb-4 form-group">
                            <label for="treatment_name">Treatment</label>
                            <input type="text" class="form-control" id="treatment_name" value="" disabled>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col mb-4 form-group">
                            <label for="registration_fee">Registration Fee</label>
                            <input type="text" class="form-control registration_fee" disabled>
                        </div>
                        <div class="col mb-4 form-group">
                            <label for="registration_fee_paid_status"></label>
                            <div><span class="badge bg-label-success me-1 m-2 ml-3 text-right" id="registration_fee_paid_status">Paid</span></div>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col mb-4 form-group">
                            <label for="treatment_fee">Treatment Fee</label>
                            <input type="text" class="form-control treatment_fee" disabled>
                        </div>
                        <div class="col mb-4 form-group">
                            <label for="treatment_fee_paid_status"></label>
                            <div class="d-block">
                                <span class="badge bg-label-warning me-1 m-2 ml-3 text-right" id="treatment_fee_paid_status">Pending</span>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col mb-4 form-group">
                            <label for="total_treatment_fee">Total Treatment fee </label>
                            <input type="text" class=" form-control total_treatment_fee" disabled>
                        </div>
                        <div class="col mb-4 form-group">
                            <label for="quantity">Per Tooth Or Quantity</label>
                            <input type="number" class="form-control" id="quantity" name="quantity" value="1" required>
                        </div>
                    </div>
                    <div class="form-check ml-3 mb-3">
                        <input class="form-check-input" type="checkbox" value="" id="treatment_fee_paid_check">
                        <label class="form-check-label" for="treatment_fee_paid_check"> Pay Treatment Fee </label>
                    </div>
                    <div class="row g-1">
                        <div class="col mb-4 form-group text-right">
                            <div id="fees" class="fees card p-3">
                                <span class='d-block'><i class='text-muted '>Registration Fee:</i>
                                    LKR <span class="registration_fee">1000</span>
                                </span>
                                <span class='d-block'><i class='text-muted '>Total Treatment Fee:</i>
                                    LKR <span class="total_treatment_fee">5000</span>
                                </span>
                                <span class='d-block'><i class='text-muted '><b>Total:</i>
                                    LKR <span class="total_fee">6000</b></span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" id="payment_id" name="payment_id">
                    <input type="hidden" id="treatment_fee_paid" name="treatment_fee_paid">
                    <input type="hidden" name="action" value="payment-save">

                    <div class="mb-3 mt-3">
                        <div id="alert-container"></div>
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    Close
                </button>
                <button type="button" id="payment-save" class="btn btn-primary">Save</button>
            </div>
            </form>
        </div>
    </div>
</div>

<!-- Invoice Modal -->
<div class="modal fade" id="invoiceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="invoiceModalLabel">Invoice #<span class="invoice-id"></span></h5>
                <!-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> -->
            </div>
            <div class="modal-body">
                <!-- Invoice Content -->
                <div class="row">
                    <div class="col-md-6">
                        <h6>From:</h6>
                        <p>Tooth Care</p>
                        <p>123 Main Street</p>
                        <p>Puttalam, North West, 61300</p>
                    </div>
                </div>

                <table class="table mt-3">
                    <thead>
                        <tr>
                            <th scope="col"></th>
                            <th class="text-center">Quantity</th>
                            <th scope="col">Price</th>
                            <th scope="col">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Registration Fee </td>
                            <td class="text-center"><span class="quantity">1</span></td>
                            <td>LKR <span class="registration_fee">0.00</span></td>
                            <td>LKR <span class="registration_fee">0.00</span></td>
                        </tr>
                        <tr>
                            <td>Treatment Fee (<span class="treatment_name"></span>)</td>
                            <td class="text-center"><span class="quantity">2</span></td>
                            <td>LKR <span class="treatment_fee">0.00</span></td>
                            <td>LKR <span class="total_treatment_fee">0.00</span></td>
                        </tr>
                    </tbody>
                </table>

                <div class="text-right" style="text-align: right;margin-right: 60px;">
                    <p><strong>Total: LKR <span class="total">0.00</span></strong></p>
                </div>
                <!-- End Invoice Content -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    Close
                </button>
                <button type="button" class="btn btn-primary" id="printBtn">Print</button>
            </div>
        </div>
    </div>
</div>
<!-- End Invoice Modal -->

<?php require_once('../layouts/footer.php'); ?>

<!-- JavaScript -->
<script>
    $(document).ready(function($) {
        // Event handler for opening the modal
        $('.invoice-modal').on('click', function(e) {
            e.preventDefault();
            var payment_id = $(this).data('id');
            var treatmentName = $(this).data('treatement-name');
            var treatmentFee = $(this).data('treatment-fee');
            var registrationFee = $(this).data('registration-fee');
            var quantity = $(this).data('quantity');
            var total_treatment_fee = calculateTotalTreatmentFee(treatmentFee, quantity);
            var total = total_treatment_fee + registrationFee;

            // Set values to modal body elements
            $('#invoiceModal .treatment_name').text(treatmentName);
            $('#invoiceModal .registration_fee').text(registrationFee);
            $('#invoiceModal .treatment_fee').text(treatmentFee);
            $('#invoiceModal .total_treatment_fee').text(total_treatment_fee);
            $('#invoiceModal .quantity').text(quantity);
            $('#invoiceModal .invoice-id').text(payment_id);
            $('#invoiceModal .total').text(total);
        });

        // Event handler for opening the modal
        $('.payment-modal').on('click', function(e) {
            e.preventDefault();

            // Get data attributes from the clicked button
            var payment_id = $(this).data('id');
            var treatmentName = $(this).data('treatement-name');
            var treatmentFeePaid = $(this).data('treatment-fee-paid');
            var registrationFeePaid = $(this).data('registration-fee-paid');
            var treatmentFee = $(this).data('treatment-fee');
            var registrationFee = $(this).data('registration-fee');
            var quantity = $(this).data('quantity');
            var total_treatment_fee = calculateTotalTreatmentFee(treatmentFee, quantity);

            if (treatmentFeePaid) {
                $('#treatment_fee_paid_check').prop('checked', true);
            }

            // Set values to modal body elements
            $('#treatment_name').val(treatmentName);
            $('#quantity').val(quantity);
            $('#payment_id').val(payment_id);
            $('.registration_fee').val(registrationFee);
            $('.treatment_fee').val(treatmentFee);
            $('.total_treatment_fee').val(total_treatment_fee);

            // Set values for fees
            updateFees(registrationFee, registrationFeePaid, treatmentFee, treatmentFeePaid, calculateTotalTreatmentFee(treatmentFee, quantity));
        });

        // Function to calculate total treatment fee
        function calculateTotalTreatmentFee(treatmentFee, quantity) {
            return parseFloat(treatmentFee) * parseFloat(quantity);
        }

        // Function to update fees based on payment status
        function updateFees(registrationFee, registrationFeePaid, treatmentFee, treatmentFeePaid, totalFee) {

            // Update registration fee badge
            updateBadgeStatus('registration_fee_paid_status', registrationFeePaid);

            // Update treatment fee badge
            updateBadgeStatus('treatment_fee_paid_status', treatmentFeePaid);

            var feesContainer = $('#fees');
            feesContainer.find('.registration_fee').text(registrationFee);
            feesContainer.find('.total_treatment_fee').text(treatmentFee);
            feesContainer.find('.total_fee').text(totalFee);
        }

        // Event handler for treatment dropdown change
        $('#quantity').on('input', function() {
            // Update total treatment fee based on quantity and treatment fee
            var quantity = $(this).val();
            var treatmentFee = $('.treatment_fee').val();
            var totalTreatmentFee = calculateTotalTreatmentFee(treatmentFee, quantity);
            $('.total_treatment_fee').val(totalTreatmentFee);

            // Update total fee
            updateTotalFee(totalTreatmentFee);
        });

        // Function to update total fee
        function updateTotalFee(totalTreatmentFee) {
            var registrationFee = $('.registration_fee').val();
            var totalFee = (parseFloat(totalTreatmentFee) + parseFloat(registrationFee)).toFixed(2);

            // Update total fee in the fees container
            var feesContainer = $('#fees');
            feesContainer.find('.total_fee').text(totalFee);
        }

        // Function to update badge status
        function updateBadgeStatus(badgeId, isPaid) {
            var badge = $('#' + badgeId);
            badge.text(isPaid == 1 ? 'Paid' : 'Pending');

            // Dynamically set background color and text color based on payment status
            if (isPaid == 1) {
                badge.removeClass('bg-label-warning').addClass('bg-label-success');
            } else {
                badge.removeClass('bg-label-success').addClass('bg-label-warning');
            }
        }

        // Event handler for the checkbox
        $('#treatment_fee_paid_check').on('change', function() {
            // If the checkbox is checked, set the value to the hidden field
            if ($(this).prop('checked')) {
                $('#treatment_fee_paid').val(1);
            } else {
                $('#treatment_fee_paid').val(0);
            }
        });

        // Handle modal button click
        $('#payment-save').on('click', function() {

            // Get the form element
            var form = $('#payment-form')[0];
            $('#payment-form')[0].reportValidity();

            // Check form validity
            if (form.checkValidity()) {

                // Serialize the form data
                var formData = $('#payment-form').serialize();
                var formAction = $('#payment-form').attr('action');

                // Perform AJAX request
                $.ajax({
                    url: formAction,
                    type: 'POST',
                    data: formData, // Form data
                    dataType: 'json',
                    success: function(response) {
                        showAlert(response.message, response.success ? 'primary' : 'danger');
                        $('.payment-modal').modal('hide');
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    },
                    error: function(error) {
                        // Handle the error
                        console.error('Error submitting the form:', error);
                    },
                    complete: function(response) {
                        // This will be executed regardless of success or error
                        console.log('Request complete:', response);
                    }
                });
            } else {
                var message = ('Form is not valid. Please check your inputs.');
                showAlert(message, 'danger');
            }
        });


    });
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.js"></script>
<script>
    $(document).ready(function() {
        // Event handler for the "Print" button
        $('#printBtn').on('click', function() {
            // Get the modal content
            var modalHeader = $('#invoiceModal .modal-header')[0].cloneNode(true);
            var modalBody = $('#invoiceModal .modal-body')[0].cloneNode(true);

            // Create a container div for both header and body
            var container = document.createElement('div');
            container.appendChild(modalHeader);
            container.appendChild(modalBody);

            // Create a configuration object for html2pdf
            var pdfOptions = {
                margin: 10,
                filename: 'invoice.pdf',
                image: {
                    type: 'jpeg',
                    quality: 0.98
                },
                html2canvas: {
                    scale: 2
                },
                jsPDF: {
                    unit: 'mm',
                    format: 'a4',
                    orientation: 'portrait'
                }
            };

            // Generate PDF
            html2pdf(container, pdfOptions);
        });
    });
</script>