
$(document).ready(function () {
    $('#create').on('click', function () {
        var form = $('#create-form')[0] ?? null;
        if (!form) console.log('Something went wrong..');

        var url = $('#create-form').attr('action');
        if (form.checkValidity() && form.reportValidity()) {
            var formData = new FormData(form);
            // Perform AJAX request
            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                contentType: false, // Don't set content type
                processData: false, // Don't process the data
                dataType: 'json',
                success: function (response) {
                    showAlert(response.message, response.success ? 'primary' : 'danger');
                    if (response.success) {
                        $('#createTreatmentModal').modal('hide');
                        setTimeout(function () {
                            location.reload();
                        }, 1000);
                    }
                },
                error: function (error) {
                    // Handle the error
                    console.error('Error submitting the form:', error);
                    showAlert('Something went wrong..!', 'danger');
                },
                complete: function (response) {
                    // This will be executed regardless of success or error
                    console.log('Request complete:', response);

                }
            });


        } else {
            showAlert('Form is not valid. Please check your inputs.', 'danger');
        }
    });

    $('.edit-treatment-btn').on('click', async function () {
        var id = $(this).data('id');
        await getById(id);
    })

    $('.delete-treatment-btn').on('click', async function () {
        var id = $(this).data('id');
        var is_confirm = confirm('Are you sure,Do you want to delete?');
        if (is_confirm) await deleteById(id);
    })

    $('#update-treatment').on('click', function () {

        // Get the form element
        var form = $('#update-form')[0];
        form.reportValidity();

        // Check form validity
        if (form.checkValidity()) {
            // Serialize the form data
            var url = $('#update-form').attr('action');
            var formData = new FormData($('#update-form')[0]);

            // Perform AJAX request
            $.ajax({
                url: url,
                type: 'POST',
                data: formData, // Form data
                dataType: 'json',
                contentType: false,
                processData: false,
                success: function (response) {
                    showAlert(response.message, response.success ? 'primary' : 'danger', 'edit-alert-container');
                    if (response.success) {
                        $('#edit-treatment-modal').modal('hide');
                        setTimeout(function () {
                            location.reload();
                        }, 1000);
                    }
                },
                error: function (error) {
                    // Handle the error
                    console.error('Error submitting the form:', error);
                },
                complete: function (response) {
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

async function getById(id) {
    var url = $('#update-form').attr('action');

    // Perform AJAX request
    $.ajax({
        url: url,
        type: 'GET',
        data: {
            id: id,
            action: 'get_treatment'
        }, // Form data
        dataType: 'json',
        success: function (response) {
            console.log(response);

            showAlert(response.message, response.success ? 'primary' : 'danger');
            if (response.success) {

                var id = response.data.id;
                var name = response.data.name;
                var edit_treatment_fee = response.data.treatment_fee;
                var edit_registration_fee = response.data.registration_fee;
                var description = response.data.description;
                var is_active = response.data.is_active;

                $('#edit-treatment-modal #id').val(id);
                $('#edit-treatment-modal #edit_name').val(name);
                $('#edit-treatment-modal #edit_treatment_fee').val(edit_treatment_fee);
                $('#edit-treatment-modal #edit_registration_fee').val(edit_registration_fee);
                $('#edit-treatment-modal #edit_description').val(description);
                $('#edit-treatment-modal #edit_is_active option[value="' + is_active + '"]').prop('selected', true);

                $('#edit-treatment-modal').modal('show');
            }
        },
        error: function (error) {
            // Handle the error
            console.error('Error submitting the form:', error);
        },
        complete: function (response) {
            // This will be executed regardless of success or error
            console.log('Request complete:', response);
        }
    });
}

async function deleteById(id) {
    var url = $('#update-form').attr('action');

    // Perform AJAX request
    $.ajax({
        url: url,
        type: 'GET',
        data: {
            id: id,
            action: 'delete_treatment'
        }, // Form data
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                setTimeout(function () {
                    location.reload();
                }, 1000);
            } else {
                showAlert(response.message, response.success ? 'primary' : 'danger', 'delete-alert-container');
            }
        },
        error: function (error) {
            // Handle the error
            console.error('Error submitting the form:', error);
        },
        complete: function (response) {
            // This will be executed regardless of success or error
            console.log('Request complete:', response);
        }
    });
}
