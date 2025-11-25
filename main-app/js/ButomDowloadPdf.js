/**
 * Función asíncrona para generar un documento PDF a partir de contenido HTML.
 * Permite dividir el contenido en múltiples partes si supera un límite especificado.
 *
 * @param {string} contenido - El contenido que será incluido en el PDF. Puede ser un un texto con el id de contenido de donde estan los div page.
 * @param {string} title - El título base para el archivo PDF generado.
 * @param {number} [max] - Número máximo de páginas por PDF parcial. Si no se especifica o es undefined, genera un solo archivo.
 */
async function generatePDF(contenido, title, max) {
    
    const paginas = [...document.querySelectorAll('.page')];// Obtiene una lista de las páginas que se encuentran en el documento. 
   
    document.getElementById('overlay').style.display = 'flex'; // Muestra un overlay de carga mientras se genera el PDF.    
    
    let cantidad = paginas.length; // Determina la cantidad de páginas en el documento.

    // Si no se especifica max o es undefined, dividir en archivos de 15 estudiantes
    if (max === undefined || max === null) {
        const maxPorArchivo = 15; // Número de estudiantes por archivo
        
        if (cantidad <= maxPorArchivo) {
            // Si hay 15 o menos estudiantes, generar un solo archivo
            await generatePDFUnico(contenido, title);
        } else {
            // Si hay más de 15 estudiantes, dividir en múltiples archivos
            let start = 0;
            let end = 0;
            let count = 0;
            let archivoNum = 1;

            for (let i = 0; i < cantidad; i++) {
                count++;
                end = i;
                
                // Generar un archivo cuando se alcance el límite o sea el último estudiante
                if (count === maxPorArchivo || i === cantidad - 1) {
                    await generatePDFPart(start, end, `${title}_Parte${archivoNum}`, cantidad);
                    start = end + 1;
                    count = 0;
                    archivoNum++;
                }
            }
        }
        document.getElementById("overlay").style.display = "none";
        return;
    }

    // Caso: hay más de una página en el documento y se especificó un límite.
    if (cantidad > 1 && max > 0) {
        let start = 0;  // Índice inicial de un bloque de páginas.
        let end = 0;    // Índice final de un bloque de páginas.
        let count = 0;  // Contador de páginas procesadas en el bloque actual.

        // Recorre las páginas del documento.
        for (let i = 0; i < cantidad; i++) {
            count++;
            end = i;
            // Genera un PDF parcial si se alcanza el límite máximo de páginas.
            if (count === max) {
                await generatePDFPart(start, end, `${title}_${start}-${end}`, cantidad);
                console.log(`${start} hasta ${end}`);
                start = end + 1;  // Ajusta el índice inicial para el siguiente bloque.
                count = 0;        // Reinicia el contador de páginas del bloque.
            }
        }

        // Genera un PDF parcial para las páginas restantes si las hay.
        if (start <= end) {
            await generatePDFPart(start, end, `${title}_${start}-${end}`, cantidad);
        }
    } 
    // Caso: hay exactamente una página en el documento.
    else if (cantidad === 1) {
        await generatePDFUnico(contenido, title); // Genera un único PDF.
    }

    // Oculta el overlay de carga una vez que se complete la generación del PDF.
    document.getElementById("overlay").style.display = "none";
}


async function generatePDFPart(start, end, title, totalEstudiantes = null) {
    const paginas = [...document.querySelectorAll('.page')];
    const contenido = document.getElementById('contenido');
    
    if (!totalEstudiantes) {
        totalEstudiantes = paginas.length;
    }

    if (paginas.length === 0) {
        console.error('No hay páginas para generar PDF');
        return;
    }
    
    console.log(`Generando PDF parte: estudiantes ${start + 1} a ${end + 1} de ${totalEstudiantes}`);

    // Ocultar botones flotantes antes de generar PDF
    const botones = document.querySelectorAll('.btn-flotante');
    botones.forEach(btn => btn.style.display = 'none');

    // Crear un contenedor temporal con solo las páginas que necesitamos
    const tempContainer = document.createElement('div');
    tempContainer.style.position = 'fixed';
    tempContainer.style.top = '0';
    tempContainer.style.left = '0';
    tempContainer.style.width = '210mm';
    tempContainer.style.backgroundColor = 'white';
    tempContainer.style.zIndex = '999999';
    tempContainer.style.overflow = 'visible';
    
    // Clonar profundamente solo las páginas del rango
    for (let i = start; i <= end && i < paginas.length; i++) {
        const pagina = paginas[i];
        const clone = pagina.cloneNode(true);
        clone.style.display = 'block';
        clone.style.visibility = 'visible';
        clone.style.pageBreakAfter = 'always';
        clone.style.margin = '0';
        clone.style.padding = '0';
        tempContainer.appendChild(clone);
    }
    
    // Agregar temporalmente al body
    document.body.appendChild(tempContainer);
    
    // Hacer scroll para que esté en la vista
    tempContainer.scrollIntoView({ behavior: 'instant', block: 'start' });

    const options = {
        margin: [8, 15, 8, 8],
        filename: title + '.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { 
            scale: 2,
            useCORS: true,
            logging: true,
            width: tempContainer.scrollWidth,
            height: tempContainer.scrollHeight,
            windowWidth: tempContainer.scrollWidth || 1200,
            windowHeight: tempContainer.scrollHeight || window.innerHeight,
            x: 0,
            y: 0,
            scrollX: 0,
            scrollY: 0,
            allowTaint: true,
            backgroundColor: '#ffffff'
        },
        jsPDF: { 
            unit: 'mm', 
            format: 'a4', 
            orientation: 'portrait',
            compress: true
        },
        pagebreak: { 
            mode: ['avoid-all', 'css', 'legacy'],
            before: '.page',
            after: '.page',
            avoid: ['tr', 'td', 'table']
        }
    };
    
    try {
        // Esperar a que el contenedor se renderice completamente
        await new Promise(resolve => setTimeout(resolve, 1000));
        
        console.log('Iniciando generación de PDF para:', title);
        console.log('Páginas en contenedor:', tempContainer.children.length);
        console.log('Contenedor height:', tempContainer.scrollHeight);
        console.log('Contenedor width:', tempContainer.scrollWidth);
        
        // Verificar que hay contenido
        if (tempContainer.children.length === 0) {
            throw new Error('No hay páginas en el contenedor temporal');
        }
        
        // Generar PDF desde el contenedor temporal
        await html2pdf().set(options).from(tempContainer).save();
        
        console.log('PDF generado exitosamente');
        
        // Limpiar contenedor temporal
        if (document.body.contains(tempContainer)) {
            document.body.removeChild(tempContainer);
        }
        
        // Restaurar botones
        botones.forEach(btn => btn.style.display = '');
        
        Swal.fire({
            position: "bottom-end",
            title: 'Generando PDF',
            text: 'Se generó archivo ' + title + ' (Estudiantes ' + (start + 1) + '-' + (end + 1) + ' de ' + totalEstudiantes + ')',
            icon: 'success',
            showCancelButton: false,
            confirmButtonText: 'Si!',
            timer: 3500
        });
    } catch (error) {
        console.error('Error al generar PDF:', error);
        console.error('Stack:', error.stack);
        
        // Limpiar contenedor temporal en caso de error
        if (document.body.contains(tempContainer)) {
            document.body.removeChild(tempContainer);
        }
        
        // Restaurar botones
        botones.forEach(btn => btn.style.display = '');
        
        Swal.fire({
            position: "bottom-end",
            title: 'Error',
            text: 'Hubo un problema al generar el archivo ' + title + '. Error: ' + error.message,
            icon: 'error',
            timer: 5000
        });
    }
}


async function generatePDFUnico(contenido, title) {
    const paginas = [...document.querySelectorAll('.page')];
    const cantidad = paginas.length;
    
    if (cantidad === 0) {
        Swal.fire({
            position: "bottom-end",
            title: 'Error',
            text: 'No hay contenido para generar PDF',
            icon: 'error',
            timer: 3000
        });
        document.getElementById("overlay").style.display = "none";
        return;
    }
    
    // Ocultar botones flotantes antes de generar PDF
    const botones = document.querySelectorAll('.btn-flotante');
    botones.forEach(btn => btn.style.display = 'none');
    
    // Si hay una sola página, generar directamente
    if (cantidad === 1) {
        const element = document.getElementById(contenido);
        
        // Asegurar que el elemento sea visible
        const originalDisplay = element.style.display;
        element.style.display = 'block';
        
        const options = {
            margin: [8, 15, 8, 8],
            filename: title + '.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { 
                scale: 2,
                useCORS: true,
                logging: true,
                windowWidth: 1200,
                scrollX: 0,
                scrollY: 0,
                allowTaint: true,
                backgroundColor: '#ffffff'
            },
            jsPDF: { 
                unit: 'mm', 
                format: 'a4', 
                orientation: 'portrait',
                compress: true
            },
            pagebreak: { 
                mode: ['avoid-all', 'css', 'legacy'],
                before: '.page',
                after: '.page',
                avoid: ['tr', 'td']
            }
        };
        
        try {
            window.scrollTo(0, 0);
            await new Promise(resolve => setTimeout(resolve, 300));
            
            await html2pdf().set(options).from(element).save();
            
            // Restaurar display original
            element.style.display = originalDisplay;
            
            // Restaurar botones
            botones.forEach(btn => btn.style.display = '');
            document.getElementById("overlay").style.display = "none";
            
            Swal.fire({
                position: "bottom-end",
                title: 'Generando PDF',
                text: 'Se generó archivo ' + title,
                icon: 'success',
                showCancelButton: false,
                confirmButtonText: 'Si!',
                timer: 3500
            });
        } catch (error) {
            console.error('Error al generar PDF:', error);
            
            // Restaurar display original en caso de error
            element.style.display = originalDisplay;
            
            botones.forEach(btn => btn.style.display = '');
            document.getElementById("overlay").style.display = "none";
            Swal.fire({
                position: "bottom-end",
                title: 'Error',
                text: 'Hubo un problema al generar el PDF. Error: ' + error.message,
                icon: 'error',
                timer: 5000
            });
        }
        return;
    }
    
    // Si hay múltiples páginas, usar el elemento original directamente
    const element = document.getElementById(contenido);
    
    // Asegurar que el elemento sea visible
    const originalDisplay = element.style.display;
    element.style.display = 'block';
    
    const options = {
        margin: [8, 15, 8, 8],
        filename: title + '.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { 
            scale: 2,
            useCORS: true,
            logging: true,
            windowWidth: element.scrollWidth || 1200,
            windowHeight: element.scrollHeight || window.innerHeight,
            scrollX: 0,
            scrollY: 0,
            allowTaint: true,
            backgroundColor: '#ffffff'
        },
        jsPDF: { 
            unit: 'mm', 
            format: 'a4', 
            orientation: 'portrait',
            compress: true
        },
        pagebreak: { 
            mode: ['avoid-all', 'css', 'legacy'],
            before: '.page',
            after: '.page',
            avoid: ['tr', 'td', 'table']
        }
    };
    
    try {
        // Forzar scroll al inicio para capturar todo
        window.scrollTo(0, 0);
        
        // Esperar un momento para que el scroll se complete
        await new Promise(resolve => setTimeout(resolve, 500));
        
        await html2pdf().set(options).from(element).save();
        
        // Restaurar display original
        element.style.display = originalDisplay;
        
        // Restaurar botones
        botones.forEach(btn => btn.style.display = '');
        document.getElementById("overlay").style.display = "none";
        
        Swal.fire({
            position: "bottom-end",
            title: 'Generando PDF',
            text: 'Se generó archivo ' + title + ' con ' + cantidad + ' estudiantes',
            icon: 'success',
            showCancelButton: false,
            confirmButtonText: 'Si!',
            timer: 3500
        });
    } catch (error) {
        console.error('Error al generar PDF:', error);
        
        // Restaurar display original en caso de error
        element.style.display = originalDisplay;
        
        // Restaurar botones en caso de error
        botones.forEach(btn => btn.style.display = '');
        document.getElementById("overlay").style.display = "none";
        
        Swal.fire({
            position: "bottom-end",
            title: 'Error',
            text: 'Hubo un problema al generar el PDF. Error: ' + error.message,
            icon: 'error',
            timer: 5000
        });
    }
}