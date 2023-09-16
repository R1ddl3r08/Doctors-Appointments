$(function(){


    function handleHashchange(){
        if(location.hash == '' || location.hash == '#'){
            $('.form-container').show()
            $('.appointmentsTableContainer').hide()
        } else if (location.hash == '#appointments') {
            $('.form-container').hide()
            $('.appointmentsTableContainer').show()
        }
    }

    
    $(window).on('hashchange', handleHashchange)
    
    handleHashchange()

})