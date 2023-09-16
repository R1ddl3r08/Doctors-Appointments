$.ajax({
    type: 'GET',
    url: './php/getDoctors.php',
    dataType: 'json',
    success: function(response){
       const doctors = response.allDoctors
       const select = $('#doctor')

       doctors.forEach(function(doctor){
            let option = document.createElement('option')
            option.value = doctor.id
            option.text = `${doctor.first_name} ${doctor.last_name}`
            select.append(option)
       })
    } 
})

$.ajax({
    type: 'GET',
    url: './php/getServices.php',
    dataType: 'json',
    success: function(response){
        const services = response.allServices
        const select = $('#service')
 
        services.forEach(function(service){
            let option = document.createElement('option')
            option.value = service.id
            option.text = service.service
            select.append(option)
        })
    } 
})

$(function(){


    $('#setAppointmentForm').on('submit', function(event){
        event.preventDefault()

        $('.error').text('')

        $.ajax({
            type: 'POST',
            url: './php/setAppointment.php',
            data: $("#setAppointmentForm").serialize(),
            dataType: 'json',
            success: function(response){
               if(response.success){
                    $('#setAppointmentForm')[0].reset();
                    Swal.fire({
                        icon: 'success',
                        title: 'Appointment Set Successfully',
                        text: 'Your appointment has been successfully set.',
                        timer: 2000, 
                        showConfirmButton: false
                    })
               } else {
                    displayErrorMessages(response.errors);
               }
            } 
        })
    })

    function displayErrorMessages(errors) {
        for (let field in errors) {
            if (errors.hasOwnProperty(field)) {
                $('.' + field + 'Error').text(errors[field]);
            }
        }
    }


})