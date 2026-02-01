// Main JavaScript for Product Inventory System

document.addEventListener("DOMContentLoaded", function () {
  // Auto-dismiss alerts after 5 seconds
  setTimeout(function () {
    const alerts = document.querySelectorAll(".alert");
    alerts.forEach((alert) => {
      const bsAlert = new bootstrap.Alert(alert);
      bsAlert.close();
    });
  }, 5000);

  // Category selection handling in add.php
  const categorySelect = document.getElementById("category");
  const newCategoryInput = document.getElementById("new_category");

  if (categorySelect && newCategoryInput) {
    newCategoryInput.addEventListener("input", function () {
      if (this.value.trim() !== "") {
        categorySelect.value = "";
      }
    });

    categorySelect.addEventListener("change", function () {
      if (this.value !== "") {
        newCategoryInput.value = "";
      }
    });
  }

  // Price validation
  const priceInputs = document.querySelectorAll('input[name="price"]');
  priceInputs.forEach((input) => {
    input.addEventListener("blur", function () {
      if (this.value <= 0) {
        this.classList.add("is-invalid");
        showError(this, "Price must be greater than 0");
      } else {
        this.classList.remove("is-invalid");
      }
    });
  });

  // Stock validation
  const stockInputs = document.querySelectorAll('input[name="stock_quantity"]');
  stockInputs.forEach((input) => {
    input.addEventListener("blur", function () {
      if (this.value < 0) {
        this.classList.add("is-invalid");
        showError(this, "Stock cannot be negative");
      } else {
        this.classList.remove("is-invalid");
      }
    });
  });

  // Helper function to show error
  function showError(input, message) {
    let errorDiv = input.parentElement.querySelector(".invalid-feedback");
    if (!errorDiv) {
      errorDiv = document.createElement("div");
      errorDiv.className = "invalid-feedback";
      input.parentElement.appendChild(errorDiv);
    }
    errorDiv.textContent = message;
  }

  // Search autocomplete (basic implementation)
  const searchInput = document.querySelector(
    'input[name="search"], input[name="name"]',
  );
  if (searchInput) {
    searchInput.addEventListener(
      "input",
      debounce(function (e) {
        const query = e.target.value;
        if (query.length > 2) {
          fetchAutocompleteSuggestions(query);
        }
      }, 300),
    );
  }
});

// Debounce function to limit API calls
function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

// Fetch autocomplete suggestions
function fetchAutocompleteSuggestions(query) {
  // This would typically make an AJAX call to a backend endpoint
  // For now, we'll just log the query
  console.log("Searching for:", query);

  // Example AJAX implementation:
  /*
    fetch(`api/search_suggestions.php?q=${encodeURIComponent(query)}`)
        .then(response => response.json())
        .then(data => {
            // Update autocomplete dropdown
            updateAutocompleteDropdown(data);
        })
        .catch(error => console.error('Error:', error));
    */
}
