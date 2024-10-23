<div class="sidebar">
    <h2><a href="#" onclick="loadDashboard()">Dashboard</a></h2>
    <ul>
        <li>
            <a href="#" onclick="toggleDropdown('invoiceDropdown')">Receipt</a>
            <ul id="invoiceDropdown" style="display:none;">
                <li><a href="#" onclick="loadPage('invoices/create_invoice.php')">Create Receipt</a></li>
                <li><a href="#" onclick="loadPage('invoices/view_invoices.php')">View Receipt</a></li>
            </ul>
        </li>
        <li>
            <a href="#" onclick="toggleDropdown('productDropdown')">Products</a>
            <ul id="productDropdown" style="display:none;">
                <li><a href="#" onclick="loadPage('products/add_product.php')">Add Product</a></li>
                <li><a href="#" onclick="loadPage('products/view_products.php')">View Products</a></li>
            </ul>
        </li>

        <li><a href="settings.php">Settings</a></li>
    </ul>
</div>
