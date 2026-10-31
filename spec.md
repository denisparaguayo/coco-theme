# Especificación Técnica y de Diseño: Tema FSE para Coco Cherry Store (cococherrypy.com)

Tema de WordPress moderno, ultraligero y compatible con **Full Site Editing (FSE / Gutenberg)** especialmente diseñado para **Coco Cherry Store** (tienda e-commerce multicategoría de moda femenina, bijouterie, belleza, tecnología y artículos del hogar en Paraguay).

El tema está 100% optimizado para el modelo de conversión real de la tienda: **Flujo Exclusivo de Pago Contra Entrega (1-Clic COD con el plugin propio `Coco COD`)**, **Pedidos directos vía WhatsApp** e incluye un **sistema de auto-actualización continua desde GitHub** idéntico al de tu plugin para no tener que subir archivos ZIP repetidamente.

---

## 1. Identidad de Marca y Datos Reales de la Tienda

- **Nombre comercial:** Coco Cherry Store
- **Slogan / Descripción:** *Moda • Bijou • Electrónica • Hogar*
- **Sitio web:** `https://cococherrypy.com`
- **Ubicación:** Guaraníes 4779, Asunción, Paraguay
- **Canal de Ventas y Soporte:** WhatsApp `+595 981 531503` (`https://wa.me/595981531503`)
- **Moneda oficial:** Guaraní Paraguayo (Gs. / PYG) sin decimales (`120.000 Gs.`).
- **Logotipo oficial:** `COCO-NEW-LOGO.svg` (disponible en la biblioteca multimedia de WordPress).
- **Favicon:** `cropped-logo-1.jpg`.
- **Diferencial clave:** Pago Contra Entrega en efectivo o QR al recibir el paquete en puerta en Asunción, Gran Asunción e interior de Paraguay.

---

## 2. Arquitectura de Venta: Integración con Plugin `Coco COD` (Sin Carrito)

El sitio opera bajo la modalidad de **Modo Exclusivo Contra Entrega (1-Clic)**:
1. **Carrito Tradicional Deshabilitado:**
   - **No se incluye minicarrito, icono de carrito ni enlaces a `/cart/`** en el Header, menú ni navegación.
   - Cualquier intento de acceder a `/cart/` es redirigido por `Coco COD` a la tienda o home.
2. **Botones de Catálogo y Grids:**
   - En lugar del botón "Añadir al carrito", las tarjetas de producto en el Home, archivo de categorías y resultados de búsqueda muestran **"Ver producto"** o **"Pedir al recibir"**, llevando al usuario a la ficha de producto donde se inicia el pedido inmediato.
   - Enlace opcional de consulta rápida por WhatsApp prellenado con el nombre del producto.
3. **Ficha de Producto (`single-product`):**
   - El plugin `Coco COD` inyecta automáticamente el botón de acción principal **`#coco-cod-open-modal` ("Pagar al recibir en casa")** a través del hook estándar `woocommerce_after_add_to_cart_button`.
   - El tema debe respetar los hooks estándar de producto de WooCommerce (`woocommerce_before_single_product_summary`, `woocommerce_single_product_summary`, `woocommerce_after_add_to_cart_button`, `woocommerce_after_single_product` y `wp_footer`) para que el modal de 3 pasos (Entrega → Datos → Confirmar) y sus scripts operen sin interferencias.
   - Oculta el botón secundario "Añadir al carrito" nativo para concentrar el 100% de la conversión en el botón de pago contra entrega.
4. **Checkout Clásico y Agradecimiento:**
   - `Coco COD` procesa los pedidos vía AJAX contra el pipeline nativo de WooCommerce y redirige a la pantalla nativa de orden recibida (`/checkout/order-received/`), donde el plugin inyecta su plantilla personalizada `templates/thankyou.php` disparando el evento de compra del Pixel de Meta.
   - La página de checkout de respaldo utiliza el shortcode clásico `[woocommerce_checkout]` (requisito estricto de compatibilidad del Health Check de Coco COD).

---

## 3. Paleta de Colores y Tipografía

### Paleta de Colores (`theme.json` y CSS)
- **Primario (Cherry Orange / Brand Accent):** `#E47B02` / `#ED8F0C` (enlaces, botones de navegación, badges de categoría y ofertas).
- **Verde COD / WhatsApp (Acción & Confianza):** `#16A34A` / `#15803D` y `#25D366` (alineado con las variables `--coco-btn-bg` del plugin `Coco COD` y el botón flotante de WhatsApp).
- **Oscuro / Texto Principal:** `#1F2933` (títulos, encabezados, alta legibilidad).
- **Gris Neutro / Texto Secundario:** `#52606D` y `#7B8794` (descripciones, precios secundarios, bordes sutiles).
- **Fondos Claros:** `#F9F9FB` y `#FFFFFF` (tarjetas limpias, alto contraste).

### Tipografía
- Tipografía moderna para e-commerce (Inter, Poppins o sans-serif nativa de alto rendimiento).
- Jerarquía limpia enfocada en legibilidad móvil.

---

## 4. Requisitos Técnicos de Desarrollo

- **Arquitectura:** Tema de bloques nativo de WordPress (Full Site Editing - FSE).
- **Sin constructores pesados:** Reemplaza Kadence Blocks, Elementor y dependencias externas que ralentizan la tienda.
- **Bloques nativos FSE:** Utilizar bloques nativos de WordPress y bloques nativos de WooCommerce compatibles con el flujo 1-clic.
- **CSS centralizado y optimizado:**
  - Variables de tema y maquetación en `theme.json`.
  - Estilos específicos de componentes organizados en `style.css`.
  - Sin estilos inline en HTML ni librerías JS redundantes.
- **Preparado para empaque:** Código listo para comprimir en `coco-theme.zip` para la primera subida a WordPress.

---

## 5. Estructura de Archivos del Tema

```
coco-theme/
├── style.css               (Metadatos del tema y CSS limpio por secciones)
├── theme.json              (Paleta de colores, tipografía, espaciados y presets FSE)
├── functions.php           (Soporte WooCommerce, hooks, soporte SVG, cargador del updater)
├── inc/
│   └── class-updater.php   (Módulo de actualización continua desde GitHub)
├── templates/
│   ├── index.html          (Plantilla de respaldo / Blog)
│   ├── front-page.html     (Landing page principal orientada a conversión COD)
│   ├── single-product.html (Ficha de producto con integración completa de Coco COD)
│   ├── archive-product.html(Catálogo y categorías con botones directos)
│   ├── page.html           (Páginas de contenido: Quiénes Somos, FAQ, Contacto)
│   └── 404.html            (Página 404 amigable)
├── parts/
│   ├── header.html         (Top banner de avisos + Logo + Navegación + CTA WhatsApp)
│   └── footer.html         (Datos de tienda en Asunción, garantía COD, WhatsApp y políticas)
└── patterns/
    ├── top-bar.html        (Barra superior: Envíos a todo el país y Pago al recibir)
    ├── hero-banner.html    (Banner principal con llamados directos a WhatsApp y catálogo)
    ├── trust-badges.html   (Pilares: Pago Contra Entrega, Envíos a Paraguay, Garantía)
    ├── category-grid.html  (Grid de categorías: Moda, Bijou, Belleza, Hogar)
    ├── product-grid.html   (Catálogo de productos con botón "Ver producto / Pedir")
    ├── promo-banner.html   (Oferta destacada de temporada)
    ├── customer-reviews.html (Testimonios reales de clientas de Asunción e interior)
    ├── faq-accordion.html  (Preguntas frecuentes sobre entrega en puerta y pedidos)
    └── whatsapp-floating.html (Acceso rápido flotante a WhatsApp)
```

---

## 6. Estructura Detallada de la Página Principal (`front-page.html`)

1. **Top Notice Bar:**
   - *"📦 Envíos seguros a todo Paraguay • 💵 Pagá en efectivo o QR al recibir en tu puerta • 📲 Pedidos: 0981 531503"*
2. **Header (Sin Carrito):**
   - Logo oficial `COCO-NEW-LOGO.svg`.
   - Navegación principal: *Inicio, Moda, Bijou, Belleza, Hogar & Tecnología, Ofertas*.
   - Barra de búsqueda de productos.
   - Botón directo de contacto / pedido: *"WhatsApp 0981 531503"*.
3. **Hero Section:**
   - Título de impacto: *"Tendencias en Moda, Accesorios y Artículos para tu Hogar"*.
   - Subtítulo: *"Elegí tu producto online y aboná recién al recibirlo en tu puerta en cualquier punto de Paraguay."*
   - Botón 1 (Primario): *"Explorar Catálogo"* (desplaza suavemente al catálogo).
   - Botón 2 (Secundario / WhatsApp): *"Pedir por WhatsApp"*.
4. **Pilares de Confianza (Trust Badges):**
   - 💵 **Pago Contra Entrega:** Cero riesgo. Abonás en efectivo o QR al recibir tu paquete.
   - 🚚 **Envíos a Todo Paraguay:** Entregas rápidas en Asunción, Gran Asunción y todas las ciudades del interior.
   - ✨ **Calidad & Tendencia:** Cuidada selección de productos en moda, bijouterie y tecnología.
   - 📲 **Atención 1 a 1 por WhatsApp:** Asesoramiento directo antes, durante y después de tu compra (`0981 531503`).
5. **Grid de Categorías Principales:**
   - Bijouterie & Accesorios (Aros, Collares).
   - Moda & Indumentaria (Vestidos, Lencería fina, Crop Tops, Fajas).
   - Belleza & Bienestar (Cuidado facial, Protector Solar Oro 24K FPS 90+).
   - Tecnología & Hogar (Gadgets y electrodomésticos prácticos).
6. **Catálogo de Productos Destacados:**
   - Cuadrícula responsiva con imagen, título, precio en Guaraníes (`Gs.`).
   - Botón de acción: *"Ver producto / Pedir al recibir"*, que lleva directo a la ficha para desplegar el modal de compra en 1 clic.
7. **Banner Promocional / Destacado de Temporada:**
   - Presentación destacada de producto estrella (ej. Protector Solar Oro 24K o nueva colección) con botón directo al producto.
8. **Testimonios de Clientes:**
   - Opiniones destacando la tranquilidad del pago en puerta y la puntualidad del repartidor.
9. **Preguntas Frecuentes (FAQ):**
   - ¿Cómo funciona el pago contra entrega? (Abonás al repartidor en efectivo o QR).
   - ¿A qué zonas envían? (Asunción, Gran Asunción y coordinación con transportadoras a todo el país).
   - ¿Cómo elijo mi talle o color? (En la misma ficha o asesorándote por WhatsApp).
10. **Llamado a la Acción Final (CTA):**
    - *"¿Tenés dudas o querés hacer tu pedido directo? Escribinos por WhatsApp al 0981 531503"*.
11. **Footer:**
    - Logo e información de la tienda.
    - Dirección física: Guaraníes 4779, Asunción, Paraguay.
    - Teléfono / WhatsApp: `+595 981 531503`.
    - Enlaces a Contacto, Quiénes Somos, FAQ y Políticas.
    - Botón flotante de WhatsApp accesible en mobile y desktop.

---

## 7. Notificaciones de Compra en Vivo (`Sales Pop` / Social Proof Toast)

Componente nativo ultraligero integrado en el tema (`functions.php` + `style.css`) para mostrar avisos flotantes de compras recientes abajo a la izquierda:

1. **Diseño Visual (UI):**
   - Tarjeta flotante en esquina inferior izquierda (`bottom: 20px; left: 20px; z-index: 9980`).
   - Miniatura real del producto a la izquierda (`68x68px`, bordes redondeados, enlace directo al producto).
   - **Línea 1:** `"[Nombre] en [Ciudad de Paraguay] compró"` + botón `✕` para cerrar.
   - **Línea 2:** Título real del producto en negrita (máximo 2 líneas, clicable hacia la ficha del producto).
   - **Línea 3:** Tiempo relativo (`"hace 18 minutos"`, `"hace 2 horas"`) + sello **`✓ Verificada`** + icono informativo `ⓘ`.
2. **Sistema de Colores (Por Defecto del Tema + Personalizable):**
   - Por defecto hereda las variables globales de `coco-theme` (`--coco-white`, `--coco-border`, `--coco-dark`, `--coco-muted`, `--coco-cod-green`, `--coco-primary`).
   - Incluye selectores de color (`WP_Customize_Color_Control`) en **Apariencia > Personalizar > Opciones Coco Cherry Store** para personalizar en vivo:
     - Color de fondo de la tarjeta (`#FFFFFF`)
     - Color de borde (`#E4E7EB`)
     - Color del título del producto (`#1F2933`)
     - Color del texto secundario (`#52606D`)
     - Color del sello `"✓ Verificada"` (`#16A34A`)
3. **Fuente de Datos Híbrida (Con Caché Transient):**
   - **Prioridad 1:** Extrae pedidos recientes reales de WooCommerce (`processing` y `completed`), mostrando únicamente el primer nombre del comprador, su ciudad y el producto adquirido.
   - **Prioridad 2:** Completa automáticamente hasta 12 notificaciones con productos publicados reales del catálogo de WooCommerce (priorizando productos destacados/estrella) combinados con nombres y ciudades reales de Paraguay (*Asunción, San Lorenzo, Luque, Ciudad del Este, Encarnación, Lambaré, Fernando de la Mora, Capiatá, Ñemby, Mariano Roque Alonso, Villarrica, Caaguazú*).
4. **Comportamiento y UX:**
   - Se pausa al pasar el cursor por encima (`mouseenter`).
   - Si el visitante pulsa `✕`, guarda el estado en `sessionStorage` para no volver a mostrarse durante esa sesión.
   - Se oculta automáticamente en la página de checkout y cuando el modal de compra contra entrega de `Coco COD` está abierto.

---

## 8. Módulo de Actualización Continua desde GitHub (`inc/class-updater.php`)

Para evitar tener que generar y subir manualmente un archivo ZIP cada vez que se realicen ajustes o mejoras en el tema, se incorpora un gestor de actualización basado en la misma lógica que `Coco COD`:
1. **Repositorio:** Conectado a `https://github.com/denisparaguayo/coco-theme` (rama `main`).
2. **Comprobación de Versiones:**
   - Hook nativo `pre_set_site_transient_update_themes`: compara la versión local de `style.css` con el encabezado de versión en GitHub (`RAW_FILE_URL`).
   - Si la versión en GitHub es superior (ej. de `1.0.0` a `1.0.1`), WordPress muestra automáticamente el aviso de actualización en **Escritorio > Actualizaciones** y en **Apariencia > Temas**.
3. **Filtro de Descompresión (`upgrader_source_selection`):**
   - GitHub descarga los repositorios como `coco-theme-main/`. El filtro normaliza la carpeta de destino a `coco-theme/` para que la actualización sobrescriba el tema en su misma carpeta sin romper rutas ni crear carpetas duplicadas.
4. **Panel de Control Manual:**
   - Página en el menú del administrador: **Apariencia > Actualizar Coco Theme** con:
     - Versión instalada vs. Versión remota en GitHub.
     - Botón **"Comprobar actualizaciones"** (limpia transitorios y consulta en vivo).
     - Botón **"Actualizar ahora desde GitHub"** (descarga, descomprime y aplica cambios al instante con 1 solo clic).

---

## 9. Rendimiento y Entrega

- **Carga ultrarrápida:** Sin librerías pesadas, optimizado para conexiones móviles en Paraguay.
- **Flujo de trabajo:**
  1. Generar la versión base `v1.0.0` en `coco-theme.zip`.
  2. Subirla a WordPress por primera y única vez vía *Apariencia > Temas > Subir tema*.
  3. Vincular el código con el repositorio de GitHub `denisparaguayo/coco-theme`.
  4. Para futuros cambios: simplemente hacer `git push`, subir versión en `style.css` y pulsar **"Actualizar"** en WordPress.
