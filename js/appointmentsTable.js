$(function(){


    function getAllAppointments(){
        $.ajax({
            type: 'GET',
            url: './php/getAppointments.php',
            dataType: 'json',
            success: function(response){
                const table = document.getElementById('appointmentsTable')
                const message = document.getElementById('appointmentsMessage')
                if(response.allAppointments.length > 0){
                    table.style.display = 'block'
                    message.style.display = 'none'
                    response.allAppointments.forEach(function(appointment){
                        const tr = document.createElement('tr')
                        tr.innerHTML = `
                            <td>${appointment.id}</td>
                            <td>${appointment.patient_name}</td>
                            <td>${appointment.patient_tel}</td>
                            <td>${appointment.doctor_first_name} ${appointment.doctor_last_name}</td>
                            <td>${appointment.service_name}</td>
                            <td>${appointment.date}</td>
                            <td>${appointment.start_time}</td>
                            <td>${appointment.end_time}</td>
                            <td>${appointment.appointment_status}</td>
                            <td>
                                <button class="editButton" data-appointment-id="${appointment.id}">Edit</button>
                                <button class="deleteButton" data-appointment-id="${appointment.id}">Delete</button>
                            </td>
                        `
    
                        $('tbody').append(tr);
                    })
                } else {
                    message.style.display = 'block'
                    table.style.display = 'none'
                }
            } 
        })
    }

    getAllAppointments()

    $('tbody').on('click', '.editButton', function () {
        const appointmentId = $(this).data('appointment-id');
    
        $.ajax({
            type: 'GET',
            url: './php/getAppointment.php',
            data: { id: appointmentId },
            dataType: 'json',
            success: function (response) {
                const appointment = response.appointment
                $('#appointmentModal').show()

                $('input[name="appointmentId"]').val(appointment.id)
                $('input[name="patientName"]').val(appointment.patient_name)
                $('input[name="patientTel"]').val(appointment.patient_tel)
                $('select[name="doctor"]').val(appointment.doctor_id)
                $('select[name="service"]').val(appointment.doctor_service_id)
                $('input[name="date"]').val(appointment.date)
                $('input[name="startTime"]').val(appointment.start_time)
                $('input[name="endTime"]').val(appointment.end_time)
                $('select[name="appointmentStatus"]').val(appointment.appointment_status_id)
            },
        });
    });

    $(document).on('click', '.close', function(){
        $('.modal').hide();
        $('#setAppointmentForm')[0].reset();
        $('#editAppointmentForm')[0].reset();
    })

    $('#editAppointmentForm').on('submit', function(event){
        event.preventDefault()

        $.ajax({
            type: 'POST',
            url: './php/editAppointment.php',
            data: $("#editAppointmentForm").serialize(),
            dataType: 'json',
            success: function(response){
               if(response.success){
                   Swal.fire({
                       icon: 'success',
                       title: 'Appointment Updated Successfully',
                       text: 'Your appointment has been successfully updated.',
                       timer: 2000, 
                       showConfirmButton: false
                    })
                    $('#appointmentModal').hide()
                    $('#editAppointmentForm')[0].reset()
                    $('#setAppintmentForm')[0].reset()
               } else {
                    displayErrorMessages(response.errors);
               }
            } 
        })

        function displayErrorMessages(errors) {
            for (let field in errors) {
                if (errors.hasOwnProperty(field)) {
                    $('.' + field + 'Error').text(errors[field]);
                }
            }
        }
        
    })

    $('tbody').on('click', '.deleteButton', function () {
        const appointmentId = $(this).data('appointment-id')
    
        Swal.fire({
            title: 'Are you sure you want to delete?',
            text: 'This action cannot be undone!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: './php/deleteAppointment.php',
                    method: 'POST',
                    data: { appointmentId: appointmentId },
                    dataType: 'json',
                    success: function (response) {
                        console.log(response)
                        if(response.success){
                            Swal.fire('Deleted!', 'The appointment has been deleted.', 'success')
                            $(`.deleteButton[data-appointment-id='${appointmentId}']`).closest('tr').remove();
                        }
                    }
                });
            }
        });
    });


})