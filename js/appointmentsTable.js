$(function(){


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
                            <button class="deleteButton">Delete</button>
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
                $('input[name="numberOfPages"]').val(book.number_of_pages)
                $('input[name="image"]').val(book.image)
                $('select[name="category"]').val(book.category_id)
            },
        });
    });


})