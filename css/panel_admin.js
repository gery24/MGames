document.addEventListener("DOMContentLoaded", () => {
    // Get all module elements
    const gamesModule = document.getElementById("games-module")
    const eventsModule = document.getElementById("events-module")
    const blogsModule = document.getElementById("blogs-module")
  
    // Get all content sections
    const gamesContent = document.getElementById("games-content")
    const eventsContent = document.getElementById("events-content")
    const blogsContent = document.getElementById("blogs-content")
  
    // Hide all content sections initially
    gamesContent.style.display = "none"
    eventsContent.style.display = "none"
    blogsContent.style.display = "none"
  
    // Add click event listeners to each module
    gamesModule.addEventListener("click", () => {
      // Hide all content sections
      eventsContent.style.display = "none"
      blogsContent.style.display = "none"
  
      // Show games content
      gamesContent.style.display = "block"
  
      // Update active state
      gamesModule.classList.add("active")
      eventsModule.classList.remove("active")
      blogsModule.classList.remove("active")
    })
  
    eventsModule.addEventListener("click", () => {
      // Hide all content sections
      gamesContent.style.display = "none"
      blogsContent.style.display = "none"
  
      // Show events content
      eventsContent.style.display = "block"
  
      // Update active state
      eventsModule.classList.add("active")
      gamesModule.classList.remove("active")
      blogsModule.classList.remove("active")
    })
  
    blogsModule.addEventListener("click", () => {
      // Hide all content sections
      gamesContent.style.display = "none"
      eventsContent.style.display = "none"
  
      // Show blogs content
      blogsContent.style.display = "block"
  
      // Update active state
      blogsModule.classList.add("active")
      gamesModule.classList.remove("active")
      eventsModule.classList.remove("active")
    })
  
    // Additional functionality for file uploads and image previews
    setupFileUploads()
  
    // Setup search functionality
    setupSearch()
  })
  
  function setupFileUploads() {
    // Setup for game image upload
    const imageInput = document.getElementById("imagen")
    const imagePreview = document.getElementById("image-preview")
    const fileNameDisplay = document.getElementById("file-name-display")
  
    if (imageInput && imagePreview && fileNameDisplay) {
      imageInput.addEventListener("change", function () {
        if (this.files && this.files[0]) {
          const reader = new FileReader()
  
          reader.onload = (e) => {
            imagePreview.src = e.target.result
            imagePreview.style.display = "block"
          }
  
          reader.readAsDataURL(this.files[0])
          fileNameDisplay.textContent = this.files[0].name
        } else {
          imagePreview.style.display = "none"
          fileNameDisplay.textContent = "Ningún archivo seleccionado"
        }
      })
    }
  
    // Setup for event image upload
    const eventImageInput = document.getElementById("imagen_evento")
    const eventImagePreview = document.getElementById("event-image-preview")
    const eventFileNameDisplay = document.getElementById("event-file-name-display")
  
    if (eventImageInput && eventImagePreview && eventFileNameDisplay) {
      eventImageInput.addEventListener("change", function () {
        if (this.files && this.files[0]) {
          const reader = new FileReader()
  
          reader.onload = (e) => {
            eventImagePreview.src = e.target.result
            eventImagePreview.style.display = "block"
          }
  
          reader.readAsDataURL(this.files[0])
          eventFileNameDisplay.textContent = this.files[0].name
        } else {
          eventImagePreview.style.display = "none"
          eventFileNameDisplay.textContent = "Ningún archivo seleccionado"
        }
      })
    }
  
    // Setup for blog image upload
    const blogImageInput = document.getElementById("imagen_blog")
    const blogImagePreview = document.getElementById("blog-image-preview")
    const blogFileNameDisplay = document.getElementById("blog-file-name-display")
  
    if (blogImageInput && blogImagePreview && blogFileNameDisplay) {
      blogImageInput.addEventListener("change", function () {
        if (this.files && this.files[0]) {
          const reader = new FileReader()
  
          reader.onload = (e) => {
            blogImagePreview.src = e.target.result
            blogImagePreview.style.display = "block"
          }
  
          reader.readAsDataURL(this.files[0])
          blogFileNameDisplay.textContent = this.files[0].name
        } else {
          blogImagePreview.style.display = "none"
          blogFileNameDisplay.textContent = "Ningún archivo seleccionado"
        }
      })
    }
  }
  
  function setupSearch() {
    // Search functionality for games
    const searchGamesInput = document.getElementById("search-games")
    if (searchGamesInput) {
      searchGamesInput.addEventListener("input", function () {
        const searchTerm = this.value.toLowerCase()
        const gameCards = document.querySelectorAll(".products-grid .product-card")
  
        gameCards.forEach((card) => {
          const title = card.querySelector("h3").textContent.toLowerCase()
          const category = card.querySelector(".product-category").textContent.toLowerCase()
  
          if (title.includes(searchTerm) || category.includes(searchTerm)) {
            card.style.display = "flex"
          } else {
            card.style.display = "none"
          }
        })
      })
    }
  
    // Search functionality for events
    const searchEventsInput = document.getElementById("search-events")
    if (searchEventsInput) {
      searchEventsInput.addEventListener("input", function () {
        const searchTerm = this.value.toLowerCase()
        const eventCards = document.querySelectorAll(".events-grid .event-card")
  
        eventCards.forEach((card) => {
          const title = card.querySelector(".event-title").textContent.toLowerCase()
          const location = card.querySelector(".event-location").textContent.toLowerCase()
  
          if (title.includes(searchTerm) || location.includes(searchTerm)) {
            card.style.display = "flex"
          } else {
            card.style.display = "none"
          }
        })
      })
    }
  
    // Search functionality for blogs
    const searchBlogsInput = document.getElementById("search-blogs")
    if (searchBlogsInput) {
      searchBlogsInput.addEventListener("input", function () {
        const searchTerm = this.value.toLowerCase()
        const blogCards = document.querySelectorAll(".blogs-grid .blog-card")
  
        blogCards.forEach((card) => {
          const title = card.querySelector(".blog-title").textContent.toLowerCase()
          const category = card.querySelector(".blog-category").textContent.toLowerCase()
          const author = card.querySelector(".blog-author").textContent.toLowerCase()
  
          if (title.includes(searchTerm) || category.includes(searchTerm) || author.includes(searchTerm)) {
            card.style.display = "flex"
          } else {
            card.style.display = "none"
          }
        })
      })
    }
  }
  