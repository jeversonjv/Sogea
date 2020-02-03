$(document).ready(function () {
    //datatables
    $('#tabela').DataTable({
        "sPaginationType": "first_last_numbers",
        "oLanguage": {
            "sProcessing": "Processando...",
            "sLengthMenu": "Mostrar _MENU_ registros",
            "sZeroRecords": "Não foram encotrados resultados",
            "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
            "sInfoEmpty": "Mostrando de 0 até 0 de 0 registros",
            "sInfoFiltered": "(filtrado de _MAX_ registros no total)",
            "sInfoPostFix": "",
            "sSearch": "Buscar:",
            "sUrl": "",
            "oPaginate": {
                "sFirst": "Primeiro",
                "sPrevious": "Anterior",
                "sNext": "Seguinte",
                "sLast": "Último"
            }
        }
    });
    //wrapper 
    if (toggle() == null) {
        sessionStorage.setItem("status", 1);
    }
    function setToggle() {
        if (toggle() == 1) {
            $("#wrapper").removeAttr("class");
        } else {
            $("#wrapper").attr("class", "toggled");
        }
    }
    $(".menu-toggle").click(function (e) {
        if (toggle() == 1) {
            sessionStorage.setItem("status", 2);
        } else {
            sessionStorage.setItem("status", 1);
        }
        setToggle();
        e.preventDefault();
    });
    function toggle() {
        return sessionStorage.getItem("status");
    }
    setToggle();

});
