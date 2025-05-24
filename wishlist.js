document.addEventListener("DOMContentLoaded", () => {
    // Función para manejar la adición a la lista de deseos
    function setupWishlistButtons() {
      // Seleccionar todos los botones de lista de deseos
      const wishlistButtons = document.querySelectorAll('.btn-wishlist, .wishlist-btn, [data-action="wishlist"]')
  
      // Si no hay botones, salir de la función sin error
      if (!wishlistButtons || wishlistButtons.length === 0) {
        console.log("No se encontraron botones de lista de deseos")
        return
      }
  
      // Agregar event listener a cada botón
      wishlistButtons.forEach((button) => {
        // Evitar añadir múltiples event listeners
        button.removeEventListener("click", handleWishlistClick)
        button.addEventListener("click", handleWishlistClick)
      })
    }
  
    // Función para manejar el clic en botones de lista de deseos
    function handleWishlistClick(e) {
      e.preventDefault()
  
      // Verificar si el usuario está logueado
      const isLoggedIn = document.querySelector(".profile-dropdown") !== null
  
      if (!isLoggedIn) {
        // Redirigir al login si no está logueado
        const currentUrl = encodeURIComponent(window.location.href)
        window.location.href = `login.php?redirect=${currentUrl}`
        return
      }
  
      // Obtener el ID del producto
      let productId
  
      // Intentar obtener el ID del producto de diferentes formas
      if (this.dataset.productId) {
        productId = this.dataset.productId
      } else if (this.closest("form") && this.closest("form").querySelector('input[name="id"]')) {
        productId = this.closest("form").querySelector('input[name="id"]').value
      } else if (this.getAttribute("href")) {
        const url = new URL(this.getAttribute("href"), window.location.origin)
        productId = url.searchParams.get("id")
      }
  
      // Asegúrate de que productId sea un número
      if (productId) {
        productId = parseInt(productId, 10);
      }
  
      if (!productId) {
        console.error("No se pudo determinar el ID del producto")
        showNotification("Error: No se pudo identificar el producto", "error")
        return
      }
  
      // Enviar solicitud AJAX
      fetch("add_to_wishlist.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-Requested-With": "XMLHttpRequest",
        },
        body: JSON.stringify({ productId: productId }),
      })
        .then((response) => response.json())
        .then((data) => {
          showNotification(data.message, data.success ? "success" : "error")
  
          // Si es exitoso, actualizar la UI si es necesario
          if (data.success) {
            // Cambiar el estilo del botón para indicar que está en la lista
            this.classList.add("in-wishlist")
  
            // Actualizar el contador de la lista de deseos si existe
            const wishlistCounter = document.querySelector(".wishlist-count")
            if (wishlistCounter) {
              const currentCount = Number.parseInt(wishlistCounter.textContent || "0")
              wishlistCounter.textContent = currentCount + 1
            }
          }
        })
        .catch((error) => {
          console.error("Error:", error)
          showNotification("Error al procesar la solicitud", "error")
        })
    }
  
    // Configurar los botones al cargar la página
    setupWishlistButtons()
  
    // Mostrar mensajes de éxito o error si existen en la URL
    function showWishlistMessages() {
      const urlParams = new URLSearchParams(window.location.search)
  
      if (urlParams.has("wishlist_success")) {
        showNotification("¡Producto añadido a tu lista de deseos!", "success")
      } else if (urlParams.has("wishlist_error")) {
        const errorMsg =
          urlParams.get("wishlist_error") === "already_exists"
            ? "Este producto ya está en tu lista de deseos"
            : "Error al añadir a la lista de deseos"
        showNotification(errorMsg, "error")
      }
    }
  
    // Función para mostrar notificaciones
    function showNotification(message, type = "success") {
      // Verificar si ya existe un contenedor de notificaciones
      let notificationContainer = document.getElementById("notification-container")
  
      if (!notificationContainer) {
        notificationContainer = document.createElement("div")
        notificationContainer.id = "notification-container"
        notificationContainer.style.position = "fixed"
        notificationContainer.style.top = "20px"
        notificationContainer.style.right = "20px"
        notificationContainer.style.zIndex = "9999"
        document.body.appendChild(notificationContainer)
      }
  
      // Crear la notificación
      const notification = document.createElement("div")
      notification.className = `notification ${type}`
      notification.innerHTML = `
              <div class="notification-content">
                  <i class="fas fa-${type === "success" ? "check-circle" : "exclamation-circle"}"></i>
                  <span>${message}</span>
              </div>
              <button class="notification-close">&times;</button>
          `
  
      // Añadir estilos inline para asegurar que se muestren correctamente
      notification.style.backgroundColor = "white"
      notification.style.borderRadius = "8px"
      notification.style.boxShadow = "0 4px 12px rgba(0, 0, 0, 0.15)"
      notification.style.padding = "15px"
      notification.style.marginBottom = "10px"
      notification.style.display = "flex"
      notification.style.alignItems = "center"
      notification.style.justifyContent = "space-between"
      notification.style.maxWidth = "350px"
      notification.style.opacity = "0"
      notification.style.transition = "opacity 0.3s ease"
      notification.style.borderLeft = `4px solid ${type === "success" ? "#2ecc71" : "#e74c3c"}`
  
      // Añadir al contenedor
      notificationContainer.appendChild(notification)
  
      // Mostrar con animación
      setTimeout(() => {
        notification.style.opacity = "1"
      }, 10)
  
      // Configurar cierre automático
      const timeout = setTimeout(() => {
        notification.style.opacity = "0"
        setTimeout(() => {
          notification.remove()
        }, 300)
      }, 5000)
  
      // Configurar botón de cierre
      const closeBtn = notification.querySelector(".notification-close")
      closeBtn.style.background = "none"
      closeBtn.style.border = "none"
      closeBtn.style.fontSize = "20px"
      closeBtn.style.cursor = "pointer"
      closeBtn.style.color = "#999"
  
      closeBtn.addEventListener("click", () => {
        clearTimeout(timeout)
        notification.style.opacity = "0"
        setTimeout(() => {
          notification.remove()
        }, 300)
      })
    }
  
    // Verificar mensajes al cargar
    function checkUrlMessages() {
      const urlParams = new URLSearchParams(window.location.search)
  
      if (urlParams.has("wishlist_success")) {
        showNotification("¡Producto añadido a tu lista de deseos!", "success")
      } else if (urlParams.has("wishlist_error")) {
        const errorMsg =
          urlParams.get("wishlist_error") === "already_exists"
            ? "Este producto ya está en tu lista de deseos"
            : "Error al añadir a la lista de deseos"
        showNotification(errorMsg, "error")
      }
    }
  
    // Inicializar los botones y verificar mensajes
    checkUrlMessages()
  
    // Para páginas con carga dinámica de contenido, puedes exponer la función de inicialización
    window.initWishlistButtons = setupWishlistButtons
  })
  
