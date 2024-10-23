function toggleDropdown(id) {
  const dropdown = document.getElementById(id);
  dropdown.style.display =
    dropdown.style.display === "none" || dropdown.style.display === ""
      ? "block"
      : "none";
}

function loadPage(page) {
  const pageContent = document.getElementById("pageContent");
  fetch(page)
    .then((response) => response.text())
    .then((data) => {
      pageContent.innerHTML = data;
      console.log("Page loaded: " + page);

      if (page.includes("create_invoice.php")) {
        const form = document.querySelector("form");
        form.addEventListener("submit", function (event) {
          event.preventDefault();

          const formData = new FormData(form);
          fetch("invoices/create_invoice.php", {
            method: "POST",
            body: formData,
          })
            .then((response) => response.text())
            .then((result) => {
              alert("Invoice created successfully!");
              window.location.href = "invoices/view_invoices.php";
            })
            .catch((error) => console.error("Error:", error));
        });
      }
    })
    .catch((error) => {
      pageContent.innerHTML = "<p>Error loading page.</p>";
      console.error("Error:", error);
    });
}
function addProductRow() {
  const productTable = document
    .getElementById("productTable")
    .getElementsByTagName("tbody")[0];
  const newRow = productTable.insertRow();

  newRow.innerHTML = `
<td>
    <select class="productSelect" name="product_id[]" onchange="updateProductDetails(this)" required>
        <option value="">Select a product</option>
        ${document.getElementById("productOptionsTemplate").innerHTML}
    </select>
</td>
<td>
    <input type="number" class="quantity" name="quantity[]" min="1" value="1" placeholder="Qty" required oninput="calculateSubtotal(this)">
</td>
<td>
    <input type="number" class="price" name="price[]" step="0.01" placeholder="Price (₦)" required readonly>
</td>
<td>
    <input type="number" class="discount" name="discount[]" step="0.01" placeholder="Discount" oninput="calculateSubtotal(this)">
</td>
<td>
    <input type="text" class="subtotal" name="subtotal[]" placeholder="Subtotal" readonly>
</td>
<td>
    <button type="button" onclick="removeProductRow(this)">
      <img src="/project/icons/icons-remove.png" alt="" style="width: 25px; height: 25px; ">
    </button>
</td>
`;

  calculateTotal(); 
}
function removeProductRow(button) {
  const row = button.parentElement.parentElement;
  row.remove();
  calculateTotal();
}

function updateProductDetails(select) {
  const selectedOption = select.options[select.selectedIndex];
  const price = selectedOption.getAttribute("data-price");
  const row = select.parentElement.parentElement;
  const priceInput = row.querySelector(".price");
  priceInput.value = price || 0;

  calculateSubtotal(priceInput);

  const productIdInput = row.querySelector(".productSelect");
  const productId = productIdInput.value;
  console.log("Selected Product ID:", productId);
}

function calculateSubtotal(inputElement) {
  const row = inputElement.parentElement.parentElement;

  const price =
    parseFloat(row.querySelector(".price").value.replace(/,/g, "")) || 0;
  const quantity = parseFloat(row.querySelector(".quantity").value) || 1;
  const discount = parseFloat(row.querySelector(".discount").value) || 0;
  const subtotal = price * quantity - discount;
  row.querySelector(".subtotal").value = subtotal.toLocaleString("en-US", {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2,
  });

  calculateTotal(); 
}

function calculateTotal() {
  let total = 0;
  const subtotals = document.querySelectorAll(".subtotal");

  subtotals.forEach(function (subtotalField) {
    const subtotal = parseFloat(subtotalField.value.replace(/,/g, "")) || 0;
    total += subtotal;
  });
  const totalField = document.getElementById("total");
  if (totalField) {
    totalField.value = total.toLocaleString("en-US", {
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    });
  } else {
    console.error("Total field not found");
  }
}

document.addEventListener("DOMContentLoaded", function () {
  const addRowButton = document.getElementById("addProductRowButton");
  if (addRowButton) {
    addRowButton.addEventListener("click", addProductRow);
  }

  const productTable = document.getElementById("productTable");
  productTable.addEventListener("change", function (e) {
    if (e.target && e.target.classList.contains("productSelect")) {
      updateProductDetails(e.target);
    }
  });

  productTable.addEventListener("input", function (e) {
    if (
      e.target &&
      (e.target.classList.contains("quantity") ||
        e.target.classList.contains("discount"))
    ) {
      calculateSubtotal(e.target);
    }
  });
});
