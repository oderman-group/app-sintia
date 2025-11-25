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

    // Si no se especifica max o es undefined, generar un solo PDF sin límite
    if (max === undefined || max === null) {
        await generatePDFUnico(contenido, title);
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
                await generatePDFPart(start, end, `${title}_${start}-${end}`);
                console.log(`${start} hasta ${end}`);
                start = end + 1;  // Ajusta el índice inicial para el siguiente bloque.
                count = 0;        // Reinicia el contador de páginas del bloque.
            }
        }

        // Genera un PDF parcial para las páginas restantes si las hay.
        if (start <= end) {
            await generatePDFPart(start, end, `${title}_${start}-${end}`);
        }
    } 
    // Caso: hay exactamente una página en el documento.
    else if (cantidad === 1) {
        await generatePDFUnico(contenido, title); // Genera un único PDF.
    }

    // Oculta el overlay de carga una vez que se complete la generación del PDF.
    document.getElementById("overlay").style.display = "none";
}


async function generatePDFPart(start, end,title) {
    

    const paginas = [...document.querySelectorAll('.page')];
    const paginasVisibles = paginas.slice(start, end);

    // Crear un contenedor temporal con los elementos visibles
    const tempContainer = document.createElement('div');
    paginasVisibles.forEach(estudiante => tempContainer.appendChild(estudiante.cloneNode(true)));

    const options = {
        margin: [8, 15, 8, 8], // top: 8, right: 15, bottom: 8, left: 8
        filename: title+'.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' }
    };
    espera = await html2pdf().set(options).from(tempContainer).save();
    Swal.fire({
            position: "bottom-end",
            title: 'Generando PDF',
            text: 'Sé generó archivo desde la pagina '+start+ ' hasta la '+end,
            icon: 'success',
            showCancelButton: false,
            confirmButtonText: 'Si!',
            cancelButtonText: 'No!',
            timer: 3500
        });
}


async function generatePDFUnico(contenido,title) {
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
    
    // Si hay una sola página, generar directamente
    if (cantidad === 1) {
        const element = document.getElementById(contenido);
        const options = {
            margin: [8, 15, 8, 8],
            filename: title + '.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { 
                scale: 2,
                useCORS: true,
                logging: false
            },
            jsPDF: { 
                unit: 'mm', 
                format: 'a4', 
                orientation: 'portrait'
            },
            pagebreak: { 
                mode: ['avoid-all', 'css', 'legacy'],
                before: '.page',
                after: '.page',
                avoid: ['tr', 'td']
            }
        };
        await html2pdf().set(options).from(element).save();
        Swal.fire({
            position: "bottom-end",
            title: 'Generando PDF',
            text: 'Se generó archivo ' + title,
            icon: 'success',
            showCancelButton: false,
            confirmButtonText: 'Si!',
            timer: 3500
        });
        return;
    }
    
    // Si hay múltiples páginas, procesarlas todas juntas pero con mejor manejo de saltos
    const element = document.getElementById(contenido);
    const options = {
        margin: [8, 15, 8, 8],
        filename: title + '.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { 
            scale: 2,
            useCORS: true,
            logging: false,
            windowWidth: 1200
        },
        jsPDF: { 
            unit: 'mm', 
            format: 'a4', 
            orientation: 'portrait'
        },
        pagebreak: { 
            mode: ['avoid-all', 'css', 'legacy'],
            before: '.page',
            after: '.page',
            avoid: ['tr', 'td', 'table']
        }
    };
    
    try {
        await html2pdf().set(options).from(element).save();
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
        Swal.fire({
            position: "bottom-end",
            title: 'Error',
            text: 'Hubo un problema al generar el PDF. Intente usar la opción de Imprimir.',
            icon: 'error',
            timer: 5000
        });
    }
}