# 🧪 GUÍA RÁPIDA DE PRUEBAS - Feed Moderno

## ⚡ CAMBIOS APLICADOS AHORA

### ✅ 1. MODALES SIN DESCRIPCIÓN
- ❌ **ELIMINADO:** Campo de texto en modales
- ✅ **AHORA:** Solo adjuntar archivo/video/foto

### ✅ 2. BOTÓN DESHABILITADO POR DEFECTO
- ✅ Gris y no clickeable al abrir modal
- ✅ Se habilita SOLO al seleccionar algo
- ✅ Opacidad 0.5 → 1.0 al habilitar

### ✅ 3. CONTADOR ARREGLADO
- ✅ Excluye `not_estado = 2` (eliminadas)
- ✅ Solo cuenta publicadas y ocultas

### ✅ 4. BUSCADOR CON LOGS EXTREMOS
- ✅ Logs en CADA paso
- ✅ Inicialización doble (DOM ready + immediate)
- ✅ Alertas si falla

---

## 🧪 PRUEBA 1: BUSCADOR (LA MÁS IMPORTANTE)

### Pasos:
```
1. Abre la página de noticias
2. Abre F12 → Console
3. Debes ver inmediatamente:
   🔧 Iniciando configuración del buscador...
   ✅ Input de búsqueda encontrado
   ✅ Buscador configurado correctamente

4. Escribe "mate" en el buscador
5. Debes ver:
   ⌨️ Usuario escribió: mate
   ⏳ Esperando 800ms para buscar...
   (espera 1 segundo)
   🚀 Ejecutando búsqueda
   ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
   🔍 FUNCIÓN searchInFeed LLAMADA
   📝 Búsqueda: mate
   📊 feedModern existe? true
   🔄 Estado del feed reseteado
   🧹 Container limpiado
   ⏳ Skeleton mostrado
   📡 Fetching URL: ../compartido/noticias-publicaciones-cargar.php?busqueda=mate
   📥 Respuesta recibida - Status: 200
   📊 Datos parseados: {success: true, ...}
   ✅ POSTS ENCONTRADOS: X
   ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━

6. Debe mostrar solo posts con "mate" en título o descripción
```

### ❌ SI NO VES ESTOS LOGS:
```
Si NO ves:
🔧 Iniciando configuración del buscador...
✅ Input de búsqueda encontrado

ENTONCES:
- El JavaScript no se está cargando
- Verifica que barra-superior-noticias.php se incluya
- Limpia caché (Ctrl + Shift + R)
```

### ❌ SI NO BUSCA:
```
Si ves los logs pero NO busca:
1. Verifica Network tab (F12)
2. Busca: noticias-publicaciones-cargar.php
3. Ve la respuesta (debe ser JSON)
4. Verifica config-general/errores_local.log
```

---

## 🧪 PRUEBA 2: MODAL DE FOTO

### Pasos:
```
1. Click en botón "Foto" (📷)
2. Modal se abre
3. Verifica:
   ❌ NO debe haber campo de texto
   ✅ Solo área de carga de foto
   ✅ Botón "Publicar Foto" DESHABILITADO (gris, opacity 0.5)

4. Click en área de carga o arrastra foto
5. Console debe mostrar:
   📸 Preview foto llamado
   📁 Archivo seleccionado: foto.jpg 245678 image/jpeg
   ✅ Preview cargado exitosamente
   ✅ Botón Publicar habilitado

6. Debes ver:
   ✅ Preview de la foto
   ✅ Botón X rojo flotante arriba a la derecha
   ✅ Botón "Publicar Foto" HABILITADO (morado, opacity 1.0)

7. Click "Publicar Foto"
8. Post se crea con la imagen
```

---

## 🧪 PRUEBA 3: MODAL DE VIDEO

### Pasos:
```
1. Click en botón "Video" (🎥)
2. Modal se abre
3. Verifica:
   ❌ NO debe haber campo de texto
   ✅ Solo input de URL de YouTube
   ✅ Botón "Publicar Video" DESHABILITADO (gris)

4. Pega: https://www.youtube.com/watch?v=dQw4w9WgXcQ
5. Console debe mostrar:
   🎥 Preview video llamado: https://...
   🔍 Extrayendo ID de: https://...
   ✅ ID extraído: dQw4w9WgXcQ
   ✅ Mostrando preview del video
   ✅ Botón Publicar habilitado

6. Debes ver:
   ✅ Preview del video (iframe)
   ✅ Input muestra solo: dQw4w9WgXcQ
   ✅ Botón "Publicar Video" HABILITADO (morado)

7. Click "Publicar Video"
8. Post se crea con el video
```

---

## 🧪 PRUEBA 4: MODAL DE ARCHIVO

### Pasos:
```
1. Click en botón "Archivo" (📎)
2. Modal se abre
3. Verifica:
   ❌ NO debe haber campo de texto
   ✅ Solo área de carga de archivo
   ✅ Botón "Publicar Archivo" DESHABILITADO (gris)

4. Click o arrastra un PDF
5. Console debe mostrar:
   📎 Preview archivo llamado
   📁 Archivo seleccionado: documento.pdf 2456789 application/pdf
   ✅ Mostrando preview
   ✅ Botón Publicar habilitado

6. Debes ver:
   ✅ Icono rojo de PDF
   ✅ Nombre: documento.pdf
   ✅ Tamaño: 2.5 MB
   ✅ Botón X para quitar
   ✅ Botón "Publicar Archivo" HABILITADO (morado)

7. Click "Publicar Archivo"
8. Post se crea con el archivo
```

---

## 🚨 DEBUGGING COMPLETO

### Si el BUSCADOR NO funciona:

**PASO 1:** Abre F12 → Console al cargar la página

**Debes ver:**
```
✅ Feed Moderno inicializado
🔧 Iniciando configuración del buscador...
✅ Input de búsqueda encontrado
✅ Buscador configurado correctamente
```

**Si NO ves estos logs:**
- El script no se está ejecutando
- Verifica que `barra-superior-noticias.php` esté incluido
- Limpia caché del navegador

**PASO 2:** Escribe algo en el buscador

**Debes ver:**
```
⌨️ Usuario escribió: mate
⏳ Esperando 800ms para buscar...
(espera)
🚀 Ejecutando búsqueda
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
🔍 FUNCIÓN searchInFeed LLAMADA
📝 Búsqueda: mate
...todos los logs...
```

**Si NO ves estos logs:**
- El evento 'input' no se está disparando
- Intenta presionar Enter
- Verifica que el input tenga el ID correcto

**PASO 3:** Verifica Network (F12 → Network)

Debes ver una petición a:
```
noticias-publicaciones-cargar.php?busqueda=mate
```

Click en ella → Response tab → Debe ser JSON

**PASO 4:** Revisa archivo de errores

Abre: `config-general/errores_local.log`

Debes ver:
```
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
🔍 BÚSQUEDA RECIBIDA: 'mate'
📡 GET params: Array([busqueda] => mate)
📊 FILTRO APLICADO: AND (not_titulo LIKE '%mate%' OR not_descripcion LIKE '%mate%')
📝 SQL: SELECT ... WHERE ... AND (not_titulo LIKE '%mate%' ...
✅ Posts encontrados: 3
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
```

---

## 🚨 SI NADA FUNCIONA

### RESETEO COMPLETO:

1. **Limpia caché del navegador:**
   ```
   Ctrl + Shift + R (Windows)
   Cmd + Shift + R (Mac)
   ```

2. **Recarga la página:**
   ```
   F5 varias veces
   ```

3. **Verifica que los archivos existan:**
   ```
   main-app/compartido/noticias-agregar-foto-modal.php
   main-app/compartido/noticias-agregar-video-modal.php
   main-app/compartido/noticias-agregar-archivo-modal.php
   main-app/compartido/barra-superior-noticias.php
   main-app/compartido/noticias-publicaciones-cargar.php
   ```

4. **Verifica consola por errores:**
   ```
   F12 → Console → Busca líneas en ROJO
   ```

---

## ✅ LO QUE DEBE PASAR:

### Modales:
```
✅ Solo área de adjuntar (sin textarea)
✅ Botón deshabilitado al abrir (gris)
✅ Preview aparece al seleccionar
✅ Botón se habilita (morado)
✅ Logs en consola
```

### Buscador:
```
✅ Logs al escribir
✅ Espera 800ms
✅ Busca automáticamente
✅ Filtra posts
✅ Botón X borra y resetea
✅ Logs en cada paso
```

---

## 📞 REPORTE DE PROBLEMAS

Si algo NO funciona:

1. **Abre F12 → Console**
2. **Copia TODOS los logs** que veas
3. **Envíalos** para poder ayudarte
4. **Revisa también:**
   - Network tab (peticiones)
   - errores_local.log (errores PHP)

---

## ✅ CHECKLIST

Prueba cada uno:

- [ ] Consola muestra: "✅ Feed Moderno inicializado"
- [ ] Consola muestra: "✅ Buscador configurado correctamente"
- [ ] Al escribir, aparecen logs
- [ ] Botón X aparece al escribir
- [ ] Búsqueda filtra posts
- [ ] Botón X limpia búsqueda
- [ ] Modal Foto sin textarea
- [ ] Modal Foto botón deshabilitado inicial
- [ ] Modal Foto preview funciona
- [ ] Modal Foto botón se habilita
- [ ] Modal Video sin textarea
- [ ] Modal Video preview funciona
- [ ] Modal Archivo sin textarea
- [ ] Modal Archivo preview funciona

---

Si TODOS los logs aparecen pero NO filtra, el problema es PHP.
Si NO aparecen logs, el problema es JavaScript.

¡Prueba ahora y dime qué logs ves! 🔍

