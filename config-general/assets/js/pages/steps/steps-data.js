var wizard = $("#wizard").steps();
 
// Add step
wizard.steps("add", {
    title: "HTML code", 
    content: "<strong>HTML code</strong>"
});

var form = $("#example-advanced-form").show();

form.steps({
    headerTag: "h3",
    bodyTag: "fieldset",
    transitionEffect: "slideLeft",
    onInit: function (event, currentIndex) {
        // Agregar el botón extra al iniciar el wizard
        addExtraButton();
    },
    onStepChanging: function (event, currentIndex, newIndex)
    {
        // Allways allow previous action even if the current form is not valid!
        if (currentIndex > newIndex)
        {
            return true;
        }
        // Forbid next action on "Warning" step if the user is to young
        if (newIndex === 3 && Number($("#age-2").val()) < 18)
        {
            return false;
        }
        // Needed in some cases if the user went back (clean up)
        if (currentIndex < newIndex)
        {
            // To remove error styles
            form.find(".body:eq(" + newIndex + ") label.error").remove();
            form.find(".body:eq(" + newIndex + ") .error").removeClass("error");
        }
        form.validate().settings.ignore = ":disabled,:hidden";
        return form.valid();
    },
    onStepChanged: function (event, currentIndex, priorIndex)
    {
        // Used to skip the "Warning" step if the user is old enough.
        if (currentIndex === 2 && Number($("#age-2").val()) >= 18)
        {
            form.steps("next");
        }
        // Used to skip the "Warning" step if the user is old enough and wants to the previous step.
        if (currentIndex === 2 && priorIndex === 3)
        {
            form.steps("previous");
        }

        // Asegurarse de que el botón extra esté en cada paso
        addExtraButton();
    },
    onFinishing: function (event, currentIndex)
    {
        form.validate().settings.ignore = ":disabled";
        return form.valid();
    },
    onFinished: function (event, currentIndex)
    {
        document.example_advanced_form.submit();
    }
}).validate({
    errorPlacement: function errorPlacement(error, element) { element.before(error); },
    rules: {
        confirm: {
            equalTo: "#password-2"
        }
    }
});

// Función para agregar el botón extra en el primer <li> solo si el div #extraButtonTrigger está presente
function addExtraButton() {
    // Verificar si el div #extraButtonTrigger existe en la página
    var trigger = $("#extraButtonTrigger");
    if (trigger.length > 0 && $("#extraButtonLi").length === 0) {
        // Crear un nuevo <li> para el botón extra
        var extraButtonLi = $("<li>", { id: "extraButtonLi", class: "extra-button" });

        // Crear el botón extra dentro del nuevo <li>
        $("<a>", {
            id: "extraButton",
            href: trigger.data("url"),
            text: trigger.data("text"),
            class: trigger.data("btn")
        }).appendTo(extraButtonLi);

        // Insertar el nuevo <li> con el botón extra al inicio de la lista de acciones
        $(".actions ul").prepend(extraButtonLi);
    }
}

$("#example-vertical").steps({
    headerTag: "h3",
    bodyTag: "section",
    transitionEffect: "slideLeft",
    stepsOrientation: "vertical"
});