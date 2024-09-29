jQuery(document).ready(function ($) {
    $(".presenceFilters input").each(function(){

        let val = $(this).val()
        let input = $(this)
        checkValue(val , input)
        $(this).on("change",function(){
            console.log(val)
            checkValue(val , input)
            
        })
    })
    function checkValue(val , input){
        $("." + val ).each(function(){
            if( $(input).is(":checked")){
                $(this).show()
            }
            else{
                $(this).hide()
            }
            calcNumero();
        })
    }
    function calcNumero(){
        $(".prenceTable table tbody").each(function(){
            let count = 1;
            $(this).find("tr:visible .num").each(function(){
                $(this).text(count)
                count++
            })
        })
    }
    $('#dlToPsf').on("click" , function(){
        console.log("click")
        const elem = document.getElementById("pdfContent")
        html2pdf().from(elem).toPdf().save($(this).attr("data-coursename"))
    })
    $("#addLine").on('click' , function(){
        let lastCount = $("#pdfContent tbody tr:last-of-type td:first-of-type").text()
        if (+lastCount <= 15){
            let line = $("<tr><td>" + (+lastCount+1) + "</td><td></td><td></td></tr>")
            $(line).on("click" , function(){
                $(this).remove()
            })
            $("#pdfContent tbody").append(line)

        }
    })
    $("#pdfContent tbody tr").on("click" , function(){
        $(this).remove()
    })
})